require('./bootstrap');

window.Vue = require('vue').default;



Vue.component('login',          require('./components/login.vue').default);
Vue.component('register',       require('./components/register.vue').default);
Vue.component('play',           require('./components/play.vue').default);
Vue.component('lobby',          require('./components/lobby.vue').default);
Vue.component('chat',           require('./components/chat.vue').default);
Vue.component('leaderboard',    require('./components/leaderboard.vue').default);
Vue.component('settings',       require('./components/settings.vue').default);
Vue.component('stats',          require('./components/stats.vue').default);
Vue.component('achievments',    require('./components/achievments.vue').default);

Vue.component('unoCard',        require('./components/props/card.vue').default);
Vue.component('deckBreakDown',  require('./components/props/deckBreakDown.vue').default);
Vue.component('gameSettings',   require('./components/props/gameSettings.vue').default);
Vue.component('colorpallet',    require('./components/props/colorpallet.vue').default);
Vue.component('mhands',         require('./components/props/mhands.vue').default);
Vue.component('memberpick',     require('./components/props/memberpick.vue').default);

Vue.component('pieChart',       require('./components/props/pieChart.vue').default);
Vue.component('barChart',       require('./components/props/barChart.vue').default);
Vue.component('lineChart',      require('./components/props/lineChart.vue').default);

const app = new Vue({
    el: '#app',
});
