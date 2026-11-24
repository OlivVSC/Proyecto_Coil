<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Acceso restringido | MarsExplorer</title>
  <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
</head>
<body>
  <main class="acceso-formulario" style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px">
    <section class="panel-acceso" style="max-width:500px">
      <img src="{{ asset('img/logo-cohete-marsexplorer.png') }}" alt="MarsExplorer" style="max-width:200px;max-height:145px;object-fit:contain">
      <h1>Acceso restringido</h1>
      <p>Tu cuenta no tiene permiso para gestionar contenido. Puedes seguir leyendo artículos y misiones.</p>
      <a class="boton principal ancho" href="{{ route('dashboard') }}" style="display:inline-flex;justify-content:center">Volver al dashboard</a>
    </section>
  </main>
</body>
</html>
