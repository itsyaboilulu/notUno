<template>
    <div id="achievments">
        <ul>
            <li v-for="l in all" :key='l.id' :class="{achieved:achieved(l.id), achievment:1}" >
                <img :src="'resources/img/achievments/'+l.id+'.png'" >
                <div class="desc">
                    <h4>{{l.name}}</h4>
                    <p>{{l.desc}}</p>
                    <div>
                        {{ Math.round((l.achieved/l.users)*100) }}% of players have this achievement
                    </div>
                    <div>
                        +{{l.rep}} RP
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    name:'achievments',
    props:['all','got'],
    methods:{
        achieved($id){
            for(var j = 0;j<this.got.length;j++){
                if ($id == this.got[j].aid){
                    return true;
                }
            }
            return false;
        },
    }

}
</script>
