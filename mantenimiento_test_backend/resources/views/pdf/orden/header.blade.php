<table class="header-table">
    <tr>
        <td class="header-left-col">
            @if(file_exists(public_path('images/logo-final.png')))
                <img src="{{ public_path('images/logo-final.png') }}" class="brand-logo" alt="Logo">
            @endif
        </td>

        <td class="header-center-col">
            <h2 class="company-name">Orden Mantenimiento</h2>
            <h3 class="company-subtitle">Responsable: Mantenimiento</h3>
        </td>

        <td class="header-right-col">
            <table class="info-table-header">
                <tr class="info-row">
                    <td>
                        <span class="info-label">Folio / Orden:</span> 
                        <span class="order-id">#{{ $orden->order_folio ?? $orden->id_order }}</span>
                    </td>
                </tr>
                <tr class="info-row">
                    <td>
                        <span class="info-label">Fecha Reporte:</span> 
                        <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="header-divider"></div>