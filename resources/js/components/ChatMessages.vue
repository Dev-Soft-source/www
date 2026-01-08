<template>
    <div>
        <!-- Ride details header - always show if ride details are available -->
        <template v-if="hasRideDetails">
            <div style="display: flex; justify-content: center; margin-bottom: 12px;">
                <div style="background-color: #22c55e; color: white; border-radius: 8px; padding: 12px 16px; max-width: 90%;">
                    <div style="font-weight: bold; margin-bottom: 4px; font-size: 14px;">Ride Details</div>
                    <div style="font-size: 13px;">{{ rideDetailsLine }}</div>
                </div>
            </div>
        </template>
        <!-- Initial system message - shown only when no actual messages exist -->
        <div v-if="!hasActualMessages" class="text-center mb-4 px-4">
            <p class="text-gray-600 text-sm italic">
                This marks the start of your chat with the driver. Please avoid sharing any contact details such as phone numbers, email addresses, or website links. Do not offer or agree to communicate or arrange payments outside the ProximaRide platform.
            </p>
        </div>
        <!-- Chat messages -->
        <ul class="chat" v-if="messages.length > 0">
            <li v-for="message in filteredMessages" :key="message.id"
                :class="[{
                  'flex justify-end': message.user && message.user.id == logged_in_user_id,
                  'flex justify-start': message.user && message.user.id != logged_in_user_id
                }, 'mb-2']"
                :style="message.user && message.user.id == logged_in_user_id ? {'marginRight': '32px'} : {}"
            >
                <div style="max-width:70%;">
                    <div class="header">
                        <p class="text-xs font-semibold mb-1"
                            :class="message.user && message.user.id == logged_in_user_id ? 'text-right' : 'text-left'">
                            {{ message.user ? message.user.first_name : 'User' }}
                        </p>
                    </div>
                    <div :style="bubbleStyle(message)">
                        <span>{{ message.message }}</span>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    props: ['messages', 'logged_in_user_id', 'empty_chat_placeholder', 'current_lang'],
    computed: {
        rideDetailMessage() {
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
            const list = Array.from(this.messages);
            list.sort((a, b) => {
                const at = new Date((a.created_at || a.message?.created_at));
                const bt = new Date((b.created_at || b.message?.created_at));
                return at - bt;
            });
            const hasNonRideDetail = list.some(m => !m.ride_detail);
            if (hasNonRideDetail) {
                return list.filter(m => !m.ride_detail);
            }
            return list;
        },
        hasActualMessages() {
            // Check if there are any actual chat messages (not just ride detail messages)
            return this.messages.some(m => !m.ride_detail && m.message);
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
        bubbleStyle(message) {
            if(message.user && message.user.id == this.logged_in_user_id) {
                // My message (blue)
                return {
                    background: '#2563eb',
                    color: '#fff',
                    'border-radius': '16px',
                    'padding': '10px 16px',
                    'margin-bottom': '2px',
                    'margin-left': '10px',
                    'display': 'inline-block',
                    'max-width': '100%',
                    'word-break': 'break-word',
                };
            }
            // Their message (gray)
            return {
                background: '#f4f4f4',
                color: '#222',
                'border-radius': '16px',
                'padding': '10px 16px',
                'margin-bottom': '2px',
                'margin-right': '10px',
                'display': 'inline-block',
                'max-width': '100%',
                'word-break': 'break-word',
            };
        }
    }
}
</script>
