<template>
  <div class="reset-password">
    <h2>Restablecer contraseña</h2>
    <p>Ingresa tu nueva contraseña para tu cuenta.</p>

    <form @submit.prevent="submitResetPassword">
      <label for="password">Nueva contraseña</label>
      <input
        type="password"
        v-model="password"
        placeholder="Nueva contraseña"
        required
      />

      <label for="confirm">Confirmar contraseña</label>
      <input
        type="password"
        v-model="confirmPassword"
        placeholder="Repite la contraseña"
        required
      />

      <button type="submit" :disabled="loading">
        {{ loading ? "Guardando..." : "Restablecer contraseña" }}
      </button>
    </form>

    <p v-if="message" class="success">{{ message }}</p>
    <p v-if="error" class="error">{{ error }}</p>

    <router-link to="/login">← Volver al login</router-link>
  </div>
</template>

<script>
export default {
  name: "ResetPassword",
  data() {
    return {
      password: "",
      confirmPassword: "",
      token: "", // viene del enlace del correo
      loading: false,
      message: "",
      error: ""
    };
  },
  mounted() {
    // Extraer el token de la URL
    this.token = this.$route.query.token || "";
    if (!this.token) {
      this.error = "Token inválido o expirado.";
    }
  },
  methods: {
    async submitResetPassword() {
      if (this.password !== this.confirmPassword) {
        this.error = "Las contraseñas no coinciden.";
        return;
      }

      this.loading = true;
      this.message = "";
      this.error = "";

      try {
        const response = await fetch("/api/reset-password", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            token: this.token,
            password: this.password
          })
        });

        const result = await response.json();

        if (result.status === "success") {
          this.message = result.message;
        } else {
          this.error = result.message;
        }
      } catch (err) {
        this.error = "Error al conectar con el servidor.";
      }

      this.loading = false;
    }
  }
};
</script>

<style scoped>
.reset-password {
  max-width: 400px;
  margin: 50px auto;
  padding: 20px;
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
  background: #28a745;
  color: white;
  border: none;
  cursor: pointer;
}
.success {
  color: green;
  margin-top: 10px;
}
.error {
  color: red;
  margin-top: 10px;
}
</style>
