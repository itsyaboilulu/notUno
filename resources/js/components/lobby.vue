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
            <div class="players">
                <h4>Players</h4>
                <span v-if="!canplay">Need at least 2 players to start a game</span>
                <ul class="members">
                    <li v-for="member in datamembers" > > {{member.username}}</li>
                </ul>
            </div>
            <div class="settings">
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
                <button id='start' :disabled="canplay == 0" v-on:click="start()"> start </button>
            </div>

        </div>
    </div>
</template>
<script>
import { ajax }         from "../ajax.min.js";
import { startTimer }   from "../timer.min.js";
export default {

    name:'lobby',
    props:['game','members','admin','deck','setting'],
    mounted(){
        this.datamembers        = this.members;
        this.canplay            = ( this.datamembers.length > 1 ) ? 1 : 0 ;
        this.settings.setting   = this.setting;
        this.settings.deck      = this.deck;
        this.joinLink           = "http://uno.yaboilulu.co.uk/join?join="+this.game.password;
    },
    created(){
        startTimer(this.check,this.checkTime);
    },
    data () {
        return {
            datamembers: '',
            checkTime: 30,
            canplay:0,
            joinLink:0,
            hs:{
                deck:0,
            },
            settings: {
                deck    :'',
                setting :'',
            },
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
            this.datamembers = $response.members;
            this.canplay = ( this.datamembers.length > 1 ) ? 1 : 0 ;
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
        }
    }
}
</script>
