<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Servicios disponibles</h1>

    <div v-if="loading" class="text-gray-500">Cargando servicios...</div>
    <div v-else-if="servicios.length === 0" class="text-gray-500">No hay servicios disponibles.</div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="servicio in servicios" :key="servicio.id" class="bg-white rounded-2xl shadow p-5 hover:shadow-md transition">
        <h2 class="text-xl font-semibold text-blue-600 mb-2">{{ servicio.title }}</h2>
        <p class="text-gray-700 mb-2">{{ servicio.description }}</p>
        <div class="text-sm text-gray-500 mb-1">
          Estado: 
          <span :class="{
            'text-green-600 font-bold': servicio.status === 'activo',
            'text-red-500': servicio.status !== 'activo'}">
{{ servicio.status }}</span>
        </div>
        <div class="text-sm text-gray-400">Publicado: {{ formatFecha(servicio.created_at) }}</div>

        <button
          @click="selectService(servicio.id)"
          class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition">
          Ver detalles
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "Home",
  data() {
    return {
      servicios: [],
      loading: true,
    };
  },
  async created() {
    try {
      const response = await axios.get("http://localhost:8000/api/services");
      this.servicios = response.data;
    } catch (error) {
      console.error("Error al cargar servicios:", error);
      this.servicios = [];
    } finally {
  this.loading = false;
    }
  },
  methods: {
    formatFecha(fecha) {
      if (!fecha) return 'Fecha inválida';
      const d = new Date(fecha);
      return d.toLocaleDateString() + ' ' + d.toLocaleTimeString();
    }, 
  selectService(id) {
      this.$router.push(`/services/${id}`);
    },
  },
};
</script>

<style scoped>
.service-card {
  background: #fff;
  padding: 15px;
  margin: 10px 0;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}
</style>
