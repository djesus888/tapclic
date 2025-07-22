import { createApp } from 'vue'
import App from './App.vue'
import router from './router.js'
import './assets/style.css'

// Crear la app de Vue
const app = createApp(App)

// Usar el router
app.use(router)

// Montar en el div con id="app" que estará en index.html
app.mount('#app')

