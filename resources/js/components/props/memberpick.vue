<template>
    <div id="memberpick"        v-on:click="close(0)">
        <div class="memberpick" v-on:click="close(1)">
            <h4>Trade With?</h4>
            <ul>
                <li v-for="m in members" v-on:click='$parent.playMemberPick(m.member)' >
                    > {{m.member}} <
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
export default {
    name:'memberpick',
    props:['members'],
    methods:{
        close($dont){
            if (!$dont){
                this.$parent.showMemberPick = 0;
            }
        }
    }

}
</script>
