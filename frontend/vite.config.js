import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    host: true,  // permite acceso desde el navegador del móvil
    port: 5173
  }
})

