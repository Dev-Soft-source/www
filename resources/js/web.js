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
            _messagesBackup: [], // Internal backup to prevent data loss
        };
    },
    watch: {
        // Watch messages array to detect if it's being cleared unexpectedly
        messages: {
            handler(newMessages, oldMessages) {
                // Update backup whenever messages change (for recovery)
                if (newMessages && newMessages.length > 0) {
                    this._messagesBackup = newMessages.map(msg => ({ ...msg }));
                }
                
                // Only log warnings, don't auto-restore to avoid loops
                if (oldMessages && oldMessages.length > 0 && newMessages.length === 0) {
                    console.error('CRITICAL ERROR: Messages array was cleared! Old count:', oldMessages.length);
                    console.error('Attempting to restore from backup...');
                    // Try to restore from backup if available
                    if (this._messagesBackup && this._messagesBackup.length > 0) {
                        console.error('Restoring', this._messagesBackup.length, 'messages from backup');
                        this.messages = this._messagesBackup.map(msg => ({ ...msg }));
                    }
                } else if (oldMessages && newMessages.length < oldMessages.length && newMessages.length > 0) {
                    // Check if we lost messages (but not all of them)
                    const lostCount = oldMessages.length - newMessages.length;
                    if (lostCount > 1) { // More than 1 message lost (1 is OK if it was a temp message being replaced)
                        console.warn('WARNING: Lost', lostCount, 'messages. Old:', oldMessages.length, 'New:', newMessages.length);
                        // Try to restore missing messages from backup
                        if (this._messagesBackup && this._messagesBackup.length > newMessages.length) {
                            const missingIds = new Set(newMessages.map(m => String(m.id)));
                            const missingMessages = this._messagesBackup.filter(m => !missingIds.has(String(m.id)));
                            if (missingMessages.length > 0) {
                                console.warn('Restoring', missingMessages.length, 'missing messages from backup');
                                const restored = [...newMessages, ...missingMessages];
                                restored.sort((a, b) => {
                                    const aTime = new Date(a.created_at || 0);
                                    const bTime = new Date(b.created_at || 0);
                                    return aTime - bTime;
                                });
                                this.messages = restored;
                            }
                        }
                    }
                }
            },
            deep: false // Don't watch deep to avoid performance issues
        }
    },
    created() {
        // Fetch messages on initial load only
        // Store initial state to prevent accidental clearing
        const initialMessageCount = this.messages.length;
        if (initialMessageCount === 0) {
            this.fetchMessages();
        } else {
            console.log('Skipping initial fetch - messages already exist:', initialMessageCount);
        }
        
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
                currentRideId,
                isOnChatDetailPage: window.isOnChatDetailPage
            });
            
            // Check if message is for current conversation
            // Message should match: same ride ID, and involves current user
            const rideMatches = rideId && currentRideId && parseInt(rideId) === currentRideId;
            const involvesCurrentUser = messageSender === currentUserId || messageReceiver === currentUserId;
            
            // Determine if we should process this message
            let shouldProcess = false;
            
            if (window.isOnChatDetailPage && currentPassenger && currentRideId) {
                // On chat detail page: message must match ride, involve current user, AND involve the passenger we're chatting with
                const involvesPassenger = messageSender === currentPassenger || messageReceiver === currentPassenger;
                shouldProcess = rideMatches && involvesCurrentUser && involvesPassenger;
            } else if (rideMatches && involvesCurrentUser) {
                // Not on chat detail page, or no passenger specified: just check ride and user involvement
                shouldProcess = true;
            }
            
            console.log('Should process message:', shouldProcess, {
                rideMatches,
                involvesCurrentUser,
                involvesPassenger: currentPassenger ? (messageSender === currentPassenger || messageReceiver === currentPassenger) : 'N/A',
                isOnChatDetailPage: window.isOnChatDetailPage
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
                    // CRITICAL: Store a deep copy of ALL current messages to ensure we never lose them
                    const messageCountBefore = this.messages.length;
                    const currentMessagesCopy = this.messages.map(msg => ({ ...msg }));
                    
                    console.log('Pusher message received. Current messages:', messageCountBefore);
                    
                    // Check if message already exists (by ID)
                    const existingIndex = currentMessagesCopy.findIndex(m => m.id === newMessage.id);
                    
                    let finalMessages = [];
                    
                    if (existingIndex === -1) {
                        // Message doesn't exist, check if it's a duplicate of an optimistic message
                        // Look for messages with same content and similar timestamp (within 5 seconds)
                        const duplicateIndex = currentMessagesCopy.findIndex(m => {
                            if (!m.message || !newMessage.message) return false;
                            const sameContent = m.message.trim() === newMessage.message.trim();
                            if (!sameContent) return false;
                            
                            // Check if timestamps are close (within 5 seconds) - likely same message
                            const mTime = new Date(m.created_at || 0).getTime();
                            const newTime = new Date(newMessage.created_at || 0).getTime();
                            const timeDiff = Math.abs(mTime - newTime);
                            return timeDiff < 5000; // 5 seconds
                        });
                        
                        if (duplicateIndex !== -1) {
                            // Replace optimistic message with real one from Pusher
                            // Create new array with ALL messages, replacing only the duplicate
                            finalMessages = currentMessagesCopy.map((msg, idx) => 
                                idx === duplicateIndex ? { ...newMessage } : { ...msg }
                            );
                            console.log('Replacing optimistic message with Pusher message');
                        } else {
                            // New message, add it to ALL existing messages
                            finalMessages = [...currentMessagesCopy, { ...newMessage }];
                            console.log('Adding new Pusher message');
                        }
                    } else {
                        // Message already exists, update it with Pusher data (more complete)
                        // Create new array with ALL messages, updating only the existing one
                        finalMessages = currentMessagesCopy.map((msg, idx) => 
                            idx === existingIndex ? { ...newMessage } : { ...msg }
                        );
                        console.log('Updating existing message with Pusher data');
                    }
                    
                    // Sort without mutating (create new sorted array)
                    finalMessages.sort((a, b) => {
                        const aTime = new Date(a.created_at || 0);
                        const bTime = new Date(b.created_at || 0);
                        return aTime - bTime;
                    });
                    
                    // CRITICAL: Verify we have at least as many messages as before (or one more for new message)
                    const expectedCount = existingIndex === -1 ? messageCountBefore + 1 : messageCountBefore;
                    
                    // CRITICAL CHECK: Ensure we didn't lose any messages
                    if (finalMessages.length < messageCountBefore) {
                        console.error('CRITICAL ERROR: Message count decreased! Before:', messageCountBefore, 'After:', finalMessages.length);
                        console.error('Restoring ALL messages from backup to prevent data loss');
                        // Restore from backup - don't lose any messages
                        finalMessages = currentMessagesCopy.map(msg => ({ ...msg }));
                        // Still try to add the new message if it wasn't a duplicate
                        if (existingIndex === -1) {
                            const duplicateCheck = finalMessages.findIndex(m => {
                                if (!m.message || !newMessage.message) return false;
                                return m.message.trim() === newMessage.message.trim() && 
                                       Math.abs(new Date(m.created_at || 0).getTime() - new Date(newMessage.created_at || 0).getTime()) < 5000;
                            });
                            if (duplicateCheck === -1) {
                                finalMessages.push({ ...newMessage });
                                finalMessages.sort((a, b) => {
                                    const aTime = new Date(a.created_at || 0);
                                    const bTime = new Date(b.created_at || 0);
                                    return aTime - bTime;
                                });
                            }
                        }
                    }
                    
                    // CRITICAL: Final verification before updating
                    if (finalMessages.length < messageCountBefore) {
                        console.error('FATAL: Still losing messages after recovery. Keeping original array.');
                        // Don't update at all if we're still losing messages
                        return;
                    }
                    
                    // CRITICAL: Final check before updating - ensure we have at least the expected count
                    const safeToUpdate = finalMessages.length >= messageCountBefore || 
                                        (existingIndex === -1 && finalMessages.length >= messageCountBefore - 1);
                    
                    if (safeToUpdate) {
                        // Update messages array with ALL messages preserved
                        this.messages = finalMessages;
                        // Update backup
                        this._messagesBackup = finalMessages.map(msg => ({ ...msg }));
                        console.log('Pusher message processed. Total messages:', this.messages.length, 'Expected:', expectedCount);
                        
                        // Scroll to bottom after adding message
                        this.scrollToBottom();
                    } else {
                        console.error('FATAL: Cannot safely update messages. Keeping original array.');
                        console.error('Final messages count:', finalMessages.length, 'Expected at least:', messageCountBefore);
                        // Don't update - keep original messages to prevent data loss
                        // But still try to add the new message if it's truly new
                        if (existingIndex === -1 && !currentMessagesCopy.some(m => {
                            if (!m.message || !newMessage.message) return false;
                            return m.message.trim() === newMessage.message.trim();
                        })) {
                            const safeAdd = [...currentMessagesCopy, { ...newMessage }];
                            safeAdd.sort((a, b) => {
                                const aTime = new Date(a.created_at || 0);
                                const bTime = new Date(b.created_at || 0);
                                return aTime - bTime;
                            });
                            this.messages = safeAdd;
                            this._messagesBackup = safeAdd.map(msg => ({ ...msg }));
                            console.log('Safely added new message. Total:', this.messages.length);
                            this.scrollToBottom();
                        }
                    }
                    // Also update chats preview
                    if (!this.chats.some(m => m.id === newMessage.id && m.created_at === newMessage.created_at)) {
                        this.chats.push(newMessage);
                    }
                    // DON'T fetch automatically - the message is already displayed via Pusher
                    // Only fetch manually or on page load, not after receiving Pusher messages
                    // This prevents messages from disappearing
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
        scrollToBottom() {
            // Use double nextTick to ensure DOM is fully updated
            this.$nextTick(() => {
                this.$nextTick(() => {
                    const chatContainer = document.querySelector('.panel-body');
                    if (chatContainer) {
                        // Scroll to bottom
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }
                });
            });
        },
        fetchMessages(shouldScrollToBottom = false, preserveMessageId = null) {
            const rideId = window.ride;
            const userId = window.passenger;
            
            if (!rideId || !userId) {
                console.warn('Cannot fetch messages: missing rideId or userId');
                return;
            }
            
            // CRITICAL: Store deep copy of ALL current messages to prevent disappearing during fetch
            // This ensures we NEVER lose messages, even if server response is delayed or empty
            const currentMessages = this.messages.map(msg => ({ ...msg }));
            const currentMessageCount = currentMessages.length;
            console.log('fetchMessages called. Current messages count:', currentMessageCount);
            
            // If we need to preserve a specific message (from Pusher), find it and store it
            let messageToPreserve = null;
            if (preserveMessageId) {
                messageToPreserve = currentMessages.find(m => {
                    // Compare both as strings and numbers to handle different ID types
                    return String(m.id) === String(preserveMessageId) || 
                           Number(m.id) === Number(preserveMessageId) ||
                           m.id === preserveMessageId;
                });
                console.log('Preserving message:', preserveMessageId, messageToPreserve ? 'FOUND' : 'NOT FOUND');
            }
            
            axios.get(`/chat-messages/${rideId}/${userId}`).then(response => {
                console.log('Server response received. Messages count:', response.data?.length || 0, 'Current messages:', currentMessages.length);
                
                if (Array.isArray(response.data) && response.data.length > 0) {
                    // Merge new messages with existing ones to prevent disappearing
                    // Use a Map to deduplicate by id
                    const messageMap = new Map();
                    
                    // CRITICAL: First, add all current messages to the map (preserve what's already there)
                    // This ensures Pusher messages are kept even if server hasn't saved them yet
                    currentMessages.forEach(msg => {
                        if (msg && msg.id) {
                            const key = String(msg.id);
                            // Create a deep copy to avoid reference issues
                            messageMap.set(key, { ...msg });
                        }
                    });
                    console.log('After adding current messages to map:', messageMap.size, '(expected:', currentMessages.length, ')');
                    
                    // Then, add/update with server messages (server data takes precedence for existing messages)
                    response.data.forEach(msg => {
                        if (msg && msg.id) {
                            const key = String(msg.id);
                            // Server data takes precedence (updates existing, adds new)
                            // Create deep copy
                            messageMap.set(key, { ...msg });
                        }
                    });
                    console.log('After adding server messages to map:', messageMap.size);
                    
                    // CRITICAL: If we have a message to preserve that's not in the server response, add it
                    // This handles the race condition where Pusher message arrives before server saves it
                    if (messageToPreserve && messageToPreserve.id) {
                        const preserveKey = String(messageToPreserve.id);
                        if (!messageMap.has(preserveKey)) {
                            console.log('Adding preserved message that was not in server response:', preserveKey);
                            messageMap.set(preserveKey, { ...messageToPreserve });
                        } else {
                            console.log('Preserved message found in server response:', preserveKey);
                        }
                    }
                    
                    // Convert map back to array and sort by created_at (create new array, don't mutate)
                    const mergedMessages = Array.from(messageMap.values());
                    mergedMessages.sort((a, b) => {
                        const aTime = new Date(a.created_at || a.message?.created_at || 0);
                        const bTime = new Date(b.created_at || b.message?.created_at || 0);
                        return aTime - bTime;
                    });
                    
                    console.log('Final merged messages count:', mergedMessages.length, 'Current was:', currentMessageCount);
                    
                    // CRITICAL: Verify we didn't lose messages - NEVER allow fewer messages than we started with
                    // ALWAYS preserve current messages if merge would result in fewer
                    if (mergedMessages.length < currentMessageCount && currentMessageCount > 0) {
                        console.error('ERROR: Merged messages count is less than current! Keeping ALL current messages.');
                        console.error('Merged:', mergedMessages.length, 'Current:', currentMessageCount);
                        // Keep ALL current messages - don't lose any
                        // Only merge in new messages from server that we don't already have
                        const existingIds = new Set(currentMessages.map(m => String(m.id)));
                        response.data.forEach(msg => {
                            if (msg && msg.id && !existingIds.has(String(msg.id))) {
                                currentMessages.push({ ...msg });
                            }
                        });
                        // Sort and update
                        currentMessages.sort((a, b) => {
                            const aTime = new Date(a.created_at || a.message?.created_at || 0);
                            const bTime = new Date(b.created_at || b.message?.created_at || 0);
                            return aTime - bTime;
                        });
                        this.messages = currentMessages;
                        // Update backup
                        this._messagesBackup = currentMessages.map(msg => ({ ...msg }));
                        console.log('Preserved all current messages and added new ones. Total:', this.messages.length);
                    } else {
                        // Update messages array with merged data (should have same or more messages)
                        this.messages = mergedMessages;
                        // Update backup
                        this._messagesBackup = mergedMessages.map(msg => ({ ...msg }));
                        console.log('Messages updated successfully. Count:', this.messages.length);
                    }
                    
                    // Always scroll to bottom if explicitly requested (when sending/receiving messages)
                    if (shouldScrollToBottom) {
                        this.scrollToBottom();
                    }
                } else if (response.data && Array.isArray(response.data) && response.data.length === 0) {
                    // Empty array from server - keep current messages if we have any
                    console.log('Server returned empty array. Keeping current messages:', currentMessages.length);
                    // Always keep current messages, don't clear them
                    if (currentMessages.length > 0) {
                        // If we have a preserved message, make sure it's included
                        if (messageToPreserve && !currentMessages.some(m => String(m.id) === String(messageToPreserve.id))) {
                            const updatedMessages = [...currentMessages, { ...messageToPreserve }];
                            updatedMessages.sort((a, b) => {
                                const aTime = new Date(a.created_at || 0);
                                const bTime = new Date(b.created_at || 0);
                                return aTime - bTime;
                            });
                            this.messages = updatedMessages;
                        } else {
                            this.messages = currentMessages;
                            // Update backup
                            this._messagesBackup = currentMessages.map(msg => ({ ...msg }));
                        }
                    } else {
                        // Only clear if we truly have no messages AND this is the initial fetch
                        // Never clear if we had messages before (they might just not be in server response yet)
                        if (currentMessageCount === 0) {
                            // Safe to clear - we had no messages to begin with
                            this.messages = [];
                            this._messagesBackup = [];
                        } else {
                            // Keep current messages - server might not have them yet
                            console.warn('Server returned empty array but we have', currentMessageCount, 'messages. Keeping them.');
                            this.messages = currentMessages;
                            this._messagesBackup = currentMessages.map(msg => ({ ...msg }));
                        }
                    }
                } else {
                    // Do NOT clear messages on invalid response, to prevent accidental UI wipeout
                    console.warn('Received invalid chat thread from server; retaining previous messages.');
                    // Keep current messages
                    this.messages = currentMessages;
                    // Update backup
                    this._messagesBackup = currentMessages.map(msg => ({ ...msg }));
                }
            }).catch(error => {
                // On error, keep current messages to prevent disappearing
                console.error('Error fetching messages:', error);
                // Always keep current messages on error
                this.messages = currentMessages;
                // Update backup
                this._messagesBackup = currentMessages.map(msg => ({ ...msg }));
            });
            
            axios.get(`/chat-details/${userId}`).then(response => {
                this.chats = response.data;
            }).catch(error => {
                console.error('Error fetching chat details:', error);
            });
        },
        addMessage(message) {
            // CRITICAL: Store deep copy of ALL current messages to ensure we never lose them
            const allCurrentMessages = this.messages.map(msg => ({ ...msg }));
            const messageCountBefore = allCurrentMessages.length;
            
            console.log('addMessage called. Current messages:', messageCountBefore);
            
            // Generate a temporary ID for optimistic message if it doesn't have one
            if (!message.id) {
                message.id = 'temp_' + Date.now() + '_' + Math.random();
            }
            if (!message.created_at) {
                message.created_at = new Date().toISOString();
            }
            
            // Optimistically add message to the array for instant display
            // Use Vue's reactivity-safe method to add message
            if (!allCurrentMessages.some(m => m.id === message.id)) {
                // Create a new array with ALL existing messages plus the new one
                const newMessages = [...allCurrentMessages, { ...message }];
                // Sort without mutating
                newMessages.sort((a, b) => {
                    const aTime = new Date(a.created_at || 0);
                    const bTime = new Date(b.created_at || 0);
                    return aTime - bTime;
                });
                // Assign the new sorted array (preserves ALL old messages)
                this.messages = newMessages;
                // Update backup
                this._messagesBackup = newMessages.map(msg => ({ ...msg }));
                // Scroll to bottom after adding message
                this.scrollToBottom();
                console.log('Optimistic message added. Messages before:', messageCountBefore, 'after:', this.messages.length);
                
                // CRITICAL: Verify we didn't lose any messages
                if (this.messages.length < messageCountBefore) {
                    console.error('CRITICAL ERROR: Lost messages when adding! Before:', messageCountBefore, 'After:', this.messages.length);
                    console.error('Restoring ALL messages from backup');
                    // Restore ALL messages from backup
                    this.messages = allCurrentMessages.map(msg => ({ ...msg }));
                    // Try to add the new message again, but more carefully
                    if (!this.messages.some(m => m.id === message.id)) {
                        const safeNewMessages = [...this.messages, { ...message }];
                        safeNewMessages.sort((a, b) => {
                            const aTime = new Date(a.created_at || 0);
                            const bTime = new Date(b.created_at || 0);
                            return aTime - bTime;
                        });
                        if (safeNewMessages.length >= messageCountBefore) {
                            this.messages = safeNewMessages;
                            console.log('Recovered and added message. Total:', this.messages.length);
                        }
                    }
                }
            }
            
            const userId = window.passenger;
            const tempMessageId = message.id; // Store temp ID to track the optimistic message
            
            axios.post('/chat-messages', { ...message, userId }).then(response => {
                // CRITICAL: Store current messages before updating
                const messagesBeforeUpdate = this.messages.map(msg => ({ ...msg }));
                
                // Update the optimistic message with server response if available
                if (response.data && response.data.id) {
                    const index = messagesBeforeUpdate.findIndex(m => m.id === tempMessageId);
                    if (index !== -1) {
                        // Create a new array with ALL messages, updating only the one at index
                        const updatedMessages = messagesBeforeUpdate.map((msg, idx) => {
                            if (idx === index) {
                                // Update this message with server data
                                return { 
                                    ...msg, 
                                    ...response.data,
                                    // Ensure we keep the message content
                                    message: msg.message || response.data.message
                                };
                            }
                            return msg; // Keep all other messages unchanged
                        });
                        // Re-sort after update
                        updatedMessages.sort((a, b) => {
                            const aTime = new Date(a.created_at || 0);
                            const bTime = new Date(b.created_at || 0);
                            return aTime - bTime;
                        });
                        // Update messages array (preserves ALL other messages)
                        this.messages = updatedMessages;
                        this.scrollToBottom();
                        console.log('Optimistic message updated with server response. New ID:', response.data.id, 'Total messages:', this.messages.length, 'Expected:', messagesBeforeUpdate.length);
                        
                        // CRITICAL: Verify we didn't lose any messages
                        if (this.messages.length < messagesBeforeUpdate.length) {
                            console.error('CRITICAL ERROR: Lost messages when updating! Before:', messagesBeforeUpdate.length, 'After:', this.messages.length);
                            console.error('Restoring ALL messages from backup');
                            // Restore ALL messages from backup
                            this.messages = messagesBeforeUpdate.map(msg => ({ ...msg }));
                            // Try to update again more carefully
                            const index = this.messages.findIndex(m => m.id === tempMessageId);
                            if (index !== -1) {
                                const safeUpdated = this.messages.map((msg, idx) => {
                                    if (idx === index) {
                                        return { ...msg, ...response.data, message: msg.message || response.data.message };
                                    }
                                    return { ...msg };
                                });
                                safeUpdated.sort((a, b) => {
                                    const aTime = new Date(a.created_at || 0);
                                    const bTime = new Date(b.created_at || 0);
                                    return aTime - bTime;
                                });
                                if (safeUpdated.length >= messagesBeforeUpdate.length) {
                                    this.messages = safeUpdated;
                                    console.log('Recovered and updated message. Total:', this.messages.length);
                                }
                            }
                        }
                    }
                }
                // Don't refetch - let Pusher handle the real-time update
                // Pusher will update/replace the message when it arrives
            }).catch(error => {
                console.error('Error sending message:', error);
                // Remove optimistic message on error, but preserve ALL other messages
                const messagesBeforeError = this.messages.map(msg => ({ ...msg }));
                this.messages = messagesBeforeError.filter(m => m.id !== tempMessageId);
                console.log('Removed optimistic message on error. Remaining messages:', this.messages.length, 'Expected:', messagesBeforeError.length - 1);
            });
        }
    }
});

app.component('chat-messages', ChatMessages);
app.component('chat-form', ChatForm);

app.mount('#ridesharing_app');