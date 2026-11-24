// Helpers used by both the dashboard and the administration panel.
function obtenerPublicaciones() {
  return window.MARS_PUBLICACIONES || [];
}

function crearElemento(etiqueta, texto, clase) {
  const elemento = document.createElement(etiqueta);

  if (texto !== undefined) elemento.textContent = texto;
  if (clase) elemento.className = clase;

  return elemento;
}

function crearBoton(texto, accion, clase) {
  const boton = crearElemento('button', texto, clase);
  boton.type = 'button';
  boton.addEventListener('click', accion);

  return boton;
}

function crearTarjeta(item) {
  const tarjeta = crearElemento('article', undefined, 'tarjeta');
  const imagen = document.createElement('img');
  imagen.src = window.MARS_IMG + item.imagen;
  imagen.alt = item.titulo;

  const cuerpo = crearElemento('div', undefined, 'tarjeta-cuerpo');
  cuerpo.append(
    crearElemento('span', item.tipo === 'articulo' ? 'Artículo' : 'Misión', 'tipo'),
    crearElemento('h3', item.titulo),
    crearElemento('p', item.resumen),
    crearBoton('Leer información →', () => abrirLectura(item.id), 'boton secundario')
  );

  tarjeta.append(imagen, cuerpo);
  return tarjeta;
}
