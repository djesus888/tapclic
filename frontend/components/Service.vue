<template>
  <div class="services">
    <h2>Servicios disponibles en TapClic</h2>
    <ul>
      <li v-for="service in services" :key="service.id">
        <h3>{{ service.name }}</h3>
        <p>{{ service.description }}</p>
        <button @click="requestService(service)">Solicitar</button>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'Service',
  data() {
    return {
      services: [
        { id: 1, name: 'Transporte', description: 'Viajes rápidos y seguros.' },
        { id: 2, name: 'Envíos', description: 'Entrega de paquetes en minutos.' },
        { id: 3, name: 'Mandados', description: 'Compras y encargos a domicilio.' }
      ]
    }
  },
  methods: {
    requestService(service) {
      console.log(`Solicitando servicio: ${service.name}`)
      // Aquí conectaremos con el backend para solicitarlo
    }
  }
}
</script>

<style scoped>
.services {
  padding: 20px;
}
li {
  list-style: none;
  border: 1px solid #ddd;
  padding: 15px;
  margin-bottom: 10px;
  border-radius: 8px;
}
button {
  padding: 8px 16px;
  background: #007bff;
  color: white;
  border: none;
  cursor: pointer;
}
</style>
