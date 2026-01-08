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
            const rideId = (e.ride && e.ride.id) || (e.ride_id) || window.ride;
            const shouldProcess = rideId && rideId == window.ride;
            
            if (shouldProcess) {
                // Format message to match database structure - handle both event formats
                const newMessage = {
                    id: (e.message && e.message.id) || e.message_id || e.message?.id,
                    message: (e.message && e.message.message) || e.message || (typeof e.message === 'object' ? e.message.message : null),
                    sender: (e.message && e.message.sender) || e.user_id || (e.user && e.user.id),
                    receiver: (e.message && e.message.receiver) || e.receiver_id || (e.message && e.message.receiver),
                    ride_id: (e.message && e.message.ride_id) || e.ride_id || rideId,
                    redirect: '0', // Regular message, not a redirect
                    user: {
                        id: (e.user && e.user.id) || e.user_id || (e.message && e.message.sender),
                        first_name: (e.user && e.user.first_name) || '',
                        last_name: (e.user && e.user.last_name) || '',
                        profile_image: (e.user && e.user.profile_image) || ''
                    },
                    created_at: (e.message && e.message.created_at) || e.created_at || new Date().toISOString()
                };
                
                if (window.isOnChatDetailPage) {
                    // Reload the full chat after receiving a real-time message
                    this.fetchMessages();
                    // Optionally update chats preview as well
                    if (!this.chats.some(m => m.id === newMessage.id && m.created_at === newMessage.created_at)) {
                        this.chats.push(newMessage);
                    }
                    this.$nextTick(() => {
                        const chatContainer = document.querySelector('.panel-body');
                        if (chatContainer) {
                            chatContainer.scrollTop = chatContainer.scrollHeight;
                        }
                    });
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
        if (authId) {
            Echo.channel(`chat.${authId}`)
                .listen('.message.event', handleMessage)
                .listen('MessageSentEvent', handleMessage); // Fallback for old format
        }
        
        // Also listen on old channel format for backward compatibility
        Echo.channel('chat')
            .listen('MessageSentEvent', handleMessage);
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