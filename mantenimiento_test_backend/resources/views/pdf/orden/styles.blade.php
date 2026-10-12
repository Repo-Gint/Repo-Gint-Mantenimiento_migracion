<style>
    /* --- ESTILOS GENERALES Y CONFIGURACIÓN DOMPDF --- */
    @page {
        margin: 20px 25px 50px 25px;
    }
    
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 10px;
        color: #2c3e50;
        margin: 0;
        padding: 0;
    }

    /* --- ENCABEZADO (HEADER) --- */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        margin-bottom: 5px;
    }

    .header-left-col {
        width: 25%;
        text-align: left;
        vertical-align: middle;
    }

    .header-center-col {
        width: 45%;
        text-align: center;
        vertical-align: middle;
    }

    .header-right-col {
        width: 30%;
        text-align: right;
        vertical-align: middle;
    }

    .brand-logo {
        max-width: 150px;
        max-height: 55px;
        object-fit: contain;
    }

    .company-name {
        margin: 0 0 3px 0;
        font-size: 15px;
        font-weight: bold;
        text-transform: uppercase;
        color: #263B5E;
        letter-spacing: 0.5px;
    }

    .company-subtitle {
        margin: 0;
        font-size: 11px;
        font-weight: bold;
        color: #3F72AF;
        text-transform: uppercase;
    }

    .info-table-header {
        width: 100%;
        border-collapse: collapse;
    }

    .info-row {
        text-align: right;
        font-size: 10px;
    }

    .info-label {
        color: #3F72AF;
        font-weight: bold;
    }

    .order-id {
        font-size: 12px;
        font-weight: bold;
        color: #263B5E;
    }

    .header-divider {
        width: 100%;
        height: 3px;
        background-color: #5AB9A8;
        margin-top: 5px;
        margin-bottom: 12px;
        border-radius: 2px;
    }

    /* --- SECCIONES Y TABLA CUADRICULADA (FORMATO OFICIAL) --- */
    .section {
        margin-bottom: 12px;
        width: 100%;
    }

    .section-title {
        text-align: center;
        background: #263B5E; 
        color: #ffffff;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: bold;
        border: 1px solid #1c2b45;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Tabla con borde completo tipo formulario */
    .details-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        border: 1px solid #263B5E;
    }

    .details-table td {
        padding: 5px 8px;
        vertical-align: middle;
        border: 1px solid #b0bec5; 
        background-color: #ffffff;
    }

    /* Celda de etiqueta (Label) con fondo suave */
    .label {
        font-size: 9px;
        font-weight: bold;
        color: #263B5E;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .value {
    font-family: 'DejaVu Sans', sans-serif !important;
    font-size: 9.5px !important;
    color: #2c3e50 !important;
    font-weight: normal !important; /* Fuerza el peso normal sin negrita */
    line-height: 1.4;
    }

    /* Variación cuando quieres la etiqueta integrada en celda sombreada */
    .table-label-cell {
        background-color: #f0f4f8 !important;
        font-weight: bold;
        color: #263B5E;
        font-size: 9px;
        text-transform: uppercase;
        border: 1px solid #b0bec5;
    }

    /* --- ESTILOS DE FIRMAS (SIGNATURE) --- */
    .signature-container {
        margin-top: 60px;
        width: 100%;
    }

    .signature-table {
        width: 100%;
        border-collapse: collapse;
    }

    .signature-box {
    width: 100%;
    text-align: center;
    vertical-align: top;
    }

    .signature-line {
    border-top: 1px solid #333333;
    padding-top: 6px;
    width: 60%; 
    margin: 0 auto;
    }

    .signature-label {
    font-size: 11px;
    font-weight: bold;
    color: #1a365d;
    }

    .signature-name {
    font-size: 11px;
    font-weight: normal;
    color: #4a5568;
    margin-left: 4px;
    }

    /* --- FOOTER --- */
    .footer {
        position: fixed;
        bottom: -20px;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 8px;
        color: #555555;
        border-top: 1px solid #EAEAEA;
        padding-top: 5px;
    }

    .footer h1 {
        font-size: 8px;
        font-weight: normal;
        margin: 0 0 2px 0;
    }

    .footer-contact {
        font-weight: bold;
        font-size: 8px;
        color: #263B5E;
    }

    /* --- ESTILOS PARA EVIDENCIAS FOTOGRÁFICAS --- */
.evidence-gallery {
    width: 100%;
    margin-top: 5px;
}

.evidence-thumb {
    max-width: 180px;
    max-height: 140px;
    width: auto;
    height: auto;
    object-fit: contain;
    border: 1px solid #b0bec5;
    border-radius: 4px;
    padding: 3px;
    background-color: #ffffff;
}

.evidence-cell {
    text-align: center;
    vertical-align: middle;
    padding: 8px !important;
}

/* --- ENCABEZADOS DE TABLA UNIFICADA --- */
.section-title-cell {
    background: #263B5E;
    color: #ffffff;
    padding: 6px 10px;
    font-size: 10.5px;
    font-weight: bold;
    border: 1px solid #1c2b45;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: center;
}

.content-cell {
    vertical-align: top !important;
    padding: 8px !important;
    height: 120px; /* Asegura un área pareja para ambos lados */
}

.evidence-container-cell {
    text-align: center;
    vertical-align: middle !important;
}

.evidence-thumb-inline {
    max-width: 200px;
    max-height: 110px;
    width: auto;
    height: auto;
    object-fit: contain;
    border: 1px solid #b0bec5;
    border-radius: 4px;
    padding: 3px;
    background-color: #ffffff;
    display: inline-block;
    margin: 2px;
}

.value-muted {
    font-size: 10px;
    color: #777777;
    font-style: italic;
}

/* --- ENCABEZADOS DE TABLA UNIFICADA Y SECCIONES --- */
.section {
    margin-bottom: 12px;
    width: 100%;
}

.section-title {
    text-align: center;
    background: #263B5E; 
    color: #ffffff;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: bold;
    border: 1px solid #1c2b45;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.section-title-cell {
    background: #263B5E;
    color: #ffffff;
    padding: 6px 10px;
    font-size: 10px;
    font-weight: bold;
    border: 1px solid #1c2b45;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: center;
}

/* Variación verde corporativo para la sección de resolución */
.bg-success-header {
    background: #1b5e20 !important;
    border: 1px solid #0d3b10 !important;
}

/* --- TABLA DE DETALLES Y CUADRÍCULA --- */
.details-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    border: 1px solid #263B5E;
}

.details-table td {
    padding: 5px 8px;
    vertical-align: middle;
    border: 1px solid #b0bec5;
    background-color: #ffffff;
}

.content-cell {
    vertical-align: top !important;
    padding: 8px !important;
    height: 110px;
}

.content-cell-sm {
    vertical-align: top !important;
    padding: 8px !important;
    min-height: 40px;
}

/* --- ETIQUETAS Y VALORES --- */
.label {
    font-size: 8.5px;
    font-weight: bold !important;
    color: #263B5E;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.value {
    font-size: 10px;
    color: #111111;
    font-weight: 500;
    line-height: 1.3;
}

.value-muted {
    font-size: 9.5px;
    color: #777777;
    font-style: italic;
    text-align: center;
    margin-top: 35px;
}

.table-label-cell {
    background-color: #f0f4f8 !important;
    font-weight: bold;
    color: #263B5E;
    font-size: 9.5px;
    text-transform: uppercase;
    text-align: right;
    padding-right: 15px !important;
}

.cost-amount {
    font-size: 12px;
    font-weight: bold;
    color: #1b5e20;
}

/* --- IMÁGENES Y EVIDENCIAS --- */
.evidence-container-cell {
    text-align: center;
    vertical-align: middle !important;
}

.evidence-thumb-inline {
    max-width: 190px;
    max-height: 100px;
    width: auto;
    height: auto;
    object-fit: contain;
    border: 1px solid #b0bec5;
    border-radius: 4px;
    padding: 2px;
    background-color: #ffffff;
    display: inline-block;
    margin: 2px;
}

.border-success {
    border: 1px solid #2e7d32 !important;
}

.text-primary {
    color: #263B5E !important;
}

.fw-bold {
    font-weight: bold;
}

.text-right {
    text-align: right;
}
</style>