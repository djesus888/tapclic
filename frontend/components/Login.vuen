<template>
  <div class="login-container">  <!-- uso la clase del registro para el contenedor -->
    <div class="login-card">         <!-- tarjeta con estilos de register-box adaptados -->
      <!-- LOGO / TÍTULO -->
      <h2 class="login-title">
        🚀 TapClic
      </h2>

      <!-- FORMULARIO LOGIN -->
      <form @submit.prevent="handleLogin" class="register-form">
        <!-- EMAIL -->
        <div class="form-group">
          <label for="email">Correo electrónico</label>
          <input
            id="email"
            v-model.trim="email"
            type="email"
            required
            placeholder="tuemail@ejemplo.com"
            aria-label="Correo electrónico"
          />
        </div>

        <!-- PASSWORD -->
        <div class="form-group">
          <label for="password">Contraseña</label>
          <input
            id="password"
            v-model.trim="password"
            type="password"
            required
            placeholder="********"
            aria-label="Contraseña"
          />
        </div>

        <!-- BOTÓN LOGIN -->
        <button type="submit" class="btn-submit" :disabled="loading">
          <span v-if="!loading">Iniciar sesión</span>
          <span v-else class="loader"></span>
        </button>

        <!-- OLVIDASTE CONTRASEÑA -->
        <p
          class="text-link"
          @click="toggleForgotPassword"
          style="cursor: pointer;"
        >
          ¿Olvidaste tu contraseña?
        </p>

        <!-- REGISTRO -->
        <p class="text-link" style="margin-top: 8px;">
          ¿No tienes cuenta?
          <router-link to="/registro" class="text-blue-500 font-semibold">
            Regístrate aquí
          </router-link>
        </p>
      </form>

      <!-- MENSAJE LOGIN -->
      <div v-if="loginMsg" class="msg" v-html="loginMsg"></div>

      <!-- FORMULARIO RECUPERAR CONTRASEÑA -->
      <transition name="fade">
        <div
          v-if="showForgotPassword"
          class="forgot-box mt-6 p-4 rounded-xl bg-white/10 backdrop-blur-md border border-white/20"
        >
          <h3 class="text-lg font-semibold text-gray-200 mb-3">Recuperar contraseña</h3>

          <input
            v-model.trim="resetEmail"
            type="email"
            placeholder="Ingresa tu correo"
            class="form-group-input mb-3"
            aria-label="Correo para recuperación"
          />

          <button
            @click="handleResetPassword"
            class="btn-secondary w-full flex items-center justify-center"
            :disabled="loadingReset"
          >
            <span v-if="!loadingReset">Enviar enlace de recuperación</span>
            <span v-else class="loader"></span>
          </button>

          <div v-if="resetMsg" class="mt-2 text-sm text-center" v-html="resetMsg"></div>
        </div>
      </transition>
    </div>
  </div>
</template>


<script>
export default {
  name: "Login",
  data() {
    return {
      email: "",
      password: "",
      loginMsg: "",
      loading: false,
      showForgotPassword: false,
      resetEmail: "",
      resetMsg: "",
      loadingReset: false
    };
  },
  methods: {
    async fetchAPI(endpoint, bodyData) {
      const response = await fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(bodyData)
      });

      const text = await response.text();
      console.log("📩 Respuesta del servidor:", text);

      try {
        return JSON.parse(text);
      } catch {
        throw new Error("Respuesta inválida del servidor");
      }
    },

    async handleLogin() {
      if (!this.email || !this.password) {
        this.loginMsg = `<span class="text-red-400">Completa todos los campos</span>`;
        return;
      }

      this.loading = true;
      this.loginMsg = `<span class="text-gray-300">Procesando...</span>`;

      try {
        const data = await this.fetchAPI("/backend/routes/api.php/api/login", {
          email: this.email,
          password: this.password
        });

        if (data.status === "success") {
          this.loginMsg = `<span class="text-green-400">${data.message || "Login exitoso"}</span>`;

          localStorage.setItem("authToken", data.token);
          localStorage.setItem("userEmail", data.user.email);
          localStorage.setItem("userRole", data.user.role);

          setTimeout(() => window.location.href = "/dashboard.html", 1000);

        } else {
          this.loginMsg = `<span class="text-red-400">${data.message || "Error desconocido"}</span>`;
        }
      } catch (error) {
        console.error("❌ Error en login:", error);
        this.loginMsg = `<span class="text-red-400">${error.message}</span>`;
      } finally {
        this.loading = false;
      }
    },

    toggleForgotPassword() {
      this.showForgotPassword = !this.showForgotPassword;
    },

    async handleResetPassword() {
      if (!this.resetEmail) {
        this.resetMsg = `<span class="text-red-400">Por favor ingresa tu correo</span>`;
        return;
      }

      this.loadingReset = true;
      this.resetMsg = `<span class="text-gray-300">Enviando solicitud...</span>`;

      try {
        const data = await this.fetchAPI("/backend/routes/api.php/api/forgot-password", {
          email: this.resetEmail
        });

        if (data.status === "success") {
          this.resetMsg = `<span class="text-green-400">${data.message}</span>`;
        } else {
          this.resetMsg = `<span class="text-red-400">${data.message || "Error en recuperación"}</span>`;
        }
      } catch (err) {
        console.error("❌ Error en recuperación:", err);
        this.resetMsg = `<span class="text-red-400">${err.message}</span>`;
      } finally {
        this.loadingReset = false;
      }
    }
  }
};
</script>

<style scoped>
/* Usamos estilos adaptados del registro para que se vea igual en dimensiones */

/* CONTENEDOR PRINCIPAL */
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: #0f172a; /* Fondo oscuro para login */
  padding: 20px;
}

/* TARJETA LOGIN adaptada de register-box */
.login-card {
  background: #1e293b; /* mantengo fondo oscuro */
  width: 100%;
  max-width: 400px;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
}

/* TÍTULO adaptado */
.login-title {
  text-align: center;
  font-size: 26px;
  font-weight: bold;
  margin-bottom: 20px;
  color: #fff;
  text-shadow: 0 0 5px rgba(0,0,0,0.7);
}

/* FORMULARIO usando registro */
.register-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

/* GRUPOS */
.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-size: 16px;
  color: #cbd5e1;
  margin-bottom: 6px;
}

.form-group input {
  padding: 12px;
  border: 1px solid #334155;
  border-radius: 8px;
  font-size: 15px;
  outline: none;
  background: #0f172a;
  color: #e2e8f0;
  transition: border 0.2s ease-in-out;
}

.form-group input::placeholder {
  color: #94a3b8;
}

.form-group input:focus {
  border-color: #3b82f6;
  background: #1e293b;
}

/* BOTÓN adaptado de btn-submit, pero con color primario login */
.btn-submit {
  width: 100%;
  padding: 14px;
  background: #2563eb; /* color azul */
  color: white;
  font-size: 17px;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s ease-in-out;
  display: flex;
  justify-content: center;
  align-items: center;
}

.btn-submit:hover:enabled {
  background: #1d4ed8;
}

.btn-submit:disabled {
  background: #93c5fd;
  cursor: not-allowed;
}

/* Link estilizado igual registro */
.text-link {
  text-align: center;
  font-size: 14px;
  color: #93c5fd;
  user-select: none;
}

.text-link a {
  color: #60a5fa;
  text-decoration: none;
  font-weight: 500;
}

.text-link a:hover {
  text-decoration: underline;
}

/* MENSAJES */
.msg {
  text-align: center;
  margin-top: 15px;
  font-size: 15px;
  color: #fbbf24; /* amarillo suave para mensajes */
}

/* FORMULARIO RECUPERAR CONTRASEÑA */
.forgot-box {
  background: #1e293b;
  border-radius: 12px;
}

/* input para recuperación */
.form-group-input {
  padding: 12px;
  border: 1px solid #334155;
  border-radius: 8px;
  font-size: 15px;
  outline: none;
  background: #0f172a;
  color: #e2e8f0;
  transition: border 0.2s ease-in-out;
  width: 100%;
}

.form-group-input::placeholder {
  color: #94a3b8;
}

.form-group-input:focus {
  border-color: #3b82f6;
  background: #1e293b;
}

/* RESPONSIVE */
@media (max-width: 480px) {
  .login-card {
    padding: 20px;
  }
  .login-title {
    font-size: 22px;
  }
  .btn-submit {
    font-size: 16px;
  }
}
</style>
