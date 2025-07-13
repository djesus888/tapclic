<template>
  <div class="container">
    <h2>Iniciar Sesión</h2>
    <form @submit.prevent="login">
      <label for="email">Correo:</label>
      <input type="email" v-model="email" required />

      <label for="password">Contraseña:</label>
      <input type="password" v-model="password" required />

      <button type="submit">Ingresar</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: ''
    };
  },
  methods: {
    async login() {
      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password
        });
        const token = response.data.token;
        localStorage.setItem('user-token', token);
        this.$router.push('/dashboard');
      } catch (error) {
        alert('Credenciales incorrectas');
      }
    }
  }
};
</script>

<style scoped>
/* usa estilos de styles.css */
</style>

