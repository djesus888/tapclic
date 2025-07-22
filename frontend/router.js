import { createRouter, createWebHistory } from 'vue-router'

// Páginas públicas (landing)
import Bienvenida from './components/Bienvenida.vue'
import Login from './components/Login.vue'
import Registro from './components/Register.vue'

// Vistas internas (usuarios autenticados)
import Dashboard from './views/Dashboard.vue'
import Profile from './views/Profile.vue'

const routes = [
  { path: '/', component: Bienvenida },
  { path: '/login', component: Login },
  { path: '/registro', component: Registro },

  // Rutas protegidas (opcionalmente podemos añadir guard)
  { path: '/dashboard', component: Dashboard },
  { path: '/profile', component: Profile }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
