<template>
  <section class="space-y-6">
    <header class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-semibold tracking-tight">Panel de proveedor</h2>
        <p class="text-sm text-gray-600">Tu estado, pedidos y rendimiento.</p>
      </div>
      <button
        class="rounded-full border px-4 py-2 text-sm shadow-sm hover:shadow transition"
        :class="available ? 'bg-green-50 border-green-200 text-green-700' : 'bg-gray-50 border-gray-200 text-gray-700'"
        @click="toggleAvailability"
      >
        {{ available ? 'Disponible' : 'No disponible' }}
      </button>
    </header>

    <!-- Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <StatCard title="Pedidos activos" :value="fmt(stats.activeOrders)" :hint="'Pendientes: ' + fmt(stats.pending)" />
      <StatCard title="Completados hoy" :value="fmt(stats.completedToday)" :hint="'Semana: ' + fmt(stats.completedWeek)" />
      <StatCard title="Ganancias hoy" :value="currency(stats.earningsToday)" :hint="'Semana: ' + currency(stats.earningsWeek)" />
      <StatCard title="Rating" :value="(stats.rating?.toFixed(2) || '—')" :hint="fmt(stats.reviews) + ' reseñas'" />
    </div>

    <!-- Acciones rápidas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <button class="rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm hover:shadow transition" @click="$router.push('/ReceivedOrders')">
        <div class="text-sm text-gray-500">Pedidos</div>
        <div class="mt-1 text-lg font-semibold">Recibidos</div>
        <p class="mt-2 text-sm text-gray-600">Aceptar o rechazar nuevas solicitudes.</p>
      </button>
      <button class="rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm hover:shadow transition" @click="$router.push('/ServicePanel')">
        <div class="text-sm text-gray-500">Servicios</div>
        <div class="mt-1 text-lg font-semibold">En curso</div>
        <p class="mt-2 text-sm text-gray-600">Gestiona tu trabajo activo.</p>
      </button>
      <button class="rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm hover:shadow transition" @click="$router.push('/ServiceStats')">
        <div class="text-sm text-gray-500">Rendimiento</div>
        <div class="mt-1 text-lg font-semibold">Estadísticas</div>
        <p class="mt-2 text-sm text-gray-600">Histórico de tiempos y calidad.</p>
      </button>
      <button class="rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm hover:shadow transition" @click="$router.push('/Settings')">
        <div class="text-sm text-gray-500">Cuenta</div>
        <div class="mt-1 text-lg font-semibold">Configuración</div>
        <p class="mt-2 text-sm text-gray-600">Disponibilidad, notificaciones, perfil.</p>
      </button>
    </div>

    <!-- Lista de asignaciones -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
      <div class="border-b border-gray-200 px-4 py-3 flex items-center justify-between">
        <h3 class="text-sm font-semibold">Asignaciones de hoy</h3>
        <button class="text-xs text-indigo-600" @click="refresh">Actualizar</button>
      </div>

      <ul class="divide-y divide-gray-100">
        <li v-for="job in assignments" :key="job.id" class="px-4 py-4 text-sm">
          <div class="flex items-center justify-between">
            <div class="font-medium">{{ job.title }}</div>
            <div class="text-xs text-gray-500">{{ formatDate(job.start_at) }}</div>
          </div>
          <div class="mt-1 text-gray-600">
            {{ job.address }} · {{ job.distance_km }} km · ETA {{ job.eta_min }} min
          </div>
          <div class="mt-3 flex items-center gap-3">
            <button class="rounded-lg border px-3 py-1.5 text-xs hover:bg-gray-50" @click="goToDetail(job)">Ver detalle</button>
            <button class="rounded-lg border px-3 py-1.5 text-xs hover:bg-gray-50" @click="markStarted(job)" v-if="job.status==='assigned'">Iniciar</button>
            <button class="rounded-lg border px-3 py-1.5 text-xs hover:bg-gray-50" @click="markDone(job)" v-if="job.status==='in_progress'">Completar</button>
          </div>
        </li>
        <li v-if="!assignments.length" class="px-4 py-6 text-center text-sm text-gray-500">Sin asignaciones pendientes.</li>
      </ul>
    </div>
  </section>
</template>

<script>
import axios from "axios";
import StatCard from "@/components/shared/StatCard.vue";

export default {
  name: "DriverPanel",
  components: { StatCard },
  data() {
    return {
      API_BASE: import.meta.env.VITE_API_BASE_URL || "http://localhost:8000",
      token: localStorage.getItem("token"),
      available: true,
      stats: { activeOrders: 0, pending: 0, completedToday: 0, completedWeek: 0, earningsToday: 0, earningsWeek: 0, rating: 4.85, reviews: 120 },
      assignments: [],
    };
  },
  async mounted() {
    await this.refresh();
  },
  methods: {
    async refresh() {
      await Promise.all([this.fetchStats(), this.fetchAssignments()]);
    },
    async fetchStats() {
      try {
        const url = `${this.API_BASE}/backend/api/driver/stats`;
        const { data } = await axios.get(url, { headers: { Authorization: `Bearer ${this.token}` } });
        if (data?.data) this.stats = { ...this.stats, ...data.data };
      } catch {
        // Demo
        this.stats = { activeOrders: 2, pending: 3, completedToday: 4, completedWeek: 17, earningsToday: 95.5, earningsWeek: 420.75, rating: 4.91, reviews: 138 };
      }
    },
    async fetchAssignments() {
      try {
        const url = `${this.API_BASE}/backend/api/driver/assignments?date=today`;
        const { data } = await axios.get(url, { headers: { Authorization: `Bearer ${this.token}` } });
        this.assignments = Array.isArray(data?.data) ? data.data : [];
      } catch {
        // Demo
        this.assignments = [
          { id: 1, title: "Entrega urgente", address: "Damrak 12, Amsterdam", distance_km: 4.2, eta_min: 12, start_at: new Date().toISOString(), status: "assigned" },
          { id: 2, title: "Servicio de instalación", address: "Museumplein 3, Amsterdam", distance_km: 2.6, eta_min: 7, start_at: new Date(Date.now()+3600e3).toISOString(), status: "in_progress" },
        ];
      }
    },
    async toggleAvailability() {
      this.available = !this.available;
      try {
        const url = `${this.API_BASE}/backend/api/driver/availability`;
        await axios.put(url, { available: this.available }, { headers: { Authorization: `Bearer ${this.token}` } });
      } catch (e) {
        console.error(e);
      }
    },
    async markStarted(job) {
      try {
        const url = `${this.API_BASE}/backend/api/driver/jobs/${job.id}/start`;
        await axios.post(url, {}, { headers: { Authorization: `Bearer ${this.token}` } });
        job.status = "in_progress";
      } catch (e) {
        console.error(e);
      }
    },
    async markDone(job) {
      try {
        const url = `${this.API_BASE}/backend/api/driver/jobs/${job.id}/complete`;
        await axios.post(url, {}, { headers: { Authorization: `Bearer ${this.token}` } });
        job.status = "done";
        await this.fetchStats();
      } catch (e) {
        console.error(e);
      }
    },
    goToDetail(job) {
      // Ajusta a tu ruta real de detalle
      this.$router.push({ path: "/ServicePanel", query: { job: job.id } });
    },
    fmt(n) { return new Intl.NumberFormat().format(n ?? 0); },
    currency(n) { return new Intl.NumberFormat(undefined, { style: "currency", currency: "EUR" }).format(n ?? 0); },
    formatDate(iso) { return iso ? new Date(iso).toLocaleString() : ""; },
  },
};
</script>
