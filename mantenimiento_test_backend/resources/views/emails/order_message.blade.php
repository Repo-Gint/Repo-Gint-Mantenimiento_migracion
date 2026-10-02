<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Seguimiento de Orden</title>
    @include('emails.styles')
</head>
<body>
    <table class="email-wrapper" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="email-container" cellpadding="0" cellspacing="0">

                    <!-- Encabezado con estilo del proyecto -->
                    <tr>
                        <td class="header-cell">
                            <h2 class="header-title">
                                Seguimiento de Orden #{{ $detalles['folio'] }}
                            </h2>
                        </td>
                    </tr>

                    <!-- Mensaje de bienvenida -->
                    <tr>
                        <td style="padding: 20px 20px 10px 20px; font-size: 13px; color: #333333; line-height: 1.5;">
                            Hola, tienes un nuevo mensaje respecto a la orden <strong>{{ $detalles['folio'] }}</strong>.
                        </td>
                    </tr>

                    <!-- Tabla de Metadatos (Remitente y Fecha) -->
                    <tr>
                        <td style="padding: 10px 20px;">
                            <table class="data-table" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="data-cell">
                                        <strong>Remitente:</strong><br>
                                        {{ $detalles['remitente'] }}
                                    </td>
                                    <td class="data-cell">
                                        <strong>Fecha:</strong><br>
                                        Hoy, {{ date('h:i A') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Caja de Mensaje -->
                    <tr>
                        <td style="padding: 10px 20px 20px 20px;">
                            <table class="data-table" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="data-cell-full">
                                        <strong>Mensaje:</strong><br>
                                        <div style="font-style: italic; margin-top: 6px; color: #555555; line-height: 1.4;">
                                            "{!! $detalles['content'] !!}"
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Botón de Acción -->
                    <tr>
                        <td style="padding: 0 20px 30px 20px; text-align: center;">
                            <a href="{{ $detalles['url'] }}" target="_blank" class="btn-custom">
                                Entrar a la Plataforma &rarr;
                            </a>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>