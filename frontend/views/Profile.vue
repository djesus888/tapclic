<template>
  <div class="container">
    <h2>Mi Perfil</h2>
    <p><strong>Nombre:</strong> {{ user.name }}</p>
    <p><strong>Email:</strong> {{ user.email }}</p>
    <p><strong>Rol:</strong> {{ user.role }}</p>
    <p><strong>Balance Token:</strong> {{ user.token_balance }}</p>
    <button @click="logout">Cerrar Sesión</button>
  </div>
</template>

<script>
export default {
  name: 'Profile',
  data() {
    return {
      user: JSON.parse(localStorage.getItem('user')) || {}
    };
  },
  methods: {
    logout() {
      localStorage.clear();
      this.$router.push('/login');
    }
  }
};
</script>

<style scoped>
/* usa estilos de styles.css */
</style>
