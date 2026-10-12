<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; max-width: 400px; }
        
        /* Estilos de los iconos SVG */
        .icon-container { display: flex; justify-content: center; margin-bottom: 20px; }
        .icon-container svg { width: 64px; height: 64px; stroke-width: 2px; }
        .success { color: #5AB9A8; }
        .warning { color: #F2B84B; }
        
        h1 { color: #263B5E; font-size: 24px; margin-top: 0; }
        p { color: #475569; font-size: 16px; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-container {{ $tipo === 'success' ? 'success' : 'warning' }}">
            @if($tipo === 'success')
                <!-- Icono de Éxito (Check) -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            @else
                <!-- Icono de Alerta (Exclamación) -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z" />
                </svg>
            @endif
        </div>
        
        <h1>{{ $titulo }}</h1>
        <p>{{ $mensaje }}</p>
    </div>
</body>
</html>