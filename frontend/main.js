import { createApp } from 'vue'
import App from './App.vue'
import router from './router.js'
import { createPinia } from 'pinia'
import './assets/style.css'



window.onerror = (msg, src, line, col, err) => {
  console.error("🔥 ERROR DETECTADO:", msg, src, line, col, err);
  alert("Error: " + msg);
};

window.addEventListener("unhandledrejection", (e) => {
  console.error("🔥 PROMESA NO MANEJADA:", e.reason);
  alert("Promesa falló: " + e.reason);
});




// ✅ 1. Crear app y Pinia
const app = createApp(App)
const pinia = createPinia()

app.use(pinia) 
app.use(router)

// ✅ 2. Importar el authStore DESPUÉS de registrar Pinia
import { useAuthStore } from './stores/auth'


// ✅ 3. Restaurar sesión antes de montar la app
const authStore = useAuthStore()
authStore.initializeAuth()


// ✅ 4. Montar la app ya con sesión lista
app.mount('#app')

