<!-- src/components/client/ExploreServices.vue -->
<template>
  <section class="bg-white p-4 rounded-2xl shadow">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Explorar servicios</h2>

    <!-- Filtro de búsqueda -->
    <input
      v-model="search"
      type="text"
      placeholder="Buscar servicios..."
      class="w-full p-2 border border-gray-300 rounded-lg mb-4"
    />

    <!-- Listado de servicios -->
    <div v-if="filteredServices.length" class="grid md:grid-cols-2 gap-4">
      <div
        v-for="service in filteredServices"
        :key="service.id"
        class="border p-4 rounded-xl shadow hover:shadow-lg transition"
      >
        <h3 class="font-semibold text-lg">{{ service.title }}</h3>
        <p class="text-sm text-gray-600 mb-2">{{ service.description }}</p>
        <p class="text-sm text-gray-400">Proveedor: {{ service.provider_name }}</p>

        <button
          @click="requestService(service.id)"
          class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Solicitar servicio
        </button>
      </div>
    </div>
    <p v-else class="text-gray-500">No se encontraron servicios disponibles.</p>
  </section>
</template>

<script>
export default {
  data() {
    return {
      services: [],
      search: ''
    }
  },
  computed: {
    filteredServices() {
      const q = this.search.toLowerCase()
      return this.services.filter(
        s =>
          s.title.toLowerCase().includes(q) ||
          s.description.toLowerCase().includes(q)
      )
    }
  },
  async mounted() {
    await this.fetchServices()
  },
  methods: {
    async fetchServices() {
      try {
        const token = localStorage.getItem('token')
        const res = await fetch(`${import.meta.env.VITE_API_URL}/services/available`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        })
        const data = await res.json()
        this.services = data.services
      } catch (err) {
        console.error('Error cargando servicios:', err)
      }
    },
    async requestService(serviceId) {
      try {
        const token = localStorage.getItem('token')
        const res = await fetch(`${import.meta.env.VITE_API_URL}/requests/create`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`
          },
          body: JSON.stringify({ service_id: serviceId })
        })
        const data = await res.json()

        if (data.success) {
          alert('Solicitud enviada con éxito')
        } else {
          alert('Error al solicitar servicio')
        }
      } catch (err) {
        console.error('Error solicitando servicio:', err)
        alert('Error de conexión')
      }
    }
  }
}
</script>
