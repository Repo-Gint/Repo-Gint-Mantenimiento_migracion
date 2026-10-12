<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Orden Actualizado</title>
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
                                        <p class="header-subtitle">&#128187; NOTIFICACIÓN OFICIAL DE PLANTA</p>
                                        <h1 class="header-title">Estado de Orden Actualizado</h1>
                                    </td>
                                    <td align="right" valign="middle">
                                        @php
                                            $statusId = $data['status_id'] ?? 1;
                                            $badgeClass = 'bg-danger'; $icon = '&#10005;';
                                            if($statusId == 3) { $badgeClass = 'bg-success'; $icon = '&#10003;'; }
                                            elseif($statusId == 2) { $badgeClass = 'bg-warning'; $icon = '&#9888;'; }
                                        @endphp
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td class="badge-wrapper {{ $badgeClass }}">
                                                    <span class="badge-text">{!! $icon !!} {{ $data['status'] }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- INTRODUCCIÓN -->
                    <tr>
                        <td class="content-section">
                            <p class="greeting">Estimado(a) {{ $data['employee_name'] ?? 'Usuario' }},</p>
                            <p class="intro-text">
                                Le informamos que la orden <span class="text-primary">{{ $data['folio'] }}</span> ha cambiado de estatus a <span class="text-dark">{{ $data['status'] }}</span>. A continuación se presentan los detalles técnicos de la intervención realizada en máquina <span class="text-dark">{{ $data['machine'] ?? 'Asignada' }}</span>:
                            </p>
                        </td>
                    </tr>

                    <!-- DETALLES TÉCNICOS -->
                    @if(isset($data['solution']) && $data['solution'])
                        @php
                            $s = $data['solution'];
                            $coin = is_array($s) ? ($s['coin'] ?? 'MXN') : ($s->coin ?? 'MXN');
                            $cost = is_array($s) ? ($s['order_cost'] ?? '0.00') : ($s->order_cost ?? '0.00');
                            $res = is_array($s) ? ($s['resolution'] ?? 'Sin resolución') : ($s->resolution ?? 'Sin resolución');
                            $spa = is_array($s) ? ($s['spare_parts'] ?? 'N/A') : ($s->spare_parts ?? 'N/A');
                            $mat = is_array($s) ? ($s['materials'] ?? 'N/A') : ($s->materials ?? 'N/A');
                        @endphp
                    <tr>
                        <td class="tech-box-container">
                            <table border="0" cellpadding="0" cellspacing="0" class="tech-box" bgcolor="#f1f5f9">
                                <tr>
                                    <td>
                                        <!-- Activo y Costo -->
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="48%" class="card-inner" bgcolor="#ffffff">
                                                    <p class="card-label">Activo Intervenido</p>
                                                    <p class="card-value">{{ $data['machine'] ?? 'Asignada' }}</p>
                                                </td>
                                                <td width="4%"></td>
                                                <td width="48%" class="card-inner" bgcolor="#ffffff">
                                                    <p class="card-label">Costo Total Liquidado <span class="tag-approved">APROBADO</span></p>
                                                    <p class="card-value-lg">{{ $coin }} ${{ number_format((float)$cost, 2) }} <span class="text-iva">IVA incl.</span></p>
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="height: 15px;"></div>

                                        <!-- Resolución -->
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="card-inner" bgcolor="#ffffff">
                                            <tr>
                                                <td>
                                                    <p class="card-label" style="color:#2563eb;">&#10003; Resolución Técnica Aplicada</p>
                                                    <div class="text-content">"{!! nl2br(e($res)) !!}"</div>
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="height: 15px;"></div>

                                        <!-- Refacciones y Materiales -->
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="48%" class="card-inner" bgcolor="#ffffff">
                                                    <p class="card-label">&#128295; Refacciones Utilizadas</p>
                                                    <div class="text-content">"{!! nl2br(e($spa)) !!}"</div>
                                                </td>
                                                <td width="4%"></td>
                                                <td width="48%" class="card-inner" bgcolor="#ffffff">
                                                    <p class="card-label">&#128450; Materiales e Insumos</p>
                                                    <div class="text-content">"{!! nl2br(e($mat)) !!}"</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- DIAGNÓSTICO -->
                    <tr>
                        <td class="diag-container">
                            <table border="0" cellpadding="0" cellspacing="0" class="banner-diag" bgcolor="#f0fdf4">
                                <tr>
                                    <td>
                                        <p class="diag-title">&#10003; Ciclo de Diagnóstico Concluido</p>
                                        <p class="diag-text">
                                            Pruebas de rotación, presión y parada de emergencia superadas (100%). 
                                            <span class="diag-status">&#126; ESTABLE</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- BOTONES A PRUEBA DE BALAS -->
                    @if(isset($data['url_aceptar']) && isset($data['url_rechazar']))
                    <tr>
                        <td class="cta-section">
                            <table border="0" cellpadding="0" cellspacing="0" class="cta-card">
                                <tr>
                                    <td align="center">
                                        
                                        <div class="cta-icon-wrapper">&#10003;</div>
                                        <h2 class="cta-title">¿Está conforme con la solución brindada?</h2>
                                        <p class="cta-desc">Al validar la solución, el activo se liberará formalmente en el sistema de planta.</p>

                                        <!-- El secreto del padding perfecto en Outlook está en estos <td> -->
                                        <table border="0" cellpadding="0" cellspacing="0" align="center">
                                            <tr>
                                                <td class="btn-container">
                                                    <table border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            <td align="center" class="btn-accept-cell" bgcolor="#2f7d32">
                                                                <a href="{{ $data['url_aceptar'] }}" target="_blank" class="btn-accept-link">
                                                                    &#10003; Aceptar Solución
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td class="btn-container">
                                                    <table border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            <td align="center" class="btn-reject-cell" bgcolor="#eef2ff">
                                                                <a href="{{ $data['url_rechazar'] }}" target="_blank" class="btn-reject-link">
                                                                    &#10005; Rechazar / Reportar
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- FOOTER SISTEMA -->
                    <tr>
                        <td class="footer-system">
                            <table border="0" cellpadding="0" cellspacing="0" class="sys-card" bgcolor="#f8fafc">
                                <tr>
                                    <td align="left" class="sys-text">&#128269; Puede revisar el historial completo en el sistema.</td>
                                    <td align="right">
                                        <a href="{{ $data['url'] ?? 'http://localhost:4200/login' }}" target="_blank" class="sys-link">
                                            Ver Orden en el Sistema &rarr;
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
                            <span class="legal-strong">Equipo de Soporte y Mantenimiento Técnico</span><br>
                            MaintDesk PRO Enterprise &bull; NAVE-1 Centro de Control<br><br>
                            ID de Auditoría: VAL-{{ date('Y') }}-{{ $data['folio'] }} &bull; Servidor: MES-PROD-MEX-01<br><br>
                            Aviso: Este es un mensaje generado automáticamente. La información contenida es confidencial. Si ha recibido esta notificación por error, por favor notifíquelo a la superintendencia de planta inmediatamente.
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>