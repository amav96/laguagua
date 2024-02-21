<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descarga tu informe</title>
</head>
<body>
    <p>Hola {{ isset($usuario->nombre) ? $usuario->nombre : $usuario->email }},</p>
    <p>Tu informe está listo para descargar. Haz clic en el siguiente enlace para descargarlo:</p>
    <p><a href="{{ $url }}">Descargar informe</a></p>
    <p>Gracias,</p>
    <p>Equipo de Tu Aplicación</p>
</body>
</html>