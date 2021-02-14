<template>
    <div id='colorPallet' v-on:click="close(0)">
        <div class="colorPallet" v-on:click="close(1)">
            <div v-on:click="playColorPick('R')"></div>
            <div v-on:click="playColorPick('G')"></div>
            <div v-on:click="playColorPick('B')" ></div>
            <div v-on:click="playColorPick('Y')"></div>
        </div>
    </div>
</template>
<script>
export default {
    name:'colorpallet',
    methods:{
        playColorPick($c){
            this.$parent.playColorPick($c);
        },
        close($dont){
            if (!$dont){
                this.$parent.showPallet = 0;
            }
        }
    }
}
</script>
