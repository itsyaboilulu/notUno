<template>
    <div id="settings" >
        <div class="settings">
            <h4>Allow push notifications</h4>
                <p> allows us to send you push notifications when you've been alerted in a game.
                    (if button does not appear check you are on https  <a href="https://uno.yaboilulu.co.uk/settings" >Link</a> )
                </p>
                <div id="pushpad-subscribe" v-on:click="updateNotify()"></div>
                <table v-if="nots">
                    <tr>
                        <td>
                            <button class="tooltip" title="users can alert you, on your turn using @alert">?</button>
                        </td>
                        <td>Allow Alerts</td>
                        <td> <input v-on:change='setSetting($event)' type="checkbox" v-model="setting.notifications.alert" name="alert" /> </td>
                    </tr>
                    <!--
                    <tr>
                        <td>?</td>
                        <td>Allow Private messages</td>
                        <td> <input type="checkbox" name="pm" /> </td>
                    </tr>
                    -->
                </table>
        </div>

    </div>
</template>
<script>
    import { ajax }         from "../ajax.min.js";
export default {
    name    : 'settings',
    props   : ['settings'],
    mounted(){
        this.setting    = this.settings;
        this.crf        = document.querySelector('meta[name="csrf-token"]').content;
        this.nots       = this.settings.notifications.allow;
    },
    data(){
        return {
            setting : '',
            crf     : '',
            nots    : 0,
        }
    },
    methods : {
        updateNotify(){
            ajax( this.checkResponse, 'api/settings', { action: 'updateNotify' }, 1 );
        },
        checkResponse($r){
            this.checkNots();
        },
        checkNots(){
            this.nots =  ( document.getElementById('pushpad-button').innerHTML === 'Subscribe') ? 0 : 1;
        },
        setSetting($e){
            var v = 0;
            switch($e.target.name){
                case 'alert':
                    v = ($e.target.checked)? 1 : 0;
                    break;
            }
            ajax( this.checkResponse, 'api/settings', {
                action: 'setSetting',
                key   : $e.target.name,
                val   : v,
            }, 1 );
        }
    }
}
</script>
