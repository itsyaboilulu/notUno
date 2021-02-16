<template>
    <div id="deckBreakDown">
        <table>
            <tr v-for="( value, key ) in deckarr" >
                <td>{{ key }}</td>
                <td>
                    <input :name="key" type="number" v-if="noncolors[key]"  :value="value" min="0" step="1" v-on:change="update($event)" >
                    <input :name="key" type="number" v-if="!noncolors[key]" :value="value" min="0" step="4" v-on:change="update($event)" >
                </td>
                <td>{{ deckPercent.ind[key] }}%</td>
            </tr>
        </table>
    </div>
</template>
<script>
export default {
    name    : 'deckBreakDown',
    props   : ['deck'],
    mounted(){
        this.deck.forEach(element => {
            if (element.charAt(0) == 'W'){
                this.deckarr[element]++;
            } else {
                this.deckarr[element.substring(1)]++;
            }
        });
        this.updatePercentage();
    },
    data(){
        return {
            deckarr:{
                0:0,1:0,2:0,3:0,4:0,5:0,6:0,7:0,8:0,9:0,
                W:0,WD4:0,D2:0,S:0,R:0
            },
            deckPercent:{
                sum:0,
                ind:{
                    0:0,1:0,2:0,3:0,4:0,5:0,6:0,7:0,8:0,9:0,
                    W:0,WD4:0,D2:0,S:0,R:0
                }
            },
            noncolors: {W:1,WD4:1},
            colors: ['R','G','Y','B'],
        }
    },
    methods:{
        update($event){
            this.deckarr[$event.target.name] = parseInt($event.target.value);
            this.updatePercentage();
            this.$parent.updateSettings('deck',this.generateDeck());
        },
        updatePercentage(){
            this.deckPercent.sum = 0;
            for(const key in this.deckarr){
                this.deckPercent.sum = this.deckPercent.sum + this.deckarr[key];
            }
            for(const key in this.deckarr){
                this.deckPercent.ind[key] = Math.round((this.deckarr[key]/this.deckPercent.sum)*100);
            };
        },
        generateDeck(){
            var i,ret=[];
            for(const key in this.deckarr){
                if ( this.noncolors[key] ){
                    for(i=0;i<this.deckarr[key];i++){
                        ret.push(key);
                    }
                } else {
                    for(i=0;i<(this.deckarr[key]/4);i++){
                        for( const c in this.colors ){
                            ret.push(this.colors[c]+key);
                        }
                    }
                }
            }
            return ret;
        }
    }
}
</script>
