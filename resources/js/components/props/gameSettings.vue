<template>
    <div id='gameSettings'>
        <table>
            <tr>
                <td style="width:100%">
                    <button class="tooltip" title="How many cards are drawn if uno is not called">?</button>
                        Uno penalty
                    </td>
                <td>
                    <input v-on:change='update($event)' type="number" min="1" step="1" v-model='setting.unoDrawPenalty' name="unoDrawPenalty" >
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
                        name="timeoutsTime" min="5" step="5" >
                </td>
            </tr>
            <tr v-if="setting.allowTimeouts">
                <td style="padding-left:20%">
                    Penalty (draw)
                </td>
                <td>
                    <input  v-on:change='update($event)' type="number" v-model='setting.timeoutsDraw'
                        name="timeoutsDraw" min="1" step="1" >
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
