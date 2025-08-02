<!-- src/components/global/NotificationPanel.vue -->
<template>
  <aside
    class="fixed right-0 top-16 w-80 bg-white shadow-lg rounded-l-2xl h-[calc(100vh-4rem)] p-4 z-50 overflow-y-auto"
  >
    <h2 class="text-lg font-bold mb-4 flex justify-between items-center">
      Notificaciones
      <button
        @click="clearAll"
        class="text-xs text-red-600 hover:underline"
      >
        Limpiar todo
      </button>
    </h2>

    <div v-if="notifications.length">
      <div
        v-for="notif in notifications"
        :key="notif.id"
        @click="markAsRead(notif.id)"
        class="mb-3 p-3 rounded-xl cursor-pointer transition hover:bg-gray-100"
        :class="notif.is_read ? 'bg-gray-50' : 'bg-blue-50'"
      >
        <p class="font-semibold">{{ notif.title }}</p>
        <p class="text-sm text-gray-600">{{ notif.message }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ formatDate(notif.created_at) }}</p>
      </div>
    </div>
    <p v-else class="text-gray-500 text-sm">No hay notificaciones nuevas.</p>
  </aside>
</template>

<script>
import { useNotificationStore } from '@/stores/notificationStore'

export default {
  setup() {
    const store = useNotificationStore()
    store.loadFromStorage()
    store.fetchNotifications()

    return {
      notifications: store.list,
      markAsRead: store.markAsRead,
      clearAll: store.clearAll
    }
  },
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleString()
    }
  }
}
</script>
