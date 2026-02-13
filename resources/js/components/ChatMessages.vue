<template>
    <div>
        <!-- Ride details header - always show if ride details are available -->
        <template v-if="hasRideDetails">
            <div style="display: flex; justify-content: center; margin-bottom: 12px;">
                <div class="bg-green-200 px-4 py-2 rounded-md">
                    <div class="text-xl text-gray-800 font-bold">Ride Detail</div>
                    <div class="text-sm text-gray-600">{{ rideDetailsLine }}</div>
                </div>
            </div>
        </template>
        <!-- Initial system message - shown only when no actual messages exist -->
        <div v-if="!hasActualMessages" class="text-center mb-4 px-4">
            <p class="text-gray-600 text-sm italic">
                This marks the start of your chat with the driver. Please avoid sharing any contact details such as
                phone
                numbers, email addresses, or website links. Do not offer or agree to communicate or arrange payments
                outside the
                ProximaRide platform.
            </p>
        </div>
        <!-- Chat messages -->
        <ul ref="messagesContainer" class="chat" v-if="messages.length > 0">
            <li v-for="message in filteredMessages" :key="message.id" :class="getListItemClasses(message)">
                <div style="max-width:70%;">
                    <div class="header">
                        <p class="text-xs mb-1 font-montserrat text-gray-500 font-semibold"
                            :class="isMe(message) ? 'text-right' : 'text-left'">
                            {{ formatMessageTime(message) }}
                        </p>
                    </div>
                    <div :class="getMessageClasses(message)">
                        {{ message.message }}
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
<script>
import clsx from 'clsx';

export default {
    props: ['messages', 'logged_in_user_id'],
    mounted() {
        // Scroll to bottom on initial mount
        this.$nextTick(() => {
            this.scrollToBottom();
        });
    },
    watch: {
        messages: {
            handler() {
                // Scroll to bottom when messages change
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            },
            deep: true,
            immediate: true
        },
        filteredMessages: {
            handler() {
                // Scroll when filtered messages update
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            },
            deep: true
        }
    },
    computed: {
        rideDetailMessage() {
            if (!Array.isArray(this.messages) || this.messages.length === 0) {
                return null;
            }
            return this.messages.find(m => m.ride_detail) || null;
        },
        hasRideDetails() {
            // Check if we have ride details from messages or from window.rideDetails
            if (this.rideDetailMessage && this.rideDetailMessage.ride_detail) {
                return true;
            }
            // Fallback to window.rideDetails
            if (typeof window !== 'undefined' && window.rideDetails) {
                const rd = window.rideDetails;
                return rd.departure && rd.destination;
            }
            return false;
        },
        rideDetailsLine() {
            // First try to get from message
            if (this.rideDetailMessage && this.rideDetailMessage.ride_detail) {
                const rd = this.rideDetailMessage.ride_detail;
                return `${rd.departure} to ${rd.destination} ${this.formatDateTime(rd.date, rd.time)}`;
            }
            // Fallback to window.rideDetails
            if (typeof window !== 'undefined' && window.rideDetails) {
                const rd = window.rideDetails;
                if (rd.departure && rd.destination) {
                    return `${rd.departure} to ${rd.destination} ${this.formatDateTime(rd.date, rd.time)}`;
                }
            }
            return '';
        },
        filteredMessages() {
            if (!Array.isArray(this.messages) || this.messages.length === 0) {
                return [];
            }

            // Sort by created_at
            const sorted = [...this.messages].sort((a, b) => {
                const at = new Date((a.created_at || a.message?.created_at || 0));
                const bt = new Date((b.created_at || b.message?.created_at || 0));
                return at - bt;
            });

            return sorted;
        },
        hasActualMessages() {
            return this.filteredMessages.length > 0;
        }
    },
    methods: {
        formatDateTime(date, time) {
            if (!date || !time) return '';
            const datetime = new Date(`${date}T${time}`);
            const datePart = datetime.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: '2-digit',
            });
            const timePart = datetime.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true,
            });
            return `${datePart} at ${timePart}`;
        },

        formatMessageTime(message) {
            const timestamp = message.created_at || message.message?.created_at;
            if (!timestamp) return '';
            const messageDate = new Date(timestamp);
            const today = new Date();

            // Check if message is from today
            const isToday = messageDate.getDate() === today.getDate() &&
                messageDate.getMonth() === today.getMonth() &&
                messageDate.getFullYear() === today.getFullYear();

            if (isToday) {
                // Show only time for today's messages
                return messageDate.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true,
                });
            } else {
                // Show full date and time for other days: "Jan 4,2026 02:40AM"
                const datePart = messageDate.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                }).replace(', ', ','); // Remove space after comma
                const timePart = messageDate.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true,
                }).replace(' ', ''); // Remove space before AM/PM
                return `${datePart} ${timePart}`;
            }
        },

        isMe(message) {
            return message.user && message.user.id == this.logged_in_user_id;
        },

        getListItemClasses(message) {
            return clsx('flex mb-2', this.isMe(message) ? 'justify-end mr-2' : 'justify-start ml-2');
        },

        getMessageClasses(message) {
            return clsx('rounded-md px-4 py-2 text-base whitespace-pre-wrap', this.isMe(message) ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-800');
        },

        scrollToBottom() {
            // Try scrolling the messages container itself
            if (this.$refs.messagesContainer) {
                this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
            }

            // Also scroll to the last message element (works with any scroll container)
            const lastMessage = this.$el?.querySelector('li:last-child');
            if (lastMessage) {
                lastMessage.scrollIntoView({ behavior: 'smooth', block: 'end' });
            }
        }
    }
}
</script>
