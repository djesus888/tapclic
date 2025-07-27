import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    role: localStorage.getItem('role') || null // 'client' | 'provider' | 'admin'
  }),

  actions: {
    /**
     * ✅ Guardar sesión al iniciar login
     */
    login(userData) {
      this.user = userData.user
      this.token = userData.token
      this.role = userData.role

      // 🔹 Guardar en localStorage para persistencia
      localStorage.setItem('user', JSON.stringify(userData.user))
      localStorage.setItem('token', userData.token)
      localStorage.setItem('role', userData.role)
    },

    /**
     * ✅ Cerrar sesión limpiando estado y almacenamiento
     */
    logout() {
      this.user = null
      this.token = null
      this.role = null

      // 🔹 Eliminar del localStorage
      localStorage.removeItem('user')
      localStorage.removeItem('token')
      localStorage.removeItem('role')
    },

    /**
     * ✅ Restaurar sesión automáticamente si existe en localStorage
     */
    initializeAuth() {
      const storedUser = localStorage.getItem('user')
      const storedToken = localStorage.getItem('token')
      const storedRole = localStorage.getItem('role')

      if (storedUser && storedToken) {
        this.user = JSON.parse(storedUser)
        this.token = storedToken
        this.role = storedRole
      }
    },

    /**
     * ✅ Saber si está autenticado (true/false)
     */
    isAuthenticated() {
      return !!this.token && !!this.user
    }
  }
})
