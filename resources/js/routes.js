import Vue from "vue";
import VueRouter from "vue-router";

let start = require('./components/views/start.vue').default;
let NewsFeed = require('./components/views/NewsFeed.vue').default;
let UserShow = require('./components/views/Show.vue').default;



Vue.use(VueRouter);

export default new VueRouter({
    mode:'history',

    routes:[
        {
            path: '/',name:'home',component:NewsFeed,
        },
        {
            path: '/users/:userId',name:'user.show',component:UserShow,
        }
    ]
});