<template>

</template>
<script>
export default {
    name:'hand',
    props:['hand'],
    watch:{
        hand: function(newVal, oldVal) {
            this.$parent.isuno = (newVal.length == 2) ? true : false;
        }
    }
}
</script>
