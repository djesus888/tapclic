<template>
  <div class="container">
    <h2>Servicios Disponibles</h2>
    <div v-for="servicio in servicios" :key="servicio.id" class="service-card">
      <h3>{{ servicio.service_type }}</h3>
      <p>{{ servicio.description }}</p>
      <p>Precio: {{ servicio.price }}</p>
      <button @click="selectService(servicio.id)">Solicitar</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Home',
  data() {
    return {
      servicios: []
    };
  },
  async created() {
    try {
      const response = await axios.get('/api/services');
      this.servicios = response.data;
    } catch (error) {
      console.error(error);
    }
  },
  methods: {
    selectService(id) {
      this.$router.push(`/services/${id}`);
    }
  }
};
</script>

<style scoped>
.service-card {
  background: #fff;
  padding: 15px;
  margin: 10px 0;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}
</style>
