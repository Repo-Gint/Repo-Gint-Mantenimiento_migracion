<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
     @include('styles')
</head>
<body>
    <div class="card">
        <div class="icon">
            @if($tipo === 'success') ✅ @else ⚠️ @endif
        </div>
        <h1>{{ $titulo }}</h1>
        <p>{{ $mensaje }}</p>
    </div>
</body>
</html>