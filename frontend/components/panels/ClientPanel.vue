<template>
  <section class="py-8 px-4 md:px-8 lg:px-12">
    <h2 class="text-2xl font-bold text-blue-700 mb-6 text-center">Servicios disponibles</h2>

    <div v-if="loading" class="text-center text-gray-500">Cargando servicios...</div>
    <div v-else-if="services.length === 0" class="text-center text-gray-500">No hay servicios disponibles</div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="service in services"
        :key="service.id"
        @click="goToService(service)"
        class="cursor-pointer bg-white rounded-xl shadow hover:shadow-md transition-shadow duration-300 p-5 border border-gray-200 hover:border-blue-500"
      >
        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ service.title }}</h3>
        <p class="text-sm text-gray-600">{{ service.description }}</p>
        <p class="mt-3 text-xs text-gray-400">Publicado: {{ formatDate(service.created_at) }}</p>
      </div>
    </div>
  </section>
</template>

<script>
import axios from "axios";

export default {
  name: "ClientPanel",
  data() {
    return {
      services: [],
      loading: false,
    };
  },
  methods: {
    async fetchServices() {
      this.loading = true;
      try {
        const token = localStorage.getItem("token");
        const response = await axios.get("http://localhost:8000/api/services", {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        this.services = response.data.services || [];
      } catch (error) {
        console.error("Error al obtener los servicios:", error);
      } finally {
        this.loading = false;
      }
    },
    formatDate(dateStr) {
      const options = { year: "numeric", month: "short", day: "numeric" };
      return new Date(dateStr).toLocaleDateString(undefined, options);
    },
    goToService(service) {
      this.$router.push({ path: "/Services", query: { id: service.id } });
    },
  },
  mounted() {
    this.fetchServices();
  },
};
</script>
