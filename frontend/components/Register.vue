<template>
  <div class="register-container">
    <div class="register-box">
      <h2 class="register-title">Crear cuenta</h2>

      <form @submit.prevent="register" class="register-form">
        <!-- Nombre -->
        <div class="form-group">
          <label>Nombre completo</label>
          <input v-model="name" type="text" required placeholder="Nombre Completo"/>
        </div>

        <!-- Correo -->
        <div class="form-group">
          <label>Correo electrónico</label>
          <input v-model="email" type="email" required placeholder="tapclic@email.com"/>
        </div>

        <!-- Teléfono -->
        <div class="form-group">
         <label>Número de teléfono</label>
         <input v-model="phone" type="tel" required placeholder="0414XXXXXXX"/>
         </div>

        <!-- Contraseña -->
        <div class="form-group">
          <label>Contraseña</label>
          <input v-model="password" type="password" required placeholder="xxxxxxxxxxx"/>
        </div>

        <!-- Rol -->
        <div class="form-group">
          <label>Rol</label>
          <select v-model="role">
            <option value="user">Cliente</option>
            <option value="driver">Proveedor</option>
          </select>
        </div>

        <!-- Botón -->
        <button type="submit" class="btn-submit">
          Registrarme
        </button>

        <!-- Link login -->
        <p class="text-link">
          ¿Ya tienes cuenta?
          <RouterLink to="/login">Inicia sesión</RouterLink>
        </p>
      </form>

      <!-- Mensaje -->
      <div v-if="registerMsg" class="msg" v-html="registerMsg"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Register",
  data() {
    return {
      name: "",
      email: "",
      phone: "",
      password: "",
      role: "user", // valor por defecto
      registerMsg: ""
    };
  },
  methods: {
    async register() {
      try {
        const res = await fetch("/api/register", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            name: this.name,
            email: this.email,
             phone: this.phone,
            password: this.password,
            role: this.role
          })
        });

        const text = await res.text();
        console.log("Respuesta del servidor:", text);

        let data;
        try {
          data = JSON.parse(text);
        } catch (err) {
          this.registerMsg =
            "<span class='error'>❌ Respuesta inválida del servidor</span>";
          return;
        }

        if (data.status === "success") {
          this.registerMsg =
            "<span class='success'>✅ Registro exitoso. Redirigiendo...</span>";
          setTimeout(() => this.$router.push("/login"), 1500);
        } else {
          this.registerMsg = `<span class='error'>❌ ${data.message}</span>`;
        }
      } catch (error) {
        console.error("Error de conexión:", error);
        this.registerMsg =
          "<span class='error'>❌ Error de conexión con el servidor</span>";
      }
    }
  }
};
</script>


<style scoped>
:root {
  --primary: #3b82f6;
  --primary-light: #60a5fa;
  --success: #10b981;
  --success-light: #34d399;
  --white-trans: rgba(255, 255, 255, 0.15);
  --border-trans: rgba(255, 255, 255, 0.3);
}

/* ✅ Fondo animado */
.register-container {
  background: linear-gradient(135deg, #1e3a8a, #2563eb, #3b82f6);
  background-size: 300% 300%;
  animation: gradientBG 10s ease infinite;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
}

/* ✅ Caja del formulario */
.register-box {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 25px;
  border-radius: 1.5rem;
  width: 100%;
  max-width: 400px;
  box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
}

/* ✅ Título */
.register-title {
  text-align: center;
  font-size: 26px;
  font-weight: bold;
  margin-bottom: 20px;
  color: white;
}

/* ✅ Formulario */
.register-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

/* ✅ Grupos de inputs */
.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-size: 16px;
  color: white;
  margin-bottom: 6px;
}

.form-group input,
.form-group select {
  padding: 12px;
  border-radius: 10px;
  border: 1px solid var(--border-trans);
  background: var(--white-trans);
  color: white;
  outline: none;
  font-size: 15px;
  transition: all 0.3s ease;
}

.form-group input::placeholder,
.form-group select {
  color: rgba(255, 255, 255, 0.6);
}

.form-group input:focus,
.form-group select:focus {
  border-color: var(--primary-light);
  box-shadow: 0 0 10px rgba(96, 165, 250, 0.4);
}

/* ✅ Botón */
.btn-submit {
  width: 100%;
  padding: 14px;
  background: linear-gradient(90deg, var(--primary), var(--primary-light));
  color: white;
  font-size: 17px;
  font-weight: 600;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

/* ✅ Link de Login */
.text-link {
  text-align: center;
  font-size: 14px;
  margin-top: 10px;
  color: #ddd;
}

.text-link a {
  color: var(--primary);
  text-decoration: none;
  font-weight: 500;
}

.text-link a:hover {
  text-decoration: underline;
}

/* ✅ Mensajes */
.msg {
  text-align: center;
  margin-top: 15px;
  font-size: 15px;
}

.success {
  color: var(--success);
  font-weight: bold;
}

.error {
  color: #dc2626;
  font-weight: bold;
}

/* ✅ Animaciones */
@keyframes gradientBG {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

/* ✅ Responsive */
@media (max-width: 480px) {
  .register-box {
    padding: 20px;
  }
  .register-title {
    font-size: 22px;
  }
  .btn-submit {
    font-size: 16px;
  }
}
</style>
