<template>
  <section class="space-y-6">
    <header class="flex items-end justify-between">
      <div>
        <h2 class="text-xl font-semibold tracking-tight">Panel administrativo</h2>
        <p class="text-sm text-gray-600">Rendimiento general y accesos rápidos.</p>
      </div>
      <div class="text-xs text-gray-500">Actualizado: {{ lastUpdated || '—' }}</div>
    </header>

    <!-- Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <StatCard title="Usuarios totales" :value="fmt(stats.users)" :hint="'Nuevos hoy: ' + fmt(stats.usersToday)" :trend="trendText(stats.usersGrowth)" />
      <StatCard title="Pedidos hoy" :value="fmt(stats.ordersToday)" :hint="'En curso: ' + fmt(stats.ordersInProgress)" :trend="trendText(stats.ordersGrowth)" />
      <StatCard title="Ingresos hoy" :value="currency(stats.revenueToday)" :hint="'Mes actual: ' + currency(stats.revenueMonth)" :trend="trendText(stats.revenueGrowth)" />
      <StatCard title="Uptime" :value="uptimeValue" :hint="uptimeHint" />
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
      <SimpleLineChart title="Pedidos últimos 7 días" :labels="chart.labels" :data="chart.orders7d" />
      <SimpleLineChart title="Ingresos últimos 7 días" :labels="chart.labels" :data="chart.revenue7d" />
    </div>

    <!-- Accesos rápidos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <button class="rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm hover:shadow transition" @click="$router.push('/Users')">
        <div class="text-sm text-gray-500">Gestión</div>
        <div class="mt-1 text-lg font-semibold">Usuarios</div>
        <p class="mt-2 text-sm text-gray-600">Crear, editar y desactivar cuentas.</p>
      </button>

      <button class="rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm hover:shadow transition" @click="$router.push('/Settings')">
        <div class="text-sm text-gray-500">Sistema</div>
        <div class="mt-1 text-lg font-semibold">Configuración</div>
        <p class="mt-2 text-sm text-gray-600">Parámetros generales y seguridad.</p>
      </button>

      <button class="rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm hover:shadow transition" @click="$router.push('/Content')">
        <div class="text-sm text-gray-500">Contenido</div>
        <div class="mt-1 text-lg font-semibold">Catálogo</div>
        <p class="mt-2 text-sm text-gray-600">Servicios, publicaciones y recursos.</p>
      </button>

      <button class="rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm hover:shadow transition" @click="$router.push('/Activity')">
        <div class="text-sm text-gray-500">Auditoría</div>
        <div class="mt-1 text-lg font-semibold">Registros</div>
        <p class="mt-2 text-sm text-gray-600">Acciones y errores del sistema.</p>
      </button>
    </div>

    <!-- Registros recientes -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
      <div class="border-b border-gray-200 px-4 py-3">
        <h3 class="text-sm font-semibold">Actividad reciente</h3>
      </div>
      <ul class="divide-y divide-gray-100 max-h-[320px] overflow-auto">
        <li v-for="log in activity" :key="log.id" class="px-4 py-3 text-sm flex items-start gap-3">
          <span class="mt-1 h-2.5 w-2.5 rounded-full"
                :class="log.level === 'error' ? 'bg-red-500' : log.level === 'warn' ? 'bg-yellow-500' : 'bg-green-500'"></span>
          <div class="flex-1">
            <div class="flex items-center justify-between">
              <span class="font-medium">{{ log.action }}</span>
              <span class="text-xs text-gray-500">{{ formatDate(log.created_at) }}</span>
            </div>
            <div class="text-gray-600">{{ log.meta || '—' }}</div>
          </div>
        </li>
        <li v-if="!activity.length" class="px-4 py-6 text-center text-sm text-gray-500">Sin registros por ahora.</li>
      </ul>
    </div>
  </section>
</template>

<script>
import axios from "axios";
import StatCard from "@/components/shared/StatCard.vue";
import SimpleLineChart from "@/components/charts/SimpleLineChart.vue";

export default {
  name: "AdminPanel",
  components: { StatCard, SimpleLineChart },
  data() {
    return {
      stats: { users: 0, usersToday: 0, usersGrowth: 0, ordersToday: 0, ordersInProgress: 0, ordersGrowth: 0, revenueToday: 0, revenueMonth: 0, revenueGrowth: 0, uptimePct: 99.98 },
      chart: { labels: [], orders7d: [], revenue7d: [] },
      activity: [],
      lastUpdated: null,
      API_BASE: import.meta.env.VITE_API_BASE_URL || "http://localhost:8000",
      loading: false,
      token: localStorage.getItem("token"),
    };
  },
  computed: {
    uptimeValue() { return `${this.stats.uptimePct?.toFixed(2)}%`; },
    uptimeHint()  { return "Disponibilidad mensual"; },
  },
  async mounted() {
    await this.refresh();
  },
  methods: {
    async refresh() {
      this.loading = true;
      try {
        await Promise.all([this.fetchStats(), this.fetchCharts(), this.fetchActivity()]);
        this.lastUpdated = new Date().toLocaleString();
      } catch (e) {
        console.error("AdminPanel refresh error:", e);
      } finally {
        this.loading = false;
      }
    },
    async fetchStats() {
      try {
        const url = `${this.API_BASE}/backend/api/admin/stats`;
        const { data } = await axios.get(url, { headers: { Authorization: `Bearer ${this.token}` } });
        // Espera estructura { data: { ... } }
        if (data?.data) this.stats = { ...this.stats, ...data.data };
      } catch {
        // fallback demo cuando el endpoint no está
        this.stats = { users: 1520, usersToday: 7, usersGrowth: +3.2, ordersToday: 42, ordersInProgress: 9, ordersGrowth: +4.7, revenueToday: 860.5, revenueMonth: 12890.2, revenueGrowth: +5.1, uptimePct: 99.98 };
      }
    },
    async fetchCharts() {
      try {
        const url = `${this.API_BASE}/backend/api/admin/metrics7d`;
        const { data } = await axios.get(url, { headers: { Authorization: `Bearer ${this.token}` } });
        if (data?.data) {
          this.chart.labels = data.data.labels || [];
          this.chart.orders7d = data.data.orders7d || [];
          this.chart.revenue7d = data.data.revenue7d || [];
          return;
        }
        throw new Error("No data");
      } catch {
        // Datos demo
        this.chart.labels = ["Lun","Mar","Mié","Jue","Vie","Sáb","Dom"];
        this.chart.orders7d = [5, 8, 7, 12, 15, 11, 9];
        this.chart.revenue7d = [120, 200, 180, 320, 450, 380, 300];
      }
    },
    async fetchActivity() {
      try {
        const url = `${this.API_BASE}/backend/api/admin/activity`;
        const { data } = await axios.get(url, { headers: { Authorization: `Bearer ${this.token}` } });
        this.activity = Array.isArray(data?.data) ? data.data : [];
      } catch {
        // Demo logs
        this.activity = [
          { id: 1, level: "info", action: "Usuario creado", meta: "user: #1023 (cliente)", created_at: new Date().toISOString() },
          { id: 2, level: "warn", action: "Pedido con retraso", meta: "order: #553", created_at: new Date(Date.now()-3600e3).toISOString() },
          { id: 3, level: "error", action: "Fallo notificaciones", meta: "socket timeout", created_at: new Date(Date.now()-2*3600e3).toISOString() },
        ];
      }
    },
    trendText(n) {
      if (typeof n !== "number") return "";
      const sign = n >= 0 ? "+" : "";
      return `${sign}${n}%`;
    },
    fmt(n) { return new Intl.NumberFormat().format(n ?? 0); },
    currency(n) { return new Intl.NumberFormat(undefined, { style: "currency", currency: "EUR" }).format(n ?? 0); },
    formatDate(iso) {
      return iso ? new Date(iso.replace(" ", "T")).toLocaleString() : "";
    },
  },
};
</script>
