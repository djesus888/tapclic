// router.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import { getActivePinia } from 'pinia'

// Páginas públicas
import Bienvenida from './components/Bienvenida.vue'
import Login from './components/Login.vue'
import Registro from './components/Register.vue'
import Home from './components/Home.vue'
//import WalletBalance from './components/WalletBalance.vue'

// Vistas internas
import Dashboard from './views/Dashboard.vue'
import Profile from './views/Profile.vue'
//import Wallet from './views/Wallet.vue'
import Settings from './components/Settings.vue'
import Admin from './components/Admin.vue'

const routes = [
  { path: '/', component: Bienvenida },
  { path: '/login', component: Login },
  { path: '/registro', component: Registro },

  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/home', component: Home, meta: { requiresAuth: true } },
  //{ path: '/wallet', component: Wallet, meta: { requiresAuth: true } },
  //{ path: '/walletbalance', component: WalletBalance, meta: { requiresAuth: true } },
  { path: '/profile', component: Profile, meta: { requiresAuth: true } },
  { path: '/settings', component: Settings, meta: { requiresAuth: true } },
  { path: '/admin', component: Admin, meta: { requiresAuth: true, requiresRole: 'admin' } }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  // 🧠 Protección contra acceso a store antes de que Pinia esté listo
  if (!getActivePinia()) {
    console.warn("Pinia aún no está activo, abortando navegación temporal.")
    return next(false)
  }

  const auth = useAuthStore()
  const isLoggedIn = !!auth.token

  if (isLoggedIn && (to.path === '/login' || to.path === '/registro')) {
    return next('/dashboard')
  }

  if (to.meta.requiresAuth && !isLoggedIn) {
    return next('/login')
  }

  if (to.meta.requiresRole && auth.role !== to.meta.requiresRole) {
    return next('/dashboard')
  }

  next()
})

export default router;
