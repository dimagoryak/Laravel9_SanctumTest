<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Resending Events - Component</div>

                    <div class="card-body d-flex">
                        <input type="text" class="form-control mr-15" v-model="message" placeholder="message">
                        <button type="button" class="btn btn-primary" @click.prevent="pingSend">Send</button>
                    </div>
                    <div class="card-body d-flex">
                        <textarea :value="messageLog" readonly rows="10" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: [
            'id'
        ],
        data () {
            return {
                messages: [],
                messageLog: ''
            }
        },
        mounted() {

            Echo.channel('notification-channel')
                .listen('.new-message', function(data) {
                    console.log(data);
                });

            Echo.private('user.' + this.id)
                .listen('.user-event', function(data) {
                    console.log(data);
                }).listen('.user-ping-message', (data) => {
                    console.log(data.message)
                    this.handleMessage(data.message);
                });
        },
        methods: {
            pingSend() {
                window.axios.post('/send', {id: this.id, message: this.message} );
            },
            handleMessage(message) {
                this.messages.push(message);
                this.messageLog = this.messages.join('\r\n');
            }
        }
    }
</script>
