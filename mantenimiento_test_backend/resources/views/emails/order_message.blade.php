<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Seguimiento de Orden</title>
    @include('emails.styles')
</head>
<body class="wrapper">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="wrapper">
        <tr>
            <td align="center">
                
                <!-- [if mso]>
                <table width="650" border="0" cellpadding="0" cellspacing="0" align="center"><tr><td>
                <![endif]-->
                
                <table border="0" cellpadding="0" cellspacing="0" class="main-card">

                    <!-- ENCABEZADO -->
                    <tr>
                        <td class="header" bgcolor="#4a65e6">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <p class="header-subtitle">Bitácora de Mantenimiento</p>
                                        <h2 class="header-title">
                                            Seguimiento de Orden #{{ $detalles['folio'] }}
                                        </h2>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- INTRODUCCIÓN -->
                    <tr>
                        <td class="content-section">
                            <p class="greeting">Hola,</p>
                            <p class="intro-text">
                                Tienes un nuevo mensaje respecto a la orden <span class="text-primary">{{ $detalles['folio'] }}</span>.
                            </p>
                        </td>
                    </tr>

                    <!-- BLOQUE DE METADATOS Y MENSAJE (Estructura Tech-Box Unificada) -->
                    <tr>
                        <td class="tech-box-container">
                            <table border="0" cellpadding="0" cellspacing="0" class="tech-box" bgcolor="#f1f5f9">
                                <tr>
                                    <td>
                                        <!-- Remitente y Fecha -->
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="48%" class="card-inner" bgcolor="#ffffff">
                                                    <p class="card-label">Remitente</p>
                                                    <p class="card-value">{{ $detalles['remitente'] }}</p>
                                                </td>
                                                <td width="4%"></td>
                                                <td width="48%" class="card-inner" bgcolor="#ffffff">
                                                    <p class="card-label">Fecha y Hora</p>
                                                    <p class="card-value">Hoy, {{ date('h:i A') }}</p>
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="height: 15px;"></div>

                                        <!-- Mensaje Registrado -->
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="card-inner" bgcolor="#ffffff">
                                            <tr>
                                                <td>
                                                    <p class="card-label" style="color:#2563eb;">Mensaje Registrado</p>
                                                    <div class="text-content">"{!! $detalles['content'] !!}"</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- BOTÓN DE ACCIÓN -->
                    <tr>
                        <td class="cta-section">
                            <table border="0" cellpadding="0" cellspacing="0" class="cta-card">
                                <tr>
                                    <td align="center">
                                        <p class="cta-title">¿Deseas dar seguimiento?</p>
                                        <p class="cta-desc">Accede a la plataforma para revisar los detalles completos de la orden.</p>
                                        <a href="{{ $detalles['url'] }}" target="_blank" class="btn-custom">
                                            Entrar a la Plataforma &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
                
                <!-- [if mso]>
                </td></tr></table>
                <![endif]-->

                <!-- FOOTER LEGAL EXTERNO -->
                <table border="0" cellpadding="0" cellspacing="0" class="legal-footer">
                    <tr>
                        <td class="legal-text">
                            Este correo fue generado automáticamente por el sistema de gestión de mantenimiento. Por favor, no respondas directamente a este mensaje.
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>