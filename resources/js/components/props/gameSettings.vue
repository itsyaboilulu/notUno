<template>
    <div id='gameSettings'>
        <table>
            <tr>
                <td style="width:100%">
                    <button class="tooltip" title="How many cards are drawn if uno is not called">?</button>
                        Uno penalty
                    </td>
                <td>
                    <input v-on:change='update($event)' type="number" min="1" step="1" max="10" v-model='setting.unoDrawPenalty' name="unoDrawPenalty" >
                </td>
            </tr>
            <tr>
                <td>
                    <button class="tooltip" title="Allow 2's and 4's to stack on each other">?</button>
                    Allow Stacking
                </td>
                <td>
                    <input v-on:change='update($event)' type="Checkbox" v-model='setting.stack'
                      name="stack" >
                </td>
            </tr>
            <tr>
                <td>
                    <button class="tooltip" title="drawing no longer end the round">?</button>
                    Draw until can play
                </td>
                <td>
                    <input v-on:change='update($event)' type="Checkbox" v-model='setting.drawUntilPlay'
                         name="drawUntilPlay" >
                </td>
            </tr>
            <tr>
                <td>
                    <button class="tooltip" title="Penalise users who dont play a card in time">?</button>
                    Allow Timeouts
                </td>
                <td>
                    <input  v-on:change='update($event)' type="checkbox" v-model='setting.allowTimeouts'
                        name="allowTimeouts" >
                </td>
            </tr>
            <tr v-if="setting.allowTimeouts">
                <td style="padding-left:20%">
                    Time (mins)
                </td>
                <td>
                    <input  v-on:change='update($event)' type="number" v-model='setting.timeoutsTime'
                        name="timeoutsTime" min="5" step="5" max="1440" >
                </td>
            </tr>
            <tr v-if="setting.allowTimeouts">
                <td style="padding-left:20%">
                    Penalty (draw)
                </td>
                <td>
                    <input  v-on:change='update($event)' type="number" v-model='setting.timeoutsDraw'
                        name="timeoutsDraw" min="1" step="1" max="10" >
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center; padding:10px">Extreme Rules</td>
            </tr>
            <tr>
                <td>
                    <button class="tooltip" title="playing a 0 causes all players to swap cards to the person going after them">?</button>
                    special 0's
                </td>
                <td>
                    <input  v-on:change='update($event)' type="checkbox" v-model='setting.extreme0'
                        name="extreme0" >
                </td>
            </tr>
            <tr>
                <td>
                    <button class="tooltip" title="playing a 1 causes everyone to draw a card">?</button>
                    special 1's
                </td>
                <td>
                    <input  v-on:change='update($event)' type="checkbox" v-model='setting.extreme1'
                        name="extreme1" >
                </td>
            </tr>
            <tr>
                <td>
                    <button class="tooltip" title="playing a 4 causes the next player draws cards until they can play">?</button>
                    special 4's
                </td>
                <td>
                    <input  v-on:change='update($event)' type="checkbox" v-model='setting.extreme4'
                        name="extreme4" >
                </td>
            </tr>
            <tr>
                <td>
                    <button class="tooltip" title="playing a 7 allows the player to swap hands with another">?</button>
                    special 7's
                </td>
                <td>
                    <input  v-on:change='update($event)' type="checkbox" v-model='setting.extreme7'
                        name="extreme7" >
                </td>
            </tr>
            <tr>
                <td>
                    <button class="tooltip" title="playing a 9 randomises the game order">?</button>
                    special 9's
                </td>
                <td>
                    <input  v-on:change='update($event)' type="checkbox" v-model='setting.extreme9'
                        name="extreme9" >
                </td>
            </tr>
        </table>
    </div>
</template>
<script>
export default {
    name:'gameSettings',
    props:['setting'],
    methods:{
        update($event){
            this.setting[$event.target.name] =
                ( $event.target.type == "checkbox" ) ?
                    (($event.target.checked)?1:0) :
                    $event.target.value;
            this.$parent.updateSettings('setting',this.setting);
        },
    }
}
</script>
