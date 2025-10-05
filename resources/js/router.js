import { createRouter, createWebHistory } from 'vue-router';
import HomePage from './components/HomePage.vue';
import SecondPage from './components/SecondPage.vue';
import Test from './components/Test.vue';

const routes = [
    { path: '/', component: HomePage },
    { path: '/second', component: SecondPage },
    { path: '/test', component: Test }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
