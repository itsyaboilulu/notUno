<template>
    <div id="stats">
        <div v-if="!stats.cards.played">
            <div class="playMessage" >
                play some cards to see your stats
            </div>
        </div>


        <div class="stats" v-else>

            <div class="cards">
                <div class="stat-item stat-2">
                    <div class="stat-lower">
                        <h4>Favourite Card</h4>
                        <unoCard
                            v-bind:cardValue=stats.favCard
                            v-bind:playable=0
                            v-bind:against=0
                        ></unoCard>
                    </div>
                    <div class="stat-lower">
                        <h4>Wins</h4>
                        <div class="divide">
                            <p>{{stats.wins}}</p>
                            <hr>
                            <p>{{stats.played}}</p>
                        </div>
                    </div>
                </div>
                <div class="stat-item">
                    <table>
                        <tr>
                            <td>Cards Played</td>
                            <td>{{stats.cards.played}}</td>
                        </tr>
                        <tr>
                            <td>Cards Drawn</td>
                            <td>{{stats.cards.drawn}}</td>
                        </tr>
                        <tr v-if="stats.plays.reverse.streak != 1">
                            <td>Longest Reverse Streak</td>
                            <td>{{stats.plays.reverse.streak}} With {{stats.plays.reverse.with}}</td>
                        </tr>
                        <tr v-if="stats.timeout">
                            <td> Ran out of Time</td>
                            <td>{{stats.timeout}}</td>
                        </tr>
                        <tr>
                            <td>Called UNO</td>
                            <td>{{stats.uno.called}}</td>
                        </tr>
                        <tr v-if="stats.uno.failed">
                            <td>Forgot To Call UNO</td>
                            <td>{{stats.uno.failed}}</td>
                        </tr>
                    </table>
                </div>
                <div class="stat-item block">
                    <h4>Colors Played</h4>
                    <pieChart :labels="colors.labels" :dataset="colors.dataset" ></pieChart>
                </div>

                <div class="stat-item block">
                    <h4>Special Cards Played</h4>
                    <pieChart :labels="special.labels" :dataset="special.dataset" ></pieChart>
                </div>
            </div>

            <div class="chat" >
                <div class="stat-item" v-if="stats.chat.wisper" >
                    <div >
                        Sent {{ stats.chat.sent }} messages
                    </div>
                </div>

                <div class="stat-item" v-if="stats.chat.wisper" >
                    <div >
                        Whispered another player {{ stats.chat.wisper }} times
                    </div>
                </div>

                <div class="stat-item" v-if="stats.chat.alerts.given || stats.chat.alerts.recived" >
                    <div >
                        Sent {{ stats.chat.alerts.given }} alerts <br> Recived {{ stats.chat.alerts.recived }}
                    </div>
                </div>
            </div>

            <div class="playTime">
                <div class="stat-item">
                    <h4>Active Days</h4>
                    <barChart :labels="days.labels" :dataset="days.dataset" ></barChart>
                </div>
                <div class="stat-item">
                    <h4>Active Hours</h4>
                    <lineChart :labels="hours.labels" :dataset="hours.dataset" ></lineChart>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    name:'stats',
    props:['stats','rep'],
    mounted(){
        console.log(this.stats);
    },
    computed:{
        days(){
            var colors = [];
            for (const [key, val] of Object.entries(this.stats.playTime.days)) {
                colors.push("rgba(0, 255, 0, 0.9)");
            }
            return this.chartData(this.stats.playTime.days,colors,'Cards Played');
        },
        hours(){
            return this.chartData(this.stats.playTime.hours,[],'Cards Played');
        },
        colors(){
            return this.chartData(this.stats.colors,['#FF0000','#00FF00','#0000FF','#FFFF00']);
        },
        special(){
            return this.chartData(this.stats.cards.special,['#FF0000','#00FF00','#0000FF','#FFFF00','#000000']);
        }
    },
    methods:{
        chartData($arr,$colors,$label=null){
            var labels = [], data=[];
            for (const [key, val] of Object.entries($arr)) {
                labels.push(key);
                data.push(val);
            }
            return {
                labels:labels,
                dataset:[{
                    label:$label,
                    backgroundColor:$colors,
                    data:data,
                }]
            };
        }
    }
}
</script>
