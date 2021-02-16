
<template>
    <div id="play">
        <div class="actions" v-if="!yourTurn">
            <h2>{{turn}}'s turn</h2>
            <p>{{nextTurn}} is going next</p>
        </div>
        <div class="actions" v-if="yourTurn">
            <h2>Your turn</h2>
            <p>{{nextTurn}} is going next</p>
        </div>
        <div class="top">
            <unoCard v-bind:cardValue=card v-bind:playable=0 v-bind:against=0></unoCard>
            <mhands v-bind:hands=mmhand ></mhands>
        </div>
        <div class="handcont">
            <div  class="flex hand">
                <div v-for="c in hand" :c="c">
                    <unoCard v-bind:cardValue=c v-bind:against="card" v-bind:playable=1 ></unoCard>
                </div>
            </div>
        </div>
        <div class="actions" v-if="yourTurn">
            <div class="btns" >
                <button v-if="!stack" v-on:click="draw()" class="draw"><img :src="'resources/img/draw.png'"></button>
                <button v-if="isuno" class="uno" v-on:click="callUno()"><img :src="'resources/img/uno.png'"></button>
            </div>
        </div>
    </div>
</template>

<script>
    import { ajax }         from "../ajax.min.js";
    import { startTimer }   from "../timer.min.js";
    export default {
        name:'play',
        props:['game','rawhand','myturn','mhand'],
        mounted (){
            this.mmhand                     = this.mhand;
            this.hand                       = this.rawhand;
            this.card                       = this.game.currcard;
            this.turn                       = this.game.turn;
            this.yourTurn                   = this.myturn;
            this.game_settinsg.password     = this.game.password;
            this.isuno                      = (this.hand.length == 2) ? true : false;
        },
        data () {
            return {
                hand:       '',
                card:       '',
                turn:       '',
                yourTurn:   false,
                postData:   false,
                uno:        false,
                isuno:      false,
                stack:      false,
                mmhand:     '',
                game_settinsg: {
                    password: '',
                },
                js: {
                    checkTime:5,
                }
            }
        },
        created(){
            this.check();
        },
        computed:{
            nextTurn: function(){
                for(var key in this.mmhand){
                    if ( this.turn == this.mmhand[key].member ){
                        try {
                            return this.mmhand[(+key + +1)].member
                        } catch {
                            return this.mmhand[0].member;
                        }
                    }
                }
            },
        },
        methods: {
            check(){
                this.update();
                return startTimer(this.check,this.js.checkTime);
            },
            update(){
                ajax( this.checkResponse, 'api/game', {
                    action:     'check',
                    password:   this.game.password,
                }, 1 );
            },
            checkResponse($response){
                if ($response.winner){
                    location.reload();
                }
                this.correctData($response);
            },
            correctData($response){
                this.card     = $response.current_card;
                this.turn     = $response.player;
                this.yourTurn = $response.yourturn
                this.isuno    = ($response.hand.length == 2) ? true : false;
                this.stack    = $response.stack;
                this.updateHand($response.hand);
                this.mmhand   = $response.mhand;
            },
            updateHand($hand){
                this.hand =  $hand;
                this.sortHand();
            },
            draw(){
                ajax( this.checkResponse, 'api/game', {
                    action:     'draw',
                    password:   this.game.password
                },1 );
            },
            callUno(){
                this.uno = true;
            },
            sortHand(){
                this.hand.sort();
            }
        }
    }
</script>
