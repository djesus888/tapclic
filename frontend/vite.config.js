import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './') // Alias para la carpeta frontend
    }
  },
  server: {
    host: true,          // Permite acceder desde otros dispositivos en la red local
    port: 5173,          // Puerto donde corre el frontend
    proxy: {
      '/api': 'http://127.0.0.1:8000' // Redirige las peticiones que comienzan con /api al backend
    }
  }
})
