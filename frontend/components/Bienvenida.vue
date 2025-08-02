<template>
  <div>
    <!-- HEADER Y MENÚ -->
    <header>
      <div class="logo">TapClic Bienvida</div>
    </header>

    <nav>
      <ul>
        <li><a href="#servicios">Servicios</a></li>
        <li><router-link to="/registro">Registro</router-link></li>
        <li><a href="#contacto">Contacto</a></li>
        <li><router-link to="/login">Ingresar</router-link></li>
      </ul>
    </nav>

    <!-- SECCIÓN HERO -->
    <section id="inicio" class="section hero">
      <h1>Conecta con tu solución ideal</h1>
      <p>Solicita o presta servicios fácilmente con TapClic</p>
      <router-link to="/registro" class="btn">Comenzar</router-link>
    </section>

    <!-- SECCIÓN SERVICIOS -->
    <section id="servicios" class="section services">
      <h2>Servicios Disponibles</h2>
      <div class="service-grid">
        <div class="service-card">🚗 Transporte</div>
        <div class="service-card">📦 Envíos</div>
        <div class="service-card">🛍️ Mandados</div>
        <div class="service-card">💻 Soporte técnico</div>
      </div>
    </section>

    <!-- SECCIÓN REGISTRO -->
    <section id="registro" class="section registro">
      <h2>Únete a TapClic</h2>
      <router-link to="/registro" class="btn">Registrarme</router-link>
    </section>

    <!-- SECCIÓN CONTACTO -->
    <section id="contacto" class="section contacto">
      <h2>Contacto</h2>
      <p>
        ¿Tienes preguntas? Escríbenos a
        <a href="mailto:soporte@tapclic.com">soporte@tapclic.com</a>
      </p>
    </section>

    <!-- FOOTER -->
    <footer>
      <p>&copy; {{ currentYear }} TapClic. Todos los derechos reservados.</p>
    </footer>
  </div>
</template>

<script>
export default {
  name: "Bienvenida",
  data() {
    return {
      currentYear: new Date().getFullYear(),
    };
  },
};
</script>

<style scoped>
/* =========================
   RESET Y CONFIGURACIÓN BASE
========================= */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f4f4f4;
  color: #333;
  line-height: 1.4;
}

/* =========================
   ENCABEZADO Y NAVEGACIÓN
========================= */
header {
  background-color: #007bff;
  color: #fff;
  padding: 15px;
  text-align: center;
  font-size: 1.4rem;
  font-weight: bold;
}

/* NAV siempre en una línea, sin scroll */
nav {
  background: #333;
}

nav ul {
  list-style: none;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 8px;
  gap: 25px; /* espacio entre enlaces en PC */
  flex-wrap: nowrap; /* nunca salta de línea */
}

nav ul li a,
nav ul li router-link {
  color: #fff;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.95rem;
  white-space: nowrap; /* evita salto de texto */
}

nav ul li a:hover,
nav ul li router-link:hover {
  text-decoration: underline;
}

/* =========================
   SECCIONES Y CONTENIDO
========================= */
.section {
  padding: 30px 15px;
  text-align: center;
}

.section h2 {
  font-size: 1.6rem;
  margin-bottom: 15px;
}

.section p {
  max-width: 800px;
  margin: auto;
  font-size: 1rem;
}

/* HERO */
.hero {
  background: linear-gradient(to bottom right, #007bff, #00bfff);
  color: white;
  padding: 40px 20px;
}

.hero h1 {
  font-size: 2rem;
  margin-bottom: 15px;
}

.hero p {
  font-size: 1.1rem;
  margin-bottom: 20px;
}

/* GRID DE SERVICIOS */
.service-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 10px;
  max-width: 800px;
  margin: auto;
}

.service-card {
  background: white;
  padding: 15px;
  border-radius: 6px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  font-size: 1rem;
}

/* BOTONES */
.btn {
  background: #007bff;
  color: #fff;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
  text-align: center;
  transition: background 0.3s;
  font-size: 0.95rem;
}

.btn:hover {
  background: #0056b3;
}

/* CONTACTO */
.contacto a {
  color: #007bff;
  text-decoration: underline;
}

/* =========================
   PIE DE PÁGINA
========================= */
footer {
  text-align: center;
  padding: 12px;
  background-color: #222;
  color: #fff;
  position: relative;
  width: 100%;
  margin-top: 15px;
  font-size: 0.85rem;
}

/* =========================
   RESPONSIVE DESIGN
========================= */

/* TABLETS Y PANTALLAS MEDIAS */
@media (max-width: 992px) {
  header {
    font-size: 1.2rem;
    padding: 12px;
  }

  nav ul {
    gap: 18px; /* reduce espacio en tablets */
  }

  nav ul li a {
    font-size: 0.9rem;
  }

  .section h2 {
    font-size: 1.4rem;
  }

  .hero h1 {
    font-size: 1.7rem;
  }
}

/* MÓVILES */
@media (max-width: 600px) {
  /* MENÚ ajustado para que quepa sin scroll */
  nav ul {
    gap: 12px; /* reduce mucho el espacio */
    padding: 6px;
  }

  nav ul li a {
    font-size: 0.8rem; /* texto más chico para que quepa */
  }

  .btn {
    width: 100%; /* botones ocupan toda la pantalla en móvil */
    padding: 10px;
    font-size: 0.9rem;
  }

  .hero {
    padding: 25px 15px;
  }

  .hero h1 {
    font-size: 1.5rem;
  }

  .hero p {
    font-size: 0.95rem;
  }

  .section h2 {
    font-size: 1.3rem;
    margin-bottom: 10px;
  }

  .section p {
    font-size: 0.9rem;
    padding: 0 8px;
  }

  .service-card {
    font-size: 0.9rem;
    padding: 12px;
  }

  footer {
    font-size: 0.8rem;
    padding: 8px;
  }
}
</style>
