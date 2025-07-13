// frontend/main.js

import { createApp } from 'vue';
import App from './App.vue';
import { createRouter, createWebHistory } from 'vue-router';

import Login from './components/Login.vue';
import Register from './components/Register.vue';
import Home from './components/Home.vue';
import Chat from './components/Chat.vue';
import Dashboard from './views/Dashboard.vue';
import Profile from './views/Profile.vue';

const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login },
  { path: '/registro', component: Register },
  { path: '/dashboard', component: Dashboard },
  { path: '/profile', component: Profile },
  { path: '/chat/:id', component: Chat }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

createApp(App).use(router).mount('#app');
