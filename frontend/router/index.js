import { createRouter, createWebHistory } from 'vue-router'

// Importar las vistas
import Bienvenida from './components/Bienvenida.vue'
import Login from './components/Login.vue'
import Registro from './components/Registro.vue'
import Dashboard from './views/Dashboard.vue'
import Profile from './views/Profile.vue'

const routes = [
  { path: '/', component: Bienvenida },   // << ESTA SERÁ LA PANTALLA INICIAL
  { path: '/login', component: Login },
  { path: '/registro', component: Registro },
  { path: '/dashboard', component: Dashboard },
  { path: '/profile', component: Profile }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
