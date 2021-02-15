<template>
    <div id="mhands">
        <h4>CARDS</h4>
        <ul>
            <li v-for="h in hand" >{{h.member}}: {{h.count}}</li>
        </ul>
    </div>
</template>
<script>
export default {
    name:'mhands',
    props:['hands'],
    computed:{
        hand: function(){
            return _.orderBy(this.hands,'member','asc').slice(0,5);
        }
    }
}
</script>
