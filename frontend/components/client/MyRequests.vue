<!-- src/components/client/MyRequests.vue -->
<template>
  <section class="bg-white p-4 rounded-2xl shadow">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Mis solicitudes</h2>

    <div class="mb-4">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        @click="currentTab = tab.key"
        :class="[
          'px-4 py-2 mr-2 rounded-full text-sm',
          currentTab === tab.key
            ? 'bg-blue-600 text-white'
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
        ]"
      >
        {{ tab.label }}
      </button>
    </div>

    <div v-if="filteredRequests.length" class="space-y-4">
      <div
        v-for="req in filteredRequests"
        :key="req.id"
        class="p-4 border rounded-xl shadow-sm flex justify-between items-center"
      >
        <div>
          <h3 class="font-semibold text-lg">{{ req.service_title }}</h3>
          <p class="text-sm text-gray-500">
            Proveedor: {{ req.provider_name }} · Fecha: {{ formatDate(req.created_at) }}
          </p>
          <p class="text-xs mt-1">
            Estado:
            <span :class="statusColor(req.status)">
              {{ translateStatus(req.status) }}
            </span>
          </p>
        </div>

        <div class="space-x-2">
          <button
            v-if="req.status === 'pending'"
            @click="cancelRequest(req.id)"
            class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600"
          >
            Cancelar
          </button>
          <button
            v-if="req.status === 'accepted'"
            @click="startChat(req.id)"
            class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
          >
            Chatear
          </button>
        </div>
      </div>
    </div>

    <p v-else class="text-gray-500">No tienes solicitudes en esta categoría.</p>
  </section>
</template>

<script>
export default {
  data() {
    return {
      requests: [],
      currentTab: 'pending',
      tabs: [
        { key: 'pending', label: 'Pendientes' },
        { key: 'accepted', label: 'Aceptadas' },
        { key: 'rejected', label: 'Rechazadas' },
        { key: 'completed', label: 'Completadas' }
      ]
    }
  },
  computed: {
    filteredRequests() {
      return this.requests.filter(r => r.status === this.currentTab)
    }
  },
  async mounted() {
    await this.fetchRequests()
  },
  methods: {
    async fetchRequests() {
      try {
        const token = localStorage.getItem('token')
        const res = await fetch(`${import.meta.env.VITE_API_URL}/requests/get`, {
          headers: { Authorization: `Bearer ${token}` }
        })
        const data = await res.json()
        this.requests = data.requests
      } catch (err) {
        console.error('Error al cargar solicitudes:', err)
      }
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString()
    },
    translateStatus(status) {
      const map = {
        pending: 'Pendiente',
        accepted: 'Aceptada',
        rejected: 'Rechazada',
        completed: 'Completada'
      }
      return map[status] || status
    },
    statusColor(status) {
      return {
        pending: 'text-yellow-600',
        accepted: 'text-green-600',
        rejected: 'text-red-600',
        completed: 'text-gray-600'
      }[status]
    },
    async cancelRequest(id) {
      const confirmCancel = confirm('¿Seguro que quieres cancelar esta solicitud?')
      if (!confirmCancel) return

      try {
        const token = localStorage.getItem('token')
        const res = await fetch(`${import.meta.env.VITE_API_URL}/requests/cancel`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`
          },
          body: JSON.stringify({ request_id: id })
        })
        const data = await res.json()
        if (data.success) {
          this.requests = this.requests.map(r =>
            r.id === id ? { ...r, status: 'cancelled' } : r
          )
        }
      } catch (err) {
        console.error('Error al cancelar:', err)
      }
    },
    startChat(requestId) {
      // Placeholder – integración con chat más adelante
      alert(`Iniciar chat con el proveedor (ID solicitud: ${requestId})`)
    }
  }
}
</script>
