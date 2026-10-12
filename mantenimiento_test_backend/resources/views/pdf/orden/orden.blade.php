<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Mantenimiento #{{ $orden->id_order }}</title>
    @include('pdf.orden.styles')
</head>
<body>
    @include('pdf.orden.header')
    @include('pdf.orden.details')
    @include('pdf.orden.signature')
    @include('pdf.orden.footer')
</body>
</html>