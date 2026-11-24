// Dashboard navigation, publication lists, and the integrated reader.
let seccionActual = 'todo';
let idActual = null;

function mostrarVista(nombre) {
  document.getElementById('vistaInicio').hidden = nombre !== 'todo';
  document.getElementById('vistaBiblioteca').hidden = nombre !== 'lista';
  document.getElementById('vistaDetalle').hidden = nombre !== 'detalle';
}

function cambiarSeccion(seccion) {
  seccionActual = seccion;
  idActual = null;

  document.querySelectorAll('[data-seccion]').forEach(function (boton) {
    const activo = boton.dataset.seccion === seccion;
    boton.classList.toggle('activo', activo);

    if (activo) boton.setAttribute('aria-current', 'page');
    else boton.removeAttribute('aria-current');
  });

  const nombres = {todo: 'Inicio', articulo: 'Artículos', mision: 'Misiones'};
  document.getElementById('rutaActual').textContent = nombres[seccion];

  if (seccion === 'todo') {
    mostrarVista('todo');
    mostrarInicio();
  } else {
    mostrarVista('lista');
    document.getElementById('buscar').value = '';

    const esArticulo = seccion === 'articulo';
    const encabezado = document.querySelector('.cabecera-categoria');
    encabezado.classList.toggle('misiones', !esArticulo);
    document.getElementById('etiquetaCategoria').textContent = esArticulo
      ? 'LECTURAS CIENTÍFICAS'
      : 'EXPLORACIÓN ESPACIAL';
    document.getElementById('tituloCategoria').textContent = esArticulo
      ? 'Artículos sobre Marte'
      : 'Misiones espaciales';
    document.getElementById('descripcionCategoria').textContent = esArticulo
      ? 'Descubre el clima, la geografía, el agua, la atmósfera y otros temas del planeta rojo. Elige un artículo para leerlo con calma.'
      : 'Explora vehículos, sondas y misiones que nos ayudan a comprender Marte. Descubre sus objetivos y aportes.';
    document.getElementById('tituloListado').textContent = esArticulo
      ? 'Todos los artículos'
      : 'Todas las misiones';
    mostrarLista();
  }

  window.scrollTo(0, 0);
}

function mostrarInicio() {
  const todas = obtenerPublicaciones();
  document.getElementById('totalPublicaciones').textContent = todas.length;
  document.getElementById('totalArticulos').textContent =
    todas.filter(publicacion => publicacion.tipo === 'articulo').length;
  document.getElementById('totalMisiones').textContent =
    todas.filter(publicacion => publicacion.tipo === 'mision').length;

  const destacados = document.getElementById('destacados');
  destacados.replaceChildren();

  const elegidos = [
    ...todas.filter(publicacion => publicacion.tipo === 'articulo').slice(0, 2),
    ...todas.filter(publicacion => publicacion.tipo === 'mision').slice(0, 1)
  ];
  elegidos.forEach(item => destacados.append(crearTarjeta(item)));

  if (elegidos.length === 0) {
    destacados.append(crearElemento('p', 'Todavía no hay publicaciones.', 'vacio'));
  }
}

function mostrarLista() {
  const texto = document.getElementById('buscar').value.toLowerCase().trim();
  const lista = obtenerPublicaciones().filter(publicacion =>
    publicacion.tipo === seccionActual &&
    (publicacion.titulo.toLowerCase().includes(texto) ||
      publicacion.resumen.toLowerCase().includes(texto))
  );

  document.getElementById('cantidad').textContent =
    lista.length + ' publicaciones encontradas';

  const contenedor = document.getElementById('listaContenido');
  contenedor.replaceChildren();
  lista.forEach(item => contenedor.append(crearTarjeta(item)));

  if (lista.length === 0) {
    contenedor.append(crearElemento('p', 'No hay contenido que coincida con la búsqueda.', 'vacio'));
  }
}

function abrirLectura(id) {
  const item = obtenerPublicaciones().find(publicacion => publicacion.id === Number(id));
  if (!item) return;

  idActual = item.id;
  seccionActual = item.tipo;

  document.querySelectorAll('[data-seccion]').forEach(function (boton) {
    const activo = boton.dataset.seccion === item.tipo;
    boton.classList.toggle('activo', activo);

    if (activo) boton.setAttribute('aria-current', 'page');
    else boton.removeAttribute('aria-current');
  });

  document.getElementById('rutaActual').textContent =
    item.tipo === 'articulo' ? 'Artículos / Lectura' : 'Misiones / Lectura';
  document.getElementById('tipoLectura').textContent =
    item.tipo === 'articulo' ? 'ARTÍCULO' : 'MISIÓN';
  document.getElementById('tituloLectura').textContent = item.titulo;
  document.getElementById('resumenLectura').textContent = item.resumen;
  document.getElementById('imagenLectura').src = window.MARS_IMG + item.imagen;
  document.getElementById('imagenLectura').alt = item.titulo;

  const palabras = item.contenido.trim().split(/\s+/).length;
  document.getElementById('tiempoLectura').textContent =
    Math.max(1, Math.ceil(palabras / 180)) + ' min de lectura';

  const contenido = document.getElementById('contenidoLectura');
  contenido.replaceChildren();

  const parrafos = item.contenido
    .split(/\n\s*\n/)
    .map(parrafo => parrafo.trim())
    .filter(Boolean);

  if (parrafos.length) {
    contenido.append(crearElemento('p', parrafos[0], 'lectura-destacada'));
  }

  const titulos = item.tipo === 'mision'
    ? ['Objetivos de la misión', 'Trabajo científico', 'Resultados y aportes']
    : ['Características principales', 'Observaciones y estudios', 'Por qué es importante'];
  const secciones = crearElemento('div', undefined, 'lectura-secciones');

  parrafos.slice(1).forEach((texto, indice) => {
    const apartado = crearElemento('section', undefined, 'lectura-apartado');
    const cabecera = crearElemento('div', undefined, 'lectura-apartado-cabecera');
    const numero = crearElemento('span', String(indice + 1).padStart(2, '0'), 'lectura-numero');
    const titulo = crearElemento('h3', titulos[indice] || 'Más información');

    cabecera.append(numero, titulo);
    apartado.append(cabecera, crearElemento('p', texto));
    secciones.append(apartado);
  });

  contenido.append(secciones);

  const relacionados = document.getElementById('relacionados');
  relacionados.replaceChildren();
  obtenerPublicaciones()
    .filter(publicacion => publicacion.id !== item.id && publicacion.tipo === item.tipo)
    .slice(0, 3)
    .forEach(publicacion => relacionados.append(crearTarjeta(publicacion)));

  mostrarVista('detalle');
  window.scrollTo(0, 0);
}

function prepararDashboard() {
  document.querySelectorAll('[data-seccion]').forEach(boton => {
    boton.addEventListener('click', () => cambiarSeccion(boton.dataset.seccion));
  });
  document.querySelectorAll('[data-ir]').forEach(boton => {
    boton.addEventListener('click', () => cambiarSeccion(boton.dataset.ir));
  });
  document.getElementById('buscar').addEventListener('input', mostrarLista);
  document.getElementById('volverLectura').addEventListener('click', () => cambiarSeccion(seccionActual));

  const parametros = new URLSearchParams(window.location.search);
  const seccion = parametros.get('seccion');
  cambiarSeccion(['articulo', 'mision'].includes(seccion) ? seccion : 'todo');

  const ver = Number(parametros.get('ver'));
  if (ver) abrirLectura(ver);
}

prepararDashboard();
