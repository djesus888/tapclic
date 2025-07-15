import { createRouter, createWebHistory } from 'vue-router'

// Rutas iniciales
const routes = [
  {
    path: '/',
    name: 'Inicio',
    component: () => import('../views/Home.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Login.vue')
  },
  {
    path: '/registro',
    name: 'Registro',
    component: () => import('../views/Registro.vue')
  },
  {
    path: '/servicios',
    name: 'Servicios',
    component: () => import('../views/Servicios.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
