<template>
  <div class="p-6 space-y-10 max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Configuración</h1>

    <!-- Perfil del Usuario -->
    <section class="space-y-4">
      <h2 class="text-xl font-semibold">Perfil</h2>
      <input v-model="form.name" placeholder="Nombre completo" class="input" />
      <input v-model="form.email" placeholder="Correo" type="email" class="input" />
      <input v-model="form.phone" placeholder="Teléfono" class="input" />
      <div class="flex items-center space-x-4">
        <img :src="form.avatar_url" class="w-16 h-16 rounded-full" v-if="form.avatar_url" />
        <input type="file" @change="onAvatarChange" class="file-input" />
      </div>
      <button @click="updateProfile" class="btn-primary">Actualizar Perfil</button>
    </section>

    <!-- Cambiar Contraseña -->
    <section class="space-y-4">
      <h2 class="text-xl font-semibold">Cambiar Contraseña</h2>
      <input v-model="passwords.current" type="password" placeholder="Contraseña actual" class="input" />
      <input v-model="passwords.new" type="password" placeholder="Nueva contraseña" class="input" />
      <input v-model="passwords.confirm" type="password" placeholder="Confirmar nueva contraseña" class="input" />
      <button @click="changePassword" class="btn-secondary">Cambiar Contraseña</button>
    </section>

    <!-- Configuración de proveedor/cliente -->
    <section class="space-y-4" v-if="isProvider">
      <h2 class="text-xl font-semibold">Datos del Proveedor</h2>
      <input v-model="provider.business_address" placeholder="Dirección del negocio" class="input" />
      <input v-model="provider.service_categories" placeholder="Categorías (coma separadas)" class="input" />
      <input v-model="provider.coverage_area" placeholder="Zona de cobertura" class="input" />
      <button @click="updateProviderData" class="btn-primary">Guardar Datos</button>
    </section>

    <!-- Configuración del Sistema (Admin) -->
    <section class="space-y-4" v-if="isAdmin">
      <h2 class="text-xl font-semibold">Configuración del Sistema</h2>
      <input v-model="system.hostname" placeholder="Hostname" class="input" />
      <select v-model="system.system_online" class="input">
        <option :value="1">🟢 En línea</option>
        <option :value="0">🔴 Mantenimiento</option>
      </select>
      <input v-model="system.api_key" placeholder="API Key" class="input" />
      <input v-model="system.timezone" placeholder="Zona horaria" class="input" />
      <input v-model="system.version" placeholder="Versión" class="input" />
      <button @click="updateSystemConfig" class="btn-danger">Actualizar Configuración</button>
    </section>

    <!-- Notificaciones y preferencias -->
    <section class="space-y-4">
      <h2 class="text-xl font-semibold">Preferencias</h2>
      <label class="flex items-center">
        <input type="checkbox" v-model="preferences.notifications" class="mr-2" />
        Recibir notificaciones
      </label>
      <label class="flex items-center">
        <input type="checkbox" v-model="preferences.dark_mode" class="mr-2" />
        Activar modo oscuro
      </label>
      <button @click="updatePreferences" class="btn-secondary">Guardar Preferencias</button>
    </section>
  </div>
</template>

<script>
import { useAuthStore } from "@/stores/auth";
import Swal from "sweetalert2";

export default {
  data() {
    return {
      form: {
        name: "",
        email: "",
        phone: "",
        avatar_url: ""
      },
      passwords: {
        current: "",
        new: "",
        confirm: ""
      },
      provider: {
        business_address: "",
        service_categories: "",
        coverage_area: ""
      },
      preferences: {
        notifications: true,
        dark_mode: false
      },
      system: {
        hostname: "",
        system_online: 1,
        api_key: "",
        timezone: "UTC",
        version: "1.0.0"
      },
      user: null
    };
  },
  computed: {
    isAdmin() {
      return this.user?.role === "admin";
    },
    isProvider() {
      return this.user?.role === "driver";
    }
  },
  async created() {
    const auth = useAuthStore();
    this.user = auth.user;
    this.loadInitialData();
  },
  methods: {
    async loadInitialData() {
      if (this.user) {
        this.form = {
          name: this.user.name,
          email: this.user.email,
          phone: this.user.phone || "",
          avatar_url: this.user.avatar_url || ""
        };
        this.preferences = this.user.preferences || this.preferences;

        if (this.isProvider) {
          Object.assign(this.provider, {
            business_address: this.user.business_address || "",
            service_categories: this.user.service_categories || "",
            coverage_area: this.user.coverage_area || ""
          });
        }

        if (this.isAdmin) {
          const res = await fetch("/api/admin/settings");
          const data = await res.json();
          if (data.status === "success") {
            this.system = data.data;
          }
        }
      }
    },
    async updateProfile() {
      const res = await fetch("/api/user/update", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(this.form)
      });
      const data = await res.json();
      Swal.fire(data.message);
    },
    async changePassword() {
      if (this.passwords.new !== this.passwords.confirm) {
        return Swal.fire("Las contraseñas no coinciden");
      }
      const res = await fetch("/api/user/change-password", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(this.passwords)
      });
      const data = await res.json();
      Swal.fire(data.message);
    },
    async updatePreferences() {
      const res = await fetch("/api/user/preferences", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(this.preferences)
      });
      const data = await res.json();
      Swal.fire(data.message);
    },
    async updateProviderData() {
      const res = await fetch("/api/user/provider-data", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(this.provider)
      });
      const data = await res.json();
      Swal.fire(data.message);
    },
    async updateSystemConfig() {
      const res = await fetch("/api/admin/settings", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(this.system)
      });
      const data = await res.json();
      Swal.fire(data.message);
    },
    async onAvatarChange(e) {
      const file = e.target.files[0];
      const formData = new FormData();
      formData.append("avatar", file);
      const res = await fetch("/api/user/avatar", {
        method: "POST",
        body: formData
      });
      const data = await res.json();
      if (data.status === "success") {
        this.form.avatar_url = data.url;
      }
      Swal.fire(data.message);
    }
  }
};
</script>

<style scoped>
.input {
  @apply border px-3 py-2 rounded w-full;
}
.file-input {
  @apply border p-2 rounded;
}
.btn-primary {
  @apply bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700;
}
.btn-secondary {
  @apply bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700;
}
.btn-danger {
  @apply bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700;
}
</style>
