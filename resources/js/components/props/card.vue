<template>
    <div>
        <div class="uno-card" :class="{ [display.color]:1, dim:!highlight }" v-on:click="play" >
            <img :src="'resources/img/cards/'+display.label+'.png'">
        </div>
        <colorpallet v-if="showPallet"></colorpallet>
        <memberpick v-if='showMemberPick && number == 7'
            v-bind:members="$parent.mmhand"
        ></memberpick>
    </div>
</template>
<script>
    import { ajax } from "../../ajax.min.js";
    export default {
        name:'card',
        props:['cardValue','playable','against'],
        mounted() {
            this.updateCardView(this.cardValue);
            this.checkSpecial(this.cardValue);
        },
        watch: {
            cardValue: function(val) {
                this.updateCardView(val);
            },
            against: function(val) {
                this.updateCardView(this.cardValue);
            }
        },
        data() {
            return {
                cname:'',
                speciel:'',
                number:'',
                color:'',
                isSpecial:0,
                showPallet:0,
                showMemberPick:0,
                highlight:1,
                display:{
                    colors: {
                        R: 'red',
                        G: 'green',
                        B: 'blue',
                        Y: 'yellow',
                    },
                    color:'',
                    label:''
                }
            }
        },
        methods: {
            updateCardView($data){
                if ( $data == 'W' || $data == 'WD4' ){
                    this.display.color = 'black';
                    this.color         = 'wild';
                    this.display.label = $data;
                } else {
                    this.display.color = this.display.colors[ $data.charAt(0) ];
                    this.display.label = $data.substring(1);
                    this.color         = this.display.colors[ $data.charAt(0) ];
                    this.number        = $data.substring(1);
                }
                if (this.playable){
                    this.highlight = (!this.$parent.yourTurn)? true : this.canBePlayed();
                }
            },
            play(){
                if (this.playable && this.$parent.yourTurn){
                    if ( this.canBePlayed() ) {
                        if (this.isWild()){
                            this.showPallet = 1;
                        } else if( this.number == 7 && this.$parent.game.extreme7) {
                            this.showMemberPick = 1;
                        } else {
                            ajax( this.checkResponse, 'api/game', {
                                action:     'playCard',
                                card:       this.cardValue,
                                password:   this.$parent.game_settinsg.password,
                                uno_call:   this.$parent.uno,
                            }, 1 );
                            this.$parent.uno = 0;
                        }
                    }
                }
            },
            playColorPick($color){
                this.showPallet = 0;
                ajax( this.checkResponse, 'api/game', {
                        action:     'playCard',
                        card:       $color + this.cardValue,
                        password:   this.$parent.game_settinsg.password,
                        uno_call:   this.$parent.uno,
                    }, 1 );
                this.$parent.uno = 0;
            },
            playMemberPick($member){
                this.showMemberPick = 0;
                ajax( this.checkResponse, 'api/game', {
                        action:     'playCard',
                        card:       this.cardValue,
                        password:   this.$parent.game_settinsg.password,
                        uno_call:   this.$parent.uno,
                        extra:      $member,
                    }, 1 );
                this.$parent.uno = 0;
            },
            checkResponse($response){
                this.$parent.update();
            },
            canBePlayed(){
                switch(true){
                    case (this.$parent.stack):
                        return (this.cardValue.substring(1) == this.against.substring(1))
                            ?((this.cardValue == 'WD4')? true : false)
                            :false;
                    case (this.cardValue.substring(1) == this.against.substring(1)):
                    case (this.color == 'wild' ):
                    case ( this.color == this.display.colors[ this.against.charAt(0) ] ):
                    case ( this.number == this.against.substring(1) ):
                        return true;
                    default:
                        return false;
                }
            },
            checkSpecial(){
                if (this.cardValue == 'WD4' ||
                    this.cardValue.substring(1) == 'D2' ||
                    this.cardValue.substring(1) == 'S' ||
                    this.cardValue.substring(1) == 'R'
                ){
                    this.isSpecial = 1
                }
            },
            isWild(){
                return ( this.display.label == 'WD4' || this.display.label == 'W' ) ? 1 : 0;
            }
        }
    }
</script>
