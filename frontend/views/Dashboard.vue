<template>
  <div class="dashboard-container">
    <!-- Topbar -->
    <header class="dashboard-header">
      <div class="logo">
        <h1>TapClic</h1>
      </div><!-- Botón notificaciones mejorado -->
  <button class="notif-btn modern-icon" @click="toggleNotifications">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon-bell" viewBox="0 0 24 24" fill="currentColor">
      <path
        d="M12 2C10.3 2 9 3.3 9 5V5.3C6.7 6.2 5 8.4 5 11V17L3 19V20H21V19L19 17V11C19 8.4 17.3 6.2 15 5.3V5C15 3.3 13.7 2 12 2ZM12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22Z"
      />
    </svg>
    <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
  </button>

  <!-- Botón menú móvil -->
  <button class="menu-toggle" @click="toggleMenu">☰</button>

  <!-- Menú Desktop -->
  <nav class="nav-links" v-if="role">
    <!-- Menú según el rol -->
    <template v-if="role === 'admin'">
      <router-link to="/Profile">Perfil</router-link>
      <router-link to="/admin">Panel Admin</router-link>
      <router-link to="/Settings">Configuración</router-link>
      <router-link to="/Users">Usuarios</router-link>
    </template>

    <template v-else-if="role === 'user'">
      <router-link to="/Profile">Perfil</router-link>
      <router-link to="/Wallet">Billetera</router-link>
      <router-link to="/Services">Servicios</router-link>
      <router-link to="/Orders">Pedidos</router-link>
      <router-link to="/History">Historial</router-link>
    </template>

    <template v-else-if="role === 'driver'">
      <router-link to="/Profile">Perfil</router-link>
      <router-link to="/Settings">Configuración</router-link>
      <router-link to="/ServicePanel">Panel de Servicios</router-link>
      <router-link to="/ReceivedOrders">Pedidos Recibidos</router-link>
      <router-link to="/ServiceStats">Estadísticas</router-link>
    </template>

    <!-- Botón logout -->
    <button @click="logout" class="logout-btn">Cerrar sesión</button>
  </nav>
</header>

<!-- Menú móvil -->
<div class="mobile-menu" v-if="showMenu && role">
  <template v-if="role === 'admin'">
    <router-link @click="closeMenu" to="/Profile">Perfil</router-link>
    <router-link @click="closeMenu" to="/admin">Panel Admin</router-link>
    <router-link @click="closeMenu" to="/Settings">Configuración</router-link>
    <router-link @click="closeMenu" to="/Users">Usuarios</router-link>
  </template>

  <template v-else-if="role === 'user'">
    <router-link @click="closeMenu" to="/Profile">Perfil</router-link>
    <router-link @click="closeMenu" to="/Wallet">Billetera</router-link>
    <router-link @click="closeMenu" to="/Services">Servicios</router-link>
    <router-link @click="closeMenu" to="/Orders">Pedidos</router-link>
    <router-link @click="closeMenu" to="/History">Historial</router-link>
  </template>

  <template v-else-if="role === 'driver'">
    <router-link @click="closeMenu" to="/Profile">Perfil</router-link>
    <router-link @click="closeMenu" to="/Settings">Configuración</router-link>
    <router-link @click="closeMenu" to="/ServicePanel">Panel de Servicios</router-link>
    <router-link @click="closeMenu" to="/ReceivedOrders">Pedidos Recibidos</router-link>
    <router-link @click="closeMenu" to="/ServiceStats">Estadísticas</router-link>
  </template>

  <button @click="logout" class="logout-btn">Cerrar sesión</button>
</div>

<!-- Contenido dinámico -->
<main class="dashboard-content">
  <div v-if="!role" class="loading-msg">Cargando menú...</div>
  <component v-else :is="activePanelComponent" />
</main>

<!-- ✅ PANEL LATERAL DE NOTIFICACIONES mejorado -->
<transition name="slide">
  <aside v-if="showNotifications" class="notifications-panel improved">
    <header class="notif-header">
      <h3>Notificaciones</h3>
      <button class="close-btn" @click="toggleNotifications">✕</button>
    </header>
    <div class="notifications-list">
      <div
        v-for="notif in notifications"
        :key="notif.id"
        class="notif-item"
        :class="{ unread: !notif.read }"
        @click="markAsRead(notif)"
      >
        <p>{{ notif.message }}</p>
        <small>{{ formatDate(notif.date) }}</small>
      </div>
    </div>
  </aside>
</transition>

  </div>
</template>



<script>
import { useAuthStore } from "@/stores/auth";
import axios from "axios";
import { io } from "socket.io-client";

// Componentes de panel por rol
import AdminPanel from "@/components/panels/AdminPanel.vue";
import ClientPanel from "@/components/client/ClientDashboard.vue";
import DriverPanel from "@/components/panels/DriverPanel.vue";

// Escucha global para promesas no manejadas
window.addEventListener("unhandledrejection", (e) => {
  console.error("🔥 PROMESA NO MANEJADA:", e.reason);

  if (e.reason && e.reason.stack) {
    console.log("🔍 Stack:", e.reason.stack);
  }

  alert("❌ Ocurrió un error inesperado:\n" + (e.reason?.message || "Error desconocido"));
});

export default {
  name: "Dashboard",
  components: {
    AdminPanel,
    ClientPanel,
    DriverPanel
  },
  data() {
    return {
      showMenu: false,
      role: null,
      showNotifications: false,
      notifications: [],
      ws: null
    };
  },
  computed: {
    unreadCount() {
      return Array.isArray(this.notifications)
        ? this.notifications.filter(n => !n.is_read).length
        : 0;
    },
    activePanelComponent() {
      switch (this.role) {
        case "admin":
          return "AdminPanel";
        case "user":
          return "ClientPanel";
        case "driver":
          return "DriverPanel";
        default:
          return null;
      }
    }
  },
  mounted() {
    this.loadRole();
    this.fetchNotifications();
    this.initWebSocket();

    const authStore = useAuthStore();
    this.$watch(
      () => authStore.role,
      newRole => {
        if (newRole && newRole !== this.role) {
          console.log("ROL ACTUALIZADO EN TIEMPO REAL:", newRole);
          this.role = newRole;
        }
      }
    );
  },
  beforeUnmount() {
    if (this.ws) this.ws.close();
  },
  methods: {
    cleanUser(user) {
      if (!user || typeof user !== "object") return {};
      const { id, name, email, role, phone } = user;
      return { id, name, email, role, phone };
    },
    loadRole() {
      const authStore = useAuthStore();

      if (authStore.role) {
        this.role = authStore.role;
        return;
      }

      const savedRole = localStorage.getItem("role");
      const savedToken = localStorage.getItem("token");
      const savedUserRaw = localStorage.getItem("user");

      if (savedRole && savedToken && savedUserRaw) {
        const savedUser = JSON.parse(savedUserRaw);
        const clean = this.cleanUser(savedUser);

        this.role = savedRole;
        authStore.login({
          user: clean,
          token: savedToken,
          role: savedRole
        });

        localStorage.setItem("user", JSON.stringify(clean));
        localStorage.setItem("role", savedRole);
      } else {
        authStore.logout();
        this.$router.push("/login");
      }
    },
    toggleNotifications() {
      this.showNotifications = !this.showNotifications;
    },
    async fetchNotifications() {
      try {
        const token = localStorage.getItem("token");
        const savedUserRaw = localStorage.getItem("user");
        const savedUser = savedUserRaw ? JSON.parse(savedUserRaw) : {};
        const userId = savedUser?.id;

        if (!userId) {
          console.error("❌ No se encontró el userId en localStorage");
          return;
        }

        const API_BASE = "http://localhost:8000";
        const url = `${API_BASE}/backend/api/notifications/${userId}`;

        console.log("📡 Solicitando notificaciones de:", url);

        const res = await axios.get(url, {
          headers: { Authorization: `Bearer ${token}` }
        });

        this.notifications = Array.isArray(res.data.data)
          ? res.data.data
          : [];

      } catch (error) {
        console.error("Error cargando notificaciones:", error);
      }
    },
    initWebSocket() {
      const token = localStorage.getItem("token");
      if (!token) return;

      this.ws = io("ws://localhost:4000", {
        query: { token }
      });

      this.ws.on("connect", () => {
        console.log("✅ WebSocket conectado");
      });

      this.ws.on("disconnect", () => {
        console.log("❌ WebSocket desconectado");
      });

      this.ws.on("connect_error", error => {
        console.error("❌ Error WebSocket:", error.message);
      });

      this.ws.on("new_notification", notif => {
        console.log("📩 Notificación recibida:", notif);
        this.notifications.unshift(notif);
      });
    },
    async markAsRead(notification) {
      if (notification.is_read) return;

      const token = localStorage.getItem("token");
      const API_BASE = "http://localhost:8000";
      const url = `${API_BASE}/backend/api/notifications/read/${notification.id}`;

      try {
        await axios.put(url, {}, {
          headers: { Authorization: `Bearer ${token}` }
        });

        const index = this.notifications.findIndex(n => n.id === notification.id);
        if (index !== -1) {
          this.notifications[index].is_read = true;
        }

        console.log(`📬 Notificación ${notification.id} marcada como leída`);
      } catch (err) {
        console.error("Error al marcar como leída:", err);
      }
    },
    toggleMenu() {
      this.showMenu = !this.showMenu;
    },
    closeMenu() {
      this.showMenu = false;
    },
    logout() {
      const authStore = useAuthStore();
      authStore.logout();
      localStorage.removeItem("role");
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      this.$router.push("/login");
    },
    formatDate(date) {
      if (!date) return "";
      return new Date(date.replace(" ", "T")).toLocaleString();
    }
  }
};
</script>



<style scoped>
/* Mejora de icono campana */
.notif-btn {
  position: relative;
  background: transparent;
  border: none;
  font-size: 22px;
  cursor: pointer;
}

.icon-bell {
  width: 26px;
  height: 26px;
  color: #333;
  transition: transform 0.2s;
}

.notif-btn:hover .icon-bell {
  transform: scale(1.2);
}

.badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: red;
  color: white;
  font-size: 10px;
  border-radius: 50%;
  padding: 2px 6px;
}

/* Panel de notificaciones */
.notifications-panel.improved {
  position: fixed;
  top: 0;
  right: 0;
  width: 300px;
  max-height: 100%;
  background: #fff;
  border-left: 1px solid #ddd;
  box-shadow: -4px 0 12px rgba(0, 0, 0, 0.15);
  overflow-y: auto;
  z-index: 9999;
  padding: 0;
}

.notif-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background: #f5f5f5;
  border-bottom: 1px solid #ddd;
}

.close-btn {
  background: transparent;
  border: none;
  font-size: 18px;
  cursor: pointer;
  color: #888;
}

.close-btn:hover {
  color: #333;
}

.notifications-list {
  padding: 12px 16px;
}

.notif-item {
  background: #fafafa;
  padding: 10px 12px;
  margin-bottom: 8px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s;
}

.notif-item:hover {
  background: #eee;
}

.notif-item.unread {
  font-weight: bold;
  background: #e3f2fd;
}





.notifications-panel {
  position: fixed;
  top: 0;
  right: 0;
  width: 300px;
  height: 100vh;
  background: #fff;
  border-left: 2px solid #ddd;
  box-shadow: -2px 0 6px rgba(0,0,0,0.1);
  z-index: 2000;
  padding: 10px;
  display: flex;
  flex-direction: column;
}

.notifications-panel header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notifications-list {
  flex: 1;
  overflow-y: auto;
}

.notif-item {
  padding: 8px;
  border-bottom: 1px solid #eee;
}
.notif-item.unread {
  background: #f0f8ff;
}

.badge {
  background: red;                                      color: white;
  border-radius: 50%;
  padding: 2px 6px;
  font-size: 12px;
}

.slide-enter-active, .slide-leave-active {
  transition: transform 0.3s ease;
}
.slide-enter-from, .slide-leave-to {
  transform: translateX(100%);
}
/* --- Estilos base --- */
.dashboard-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: #f4f6f9;
}
                                                      /* Header */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #1e1e2f;
  color: white;
  padding: 1rem;
}

.logo h1 {
  font-size: 1.4rem;
  font-weight: bold;
}

/* Botón menú móvil */
.menu-toggle {
  background: none;
  border: none;
  font-size: 1.8rem;
  color: white;
  cursor: pointer;
  display: none;
}

/* Menú desktop */
.nav-links {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.nav-links a {
  text-decoration: none;
  color: white;
  padding: 8px 12px;
  border-radius: 6px;
  transition: background 0.2s ease;
}

.nav-links a:hover {
  background: rgba(255, 255, 255, 0.2);
}

.logout-btn {
  background: #ff4d4d;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
}

/* Menú móvil */
.mobile-menu {
  background: #1e1e2f;
  padding: 1rem;                                        display: flex;
  flex-direction: column;
}

.mobile-menu a {
  color: white;
  padding: 12px;
  text-decoration: none;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);  }

.mobile-menu a:last-child {
  border-bottom: none;
}

/* Loader simple */
.loading-msg {
  text-align: center;
  padding: 2rem;
  font-size: 1.2rem;
  color: #555;
}

/* Contenido dinámico */
.dashboard-content {
  flex: 1;
  padding: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
  .nav-links {
    display: none;                                      }
  .menu-toggle {                                          display: block;
  }                                                   }
</style>
