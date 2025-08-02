// src/stores/notificationStore.js
import { defineStore } from 'pinia'

export const useNotificationStore = defineStore('notifications', {
  state: () => ({
    list: []
  }),
  actions: {
    async fetchNotifications() {
      const token = localStorage.getItem('token')
      const res = await fetch(`${import.meta.env.VITE_API_URL}/notifications/my`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      const data = await res.json()
      this.list = data.notifications
      localStorage.setItem('notifications', JSON.stringify(this.list))
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
      })
    },
    clearAll() {
      this.list = []
      localStorage.removeItem('notifications')
    },
    loadFromStorage() {
      const stored = localStorage.getItem('notifications')
      if (stored) this.list = JSON.parse(stored)
    }
  }
})
