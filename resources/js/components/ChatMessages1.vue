<template>
    <div>
        <!-- Ride details header - always show if ride details are available -->
        <template v-if="hasRideDetails">
            <div style="display: flex; justify-content: center; margin-bottom: 12px;">
                <div class="bg-green-200 px-4 py-2 rounded-md">
                    <div class="text-xl text-gray-800 font-bold">{{ ride_detail_header }}</div>
                    <div class="text-sm text-gray-600">{{ rideDetailsLine }}</div>
                </div>
            </div>
        </template>
        <!-- Initial system message - shown only when no actual messages exist -->
        <div v-if="!hasActualMessages" class="text-center mb-4 px-4">
            <p class="text-gray-500 text-sm mb-2">{{ empty_chat_placeholder }}</p>
            <p class="text-gray-600 text-sm italic">
                {{ chat_start_mark }}
            </p>
        </div>
        <!-- Chat messages grouped by day -->
        <div ref="messagesContainer" class="chat" v-if="messages.length > 0">
            <div v-for="group in groupedMessages" :key="group.dateKey" class="mb-4">
                <!-- Day label -->
                <div class="flex justify-center mb-2">
                    <span class="px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-xs font-semibold">
                        {{ group.dateLabel }}
                    </span>
                </div>
                <!-- Messages for this day -->
                <ul>
                    <li v-for="message in group.messages" :key="message.id" :class="getListItemClasses(message)">
                        <div style="max-width:70%;">
                            <div :class="getMessageClasses(message)">
                                {{ message.message }}
                            </div>
                            <div class="header">
                                <p class="text-xs mb-1 font-montserrat text-blue-500 time-bar-text"
                                    :class="isMe(message) ? 'text-right' : 'text-left'">
                                    {{ formatMessageTime(message) }}
                                </p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script>
import clsx from 'clsx';

export default {
    props: {
        messages: {
            type: Array,
            default: () => [],
        },
        logged_in_user_id: {
            type: [Number, String],
            default: null,
        },
        empty_chat_placeholder: {
            type: String,
            default: 'No messages yet',
        },
        ride_detail_header: {
            type: String,
            default: 'Ride Detail',
        },
        chat_start_mark: {
            type: String,
            default: 'This marks the start of your chat with the driver. Please avoid sharing any contact details such as phone numbers, email addresses, or website links. Do not offer or agree to communicate or arrange payments outside the ProximaRide platform.',
        },
    },
    data() {
        return {
            hasScrolledOnLoad: false, // Track if we've scrolled on initial load
            initialLoadComplete: false, // Track if initial messages have been loaded
        };
    },
    mounted() {
        // If messages are already available, scroll immediately
        console.log('ChatMessages component mounted', this.messages.length);
        if (this.messages.length > 0) {
            this.scrollToBottomDelayed();
            this.hasScrolledOnLoad = true;
            this.initialLoadComplete = true;
        }
    },
    watch: {
        messages: {
            handler(newMessages, oldMessages) {
                const hadMessages = oldMessages && oldMessages.length > 0;
                const hasMessages = newMessages && newMessages.length > 0;

                // If messages just became available (initial load), scroll to bottom
                if (!hadMessages && hasMessages && !this.hasScrolledOnLoad) {
                    console.log('Messages loaded for first time, scrolling to bottom');
                    this.scrollToBottomDelayed();
                    this.hasScrolledOnLoad = true;
                    this.initialLoadComplete = true;
                }
                // If messages already existed and changed, scroll (for new messages)
                else if (hasMessages && this.initialLoadComplete) {
                    this.scrollToBottomDelayed();
                }
            },
            deep: true,
            immediate: true
        },
        filteredMessages: {
            handler(newFiltered, oldFiltered) {
                const hadMessages = oldFiltered && oldFiltered.length > 0;
                const hasMessages = newFiltered && newFiltered.length > 0;

                // If filtered messages just became available, scroll
                if (!hadMessages && hasMessages && !this.hasScrolledOnLoad) {
                    this.scrollToBottomDelayed();
                    this.hasScrolledOnLoad = true;
                    this.initialLoadComplete = true;
                }
            },
            deep: true
        },
        groupedMessages: {
            handler(newGrouped, oldGrouped) {
                const hadGroups = oldGrouped && oldGrouped.length > 0;
                const hasGroups = newGrouped && newGrouped.length > 0;

                // If grouped messages just became available, scroll
                if (!hadGroups && hasGroups && !this.hasScrolledOnLoad) {
                    console.log('Grouped messages ready, scrolling to bottom');
                    this.scrollToBottomDelayed();
                    this.hasScrolledOnLoad = true;
                    this.initialLoadComplete = true;
                }
                // If groups already existed and changed, scroll (for new messages)
                else if (hasGroups && this.initialLoadComplete) {
                    this.scrollToBottomDelayed();
                }
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
        },
        groupedMessages() {
            const groups = [];
            if (!this.filteredMessages.length) {
                return groups;
            }

            const byDate = {};

            this.filteredMessages.forEach(msg => {
                const timestamp = msg.created_at || msg.message?.created_at;
                if (!timestamp) {
                    return;
                }
                const d = new Date(timestamp);
                const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
                if (!byDate[key]) {
                    byDate[key] = {
                        dateKey: key,
                        date: d,
                        dateLabel: d.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: '2-digit',
                        }),
                        messages: [],
                    };
                }
                byDate[key].messages.push(msg);
            });

            // Keep chronological order by date
            Object.values(byDate)
                .sort((a, b) => a.date - b.date)
                .forEach(group => groups.push(group));

            return groups;
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
            // Always show only time in 24-hour format, e.g. "16:45"
            return messageDate.toLocaleTimeString('en-GB', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
            });
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
        },

        scrollToBottomDelayed() {
            // Use multiple strategies to ensure scrolling works after DOM is fully rendered
            this.$nextTick(() => {
                // First attempt after Vue updates
                this.scrollToBottom();

                // Second attempt after browser paints (for grouped messages)
                requestAnimationFrame(() => {
                    this.scrollToBottom();

                    // Additional attempts with increasing delays to ensure container is rendered
                    setTimeout(() => {
                        this.scrollToBottom();
                    }, 50);

                    setTimeout(() => {
                        this.scrollToBottom();
                    }, 150);

                    setTimeout(() => {
                        this.scrollToBottom();
                    }, 300);
                });
            });
        }
    }
}
</script>
