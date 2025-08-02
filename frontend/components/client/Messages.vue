<!-- src/components/client/Messages.vue -->
<template>
  <section class="bg-white p-4 rounded-2xl shadow h-[400px] flex flex-col">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Chat con proveedor</h2>

    <div class="flex-1 overflow-y-auto space-y-2 mb-4 bg-gray-50 p-2 rounded-xl" ref="chatBox">
      <div
        v-for="msg in messages"
        :key="msg.id"
        class="max-w-[80%] px-3 py-2 rounded-lg text-sm"
        :class="msg.from_id === user.id
          ? 'bg-blue-500 text-white self-end'
          : 'bg-gray-200 text-gray-900 self-start'"
      >
        <p>{{ msg.message }}</p>
        <p class="text-xs text-gray-300 mt-1 text-right">{{ formatTime(msg.created_at) }}</p>
      </div>
    </div>

    <div class="flex items-center">
      <input
        v-model="newMessage"
        type="text"
        placeholder="Escribe un mensaje..."
        class="flex-1 p-2 border border-gray-300 rounded-l-lg"
        @keyup.enter="sendMessage"
      />
      <button
        @click="sendMessage"
        class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700"
      >
        Enviar
      </button>
    </div>
  </section>
</template>

<script>
import { useAuthStore } from '@/stores/authStore'

export default {
  props: {
    providerId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      messages: [],
      newMessage: '',
      intervalId: null,
      user: null
    }
  },
  async created() {
    const auth = useAuthStore()
    this.user = auth.user
    await this.fetchMessages()
    this.intervalId = setInterval(this.fetchMessages, 3000) // Actualiza cada 3s
  },
  unmounted() {
    clearInterval(this.intervalId)
  },
  methods: {
    async fetchMessages() {
      try {
        const token = localStorage.getItem('token')
        const res = await fetch(`${import.meta.env.VITE_API_URL}/messages/${this.providerId}`, {
          headers: { Authorization: `Bearer ${token}` }
        })
        const data = await res.json()
        this.messages = data.messages
        this.$nextTick(() => {
          this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight
        })
      } catch (err) {
        console.error('Error cargando mensajes:', err)
      }
    },
    async sendMessage() {
      if (!this.newMessage.trim()) return

      const messageToSend = this.newMessage
      this.newMessage = ''

      try {
        const token = localStorage.getItem('token')
        const res = await fetch(`${import.meta.env.VITE_API_URL}/messages/send`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`
          },
          body: JSON.stringify({
            to_id: this.providerId,
            message: messageToSend
          })
        })
        const data = await res.json()
        if (data.success) {
          this.fetchMessages()
        }
      } catch (err) {
        console.error('Error al enviar mensaje:', err)
      }
    },
    formatTime(timestamp) {
      return new Date(timestamp).toLocaleTimeString()
    }
  }
}
</script>
