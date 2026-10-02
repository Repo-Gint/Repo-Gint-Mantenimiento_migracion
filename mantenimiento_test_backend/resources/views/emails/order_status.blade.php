<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Orden Actualizado</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    
    <!-- Contenedor General del Correo -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                
                <!-- Tarjeta Principal Blanca (Ancho fijo de 600px para compatibilidad perfecta) -->
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);">
                    <tr>
                        <td style="padding: 40px 35px; text-align: center;">
                            
                            <!-- Icono o Encabezado Visual -->
                            <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin-bottom: 20px;">
                                <tr>
                                    <td align="center" style="width: 48px; height: 48px; background-color: #eff6ff; border-radius: 50%;">
                                        <span style="font-size: 20px; line-height: 48px;">🔔</span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Título Principal -->
                            <h1 style="color: #0f172a; font-size: 22px; font-weight: bold; margin: 0 0 15px 0; letter-spacing: -0.3px;">
                                Estado de Orden Actualizado
                            </h1>
                            
                            <!-- Mensaje de Estado -->
                            <p style="color: #475569; font-size: 15px; margin: 0 0 25px 0; line-height: 1.5;">
                                La orden <strong>{{ $data['folio'] }}</strong> ha cambiado de estatus a: <br><br>
                                @php
                                    $statusId = $data['status_id'] ?? 1;
                                    $badgeBg = '#e2e8f0';
                                    $badgeColor = '#1e293b';
                                    if($statusId == 3) { $badgeBg = '#d1fae5'; $badgeColor = '#065f46'; }
                                    elseif($statusId == 2) { $badgeBg = '#fef3c7'; $badgeColor = '#b45309'; }
                                    else { $badgeBg = '#fee2e2'; $badgeColor = '#991b1b'; }
                                @endphp
                                <span style="background-color: {{ $badgeBg }}; color: {{ $badgeColor }}; padding: 6px 16px; border-radius: 20px; font-weight: bold; font-size: 13px; display: inline-block; border: 1px solid rgba(0,0,0,0.05);">
                                    {{ $data['status'] }}
                                </span>
                            </p>

                            <!-- SI EXISTEN DATOS DE SOLUCIÓN (Estatus Finalizado) -->
                            @if(isset($data['solution']) && $data['solution'])
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; margin: 25px 0; text-align: left;">
                                    <tr>
                                        <td style="padding: 24px;">
                                            <h3 style="color: #0f172a; font-size: 13px; font-weight: bold; margin-top: 0; margin-bottom: 15px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; letter-spacing: 0.5px;">
                                                📋 Detalles de la Solución Aplicada
                                            </h3>
                                            
                                            @php
                                                $solution = $data['solution'];
                                                $coin = is_array($solution) ? ($solution['coin'] ?? '') : ($solution->coin ?? '');
                                                $cost = is_array($solution) ? ($solution['order_cost'] ?? '') : ($solution->order_cost ?? '');
                                                $resolution = is_array($solution) ? ($solution['resolution'] ?? '') : ($solution->resolution ?? '');
                                                $spareParts = is_array($solution) ? ($solution['spare_parts'] ?? '') : ($solution->spare_parts ?? '');
                                                $materials = is_array($solution) ? ($solution['materials'] ?? '') : ($solution->materials ?? '');
                                            @endphp

                                            @if(!empty($cost))
                                                <p style="font-size: 13px; color: #334155; margin: 6px 0;"><strong>Costo:</strong> <span style="color: #0f172a; font-weight: 600;">{{ $coin }} {{ number_format((float)$cost, 2) }}</span></p>
                                            @endif

                                            @if(!empty($resolution))
                                                <p style="font-size: 13px; color: #334155; margin: 6px 0;"><strong>Resolución:</strong> {!! nl2br(e($resolution)) !!}</p>
                                            @endif
                                            
                                            @if(!empty($spareParts))
                                                <p style="font-size: 13px; color: #334155; margin: 6px 0;"><strong>Refacciones:</strong> {!! nl2br(e($spareParts)) !!}</p>
                                            @endif

                                            @if(!empty($materials))
                                                <p style="font-size: 13px; color: #334155; margin: 6px 0;"><strong>Materiales:</strong> {!! nl2br(e($materials)) !!}</p>
                                            @endif
                                        </td>
                                    </tr>
                                </table>

                                <!-- BOTONES DE CONFORMIDAD (Aceptar / Rechazar) -->
                                @if(isset($data['url_aceptar']) && isset($data['url_rechazar']))
                                    <div style="text-align: center; margin: 30px 0; border-top: 1px solid #e2e8f0; padding-top: 25px;">
                                        <p style="font-size: 14px; font-weight: bold; color: #0f172a; margin-bottom: 15px;">
                                            ¿Está conforme con la solución brindada para esta orden?
                                        </p>
                                        
                                        <table border="0" cellpadding="0" cellspacing="0" align="center">
                                            <tr>
                                                <td style="padding-right: 10px;">
                                                    <a href="{{ $data['url_aceptar'] }}" target="_blank" style="background-color: #16a34a; color: #ffffff !important; padding: 11px 22px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px; display: inline-block;">
                                                        ✓ Sí, Aceptar Solución
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ $data['url_rechazar'] }}" target="_blank" style="background-color: #dc2626; color: #ffffff !important; padding: 11px 22px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px; display: inline-block;">
                                                        ✕ No, Rechazar / Reportar
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                @endif
                            @endif

                            <!-- ENLACE A LA PLATAFORMA (Apunta por defecto al login de pruebas) -->
                            <div style="margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                                <p style="color: #64748b; font-size: 13px; margin-bottom: 15px;">Puedes revisar los detalles completos ingresando al sistema.</p>
                                
                                <table border="0" cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td align="center" style="border-radius: 6px; background-color: #2563eb;">
                                            <a href="{{ $data['url'] ?? 'http://localhost:4200/login' }}" target="_blank" style="font-size: 13px; font-weight: 600; color: #ffffff !important; text-decoration: none !important; padding: 11px 26px; border-radius: 6px; display: inline-block;">
                                                Ver Orden en la Plataforma &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>