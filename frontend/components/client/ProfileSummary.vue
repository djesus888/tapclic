<!-- src/components/client/ProfileSummary.vue -->
<template>
  <section class="bg-white p-4 rounded-2xl shadow">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Mi perfil</h2>

    <div class="flex items-center space-x-4 mb-4">
      <img
        :src="user?.avatar_url || defaultAvatar"
        alt="Avatar"
        class="w-16 h-16 rounded-full object-cover"
      />
      <div>
        <p class="font-semibold text-lg">{{ user?.name }}</p>
        <p class="text-sm text-gray-500">{{ user?.email }}</p>
      </div>
    </div>

    <div class="text-sm text-gray-700 space-y-1">
      <p><strong>Dirección:</strong> {{ user?.business_address || 'No definida' }}</p>
      <p><strong>Preferencias:</strong> {{ user?.preferences || 'Ninguna' }}</p>
    </div>

    <div class="mt-4">
      <button
        @click="goToEdit"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        Editar perfil
      </button>
    </div>
  </section>
</template>

<script>
import { useAuthStore } from '@/stores/authStore'

export default {
  data() {
    return {
      user: null,
      defaultAvatar: '/img/default-avatar.png'
    }
  },
  async created() {
    const store = useAuthStore()
    this.user = store.user
    await this.fetchProfile()
  },
  methods: {
    async fetchProfile() {
      try {
        const res = await fetch(`${import.meta.env.VITE_API_URL}/user/profile`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        })
        const data = await res.json()
        if (data.user) {
          this.user = data.user
        }
      } catch (err) {
        console.error('Error al cargar perfil:', err)
      }
    },
    goToEdit() {
      this.$router.push('/profile/edit')
    }
  }
}
</script>
