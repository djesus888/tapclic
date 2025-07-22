<template>
  <div class="forgot-password">
    <h2>Recuperar contraseña</h2>
    <p>Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>

    <form @submit.prevent="sendResetRequest">
      <input 
        type="email" 
        v-model="email" 
        placeholder="Correo electrónico" 
        required 
      />

      <button type="submit" :disabled="loading">
        {{ loading ? "Enviando..." : "Enviar enlace" }}
      </button>
    </form>

    <p v-if="message" :class="{'success': success, 'error': !success}">
      {{ message }}
    </p>

    <router-link to="/login">Volver a iniciar sesión</router-link>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: "",
      loading: false,
      message: "",
      success: false
    }
  },
  methods: {
    async sendResetRequest() {
      this.loading = true;
      this.message = "";

      try {
        const response = await fetch("http://tu-dominio.com/api/forgot-password", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ email: this.email })
        });

        const data = await response.json();

        this.success = data.status === "success";
        this.message = data.message;
      } catch (error) {
        this.success = false;
        this.message = "Error de conexión. Inténtalo de nuevo.";
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.forgot-password {
  max-width: 400px;
  margin: auto;
  text-align: center;
}
input {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
}
button {
  width: 100%;
  padding: 10px;
  background: #007bff;
  color: white;
  border: none;
  cursor: pointer;
}
button:disabled {
  background: #aaa;
}
.success {
  color: green;
}
.error {
  color: red;
}
</style>
