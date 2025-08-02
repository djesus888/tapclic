<template>
  <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
    <!-- Encabezado -->
    <h1 class="text-2xl font-bold text-gray-800">Bienvenido, {{ user?.name || 'Cliente' }}</h1>

    <!-- Vista resumen del perfil -->
    <ProfileSummary />

    <!-- Sección de servicios disponibles -->
    <section>
      <h2 class="text-xl font-semibold mb-2">Servicios disponibles</h2>
      <ServiceCards />
    </section>

    <!-- Solicitudes actuales -->
    <section>
      <h2 class="text-xl font-semibold mb-2">Mis solicitudes</h2>
      <MyRequests />
    </section>

    <!-- Calificación de servicios -->
    <section>
      <h2 class="text-xl font-semibold mb-2">Califica tus servicios</h2>
      <RateService />
    </section>

    <!-- Chat con proveedores -->
    <section>
      <h2 class="text-xl font-semibold mb-2">Mensajes</h2>
      <Messages />
    </section>

    <!-- Soporte técnico -->
    <section>
      <h2 class="text-xl font-semibold mb-2">Soporte</h2>
      <SupportTickets />
    </section>

    <!-- Botón flotante de notificaciones -->
    <button
      @click="showNotifications = true"
      class="fixed bottom-6 right-6 bg-yellow-500 text-white p-3 rounded-full shadow-lg hover:bg-yellow-600"
    >
      🔔
    </button>

    <!-- Panel lateral de notificaciones -->
    <transition name="slide">
      <NotificationsPanel v-if="showNotifications" @close="showNotifications = false" />
    </transition>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth'

import ServiceCards from '@/components/shared/ServiceCards.vue'
import MyRequests from '@/components/client/MyRequests.vue'
import ProfileSummary from '@/components/client/ProfileSummary.vue'
import Messages from '@/components/client/Messages.vue'
import SupportTickets from '@/components/client/SupportTickets.vue'
import RateService from '@/components/client/RateService.vue'
import NotificationsPanel from '@/components/client/NotificationsPanel.vue'

export default {
  components: {
    ServiceCards,
    MyRequests,
    ProfileSummary,
    Messages,
    SupportTickets,
    RateService,
    NotificationsPanel
  },
  data() {
    return {
      showNotifications: false,
      user: null
    }
  },
  created() {
    const store = useAuthStore()
    this.user = store.user
  }
}
</script>

<style>
/* Animación del panel lateral */
.slide-enter-active, .slide-leave-active {
  transition: all 0.3s ease;
}
.slide-enter-from {
  transform: translateX(100%);
}
.slide-leave-to {
  transform: translateX(100%);
}
</style>
