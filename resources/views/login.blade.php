<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión | MarsExplorer</title>
  <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
  

  <style>
    /* Solo el inicio de sesión: ocupa la pantalla sin scroll. */
    html:has(body[data-pagina="login"]),
    body[data-pagina="login"] {
      height: 100%;
      overflow: hidden;
    }

    body[data-pagina="login"] .acceso {
      height: 100vh;
      height: 100dvh;
      min-height: 0;
      overflow: hidden;
    }

    body[data-pagina="login"] .acceso-imagen,
    body[data-pagina="login"] .acceso-formulario {
      min-width: 0;
      min-height: 0;
      height: 100%;
    }

    body[data-pagina="login"] .acceso-imagen {
      padding: clamp(18px, 3vh, 35px) 7%;
      gap: 10px;
    }

    body[data-pagina="login"] .logo-login {
      width: min(100%, 280px);
      max-height: 25vh;
      max-height: 25dvh;
      object-fit: contain;
      object-position: left center;
    }

    body[data-pagina="login"] .texto-imagen {
      padding-bottom: clamp(0px, 3vh, 24px);
    }

    body[data-pagina="login"] .texto-imagen h2 {
      font-size: clamp(1.4rem, 3.1vh, 2.5rem);
      line-height: 1.13;
      margin: 8px 0;
    }

    body[data-pagina="login"] .texto-imagen p {
      margin: 8px 0;
      font-size: clamp(.78rem, 1.65vh, .98rem);
      line-height: 1.45;
    }

    body[data-pagina="login"] .tarjetas-beneficios {
      margin-top: 15px;
      gap: 7px;
    }

    body[data-pagina="login"] .mini-beneficio {
      padding: 10px;
    }

    body[data-pagina="login"] .acceso-formulario {
      overflow: hidden;
      padding: 16px clamp(18px, 4vw, 36px);
    }

    body[data-pagina="login"] .panel-acceso h1 {
      font-size: clamp(1.8rem, 4vh, 2.7rem);
      margin: 8px 0;
    }

    body[data-pagina="login"] .panel-acceso > p:not(.mensaje):not(.pie-formulario) {
      font-size: .88rem;
      line-height: 1.4;
      margin: 7px 0;
    }

    body[data-pagina="login"] .roles {
      margin: 15px 0 12px;
    }

    body[data-pagina="login"] .rol span {
      padding: 12px 8px;
    }

    body[data-pagina="login"] .panel-acceso > label {
      margin: 11px 0 5px;
    }

    body[data-pagina="login"] .panel-acceso input:not([type=radio]) {
      padding: 10px 12px;
    }

    body[data-pagina="login"] .panel-acceso .mensaje {
      min-height: 18px;
      margin: 6px 0;
    }

    body[data-pagina="login"] .panel-acceso .pie-formulario {
      margin: 12px 0 0;
      font-size: .84rem;
    }

    .logo-solo-movil { display: none; }
    .enlace-recuperacion { display:block; width:max-content; max-width:100%; margin:8px 0 0 auto; color:var(--naranja); font-size:.84rem; font-weight:700; }
    .enlace-recuperacion:hover { color:#ffb58e; text-decoration:underline; }

    @media (max-height: 670px) and (min-width: 801px) {
      body[data-pagina="login"] .tarjetas-beneficios { display: none; }
      body[data-pagina="login"] .texto-imagen h2 { font-size: 1.6rem; }
      body[data-pagina="login"] .logo-login { max-height: 20dvh; }
    }

    @media (max-width: 800px) {
      body[data-pagina="login"] .acceso { display: block; }
      body[data-pagina="login"] .acceso-imagen { display: none; }
      body[data-pagina="login"] .acceso-formulario {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px 22px;
        height: 100%;
      }
      body[data-pagina="login"] .panel-acceso { max-width: 430px; }
      .logo-solo-movil {
        display: block;
        width: min(190px, 43vw);
        height: auto;
        max-height: 18dvh;
        object-fit: contain;
        margin: 0 auto 10px;
      }
    }

    @media (max-width: 800px) and (max-height: 680px) {
      .logo-solo-movil { max-height: 11dvh; width: 135px; margin-bottom: 5px; }
      body[data-pagina="login"] .panel-acceso h1 { font-size: 1.7rem; margin: 5px 0; }
      body[data-pagina="login"] .roles { margin: 9px 0; }
      body[data-pagina="login"] .rol span { padding: 9px 6px; }
      body[data-pagina="login"] .panel-acceso > label { margin: 8px 0 4px; }
      body[data-pagina="login"] .panel-acceso input:not([type=radio]) { padding: 8px 11px; }
      body[data-pagina="login"] .panel-acceso .pie-formulario { margin-top: 8px; }
    }

    @media (max-width: 800px) and (max-height: 520px) {
      .logo-solo-movil { display: none; }
      body[data-pagina="login"] .panel-acceso > p:not(.mensaje):not(.pie-formulario) { display: none; }
      body[data-pagina="login"] .panel-acceso .mensaje { margin: 2px 0; }
    }
  </style>
</head>
<body data-pagina="login">
  <main class="acceso">
    <section class="acceso-imagen">
      <div class="marca marca-grande">
        <img src="{{ asset('img/logo-cohete-marsexplorer.png') }}" alt="MarsExplorer" class="logo-imagen logo-login">
      </div>
      <div class="texto-imagen">
        <span class="etiqueta">EXPLORACIÓN MARCIANA</span>
        <h2>Descubre el planeta rojo con una vista más clara, moderna y organizada.</h2>
        <p>MarsExplorer reúne artículos y misiones en un solo panel para leer, aprender y gestionar contenido.</p>
        <div class="tarjetas-beneficios">
          <article class="mini-beneficio"><strong>Artículos</strong><span>Clima, atmósfera, agua y geografía.</span></article>
          <article class="mini-beneficio"><strong>Misiones</strong><span>Rovers, sondas y hallazgos científicos.</span></article>
          <article class="mini-beneficio"><strong>Administración</strong><span>CRUD sencillo para gestionar publicaciones.</span></article>
        </div>
      </div>
    </section>
    <section class="acceso-formulario">
      <form id="formLogin" class="panel-acceso" action="{{ route('login.post') }}" method="POST">
        @csrf
        <img src="{{ asset('img/logo-cohete-marsexplorer.png') }}" class="logo-solo-movil" alt="MarsExplorer">
        <span class="etiqueta">BIENVENIDO</span>
        <h1>Iniciar sesión</h1>
        <p>Selecciona tu tipo de cuenta para continuar en MarsExplorer.</p>

        <div class="roles" role="group" aria-label="Tipo de cuenta">
          <label class="rol"><input type="radio" name="rol" value="usuario" {{ old('rol', 'usuario') === 'usuario' ? 'checked' : '' }}><span>Usuario</span></label>
          <label class="rol"><input type="radio" name="rol" value="admin" {{ old('rol') === 'admin' ? 'checked' : '' }}><span>Administrador</span></label>
        </div>

        <label for="correo">Correo electrónico</label>
        <input id="correo" name="email" type="email" value="{{ old('email') }}" placeholder="nombre@correo.com" autocomplete="username" required>
        <label for="clave">Contraseña</label>
        <input id="clave" name="password" type="password" placeholder="Tu contraseña" autocomplete="current-password" required>
        <a class="enlace-recuperacion" href="{{ route('password.recovery.request') }}">¿Olvidaste tu contraseña?</a>
        <p id="mensaje" class="mensaje" role="status">{{ $errors->first('email') ?: $errors->first('password') ?: $errors->first('rol') ?: session('status') }}</p>
        <button class="boton principal ancho" type="submit">Ingresar a MarsExplorer</button>
        <p class="pie-formulario">¿Aún no tienes cuenta? <a href="{{ route('registro') }}">Crear cuenta de usuario</a></p>
      </form>
    </section>
  </main>
</body>
</html>
