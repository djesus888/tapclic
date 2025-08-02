import { createApp } from 'vue'
import App from './App.vue'
import router from './router.js'
import { createPinia } from 'pinia'
import './assets/tailwind.css'

import { useAuthStore } from './stores/authStore'

const app = createApp(App)

// 1. Montar Pinia primero
const pinia = createPinia()
app.use(pinia)

// 2. Restaurar sesión ANTES de cargar el router
const auth = useAuthStore()
auth.initializeAuth()

// 🐞 Verificación opcional para depuración
console.log("🔐 Sesión restaurada:", {
  user: auth.user,
  role: auth.role,
  token: auth.token ? '✅' : '❌'
})

// 3. Luego router y app
app.use(router)
app.mount('#app')

// 🌐 Captura de errores globales
window.onerror = (msg, src, line, col, err) => {
  console.error("🔥 ERROR DETECTADO:", msg, src, line, col, err)
  alert("Error: " + msg)
}

window.addEventListener("unhandledrejection", (e) => {
  console.error("🔥 PROMESA NO MANEJADA:", e.reason)

  if (e.reason && e.reason.stack) {
    console.log("🔍 Stack:", e.reason.stack)
  }

  alert("Promesa falló: " + e.reason.message)
})

window.addEventListener("unhandledrejection", (e) => {
  console.error("🔥 PROMESA NO MANEJADA:", e.reason)
  alert("Promesa falló: " + e.reason)
})
