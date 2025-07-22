<template>
  <div id="app">
    <!-- Aquí Vue inyecta automáticamente la ruta actual -->
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
body {
  margin: 0;
  font-family: Arial, sans-serif;
}
</style>
