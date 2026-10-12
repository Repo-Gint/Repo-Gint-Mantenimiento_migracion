{{-- SECCIÓN 1: DATOS GENERALES DE LA ORDEN --}}
<div class="section">
    <div class="section-title">Datos de la Orden</div>

    <table class="details-table">
        <tr>
            <td width="50%">
                <div class="label">No. Orden</div>
                <div class="value fw-bold text-primary">#{{ $orden->order_folio ?? $orden->id_order }}</div>
            </td>
            <td width="50%">
                <div class="label">Prioridad</div>
                <div class="value fw-bold">{{ $orden->priority ?? 'N/A' }}</div>
            </td>
        </tr>
        <tr>
            <td width="50%">
                <div class="label">Tipo de Orden</div>
                <div class="value">{{ $orden->type_orders ?? 'N/A' }}</div>
            </td>
            <td width="50%">
                <div class="label">Departamento</div>
                <div class="value">{{ $orden->Departament_ES ?? 'N/A' }}</div>
            </td>
        </tr>
        <tr>
            <td width="50%">
                <div class="label">Solicitado Por</div>
                <div class="value">{{ $orden->Name ?? 'N/A' }}</div>
            </td>
            <td width="50%">
                <div class="label">Fecha de servicio</div>
                <div class="value">
                    @php
                        $fechaServicio = $orden->created_at ?? $orden->application ?? $orden->start_date ?? null;
                    @endphp
                    {{ $fechaServicio ? \Carbon\Carbon::parse($fechaServicio)->format('d/m/Y H:i') : 'N/A' }}
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- SECCIÓN 2: PROBLEMA Y EVIDENCIA INICIAL (LADO A LADO) --}}
<div class="section">
    <table class="details-table">
        <thead>
            <tr>
                <th width="50%" class="section-title-cell">Descripción del Problema</th>
                <th width="50%" class="section-title-cell">Evidencia Fotográfica Inicial</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="50%" class="content-cell">
                    <div class="value">{{ !empty($orden->problem_description) ? $orden->problem_description : 'Sin descripción registrada' }}</div>
                </td>
                <td width="50%" class="content-cell evidence-container-cell">
                    @if(!empty($evidencias) && count($evidencias) > 0)
                        @foreach($evidencias as $evidencia)
                            @php
                                $rawPath = parse_url($evidencia->url_evidence_image ?? '', PHP_URL_PATH);
                                $cleanPath = preg_replace('/^\/?storage\//', '', ltrim($rawPath, '/'));
                                
                                $possiblePaths = [
                                    storage_path('app/public/' . $cleanPath),
                                    public_path('storage/' . $cleanPath),
                                    public_path($cleanPath)
                                ];

                                $realPath = null;
                                foreach ($possiblePaths as $p) {
                                    if (file_exists($p) && is_file($p)) {
                                        $realPath = $p;
                                        break;
                                    }
                                }
                            @endphp
                            @if($realPath)
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($realPath)) }}" class="evidence-thumb-inline" alt="Evidencia Inicial">
                            @endif
                        @endforeach
                    @else
                        <div class="value-muted">Sin evidencia inicial adjunta</div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- SECCIÓN 3: RESOLUCIÓN DEL PROBLEMA E IMAGEN DE RESOLUCIÓN (LADO A LADO) --}}
@if(!empty($solucion) || (!empty($resoluciones) && count($resoluciones) > 0))
<div class="section">
    <table class="details-table">
        <thead>
            <tr>
                <th width="50%" class="section-title-cell bg-success-header">Resolución del Problema</th>
                <th width="50%" class="section-title-cell bg-success-header">Evidencia de Trabajos Concluidos</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="50%" class="content-cell">
                    <div class="value">
                        {{ !empty($solucion->resolution) ? $solucion->resolution : 'Sin detalle de resolución' }}
                    </div>
                </td>
                <td width="50%" class="content-cell evidence-container-cell">
                    @if(!empty($resoluciones) && count($resoluciones) > 0)
                        @foreach($resoluciones as $resImg)
                            @php
                                $rawResPath = parse_url($resImg->url_resolution_image ?? '', PHP_URL_PATH);
                                $cleanResPath = preg_replace('/^\/?storage\//', '', ltrim($rawResPath, '/'));

                                $possibleResPaths = [
                                    storage_path('app/public/' . $cleanResPath),
                                    public_path('storage/' . $cleanResPath),
                                    public_path($cleanResPath)
                                ];

                                $realResPath = null;
                                foreach ($possibleResPaths as $p) {
                                    if (file_exists($p) && is_file($p)) {
                                        $realResPath = $p;
                                        break;
                                    }
                                }
                            @endphp
                            @if($realResPath)
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($realResPath)) }}" class="evidence-thumb-inline border-success" alt="Evidencia Resolución">
                            @endif
                        @endforeach
                    @else
                        <div class="value-muted">Sin imágenes de resolución adjuntas</div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endif

{{-- SECCIÓN 4: REFACCIONES Y MATERIALES UTILIZADOS (DOS COLUMNAS) --}}
@if(!empty($solucion))
<div class="section">
    <table class="details-table">
        <thead>
            <tr>
                <th width="50%" class="section-title-cell">Refacciones (Spare Parts)</th>
                <th width="50%" class="section-title-cell">Materiales Utilizados</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="50%" class="content-cell-sm">
                    <div class="value">
                        {{ !empty($solucion->spare_parts) ? $solucion->spare_parts : 'N/A' }}
                    </div>
                </td>
                <td width="50%" class="content-cell-sm">
                    <div class="value">
                        {{ !empty($solucion->materials) ? $solucion->materials : 'N/A' }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- DESGLOSE DE COSTO Y MONEDA --}}
<div class="section">
    <table class="details-table">
        <tr>
            <td width="50%" class="table-label-cell">Costo Total de la Orden:</td>
            <td width="50%" class="text-right">
                <span class="cost-amount">
                    $ {{ number_format($solucion->order_cost ?? 0, 2) }} {{ $solucion->coin ?? 'MXN' }}
                </span>
            </td>
        </tr>
    </table>
</div>
@endif

{{-- SECCIÓN 5: CONCLUSIÓN / OBSERVACIONES --}}
@if(!empty($orden->conclusion))
<div class="section">
    <div class="section-title">Conclusión / Observaciones Finales</div>
    <table class="details-table">
        <tr>
            <td width="100%" class="content-cell-sm">
                <div class="value">{{ $orden->conclusion }}</div>
            </td>
        </tr>
    </table>
</div>
@endif