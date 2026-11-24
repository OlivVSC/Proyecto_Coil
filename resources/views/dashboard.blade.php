<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Biblioteca de Marte | MarsExplorer</title>
  <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
  <script>
    window.MARS_PUBLICACIONES = {{ \Illuminate\Support\Js::from($publicaciones) }};
    window.MARS_IMG = "{{ asset('img') }}/";
  </script>
  <script src="{{ asset('js/shared.js') }}" defer></script>
  <script src="{{ asset('js/dashboard.js') }}" defer></script>
</head>
<body data-pagina="dashboard">
  <div class="aplicacion">
    <aside class="lateral" aria-label="Menú de MarsExplorer">
      <a class="marca marca-lateral" href="{{ route('dashboard') }}">
        <img src="{{ asset('img/logo-cohete-marsexplorer.png') }}" alt="MarsExplorer" class="logo-imagen logo-lateral">
      </a>
      <div class="lateral-seccion">
        <p class="lateral-titulo">EXPLORAR</p>
        <nav class="navegacion" aria-label="Secciones de la biblioteca">
          <button type="button" class="nav-item activo" data-seccion="todo" aria-current="page"><span class="nav-icono">◫</span> Todo <span class="nav-flecha">›</span></button>
          <button type="button" class="nav-item" data-seccion="articulo"><span class="nav-icono">▤</span> Artículos <span class="nav-flecha">›</span></button>
          <button type="button" class="nav-item" data-seccion="mision"><span class="nav-icono">◇</span> Misiones <span class="nav-flecha">›</span></button>
        </nav>
      </div>
      @can('administrar')
      <div class="lateral-seccion lateral-admin" id="enlaceAdmin">
        <p class="lateral-titulo">ADMINISTRACIÓN</p>
        <a class="nav-item" href="{{ route('admin.index') }}"><span class="nav-icono">⚙</span> Gestionar contenido <span class="nav-flecha">›</span></a>
      </div>
      @endcan
      <div class="lateral-pie">
        <span class="punto-vivo"></span>
        Biblioteca digital sobre Marte
        <small>COIL · UFPSO + UIDE</small>
      </div>
    </aside>

    <div class="zona-principal">
      <header class="barra-superior">
        <div class="ruta">MARSEXPLORER <span>/</span> <strong id="rutaActual">Inicio</strong></div>
        <div class="cuenta"><span class="saludo">Hola, <strong id="nombreUsuario">{{ auth()->user()->name }}</strong></span><form action="{{ route('salir') }}" method="POST">@csrf<button type="submit" class="boton boton-salir">Cerrar sesión</button></form></div>
      </header>
      <main class="contenido-principal">
        <section id="vistaInicio" class="vista">
          <section class="hero">
            <div class="hero-grid">
              <div class="hero-texto">
                <span class="etiqueta">BIBLIOTECA DEL PLANETA ROJO</span>
                <h1>Un planeta por descubrir: bienvenido a Marte.</h1>
                <p>Descubre cómo es Marte, qué hemos aprendido sobre su pasado y cuáles son las misiones que lo exploran. Lee artículos y descubre sus historias en un solo lugar.</p>
                <div class="hero-botones">
                  <button class="boton principal" type="button" data-ir="articulo">Leer artículos</button>
                  <button class="boton secundario" type="button" data-ir="mision">Explorar misiones</button>
                </div>
              </div>

            </div>
          </section>

          <!-- Datos sencillos sobre el planeta, separados visualmente en tres tarjetas. -->
          <section class="datos-marte" aria-label="Datos destacados de Marte">
            <article class="dato-marte">
              <span class="dato-insignia" aria-hidden="true">04</span>
              <div>
                <span class="dato-etiqueta">EN EL SISTEMA SOLAR</span>
                <h3>Cuarto planeta desde el Sol</h3>
                <p>Marte es un planeta rocoso, conocido por el tono rojizo de su superficie.</p>
              </div>
            </article>
            <article class="dato-marte">
              <span class="dato-insignia" aria-hidden="true">CO₂</span>
              <div>
                <span class="dato-etiqueta">ATMÓSFERA</span>
                <h3>Un aire muy tenue</h3>
                <p>Su atmósfera es delgada y está compuesta principalmente por dióxido de carbono.</p>
              </div>
            </article>
            <article class="dato-marte">
              <span class="dato-insignia" aria-hidden="true">✦</span>
              <div>
                <span class="dato-etiqueta">EXPLORACIÓN</span>
                <h3>Misiones robóticas</h3>
                <p>Rovers y orbitadores estudian su geología, clima e historia.</p>
              </div>
            </article>
          </section>

          <section class="bloque-interior" aria-label="Resumen de la biblioteca">
            <div class="fila-titulo"><div><span class="subetiqueta">PANORAMA</span><h2>Tu punto de partida</h2><p>Consulta los contenidos de la biblioteca y elige el tema que quieras descubrir.</p></div></div>
            <div class="resumen-dashboard">
              <div class="resumen-caja"><span>Total de publicaciones</span><strong id="totalPublicaciones">0</strong><small>Información disponible</small></div>
              <div class="resumen-caja"><span>Artículos científicos</span><strong id="totalArticulos">0</strong><small>Clima, relieve, agua y atmósfera</small></div>
              <div class="resumen-caja"><span>Misiones espaciales</span><strong id="totalMisiones">0</strong><small>Rovers, módulos y sondas</small></div>
            </div>
          </section>


          <section class="bloque-interior" aria-labelledby="galeriaTitulo">
            <div class="fila-titulo"><div><span class="subetiqueta">GALERÍA VISUAL</span><h2 id="galeriaTitulo">Paisajes y exploración de Marte</h2><p>Imágenes relacionadas con los temas que encontrarás en la biblioteca.</p></div></div>
            <div class="galeria-marte">
              <figure><img src="{{ asset('img/valles-marineris.jpg') }}" alt="Ilustración de un extenso sistema de cañones marcianos" loading="lazy"><figcaption>Valles y cañones</figcaption></figure>
              <figure><img src="{{ asset('img/perseverance-rover.jpg') }}" alt="Ilustración de un rover explorando Marte" loading="lazy"><figcaption>Exploración robótica</figcaption></figure>
              <figure><img src="{{ asset('img/casquete-polar.jpg') }}" alt="Ilustración del casquete polar marciano" loading="lazy"><figcaption>Hielo en Marte</figcaption></figure>
              <figure><img src="{{ asset('img/insight-lander.jpg') }}" alt="Ilustración de un módulo de aterrizaje en Marte" loading="lazy"><figcaption>Misiones científicas</figcaption></figure>
            </div>
            <p class="aviso-galeria">Imágenes ilustrativas creadas para este proyecto académico; no son fotografías documentales de NASA.</p>
          </section>

          <section class="bloque-interior" aria-labelledby="temasTitulo">
            <div class="fila-titulo"><div><span class="subetiqueta">CATEGORÍAS</span><h2 id="temasTitulo">Explora por secciones</h2><p>Todo queda separado para que el contenido no se vea cargado en una sola pantalla.</p></div></div>
            <div class="categorias-grid">
              <button type="button" class="categoria-tarjeta categoria-articulos" data-ir="articulo"><span class="categoria-icono">▤</span><span><strong>Artículos sobre Marte</strong><small>Geografía, clima, atmósfera, agua y curiosidades del planeta rojo.</small><b>Ir a Artículos →</b></span></button>
              <button type="button" class="categoria-tarjeta categoria-misiones" data-ir="mision"><span class="categoria-icono">◇</span><span><strong>Misiones de exploración</strong><small>Rovers, sondas y proyectos científicos que ayudan a entender Marte.</small><b>Ir a Misiones →</b></span></button>
            </div>
          </section>

          <section class="bloque-interior" aria-labelledby="destacadosTitulo">
            <div class="fila-titulo"><div><span class="subetiqueta">DESTACADOS</span><h2 id="destacadosTitulo">Lecturas recomendadas</h2><p>Selecciona una publicación para abrir su contenido dentro de este mismo panel.</p></div></div>
            <div id="destacados" class="tarjetas"></div>
          </section>
        </section>

        <section id="vistaBiblioteca" class="vista" hidden>
          <div class="cabecera-categoria">
            <div>
              <span class="etiqueta" id="etiquetaCategoria">ARTÍCULOS</span>
              <h1 id="tituloCategoria">Artículos sobre Marte</h1>
              <p id="descripcionCategoria"></p>
            </div>
            <div class="imagen-categoria" aria-hidden="true"></div>
          </div>
          <section class="bloque-interior">
            <div class="fila-titulo"><div><h2 id="tituloListado">Explorar publicaciones</h2><p>Haz clic en una tarjeta para ver la lectura dentro del mismo dashboard.</p></div></div>
            <div class="barra-busqueda"><label for="buscar">Buscar en esta sección</label><input id="buscar" type="search" placeholder="Escribe un título o tema..." autocomplete="off"></div>
            <p id="cantidad" class="contador" role="status"></p>
            <div id="listaContenido" class="tarjetas"></div>
          </section>
        </section>

        <section id="vistaDetalle" class="vista" hidden>
          <button id="volverLectura" type="button" class="boton-volver">← Volver a la sección</button>
          <article class="articulo-completo" id="articuloCompleto">
            <!-- Imagen y título comparten la misma portada, como una lectura editorial. -->
            <div class="portada-editorial">
              <img id="imagenLectura" class="portada-lectura" alt="">
              <div class="cabecera-articulo">
                <span id="tipoLectura" class="tipo">ARTÍCULO</span>
                <h1 id="tituloLectura"></h1>
                <p id="resumenLectura" class="introduccion-lectura"></p>
                <div class="meta-lectura"><span>✦ MarsExplorer</span><span id="tiempoLectura"></span></div>
              </div>
            </div>
            <!-- Texto de la lectura inmediatamente debajo de la imagen, sin tarjetas aisladas. -->
            <div class="cuerpo-lectura">
              <div class="texto-articulo">
                <div class="encabezado-lectura">
                  <div>
                    <span class="subetiqueta">LECTURA EN PROFUNDIDAD</span>
                    <h2>Explorando este tema</h2>
                    <p>Conoce las ideas principales de esta publicación, organizadas para una lectura más clara.</p>
                  </div>
                  <span class="marca-informativa">MarsExplorer · Biblioteca</span>
                </div>
                <div id="contenidoLectura" class="texto-lectura"></div>
                <p class="nota-imagen">Las imágenes son ilustraciones educativas del proyecto; no corresponden a fotografías oficiales de NASA.</p>
              </div>
            </div>
          </article>
          <section class="bloque-interior"><div class="fila-titulo"><div><h2>Continúa explorando</h2><p>Otras publicaciones relacionadas con la exploración marciana.</p></div></div><div id="relacionados" class="tarjetas"></div></section>
        </section>
      </main>
      <footer>MarsExplorer · Proyecto académico COIL · UFPSO – UIDE</footer>
    </div>
  </div>
</body>
</html>
