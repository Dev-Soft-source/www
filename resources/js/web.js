require("./bootstrap");

import { createApp } from 'vue';
import axios from 'axios';
import ChatMessages from './components/ChatMessages.vue';
import ChatForm from './components/ChatForm.vue';

const app = createApp({
    data() {
        return {
            messages: [],
            chats: [],
        };
    },
    created() {
        this.fetchMessages();
        
        const authId = window.authUserId || window.logged_in_user_id || (window.Laravel && window.Laravel.userId);
        const handleMessage = (e) => {
            console.log('Pusher message received:', e);
            
            // Handle both old and new event structures
            const rideId = (e.ride && e.ride.id) || (e.ride_id) || (e.message && e.message.ride_id);
            const messageSender = parseInt((e.message && e.message.sender) || e.user_id || (e.user && e.user.id));
            const messageReceiver = parseInt((e.message && e.message.receiver) || e.receiver_id || (e.message && e.message.receiver));
            const currentPassenger = parseInt(window.passenger);
            const currentUserId = parseInt(authId);
            const currentRideId = parseInt(window.ride);
            
            console.log('Message details:', {
                rideId: rideId,
                messageSender,
                messageReceiver,
                currentPassenger,
                currentUserId,
                currentRideId
            });
            
            // Check if message is for current conversation
            // Message should match: same ride ID, and involves current user
            const rideMatches = rideId && currentRideId && parseInt(rideId) === currentRideId;
            const involvesCurrentUser = messageSender === currentUserId || messageReceiver === currentUserId;
            
            // If we have a passenger ID, also check that the message involves that user
            let shouldProcess = rideMatches && involvesCurrentUser;
            if (currentPassenger && window.isOnChatDetailPage) {
                // On chat detail page, only show messages involving the current passenger
                const involvesPassenger = messageSender === currentPassenger || messageReceiver === currentPassenger;
                shouldProcess = shouldProcess && involvesPassenger;
            }
            
            console.log('Should process message:', shouldProcess, {
                rideMatches,
                involvesCurrentUser,
                involvesPassenger
            });
            
            if (shouldProcess) {
                // Format message to match database structure - handle both event formats
                const newMessage = {
                    id: (e.message && e.message.id) || e.message_id || e.message?.id,
                    message: (e.message && e.message.message) || e.message || (typeof e.message === 'object' ? e.message.message : null),
                    sender: messageSender,
                    receiver: messageReceiver,
                    ride_id: (e.message && e.message.ride_id) || e.ride_id || rideId,
                    redirect: '0', // Regular message, not a redirect
                    user: {
                        id: (e.user && e.user.id) || e.user_id || messageSender,
                        first_name: (e.user && e.user.first_name) || '',
                        last_name: (e.user && e.user.last_name) || '',
                        profile_image: (e.user && e.user.profile_image) || ''
                    },
                    created_at: (e.message && e.message.created_at) || e.created_at || new Date().toISOString()
                };
                
                if (window.isOnChatDetailPage) {
                    // Immediately add message to messages array for instant display
                    if (!this.messages.some(m => m.id === newMessage.id)) {
                        this.messages.push(newMessage);
                        // Scroll to bottom after adding message
                        this.$nextTick(() => {
                            const chatContainer = document.querySelector('.panel-body');
                            if (chatContainer) {
                                chatContainer.scrollTop = chatContainer.scrollHeight;
                            }
                        });
                    }
                    // Also update chats preview
                    if (!this.chats.some(m => m.id === newMessage.id && m.created_at === newMessage.created_at)) {
                        this.chats.push(newMessage);
                    }
                    // Optionally fetch to ensure consistency (but message already displayed)
                    setTimeout(() => {
                        this.fetchMessages();
                    }, 500);
                } else {
                    // Inbox view: update only chats preview/list (do not touch messages)
                    // Optionally update or highlight chat preview for this ride/user
                    if (!this.chats.some(m => m.id === newMessage.id && m.created_at === newMessage.created_at)) {
                        this.chats.push(newMessage);
                    }
                }
            }
        };
        
        // Listen on new format: chat.{userId} with message.event
        if (authId && window.Echo) {
            console.log('Initializing Echo listener for user:', authId);
            
            const userChannel = Echo.channel(`chat.${authId}`);
            
            // Log when channel is subscribed
            userChannel.subscribed(() => {
                console.log('✅ Successfully subscribed to chat channel: chat.' + authId);
            });
            
            // Log any subscription errors
            userChannel.error((error) => {
                console.error('❌ Error subscribing to chat channel:', error);
            });
            
            // Listen for the message event (with dot prefix because of broadcastAs)
            userChannel.listen('.message.event', (data) => {
                console.log('📨 Received .message.event:', data);
                handleMessage(data);
            });
            
            // Fallback for old format without broadcastAs
            userChannel.listen('MessageSentEvent', (data) => {
                console.log('📨 Received MessageSentEvent (fallback):', data);
                handleMessage(data);
            });
            
            // Test connection
            console.log('Echo initialized and listening on chat.' + authId);
            console.log('Echo connection state:', window.Echo ? 'Connected' : 'Not connected');
        } else {
            console.error('❌ Echo or authId not available. Real-time chat may not work.', {
                hasEcho: !!window.Echo,
                authId: authId,
                windowAuthUserId: window.authUserId,
                windowLoggedInUserId: window.logged_in_user_id
            });
        }
        
        // Also listen on old channel format for backward compatibility
        if (window.Echo) {
            const globalChannel = Echo.channel('chat');
            globalChannel.listen('MessageSentEvent', (data) => {
                console.log('📨 Received from global chat channel:', data);
                handleMessage(data);
            });
        }
    },
    methods: {
        fetchMessages() {
            const rideId = window.ride;
            const userId = window.passenger;
            axios.get(`/chat-messages/${rideId}/${userId}`).then(response => {
                if (Array.isArray(response.data) && response.data.length > 0) {
                    this.messages = response.data;
                } else {
                    // Do NOT clear messages on empty response, to prevent accidental UI wipeout
                    console.warn('Received empty or invalid chat thread from server; retaining previous messages.');
                }
            });
            axios.get(`/chat-details/${userId}`).then(response => {
                this.chats = response.data;
            });
        },
        addMessage(message) {
            if (!this.messages.some(m => m.id === message.id && m.created_at === message.created_at)) {
                this.messages.push(message);
            }
            const userId = window.passenger;
            axios.post('/chat-messages', { ...message, userId }).then(response => {
                // After sending, always refetch full message list
                this.fetchMessages();
            });
        }
    }
});

app.component('chat-messages', ChatMessages);
app.component('chat-form', ChatForm);

app.mount('#ridesharing_app');