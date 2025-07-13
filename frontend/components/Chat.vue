<template>
  <div class="container">
    <h2>Chat del Servicio</h2>
    <div class="chat-window">
      <div v-for="msg in messages" :key="msg.id" class="message">
        <strong>{{ msg.sender_id }}</strong>: {{ msg.message }}
      </div>
    </div>
    <form @submit.prevent="sendMessage">
      <input type="text" v-model="text" placeholder="Escribe un mensaje..." required />
      <button type="submit">Enviar</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Chat',
  data() {
    return {
      serviceId: this.$route.params.id,
      userId: localStorage.getItem('user-id'),
      messages: [],
      text: ''
    };
  },
  async created() {
    await this.loadMessages();
  },
  methods: {
    async loadMessages() {
      const response = await axios.get(`/api/chat/${this.serviceId}`);
      this.messages = response.data;
    },
    async sendMessage() {
      if (!this.text) return;
      await axios.post('/api/chat', {
        service_id: this.serviceId,
        sender_id: this.userId,
        recipient_id: null,
        message: this.text
      });
      this.text = '';
      await this.loadMessages();
    }
  }
};
</script>

<style scoped>
.chat-window {
  height: 300px;
  overflow-y: scroll;
  background: #fff;
  padding: 10px;
}
.message {
  margin-bottom: 5px;
}
</style>
