<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestionar contenido | MarsExplorer</title>
  <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
  <script>
    window.MARS_PUBLICACIONES = {{ \Illuminate\Support\Js::from($publicaciones) }};
    window.MARS_IMG = "{{ asset('img') }}/";
  </script>
  <script>
    window.MARS_ADMIN_CREATE = "{{ route('admin.store') }}";
    window.MARS_ADMIN_BASE = "{{ url('admin/publicaciones') }}";
    window.MARS_CSRF = "{{ csrf_token() }}";
    window.MARS_OLD = {{ \Illuminate\Support\Js::from(old()) }};
    window.MARS_HAS_ERRORS = {{ $errors->any() ? 'true' : 'false' }};
  </script>
  <script src="{{ asset('js/shared.js') }}" defer></script>
  <script src="{{ asset('js/admin.js') }}" defer></script>
</head>
<body data-pagina="admin">
  <div class="aplicacion">
    <aside class="lateral" aria-label="Menú de MarsExplorer">
      <a class="marca marca-lateral" href="{{ route('dashboard') }}">
        <img src="{{ asset('img/logo-cohete-marsexplorer.png') }}" alt="MarsExplorer" class="logo-imagen logo-lateral">
      </a>
      <div class="lateral-seccion"><p class="lateral-titulo">EXPLORAR</p><nav class="navegacion">
        <a class="nav-item" href="{{ route('dashboard', ['seccion' => 'todo']) }}"><span class="nav-icono">◫</span> Todo <span class="nav-flecha">›</span></a>
        <a class="nav-item" href="{{ route('dashboard', ['seccion' => 'articulo']) }}"><span class="nav-icono">▤</span> Artículos <span class="nav-flecha">›</span></a>
        <a class="nav-item" href="{{ route('dashboard', ['seccion' => 'mision']) }}"><span class="nav-icono">◇</span> Misiones <span class="nav-flecha">›</span></a>
      </nav></div>
      <div class="lateral-seccion"><p class="lateral-titulo">ADMINISTRACIÓN</p><a class="nav-item activo" href="{{ route('admin.index') }}" aria-current="page"><span class="nav-icono">⚙</span> Gestionar contenido <span class="nav-flecha">›</span></a></div>
      <div class="lateral-pie"><span class="punto-vivo"></span> Panel de administración <small>COIL · UFPSO + UIDE</small></div>
    </aside>
    <div class="zona-principal">
      <header class="barra-superior"><div class="ruta">MARSEXPLORER <span>/</span> <strong>Administración</strong></div><div class="cuenta"><span class="saludo">Hola, <strong id="nombreUsuario">{{ auth()->user()->name }}</strong></span><form action="{{ route('salir') }}" method="POST">@csrf<button class="boton boton-salir" type="submit">Cerrar sesión</button></form></div></header>
      <main class="contenido-principal">
        <section class="cabecera-admin"><span class="etiqueta">PANEL DE ADMINISTRACIÓN</span><h1>Gestionar contenido</h1><p>Administra la información que aparece en las secciones Artículos y Misiones con un editor sencillo y visual.</p></section>
        <p id="mensaje" class="mensaje" role="status">{{ session('status') ?: ($errors->any() ? $errors->first() : '') }}</p>
        <section id="seccionListado" class="bloque-interior">
          <div class="fila-titulo fila-flex"><div><span class="subetiqueta">PUBLICACIONES</span><h2>Contenido publicado</h2><p>Consulta, edita o elimina los registros existentes.</p></div><button class="boton principal" type="button" id="nuevo">+ Nueva publicación</button></div>
          <div class="herramientas-admin"><div class="filtros"><button class="filtro activo" type="button" data-filtro="todo">Todo</button><button class="filtro" type="button" data-filtro="articulo">Artículos</button><button class="filtro" type="button" data-filtro="mision">Misiones</button></div><input id="buscar" type="search" placeholder="Buscar publicaciones..."></div>
          <p class="contador" id="cantidad"></p><div id="listaAdmin" class="lista-admin"></div>
        </section>
        <section id="seccionFormulario" class="bloque-interior" hidden>
          <button type="button" id="cancelar" class="boton-volver">← Volver al listado</button>
          <div class="editor"><span class="subetiqueta">EDITOR DE PUBLICACIONES</span><h2 id="tituloFormulario">Nuevo contenido</h2><p>Completa los campos. Los cambios se mostrarán automáticamente en la biblioteca.</p>
            <form id="formContenido" action="{{ route('admin.store') }}" method="POST">
              @csrf
              <input type="hidden" name="_method" id="metodoFormulario" value="POST">
              <input type="hidden" id="identificador" name="_id">
              <div class="campos-dos"><div><label for="tipo">Categoría</label><select id="tipo" name="tipo" required><option value="articulo">Artículo</option><option value="mision">Misión</option></select></div><div><label for="imagen">Imagen</label><select id="imagen" name="imagen" required><option value="marte-atardecer.jpg">Marte al atardecer</option><option value="marte-panorama-exploracion.jpg">Panorama de exploración</option><option value="marte-hielo.jpg">Hielo polar</option><option value="casquete-polar.jpg">Casquete polar</option><option value="monte-olimpo.jpg">Monte Olimpo</option><option value="valles-marineris.jpg">Valles Marineris</option><option value="canones-marcianos.jpg">Cañones marcianos</option><option value="perseverance-rover.jpg">Rover Perseverance</option><option value="curiosity-rover.jpg">Rover Curiosity</option><option value="rover-terreno.jpg">Rover en el terreno</option><option value="insight-lander.jpg">Módulo de aterrizaje</option><option value="marte-panoramica.jpg">Imagen anterior: Marte</option><option value="rover.jpg">Rover</option><option value="paisaje-marte.jpg">Paisaje</option><option value="superficie-marte.jpg">Superficie</option><option value="explorador-marte.jpg">Explorador</option></select></div></div>
              <label for="titulo">Título</label><input id="titulo" name="titulo" type="text" required maxlength="90" placeholder="Ej.: El clima de Marte">
              <label for="resumen">Resumen breve</label><textarea id="resumen" name="resumen" rows="3" required maxlength="260" placeholder="¿De qué trata esta publicación?"></textarea>
              <label for="contenido">Información completa</label><textarea id="contenido" name="contenido" rows="9" required placeholder="Escribe varios párrafos sobre el tema. Separa los párrafos con una línea en blanco."></textarea>
              <div class="acciones-editor"><button type="button" class="boton secundario" id="cancelarAbajo">Cancelar</button><button type="submit" class="boton principal">Guardar publicación</button></div>
            </form>
          </div>
        </section>
      </main>
      <dialog id="confirmacionEliminar" class="dialogo-confirmacion" aria-labelledby="tituloConfirmacionEliminar" aria-describedby="descripcionConfirmacionEliminar">
        <div class="dialogo-confirmacion-contenido">
          <span class="etiqueta">CONFIRMAR ACCIÓN</span>
          <h2 id="tituloConfirmacionEliminar">¿Eliminar publicación?</h2>
          <p id="descripcionConfirmacionEliminar">Se eliminará <strong id="nombrePublicacionEliminar"></strong>. Esta acción no se puede deshacer.</p>
          <div class="acciones-confirmacion">
            <button type="button" class="boton secundario" id="cancelarEliminar">Cancelar</button>
            <button type="button" class="boton boton-eliminar" id="confirmarEliminar">Eliminar publicación</button>
          </div>
        </div>
      </dialog>
      <footer>MarsExplorer · Proyecto académico COIL · UFPSO – UIDE</footer>
    </div>
  </div>
</body>
</html>
