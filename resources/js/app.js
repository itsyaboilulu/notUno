require('./bootstrap');

window.Vue = require('vue').default;



Vue.component('login',          require('./components/login.vue').default);
Vue.component('register',       require('./components/register.vue').default);
Vue.component('play',           require('./components/play.vue').default);
Vue.component('lobby',          require('./components/lobby.vue').default);
Vue.component('chat',           require('./components/chat.vue').default);
Vue.component('leaderboard',    require('./components/leaderboard.vue').default);
Vue.component('settings',       require('./components/settings.vue').default);


Vue.component('unoCard',        require('./components/props/card.vue').default);
Vue.component('deckBreakDown',  require('./components/props/deckBreakDown.vue').default);
Vue.component('gameSettings',   require('./components/props/gameSettings.vue').default);
Vue.component('colorpallet',    require('./components/props/colorpallet.vue').default);
Vue.component('mhands',         require('./components/props/mhands.vue').default);

const app = new Vue({
    el: '#app',
});
