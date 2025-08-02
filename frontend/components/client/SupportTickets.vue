<template>
  <section class="bg-white p-6 rounded-2xl shadow">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Centro de soporte</h2>

    <!-- Formulario -->
    <div class="mb-6">
      <input
        v-model="newTicket.title"
        type="text"
        class="w-full p-2 border rounded mb-2"
        placeholder="Título del problema"
      />
      <textarea
        v-model="newTicket.message"
        class="w-full p-2 border rounded mb-2"
        placeholder="Describe tu problema..."
        rows="3"
      />
      <button
        @click="createTicket"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Enviar Ticket
      </button>
    </div>

    <!-- Lista de tickets -->
    <div v-if="tickets.length">
      <h3 class="text-lg font-semibold mb-2 text-gray-700">Mis tickets</h3>
      <div
        v-for="ticket in tickets"
        :key="ticket.id"
        class="border rounded p-4 mb-3"
      >
        <h4 class="font-semibold text-gray-800">{{ ticket.title }}</h4>
        <p class="text-sm text-gray-600">
          Estado: <span :class="statusColor(ticket.status)">{{ ticket.status }}</span>
          | Fecha: {{ formatDate(ticket.created_at) }}
        </p>
      </div>
    </div>

    <p v-else class="text-gray-500 text-center">No tienes tickets enviados aún.</p>
  </section>
</template>

<script>
export default {
  data() {
    return {
      newTicket: {
        title: '',
        message: ''
      },
      tickets: []
    }
  },
  async created() {
    await this.fetchTickets()
  },
  methods: {
    async fetchTickets() {
      try {
        const res = await fetch(`${import.meta.env.VITE_API_URL}/support/tickets`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        })
        const data = await res.json()
        this.tickets = data.tickets || []
      } catch (err) {
        console.error('Error al obtener tickets:', err)
      }
    },
    async createTicket() {
      const { title, message } = this.newTicket
      if (!title || !message) return alert('Completa todos los campos.')

      try {
        const res = await fetch(`${import.meta.env.VITE_API_URL}/support/tickets/create`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify({ title, message })
        })
        const data = await res.json()
        alert(data.message || 'Ticket enviado correctamente.')
        this.newTicket.title = ''
        this.newTicket.message = ''
        await this.fetchTickets()
      } catch (err) {
        console.error('Error al crear ticket:', err)
      }
    },
    formatDate(dateStr) {
      return new Date(dateStr).toLocaleDateString()
    },
    statusColor(status) {
      return {
        'text-yellow-600': status === 'Abierto',
        'text-blue-600': status === 'En progreso',
        'text-green-600': status === 'Cerrado'
      }[`text-${status}`] || 'text-gray-600'
    }
  }
}
</script>
