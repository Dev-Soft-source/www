<template>
    <div class="my-4 input-group flex items-center gap-2">
        <!-- <input id="btn-input" type="text" name="message" class="form-control input-sm mt-1 block w-full py-2 px-3 border border-gray-100 bg-gray-50 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-none sm:text-sm font-montserrat" :placeholder="type_message_placeholder" v-model="newMessage" @keyup.enter="sendMessage"> -->
        <textarea
        :disabled="!allow_chat"
            id="btn-input"
            name="message"
            class="form-control input-sm mt-1 block w-full py-2 px-3 border border-gray-100 bg-gray-50 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-none sm:text-sm font-montserrat"
            :placeholder="type_message_placeholder"
            v-model="newMessage"
            rows="3"
            @keydown.enter="handleEnter"
        ></textarea>
        <span class="input-group-btn">
            <button class="btn btn-primary btn-sm text-white rounded-md h-10 w-10 flex items-center justify-center border border-transparent shadow-sm font-semibold bg-blue-500 hover:bg-blue-500 focus:outline-none focus:ring-0 font-SairaCondensed" id="btn-chat" @click="sendMessage">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>
            </button>
        </span>
    </div>
</template>

<script>
    export default {
        props: ['ride_id','user', 'type_message_placeholder','allow_chat'],

        data() {
            return {
                newMessage: ''
            }
        },

        methods: {
            handleEnter(event) {
                // If Shift + Enter is pressed, allow default behavior (new line)
                if (event.shiftKey) {
                    return;
                }
                // If only Enter is pressed, prevent default and send message
                event.preventDefault();
                this.sendMessage();
            },

            sendMessage() {
                if (this.newMessage.trim() === '') return;
                this.$emit('MessageSentEvent', {
                    ride_id: this.ride_id,
                    user: this.user,
                    message: this.newMessage
                });

                this.newMessage = '';
            }
        }
    }
</script>
