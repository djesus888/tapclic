<template>
  <header class="bg-blue-600 text-white p-4 shadow-md flex justify-between items-center">
    <h1 class="text-xl font-bold">Tapclic</h1>
    <nav>
      <router-link to="/" class="mx-2 hover:underline">Inicio</router-link>
      <router-link to="/profile" class="mx-2 hover:underline">Perfil</router-link>
      <router-link to="/config" class="mx-2 hover:underline">Configuración</router-link>
    </nav>
  </header>
</template>

<script>
export default {
  name: "Topbar"
}
</script>
