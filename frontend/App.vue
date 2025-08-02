<template>
  <div id="app">
    <!-- 🌐 Vista de la ruta actual -->
    <Suspense>
      <router-view />
      <template #fallback>
        <div class="text-center py-10 text-gray-500">Cargando...</div>
      </template>
    </Suspense>
  </div>
</template>

<script>
import { onMounted } from 'vue'
import { useAuthStore } from './stores/authStore'

export default {
  name: 'App',
  setup() {
    const auth = useAuthStore()

    onMounted(() => {
      // 🔐 Por seguridad, asegúrate que la sesión está restaurada
      if (!auth.user || !auth.token || !auth.role) {
        auth.initializeAuth()
      }

      console.log("✅ App.vue montado con rol:", auth.role)
    })
  }
}
</script>

<style>
body {
  margin: 0;
  font-family: Arial, sans-serif;
}
</style>
