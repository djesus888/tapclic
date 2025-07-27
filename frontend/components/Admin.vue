<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
      <h2 class="text-2xl font-bold text-center mb-6">Crear cuenta en TapClic</h2>

      <form @submit.prevent="handleRegister">
        <!-- Nombre -->
        <div class="mb-4">
          <label class="block text-gray-700">Nombre completo</label>
          <input v-model="name" type="text" required
            class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
        </div>

        <!-- Email -->
        <div class="mb-4">
          <label class="block text-gray-700">Correo electrónico</label>
          <input v-model="email" type="email" required
            class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
        </div>

        <!-- Teléfono -->
        <div class="mb-4">
          <label class="block text-gray-700">Teléfono</label>
          <input v-model="phone" type="tel" required
            class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
        </div>

        <!-- Contraseña -->
        <div class="mb-4">
          <label class="block text-gray-700">Contraseña</label>
          <input v-model="password" type="password" required
            class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
        </div>

        <!-- Rol -->
        <div class="mb-4">
          <label class="block text-gray-700 mb-1">Tipo de cuenta</label>
          <select v-model="role" required
            class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
            <option disabled value="">Selecciona una opción</option>
            <option value="client">Cliente</option>
            <option value="provider">Proveedor</option>
          </select>
        </div>

        <!-- Botón -->
        <button type="submit"
          class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
          Registrarme
        </button>

        <!-- Mensajes -->
        <p v-if="error" class="text-red-500 text-sm mt-2 text-center">{{ error }}</p>
        <p v-if="success" class="text-green-500 text-sm mt-2 text-center">
          Cuenta creada con éxito ✅ Redirigiendo...
        </p>

        <!-- Link login -->
        <p class="text-center text-gray-500 text-sm mt-4">
          ¿Ya tienes cuenta?
          <router-link to="/login" class="text-blue-600 hover:underline">Inicia sesión</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const name = ref('')
const email = ref('')
const phone = ref('')
const password = ref('')
const role = ref('') // <-- Nuevo: cliente o proveedor
const error = ref('')
const success = ref(false)

const handleRegister = async () => {
  if (!role.value) {
    error.value = 'Debes seleccionar el tipo de cuenta'
    return
  }

  try {
    error.value = ''
    success.value = false

    await axios.post('https://api.tapclic.com/register', {
      name: name.value,
      email: email.value,
      phone: phone.value,
      password: password.value,
      role: role.value // 🔑 CLIENTE o PROVEEDOR según selección
    })

    success.value = true

    setTimeout(() => {
      router.push('/login')
    }, 1500)

  } catch (err) {
    error.value = err.response?.data?.message || 'No se pudo crear la cuenta'
  }
}
</script>
