<template>
  <section class="space-y-6">
    <header class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-semibold tracking-tight">Solicitudes recibidas</h2>
        <p class="text-sm text-gray-600">Gestiona las solicitudes de tus servicios.</p>
      </div>
      <button @click="fetchRequests" class="btn-refresh text-sm px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
        🔄 Recargar
      </button>
    </header>

    <div v-if="loading" class="text-gray-500">Cargando solicitudes...</div>

    <div v-else-if="requests.length === 0" class="text-gray-400 italic">No tienes solicitudes por el momento.</div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <div v-for="req in requests" :key="req.id" class="rounded-xl border p-4 shadow-sm bg-white">
        <h3 class="text-lg font-bold text-indigo-700">{{ req.title }}</h3>
        <p class="text-gray-700 mt-1">{{ req.description }}</p>
        <p class="text-sm text-gray-500 mt-2">Estado: <span class="font-medium">{{ req.status }}</span></p>
        <p class="text-xs text-gray-400">Fecha: {{ formatDate(req.created_at) }}</p>

        <div class="mt-4 flex gap-2">
          <button @click="acceptRequest(req.id)" class="px-3 py-1 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">Aceptar</button>
          <button @click="rejectRequest(req.id)" class="px-3 py-1 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600">Rechazar</button>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { useAuthStore } from "@/stores/auth";

export default {
  name: "RequestsPanel",
  data() {
    return {
      requests: [],
      loading: false,
    };
  },
  methods: {
    async fetchRequests() {
      this.loading = true;
      const authStore = useAuthStore();
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/requests/get`, {
          headers: {
            "Authorization": `Bearer ${authStore.token}`,
          },
        });
        const data = await response.json();
        this.requests = data.data || [];
      } catch (error) {
        console.error("Error al obtener solicitudes:", error);
        alert("Error al cargar solicitudes.");
      } finally {
        this.loading = false;
      }
    },
    async acceptRequest(id) {
      const authStore = useAuthStore();
      try {
        const res = await fetch(`${import.meta.env.VITE_API_URL}/requests/accept`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${authStore.token}`,
          },
          body: JSON.stringify({ id }),
        });
        if (res.ok) {
          this.fetchRequests();
        }
      } catch (err) {
        alert("Error al aceptar solicitud");
      }
    },
    async rejectRequest(id) {
      const authStore = useAuthStore();
      try {
        const res = await fetch(`${import.meta.env.VITE_API_URL}/requests/reject`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${authStore.token}`,
          },
          body: JSON.stringify({ id }),
        });
        if (res.ok) {
          this.fetchRequests();
        }
      } catch (err) {
        alert("Error al rechazar solicitud");
      }
    },
    formatDate(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleDateString();
    },
  },
  mounted() {
    this.fetchRequests();
  },
};
</script>
