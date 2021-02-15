<template>
    <div id='chat' class="close">
        <div class="chat">
            <div class="options">
                Mute:
                    <strong :class="{ strickthough:!showusers }" v-on:click=" showusers = (showusers)?0:1" >players</strong>
                    <strong :class="{ strickthough:!showuno }" v-on:click=" showuno = (showuno)?0:1">unobot</strong>

            </div>
            <div class="messages" id='messages'>
                <ul>
                    <li v-for="l in log" :class="{ recived:l.username!=user && l.username!='uno', sent:l.username==user, uno:l.username=='uno' }" >
                        <div v-if="l.username == 'uno' && showuno" v-html="l.message"></div>
                        <div v-if="l.username != 'uno' && showusers">
                                <strong>{{l.username}}</strong>: {{l.message}}
                        </div>
                    </li>
                </ul>
            </div>
            <div class="input">
                <input type="text" v-model="message" v-on:keyup.enter="send()" placeholder="type...">
                <button v-on:click="send()">></button>
            </div>
        </div>
    </div>
</template>
<script>
    import { ajax }         from "../ajax.min.js";
    import { startTimer }   from "../timer.min.js";
    export default {
        name:'chat',
        props:['logs','user','password'],
        data(){
            return {
                close       :'close',
                message     :'',
                log         :[1,2,3],
                checkTime   :10,
                showuno     :1,
                showusers   :1,
            }
        },
        mounted(){
            this.log = this.logs;
            startTimer(this.scrollBottom,2);
        },
        created(){
            startTimer(this.check,this.checkTime);
        },
        methods:{
            getLastId(){
                var ret;
                this.log.map(function (value){
                    ret = value.id;
                });
                return ret;
            },
            check(){
                this.update();
                return startTimer(this.check,this.checkTime);
            },
            update(){
                ajax( this.checkResponse, 'api/chat', {
                    action:     'check',
                    password:   this.password,
                    lastUpdate: this.getLastId(),
                }, 1 );
            },
            checkResponse($response){
                $response.forEach(element => {
                    this.log.push(element);
                });
                this.scrollBottom();
            },
            send(){
                if (this.message && this.message.replaceAll(' ','')){
                    ajax( this.update, 'api/chat', {
                        action:     'send',
                        password:   this.password,
                        message:    this.message
                    }, 1 );
                    this.message = '';
                }
            },
            scrollBottom(){
                document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
            }
        }
    }
</script>
