<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>
    @switch($stage)
      @case('request') Recuperar contraseña @break
      @case('verify') Verificar código @break
      @case('reset') Crear nueva contraseña @break
      @default Contraseña actualizada
    @endswitch
    | MarsExplorer
  </title>
  <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
  <style>
    body[data-pagina="recovery"] { min-height:100vh; }
    body[data-pagina="recovery"] .acceso-formulario { min-height:100vh; }
    body[data-pagina="recovery"] .panel-acceso { padding:24px 0; }
    body[data-pagina="recovery"] .logo-solo-movil { display:none; }
    body[data-pagina="recovery"] .enlace-auxiliar { display:inline-block; margin:9px 0 0; color:var(--naranja); font-size:.88rem; font-weight:700; }
    body[data-pagina="recovery"] .enlace-auxiliar:hover { color:#ffb58e; text-decoration:underline; }
    body[data-pagina="recovery"] .mensaje-info { color:#bcd3ec; font-size:.82rem; }
    body[data-pagina="recovery"] .codigo-casillas { display:flex; justify-content:space-between; gap:8px; margin:20px 0; }
    body[data-pagina="recovery"] .codigo-casilla { width:calc((100% - 40px) / 6)!important; min-width:0; padding:12px 3px!important; text-align:center; font-size:1.35rem; font-weight:800; }
    body[data-pagina="recovery"] .codigo-casilla-error { border-color:#ff8f86!important; box-shadow:0 0 0 1px #ff8f86; }
    body[data-pagina="recovery"] .acciones-recuperacion { display:grid; gap:11px; margin-top:17px; }
    body[data-pagina="recovery"] .boton-texto { border:0; background:transparent; color:var(--naranja); font-weight:700; }
    body[data-pagina="recovery"] .boton-texto:hover { color:#ffb58e; text-decoration:underline; }
    body[data-pagina="recovery"] .texto-correo { overflow-wrap:anywhere; color:#d5dfec; }
    body[data-pagina="recovery"] .campo-con-accion { position:relative; }
    body[data-pagina="recovery"] .campo-con-accion input { min-width:0; padding-right:52px; }
    body[data-pagina="recovery"] .campo-con-accion button { position:absolute; top:calc(50% + 6px); right:7px; display:grid; width:38px; height:38px; place-items:center; transform:translateY(-50%); border:0; border-radius:8px; background:transparent; color:var(--naranja); }
    body[data-pagina="recovery"] .campo-con-accion button:hover { background:#233951; color:#ffb58e; }
    body[data-pagina="recovery"] .campo-con-accion button svg { width:21px; height:21px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:1.8; }
    body[data-pagina="recovery"] .boton-guardar-contrasena { margin-top:14px; }
    @media(max-width:800px) {
      body[data-pagina="recovery"] .acceso-imagen { display:none; }
      body[data-pagina="recovery"] .acceso-formulario { min-height:100vh; padding:18px 22px; }
      body[data-pagina="recovery"] .panel-acceso { max-width:430px; }
      body[data-pagina="recovery"] .logo-solo-movil { display:block; width:min(190px,43vw); max-height:18dvh; object-fit:contain; margin:0 auto 10px; }
    }
    @media(max-width:420px) {
      body[data-pagina="recovery"] .codigo-casillas { gap:5px; }
      body[data-pagina="recovery"] .codigo-casilla { padding:10px 1px!important; font-size:1.12rem; }
    }
  </style>
</head>
<body data-pagina="recovery">
  <main class="acceso">
    <section class="acceso-imagen">
      <a class="marca marca-grande" href="{{ route('login') }}">
        <img src="{{ asset('img/logo-cohete-marsexplorer.png') }}" alt="MarsExplorer" class="logo-imagen logo-login">
      </a>
      <div class="texto-imagen">
        <span class="etiqueta">SEGURIDAD MARSEXPLORER</span>
        <h2>Tu próxima exploración empieza con una cuenta segura.</h2>
        <p>Recupera el acceso a tu cuenta para continuar descubriendo artículos y misiones del planeta rojo.</p>
      </div>
    </section>
    <section class="acceso-formulario">
      <div class="panel-acceso">
        <a class="volver" href="{{ route('login') }}">← Volver al inicio de sesión</a>
        <img src="{{ asset('img/logo-cohete-marsexplorer.png') }}" class="logo-solo-movil" alt="MarsExplorer">
        <span class="etiqueta">RECUPERACIÓN SEGURA</span>

        @if($stage === 'request')
          <h1>Recuperar contraseña</h1>
          <p>Ingresa el correo electrónico asociado a tu cuenta de MarsExplorer. Si la cuenta existe y el servicio de correo está configurado, recibirás un código para restablecer tu contraseña.</p>
          <form action="{{ route('password.recovery.send') }}" method="POST">
            @csrf
            <label for="correo">Correo electrónico</label>
            <input id="correo" name="email" type="email" value="{{ old('email') }}" placeholder="nombre@correo.com" autocomplete="email" required maxlength="255">
            @if($errors->any())
              <p class="mensaje" role="alert">{{ $errors->first() }}</p>
            @endif
            <p class="mensaje mensaje-info" role="status">{{ session('status') }}</p>
            <button class="boton principal ancho" type="submit">Enviar código</button>
          </form>

        @elseif($stage === 'verify')
          <h1>Verificar código</h1>
          <p>Ingresa el código de 6 dígitos que enviamos a tu correo electrónico.</p>
          <p class="texto-correo">{{ $maskedEmail }}</p>
          <p class="mensaje mensaje-info" role="status">{{ session('status') }}</p>
          <form id="formCodigo" action="{{ route('password.recovery.verify') }}" method="POST">
            @csrf
            <label for="digito-1">Código de verificación</label>
            <div class="codigo-casillas" aria-label="Código de seis dígitos">
              @for($i = 0; $i < 6; $i++)
                <input id="digito-{{ $i + 1 }}" class="codigo-casilla{{ $errors->any() ? ' codigo-casilla-error' : '' }}" type="text" name="digits[]" inputmode="numeric" pattern="[0-9]" maxlength="1" autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}" aria-label="Dígito {{ $i + 1 }}" required>
              @endfor
            </div>
            @if($errors->any())
              <p class="mensaje" role="alert">{{ $errors->first() }}</p>
            @endif
            <button class="boton principal ancho" type="submit">Verificar código</button>
          </form>
          <div class="acciones-recuperacion">
            <form action="{{ route('password.recovery.resend') }}" method="POST">
              @csrf
              <button class="boton-texto" type="submit">Reenviar código</button>
            </form>
            <a class="enlace-auxiliar" href="{{ route('password.recovery.request') }}">Usar otro correo</a>
          </div>
          <script>
            (function () {
              const casillas = Array.from(document.querySelectorAll('.codigo-casilla'));
              casillas.forEach(function (casilla, indice) {
                casilla.addEventListener('input', function () {
                  const digitos = casilla.value.replace(/[^0-9]/g, '');
                  if (digitos.length > 1) {
                    digitos.slice(0, 6).split('').forEach(function (digito, posicion) {
                      if (casillas[posicion]) casillas[posicion].value = digito;
                    });
                    casillas[Math.min(digitos.length, 6) - 1].focus();
                    return;
                  }
                  casilla.value = digitos.slice(0, 1);
                  if (casilla.value && casillas[indice + 1]) casillas[indice + 1].focus();
                });
                casilla.addEventListener('keydown', function (evento) {
                  if (evento.key === 'Backspace' && !casilla.value && casillas[indice - 1]) casillas[indice - 1].focus();
                  if (evento.key === 'ArrowLeft' && casillas[indice - 1]) casillas[indice - 1].focus();
                  if (evento.key === 'ArrowRight' && casillas[indice + 1]) casillas[indice + 1].focus();
                });
                casilla.addEventListener('paste', function (evento) {
                  const portapapeles = evento.clipboardData || window.clipboardData;
                  if (!portapapeles) return;
                  const pegado = portapapeles.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                  if (!pegado) return;
                  evento.preventDefault();
                  pegado.split('').forEach(function (digito, posicion) {
                    if (casillas[posicion]) casillas[posicion].value = digito;
                  });
                  casillas[Math.min(pegado.length, 5)].focus();
                });
              });
            })();
          </script>

        @elseif($stage === 'reset')
          <h1>Crear nueva contraseña</h1>
          <p>Elige una contraseña segura de al menos 8 caracteres.</p>
          <form action="{{ route('password.recovery.reset') }}" method="POST">
            @csrf
            <label for="clave">Nueva contraseña</label>
            <div class="campo-con-accion">
              <input id="clave" name="password" type="password" autocomplete="new-password" minlength="8" required>
              <button type="button" data-mostrar="clave" aria-label="Mostrar contraseña" aria-pressed="false">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              </button>
            </div>
            <label for="confirmar">Confirmar nueva contraseña</label>
            <div class="campo-con-accion">
              <input id="confirmar" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
              <button type="button" data-mostrar="confirmar" aria-label="Mostrar contraseña" aria-pressed="false">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              </button>
            </div>
            @if($errors->any())
              <p class="mensaje" role="alert">{{ $errors->first() }}</p>
            @endif
            <button class="boton principal ancho boton-guardar-contrasena" type="submit">Guardar nueva contraseña</button>
          </form>
          <script>
            document.querySelectorAll('[data-mostrar]').forEach(function (boton) {
              boton.addEventListener('click', function () {
                const campo = document.getElementById(boton.dataset.mostrar);
                const visible = campo.type === 'password';
                campo.type = visible ? 'text' : 'password';
                boton.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
                boton.setAttribute('aria-pressed', String(visible));
                boton.innerHTML = visible
                  ? '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18"></path><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path><path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6.4 0 10 7 10 7a16 16 0 0 1-3.1 3.8"></path><path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10.7 10.7 0 0 0 3-.4"></path></svg>'
                  : '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
              });
            });
          </script>

        @else
          <h1>Contraseña actualizada</h1>
          <p class="mensaje exito" role="status">Tu contraseña se ha actualizado correctamente</p>
          <a class="boton principal ancho" href="{{ route('login') }}" style="text-align:center">Volver a iniciar sesión</a>
        @endif
      </div>
    </section>
  </main>
</body>
</html>
