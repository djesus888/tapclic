import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    role: localStorage.getItem('role') || null // 'user' | 'driver' | 'admin'
  }),

  actions: {
    /**
     * ✅ Guardar sesión al iniciar login
     */
    login(userData) {
      const { id, name, email, role } = userData.user

      const cleanedUser = { id, name, email }

      this.user = cleanedUser
      this.token = userData.token
      this.role = role

      localStorage.setItem('user', JSON.stringify(cleanedUser))
      localStorage.setItem('token', userData.token)
      localStorage.setItem('role', role)
    },

    /**
     * ✅ Cerrar sesión limpiando estado y almacenamiento
     */
    logout() {
      this.user = null
      this.token = null
      this.role = null

      localStorage.removeItem('user')
      localStorage.removeItem('token')
      localStorage.removeItem('role')
    },

    /**
     * ✅ Restaurar sesión automáticamente si existe en localStorage
     */
    initializeAuth() {
      try {
        const storedUser = localStorage.getItem('user')
        const storedToken = localStorage.getItem('token')
        const storedRole = localStorage.getItem('role')

        if (storedUser && storedToken && storedRole) {
          this.user = JSON.parse(storedUser)
          this.token = storedToken
          this.role = storedRole
        } else {
          this.logout() // 🔐 Seguridad: limpiar si falta algún dato
        }
      } catch (error) {
        console.error("Error restaurando auth:", error)
        this.logout()
      }
    }
  },

  getters: {
    isLoggedIn: (state) => !!state.token && !!state.user,
    userRole: (state) => state.role
  }
})
