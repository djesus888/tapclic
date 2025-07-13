<template>
  <div class="container">
    <h2>Dashboard</h2>
    <p>Bienvenido, {{ user.name }}</p>
    <div>
      <h3>Tus Servicios Solicitados</h3>
      <ul>
        <li v-for="srv in myServices" :key="srv.id">
          {{ srv.service_type }} - {{ srv.status }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Dashboard',
  data() {
    return {
      user: JSON.parse(localStorage.getItem('user')) || {},
      myServices: []
    };
  },
  async created() {
    // Suponiendo que existe endpoint para mis servicios
    const response = await axios.get(`/api/services?user_id=${this.user.id}`);
    this.myServices = response.data;
  }
};
</script>

<style scoped>
/* usa estilos de styles.css */
</style>
