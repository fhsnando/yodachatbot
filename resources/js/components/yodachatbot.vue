<template>
    <div class="col-md-6" id="conteudo-chat">
        <div class="card">
            <div class="card-header">
                Messages
            </div>
            <div class="card-body" id="caixa-chat">

                <div class="" id="chat" v-if='messages && messages.length'>

                    <div id="" v-for='line in messages'>

                        <div class="text-right alert alert-primary" v-if="line.from !== 'Me:' ">
                            <div class="">
                                <span style="color: #1C62C4;">{{ line.from }}</span>
                                <span style="color: black;">{{ line.message }}</span>
                            </div>
                        </div>

                        <div class="text-left alert alert-dark" v-else>
                            <span style="color: #848484;">{{ line.from }}</span>
                            <span style="color: black;">{{ line.message }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="form-group" style="display:flex;">
                    <div class="input-group">
                        <input id="message" @keyup.enter.stop.prevent='sendMesaage' class="form-control"
                               placeholder="Write your message here..." type="text"
                               v-model='message'>
                        <div class="input-group-append">
                            <button class="btn btn-primary" id="btn-chat" v-on:click.stop.prevent="sendMesaage">
                                <span class="glyphicon glyphicon-send"></span> Send
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    props: [
        "name",
        "session_messages"
    ],
    data() {
        return {
            // moment: moment,
            messages: [],
            message: '',
            atualizar: true,
            insteval: null,
            tamanho: 0,
            // name: $('.dropdown .dropdown-toggle').text()
        }
    },
    watch: {
        messages: function (val) {
            // this.fullName = val + ' ' + this.lastName
            let scroll = $('#caixa-chat');
            scroll.animate({scrollTop: scroll.prop("scrollHeight")});
        },
    },
    methods: {
        sendMesaage() {
            let mensagem = this.message;
            //Clean value;
            this.message = "";
            if (mensagem !== "") {
                this.messages.push({from: "Me:", message: mensagem});
                let that = this;

                setTimeout(function () {
                    that.messages.push({from: "YodaBot:", message: "is writing..."});
                    axios.post('/sendMessage', {mensagem: mensagem})
                        .then(response => {
                            that.messages.pop();
                            that.messages.push({from: "YodaBot:", message: response.data});
                        });
                    // this.messages.push();
                    //wait for 1 secs, do nothing
                }, 2000);

            }
        },
    },
    mounted() {
        this.messages = JSON.parse(this.session_messages);
    }
}

</script>

<style>

#caixa-chat {
    height: 400px;
    overflow: auto;

}

input[type=text] {
    width: 100%;
    height: 40px;
    border: 1px solid gray;
    border-radius: 5px;
}

input[type=submit] {
    width: 100%;
    height: 40px;
    border: 1px solid gray;
    border-radius: 5px;
    cursor: pointer;

}

textarea {
    width: 100%;
    height: 40px;
    border: 1px solid gray;
    border-radius: 5px;
}

input, textarea {
    margin-bottom: 3px;
}

.media {
    overflow: auto;
}

.text-left.alert.alert-dark {
    margin-right: 35px;
}

.text-right.alert.alert-primary {
    margin-left: 35px;
}
</style>
