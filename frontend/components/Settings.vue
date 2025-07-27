<template>
  <div class="login-container">
    <div class="login-card">
      <!-- LOGO / TÍTULO -->
      <h2 class="login-title">🚀 TapClic</h2>

      <!-- FORMULARIO LOGIN -->
      <form @submit.prevent="handleLogin" class="register-form">
        <!-- IDENTIFICADOR (Correo o Teléfono) -->
        <div class="form-group">
          <label for="identifier">Correo electrónico o Teléfono</label>
          <input
            id="identifier"
            v-model.trim="identifier"
            type="text"
            required
            placeholder="tuemail@ejemplo.com o 0414XXXXXXX"
            aria-label="Correo electrónico o teléfono"
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
          <h3 class="text-lg font-semibold text-gray-200 mb-3">
            Recuperar contraseña
          </h3>

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
import Swal from "sweetalert2";
import { useAuthStore } from "@/stores/auth";

export default {
  name: "Login",
  data() {
    return {
      identifier: "", // Puede ser correo o teléfono
      password: "",
      loading: false,
      showForgotPassword: false,
      resetEmail: "",
      loadingReset: false,
    };
  },

  computed: {
    // Validaciones
    isEmail() {
      return /\S+@\S+\.\S+/.test(this.identifier);
    },
    isPhone() {
      return /^[0-9]{7,15}$/.test(this.identifier);
    },
  },

  methods: {
    /**
     * Petición POST con mejor manejo de errores
     */
    async fetchAPI(endpoint, payload) {
      try {
        const response = await fetch(endpoint, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload),
        });

        // Si el servidor responde con error 4xx/5xx
        if (!response.ok) {
          let errorText = await response.text();
          throw new Error(
            `Error ${response.status}: ${
              errorText || "El servidor no respondió correctamente"
            }`
          );
        }

        const result = await response.json();

        // Validar que el backend devuelva datos esperados
        if (!result || typeof result !== "object") {
          throw new Error("La respuesta del servidor está mal formateada");
        }

        return result;
      } catch (error) {
        // Si hubo un problema de red (desconexión, timeout, etc.)
        if (error.name === "TypeError") {
          throw new Error(
            "No se pudo conectar con el servidor. Verifica tu conexión a internet."
          );
        }
        throw error;
      }
    },

    /**
     * Inicio de sesión profesional con validaciones avanzadas
     */
    async handleLogin() {
      // Validaciones locales
      if (!this.identifier || !this.password) {
        Swal.fire({
          title: "⚠️ Campos incompletos",
          text: "Por favor ingresa tu correo o teléfono y contraseña",
          icon: "warning",
          confirmButtonColor: "#3085d6",
        });
        return;
      }

      if (!this.isEmail && !this.isPhone) {
        Swal.fire({
          title: "⚠️ Formato inválido",
          text: "Debes ingresar un correo electrónico válido o un número de teléfono",
          icon: "warning",
        });
        return;
      }

      this.loading = true;

      // Mostrar alerta de carga mientras espera respuesta
      Swal.fire({
        title: "Iniciando sesión...",
        text: "Por favor espera",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => Swal.showLoading(),
      });

      try {
        const payload = this.isEmail
          ? { email: this.identifier.trim(), password: this.password }
          : { phone: this.identifier.trim(), password: this.password };

        const data = await this.fetchAPI("/api/login", payload);

        if (data.status === "success" && data.token) {
          // ✅ Guardar sesión en Pinia
          const authStore = useAuthStore();
          authStore.setAuth(data.token, data.user);

          // ✅ Determinar redirección según rol
          const role = data.user?.role || "guest";
          const routesByRole = {
            admin: "/dashboard/admin/users",
            provider: "/dashboard/provider/requests",
            client: "/dashboard/client/home",
          };
          const redirectPath = routesByRole[role] || "/dashboard";

          // ✅ Mostrar éxito y redirigir
          Swal.fire({
            icon: "success",
            title: "Bienvenido",
            text: `Hola, ${data.user?.name || "usuario"}`,
            timer: 2000,
            showConfirmButton: false,
          });

          setTimeout(() => this.$router.push(redirectPath), 1800);
        } else {
          // ❌ Backend devolvió error
          Swal.fire({
            icon: "error",
            title: "Error de autenticación",
            text: data.message || "Usuario o contraseña incorrectos",
          });
        }
      } catch (err) {
        // ❌ Error general de red o servidor
        Swal.fire({
          icon: "error",
          title: "Error de conexión",
          text: err.message,
        });
      } finally {
        this.loading = false;
        this.password = ""; // Limpia la contraseña por seguridad
      }
    },

    toggleForgotPassword() {
      this.showForgotPassword = !this.showForgotPassword;
    },

    /**
     * Recuperar contraseña con feedback profesional
     */
    async handleResetPassword() {
      if (!this.resetEmail) {
        Swal.fire({
          title: "⚠️ Campo vacío",
          text: "Ingresa tu correo electrónico para continuar",
          icon: "warning",
        });
        return;
      }

      Swal.fire({
        title: "Procesando...",
        text: "Estamos verificando tu solicitud",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => Swal.showLoading(),
      });

      try {
        const data = await this.fetchAPI(
          "/api.php/api/forgot-password",
          { email: this.resetEmail.trim() }
        );

        if (data.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Correo enviado",
            text: data.message || "Revisa tu bandeja de entrada",
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "No se pudo procesar",
            text: data.message || "Intenta de nuevo más tarde",
          });
        }
      } catch (err) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: err.message,
        });
      }
    },
  },
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
  margin-bottom: 20px;                                                  color: #fff;
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
  background: #2563eb; /* color azul */                                 color: white;
  font-size: 17px;                                                      font-weight: 600;
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
.form-group-input {                                                     padding: 12px;
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
  border-color: #3b82f6;                                                background: #1e293b;
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
    font-size: 16px;                                                    }
}
</style>
