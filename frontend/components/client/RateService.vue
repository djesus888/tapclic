<template>
  <section class="bg-white p-6 rounded-2xl shadow">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Calificar servicios</h2>

    <div v-if="services.length">
      <div
        v-for="service in services"
        :key="service.id"
        class="mb-6 border-b pb-4"
      >
        <h3 class="text-lg font-semibold text-gray-700">{{ service.title }}</h3>
        <p class="text-sm text-gray-500">Proveedor: {{ service.provider }} | Fecha: {{ formatDate(service.date) }}</p>

        <div class="flex items-center my-2 space-x-1">
          <button
            v-for="n in 5"
            :key="n"
            @click="setRating(service.id, n)"
            class="text-2xl"
          >
            <span :class="n <= ratings[service.id] ? 'text-yellow-400' : 'text-gray-300'">★</span>
          </button>
        </div>

        <textarea
          v-model="comments[service.id]"
          class="w-full p-2 border rounded mb-2 text-sm"
          placeholder="Escribe un comentario..."
        />

        <button
          @click="submitRating(service.id)"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Enviar calificación
        </button>
      </div>
    </div>

    <p v-else class="text-gray-500 text-center">No tienes servicios por calificar.</p>
  </section>
</template>

<script>
export default {
  data() {
    return {
      services: [],
      ratings: {},
      comments: {}
    }
  },
  async created() {
    await this.fetchUnratedServices()
  },
  methods: {
    async fetchUnratedServices() {
      try {
        const res = await fetch(`${import.meta.env.VITE_API_URL}/services/unrated`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        })
        const data = await res.json()
        this.services = data.services || []
      } catch (err) {
        console.error('Error al obtener servicios sin calificar:', err)
      }
    },
    setRating(serviceId, stars) {
      this.ratings[serviceId] = stars
    },
    formatDate(dateStr) {
      const d = new Date(dateStr)
      return d.toLocaleDateString()
    },
    async submitRating(serviceId) {
      const rating = this.ratings[serviceId]
      const comment = this.comments[serviceId] || ''

      if (!rating) return alert('Debes asignar una calificación.')

      try {
        const res = await fetch(`${import.meta.env.VITE_API_URL}/services/rate`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify({ service_id: serviceId, rating, comment })
        })
        const data = await res.json()
        alert(data.message || 'Gracias por tu calificación.')
        await this.fetchUnratedServices()
      } catch (err) {
        console.error('Error al enviar calificación:', err)
      }
    }
  }
}
</script>
