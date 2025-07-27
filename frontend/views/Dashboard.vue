<template>
  <div class="dashboard-container">
    <!-- Topbar -->
    <header class="dashboard-header">
      <div class="logo">
        <h1>TapClic</h1>
      </div>

      <!-- Botón notificaciones -->
      <button class="notif-btn" @click="toggleNotifications">
        🔔 <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
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
      <router-view v-else />
    </main>

    <!-- ✅ PANEL LATERAL DE NOTIFICACIONES -->
    <transition name="slide">
      <aside v-if="showNotifications" class="notifications-panel">
        <header>
          <h3>Notificaciones</h3>
          <button @click="toggleNotifications">✖</button>
        </header>
        <div class="notifications-list">
          <div 
            v-for="notif in notifications" 
            :key="notif.id" 
            class="notif-item"
            :class="{ unread: !notif.read }"
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

export default {
  name: "Dashboard",
  data() {
    return {
      showMenu: false,
      role: null,
      showNotifications: false,
      notifications: [],
      ws: null // conexión WebSocket
    };
  },

  computed: {
    unreadCount() {
      return this.notifications.filter(n => !n.read).length;
    }
  },

  mounted() {
    this.loadRole();
    this.fetchNotifications();   // ✅ Carga inicial desde API REST
    this.initWebSocket();        // ✅ Conexión en tiempo real

    // ✅ Si el rol cambia dinámicamente en Pinia, actualizamos el dashboard
    const authStore = useAuthStore();
    this.$watch(
      () => authStore.role,
      (newRole) => {
        if (newRole && newRole !== this.role) {
          console.log("ROL ACTUALIZADO EN TIEMPO REAL:", newRole);
          this.role = newRole;
        }
      }
    );
  },

  beforeUnmount() {
    if (this.ws) this.ws.close(); // cerrar conexión cuando salga
  },

  methods: {
    /** ✅ Obtiene el rol desde Pinia/localStorage */
    loadRole() {
      const authStore = useAuthStore();
      if (authStore.role) {
        this.role = authStore.role;
        return;
      }

      const savedRole = localStorage.getItem("role");
      const savedToken = localStorage.getItem("token");
      const savedUser = localStorage.getItem("user");

      if (savedRole && savedToken) {
        this.role = savedRole;
        authStore.login({
          user: savedUser ? JSON.parse(savedUser) : {},
          token: savedToken,
          role: savedRole
        });
      } else {
        authStore.logout();
        this.$router.push("/login");
      }
    },

    /** ✅ Abre/cierra el panel de notificaciones */
    toggleNotifications() {
      this.showNotifications = !this.showNotifications;
    },

    /** ✅ Llama a la API REST para obtener notificaciones previas */
    async fetchNotifications() {
      try {
        const token = localStorage.getItem("token");
        const res = await axios.get("https://TU_API.com/api/notifications", {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.notifications = res.data;
      } catch (error) {
        console.error("Error cargando notificaciones:", error);
      }
    },

    /** ✅ Conexión WebSocket para recibir nuevas notificaciones */
    initWebSocket() {
      const token = localStorage.getItem("token");
      if (!token) return;

      // 🔗 Conéctate a tu servidor WebSocket
      this.ws = new WebSocket(`wss://TU_API.com/ws/notifications?token=${token}`);

      this.ws.onopen = () => console.log("✅ WebSocket conectado");
      this.ws.onclose = () => console.log("❌ WebSocket desconectado");
      this.ws.onerror = (e) => console.error("Error WebSocket", e);

      // Cuando llega una nueva notificación
      this.ws.onmessage = (event) => {
        try {
          const notif = JSON.parse(event.data);
          console.log("📩 Nueva notificación:", notif);
          this.notifications.unshift(notif); // agregar al inicio
        } catch (err) {
          console.error("Error procesando mensaje WS:", err);
        }
      };
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
      return new Date(date).toLocaleString();
    }
  }
};
</script>


<style scoped>
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
  background: red;
  color: white;
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
  padding: 1rem;
  display: flex;
  flex-direction: column;
}

.mobile-menu a {
  color: white;
  padding: 12px;
  text-decoration: none;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

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
    display: none;
  }
  .menu-toggle {
    display: block;
  }
}
</style>
