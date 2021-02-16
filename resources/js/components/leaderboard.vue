<template>
    <div id='leaderboard'>
        <div class="leaderboard">
            <h4>{{type}}</h4>
            <table >
                <tr v-for="(l,index) in leaderboard" :key="index" >
                    <td>#{{(index + 1)}}</td>
                    <td>{{l.username}}</td>
                    <td>{{l.wins}} Wins</td>

                </tr>
            </table>
        </div>
    </div>
</template>
<script>
export default {
    name:'leaderboard',
    props:['leaderboard','type'],
}
</script>
