import { defineStore } from 'pinia'

export const useNotificationStore = defineStore('notifications', {
  state: () => ({
    list: []
  }),
  actions: {
    async fetchNotifications() {
      const token = localStorage.getItem('token')
      try {
        const res = await fetch(`${import.meta.env.VITE_API_URL}/notifications/my`, {
          headers: { Authorization: `Bearer ${token}` }
        })

        const data = await res.json()

        // ✅ Limpiar estructura para evitar errores por propiedades complejas o cíclicas
        this.list = (data.notifications || []).map(n => ({
          id: n.id,
          title: n.title,
          message: n.message,
          is_read: n.is_read,
          created_at: n.created_at
        }))

        localStorage.setItem('notifications', JSON.stringify(this.list))
      } catch (error) {
        console.error('Error al obtener notificaciones:', error)
      }
    },

    markAsRead(id) {
      const notif = this.list.find(n => n.id === id)
      if (notif) notif.is_read = true

      localStorage.setItem('notifications', JSON.stringify(this.list))

      fetch(`${import.meta.env.VITE_API_URL}/notifications/read`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({ id })
      }).catch(err =>
        console.error('Error al marcar como leída:', err)
      )
    },

    clearAll() {
      this.list = []
      localStorage.removeItem('notifications')
    },

    loadFromStorage() {
      const stored = localStorage.getItem('notifications')
      if (stored) {
        try {
          this.list = JSON.parse(stored)
        } catch (e) {
          console.error('Error al leer notificaciones guardadas:', e)
          this.list = []
        }
      }
    }
  }
})

