<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro | MarsExplorer</title>
  <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
  
</head>
<body data-pagina="registro">
  <main class="acceso">
    <section class="acceso-imagen">
      <div class="marca marca-grande">
        <img src="{{ asset('img/logo-cohete-marsexplorer.png') }}" alt="MarsExplorer" class="logo-imagen logo-login">
      </div>
      <div class="texto-imagen">
        <span class="etiqueta">ÚNETE A LA EXPLORACIÓN</span>
        <h2>Un espacio sencillo para aprender sobre Marte.</h2>
        <p>Crea tu cuenta de usuario y accede al dashboard con artículos, misiones y lecturas integradas.</p>
      </div>
    </section>
    <section class="acceso-formulario">
      <form id="formRegistro" class="panel-acceso" action="{{ route('registro.post') }}" method="POST">
        @csrf
        <a href="{{ route('login') }}" class="volver">← Volver al inicio de sesión</a>
        <span class="etiqueta">NUEVA CUENTA</span>
        <h1>Crear cuenta</h1>
        <p>El registro crea una cuenta de Usuario para consultar el contenido de la biblioteca.</p>
        <label for="nombre">Nombre</label><input id="nombre" name="name" type="text" value="{{ old('name') }}" required minlength="2" maxlength="60" autocomplete="name" placeholder="Tu nombre">
        <label for="correo">Correo electrónico</label><input id="correo" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nombre@correo.com">
        <label for="clave">Contraseña</label><input id="clave" name="password" type="password" required minlength="8" autocomplete="new-password" placeholder="Mínimo 8 caracteres">
        <label for="confirmar">Confirmar contraseña</label><input id="confirmar" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" placeholder="Repite tu contraseña">
        <p id="mensaje" class="mensaje" role="status">@if($errors->any()) {{ $errors->first() }} @endif</p>
        <button class="boton principal ancho" type="submit">Registrarme</button>
      </form>
    </section>
  </main>
</body>
</html>
