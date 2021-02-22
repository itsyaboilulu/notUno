<style scoped>
    .s_option {
        text-decoration: underline;

    }
    .pointer{
        cursor: pointer;
    }
</style>
<template>
    <div id='lobby'>
        <div class="lobby">
            <leaderboard
                v-if                = "leaderboard.length"
                v-bind:type         = "lbType"
                v-bind:leaderboard  = "leaderboard"
            ></leaderboard>
            <div v-if="!canplay" class="players">
                <span >Need at least 2 players to start a game</span>
            </div>
            <div class="settings" v-if="admin">
                <h4>Settings</h4>
                <form v-if="admin" id='startgame' action="startgame" >
                    <input type="hidden" name="password" v-bind:value="game.password" />
                    <input type="hidden" name="settings" id='settings' />
                    <input type="hidden" name="deck" id='deck' />
                    <label for='name'> name:  <input name="name" v-model="game.name" required></label><br>
                </form>
                    Join Link:<br>
                    <span class="joinLink" v-on:click="copy()">
                        <button>Copy</button>
                        <label>{{ joinLink }}</label>
                    </span>
                <span v-if="admin">
                    <span v-on:click='hs.deck=0' v-bind:class=" { s_option:!hs.deck, pointer:1 } " >Settings</span>
                    <span v-on:click='hs.deck=1' v-bind:class=" { s_option:hs.deck, pointer:1 } " >Cards</span>
                </span>
                <deckBreakDown v-if="hs.deck && admin"  v-bind:deck="settings.deck"       ></deckBreakDown>
                <gameSettings  v-if="!hs.deck && admin" v-bind:setting="settings.setting" ></gameSettings>
                <button v-if="admin" id='start' :disabled="canplay == 0" v-on:click="start()"> start </button>
            </div>
            <div id='remove'>
                <button v-if="admin"  v-on:click="remove()" > Delete Lobby </button>
                <form v-if="admin" id='removeForm' action="lobby/remove" method="post">
                    <input type="hidden" name="password" v-bind:value="game.password" />
                    <input type="hidden" name="_token" v-bind:value="crf" />
                </form>
            </div>

        </div>
    </div>
</template>
<script>
import { ajax }         from "../ajax.min.js";
import { startTimer }   from "../timer.min.js";
export default {

    name:'lobby',
    props:['game','admin','deck','setting','dleaderboard'],
    mounted(){
        this.canplay            = ( this.dleaderboard.length > 1 ) ? 1 : 0 ;
        this.settings.setting   = this.setting;
        this.settings.deck      = this.deck;
        this.joinLink           = "http://uno.yaboilulu.co.uk/join?join="+this.game.password;
        this.leaderboard        = this.dleaderboard;
    },
    created(){
        startTimer(this.check,this.checkTime);
    },
    data () {
        return {
            checkTime   : 30,
            canplay     : 0,
            joinLink    : 0,
            lbType      : 'Leaderboard',
            leaderboard : '',
            hs:{
                deck:0,
            },
            settings: {
                deck    :'',
                setting :'',
            },
        }
    },
    computed:{
        crf(){
            return document.querySelector('meta[name="csrf-token"]').content;
        }
    },
    methods: {
        copy(){
            var cpy = this.joinLink;
            this.joinLink = 'copied!';
            const el = document.createElement('textarea');
            el.value = cpy;
            document.body.appendChild(el);
            el.select();
            el.setSelectionRange(0, 99999);
            document.execCommand("copy");
            document.body.removeChild(el);
            this.joinLink = cpy;

        },
        check(){
            ajax( this.updateMembers, 'api/lobby', {
                action:     'checkMembers',
                password:   this.game.password
            }, 1 );
        },
        updateMembers($response){
            this.leaderboard = $response.members;
            this.canplay = ( this.leaderboard.length > 1 ) ? 1 : 0 ;
            if ($response.started){
                location.reload();
            }
            startTimer(this.check,this.checkTime);
        },
        updateSettings($name,$data){
            this.settings[$name] = $data;
            document.getElementById('settings').value   = Object.keys(this.settings.setting).map(key => key + '=' + this.settings.setting[key]).join('&');
            document.getElementById('deck').value       = this.settings.deck;
        },
        start(){
            if (this.canplay){
                document.getElementById('startgame').submit();
            }
        },
        remove(){
            if (confirm('are you sure you want to delete this lobby?')){
                document.getElementById('removeForm').submit();
            }
        },
    }
}
</script>
