<template>
    <div id='chat' class="close">
        <div class="chat">
            <div class="options">
                <a v-on:click=" showchat = 1; showpbp=0; showhelp=0;" :class="{ selected:showchat, button:1 }" >Chat</a>
                <a v-if="plays.length" v-on:click=" showchat = 0; showpbp=1; showhelp=0;" :class="{ selected:showpbp, button:1  }" >Plays</a>
                <a v-on:click=" showchat = 0; showpbp=0; showhelp=1" :class="{ selected:showhelp, button:1  }" >Commands</a>
            </div>
            <div class="messages" id='messages' v-if="showchat">
                <ul >
                    <li v-for="l in log" :class="{ recived:l.username!=user && l.username!='uno', sent:l.username==user, uno:l.username=='uno' }" >
                        <div v-if="l.username == 'uno'" v-html="l.message" ></div>
                        <div v-if="l.username != 'uno'">
                            {{l.username}}: {{l.message}}
                        </div>
                    </li>
                </ul>
            </div>
            <div class="input" v-if="showchat">
                <input type="text" v-model="message" v-on:keyup.enter="send()" placeholder="type...">
                <button v-on:click="send()">></button>
            </div>
            <div id='playByPlay' v-if="showpbp && plays.length">
                <table>
                    <tr v-for="p in plays"  >
                        <td>{{p.username}}</td>
                        <td v-if="p.action == 'play'" >{{p.data}}</td>
                        <td v-if="p.action == 'draw' && p.data == 1" >draw</td>
                        <td v-if="p.action == 'draw' && p.data != 1" >draw * {{p.data}}</td>
                        <td v-if="p.action == 'uno' && p.data == 1" >uno!!</td>
                        <td v-if="p.action == 'timeout'">timed out</td>
                    </tr>
                </table>
            </div>
            <div class="commands" v-if="showhelp">
                <ul>
                    <li>
                        <h4>@alert</h4>
                        If the current player has notifications turned on they
                        will recieve an alert<br>
                        ( limited 1 per 5 mins )
                    </li>
                    <li>
                        <h4>@wisper username message </h4>
                        Send a priave message to the given username
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script>
    import { ajax }         from "../ajax.min.js";
    import { startTimer }   from "../timer.min.js";
    export default {
        name:'chat',
        props:['logs','user','password','pbp'],
        data(){
            return {
                close       :'close',
                message     :'',
                log         :[1,2,3],
                plays       :[1,2,3],
                checkTime   :10,
                showchat    :1,
                showpbp     :0,
                showhelp    :0,
                prev        :'',
            }
        },
        mounted(){
            this.log    = this.logs;
            this.plays  = this.pbp;
        },
        created(){
            startTimer(this.check,this.checkTime);
        },
        updated() {
            this.scrollBottom();
        },
        methods:{
            getLastId($i){
                var ret;
                if ($i=='c'){
                    this.log.map(function (value){
                        ret = value.id;
                    });
                } else {
                    if (this.plays.length){
                        this.plays.map(function (value){
                            ret = value.id;
                        });
                    } else {
                        ret=0;
                    }

                }

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
                    clastUpdate: this.getLastId('c'),
                    plastUpdate: this.getLastId('p'),
                }, 1 );
            },
            checkResponse($response){
                $response.chat.forEach(element => {
                    this.log.push(element);
                });
                $response.pbp.forEach(element => {
                    this.plays.push(element);
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
                    this.scrollBottom();
                }
            },
            scrollBottom(){
                if (this.showchat){
                     document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
                }
                if (this.showpbp){
                    document.getElementById('playByPlay').scrollTop = document.getElementById('playByPlay').scrollHeight;
                }
            }
        }
    }
</script>
