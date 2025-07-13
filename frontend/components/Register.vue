<template>
  <div class="container">
    <h2>Registrarse</h2>
    <form @submit.prevent="register">
      <label for="name">Nombre:</label>
      <input type="text" v-model="name" required />

      <label for="email">Correo:</label>
      <input type="email" v-model="email" required />

      <label for="password">Contraseña:</label>
      <input type="password" v-model="password" required />

      <label for="role">Tipo:</label>
      <select v-model="role">
        <option value="cliente">Cliente</option>
        <option value="prestador">Prestador</option>
      </select>

      <button type="submit">Registrarme</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      role: 'cliente'
    };
  },
  methods: {
    async register() {
      try {
        await axios.post('/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          role: this.role
        });
        alert('Usuario registrado');
        this.$router.push('/login');
      } catch (error) {
        alert('Error al registrar');
      }
    }
  }
};
</script>

<style scoped>
/* usa estilos de styles.css */
</style>
