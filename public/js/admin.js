// Administration panel: publication CRUD, filters, and confirmation dialog.
let filtroAdmin = 'todo';
let editando = null;
let publicacionPendienteEliminar = null;

function cambiarVistaAdmin(formulario) {
  document.getElementById('seccionListado').hidden = formulario;
  document.getElementById('seccionFormulario').hidden = !formulario;
  window.scrollTo(0, 0);
}

function nuevaPublicacion() {
  editando = null;

  const form = document.getElementById('formContenido');
  form.reset();
  form.action = window.MARS_ADMIN_CREATE;
  document.getElementById('metodoFormulario').value = 'POST';
  document.getElementById('identificador').value = '';
  document.getElementById('tituloFormulario').textContent = 'Nueva publicación';

  cambiarVistaAdmin(true);
}

function editarPublicacion(id) {
  const item = obtenerPublicaciones().find(publicacion => Number(publicacion.id) === Number(id));
  if (!item) return;

  editando = item.id;

  const form = document.getElementById('formContenido');
  form.action = window.MARS_ADMIN_BASE + '/' + item.id;
  document.getElementById('metodoFormulario').value = 'PUT';
  document.getElementById('identificador').value = item.id;

  ['tipo', 'imagen', 'titulo', 'resumen', 'contenido'].forEach(campo => {
    document.getElementById(campo).value = item[campo];
  });

  document.getElementById('tituloFormulario').textContent = 'Editar publicación';
  cambiarVistaAdmin(true);
}

function solicitarEliminacionPublicacion(id, titulo) {
  publicacionPendienteEliminar = id;
  document.getElementById('nombrePublicacionEliminar').textContent = titulo;
  document.getElementById('confirmacionEliminar').showModal();
}

function eliminarPublicacion(id) {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = window.MARS_ADMIN_BASE + '/' + id;

  [['_token', window.MARS_CSRF], ['_method', 'DELETE']].forEach(([nombre, valor]) => {
    const campo = document.createElement('input');
    campo.type = 'hidden';
    campo.name = nombre;
    campo.value = valor;
    form.append(campo);
  });

  document.body.append(form);
  form.submit();
}

function mostrarAdministracion() {
  const texto = document.getElementById('buscar').value.trim().toLowerCase();
  const lista = obtenerPublicaciones().filter(publicacion =>
    (filtroAdmin === 'todo' || publicacion.tipo === filtroAdmin) &&
    (publicacion.titulo.toLowerCase().includes(texto) ||
      publicacion.resumen.toLowerCase().includes(texto))
  );

  document.getElementById('cantidad').textContent =
    lista.length + ' publicaciones encontradas';

  const contenedor = document.getElementById('listaAdmin');
  contenedor.replaceChildren();

  lista.forEach(item => {
    const fila = crearElemento('article', undefined, 'fila-admin');
    const info = crearElemento('div', undefined, 'info');
    const imagen = document.createElement('img');
    imagen.src = window.MARS_IMG + item.imagen;
    imagen.alt = '';

    const textoItem = document.createElement('div');
    textoItem.append(
      crearElemento('h3', item.titulo),
      crearElemento('p', item.tipo === 'articulo' ? 'Artículo' : 'Misión')
    );
    info.append(imagen, textoItem);

    const acciones = crearElemento('div', undefined, 'acciones');
    const ver = crearElemento('a', 'Ver', 'boton mini secundario');
    ver.href = '/dashboard?ver=' + encodeURIComponent(item.id);
    acciones.append(
      ver,
      crearBoton('Editar', () => editarPublicacion(item.id), 'boton mini secundario'),
      crearBoton(
        'Eliminar',
        () => solicitarEliminacionPublicacion(item.id, item.titulo),
        'boton mini peligro'
      )
    );

    fila.append(info, acciones);
    contenedor.append(fila);
  });

  if (lista.length === 0) {
    contenedor.append(crearElemento('p', 'No hay publicaciones para mostrar.', 'vacio'));
  }
}

function prepararDialogoEliminacion() {
  const dialogo = document.getElementById('confirmacionEliminar');

  document.getElementById('cancelarEliminar').addEventListener('click', () => dialogo.close());
  document.getElementById('confirmarEliminar').addEventListener('click', () => {
    if (publicacionPendienteEliminar === null) return;

    const id = publicacionPendienteEliminar;
    dialogo.close();
    eliminarPublicacion(id);
  });
  dialogo.addEventListener('close', () => {
    publicacionPendienteEliminar = null;
  });
}

function restaurarFormularioConErrores() {
  if (!window.MARS_HAS_ERRORS) return;

  const anterior = window.MARS_OLD || {};
  if (anterior._method === 'PUT' && anterior._id) editarPublicacion(anterior._id);
  else nuevaPublicacion();

  ['tipo', 'imagen', 'titulo', 'resumen', 'contenido'].forEach(campo => {
    if (anterior[campo] !== undefined) {
      document.getElementById(campo).value = anterior[campo];
    }
  });
}

function prepararAdministracion() {
  document.getElementById('nuevo').addEventListener('click', nuevaPublicacion);
  document.getElementById('cancelar').addEventListener('click', () => cambiarVistaAdmin(false));
  document.getElementById('cancelarAbajo').addEventListener('click', () => cambiarVistaAdmin(false));
  document.querySelectorAll('[data-filtro]').forEach(boton => {
    boton.addEventListener('click', function () {
      filtroAdmin = boton.dataset.filtro;
      document.querySelectorAll('[data-filtro]').forEach(filtro => {
        filtro.classList.toggle('activo', filtro === boton);
      });
      mostrarAdministracion();
    });
  });

  document.getElementById('buscar').addEventListener('input', mostrarAdministracion);
  prepararDialogoEliminacion();
  mostrarAdministracion();
  restaurarFormularioConErrores();
}

prepararAdministracion();
