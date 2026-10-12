<style>
    /* Reset Base */
    body { margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
    .wrapper { width: 100%; background-color: #f8fafc; padding: 40px 10px; }
    
    /* Contenedor Principal */
    .main-card { max-width: 650px; width: 100%; margin: 0 auto; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; border-collapse: collapse; box-shadow: 0 8px 20px rgba(0,0,0,0.04); }

    /* Encabezado */
    .header { background-color: #4a65e6; padding: 25px 30px; border-top-left-radius: 12px; border-top-right-radius: 12px; }
    .header-subtitle { margin: 0; font-size: 11px; color: #e2e8f0; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; }
    .header-title { margin: 5px 0 0 0; color: #ffffff; font-size: 22px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase; }

    /* Estatus Badges */
    .badge-wrapper { border-radius: 20px; padding: 6px 16px; }
    .badge-text { font-weight: bold; font-size: 12px; text-transform: uppercase; }
    .bg-success { background-color: #22c55e; color: #ffffff; }
    .bg-warning { background-color: #f59e0b; color: #ffffff; }
    .bg-danger { background-color: #ef4444; color: #ffffff; }

    /* Textos Generales */
    .content-section { padding: 30px; }
    .greeting { margin: 0 0 10px 0; font-size: 17px; color: #1e293b; font-weight: bold; }
    .intro-text { margin: 0; font-size: 15px; color: #475569; line-height: 1.6; }
    .text-primary { color: #2563eb; font-weight: bold; }
    .text-dark { color: #0f172a; font-weight: bold; }

    /* Detalles Técnicos (Caja Gris) */
    .tech-box-container { padding: 0 30px 20px 30px; }
    .tech-box { background-color: #f1f5f9; padding: 20px; border-radius: 8px; width: 100%; }
    
    /* Tarjetas internas */
    .card-inner { background-color: #ffffff; padding: 18px; border-radius: 6px; border: 1px solid #e2e8f0; vertical-align: top; }
    .card-label { margin: 0 0 5px 0; font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase; }
    .card-value { margin: 0; font-size: 15px; color: #0f172a; font-weight: bold; }
    .card-value-lg { margin: 0; font-size: 18px; color: #0f172a; font-weight: 900; }
    
    .tag-approved { background-color: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 9px; margin-left: 5px; vertical-align: middle; }
    .text-iva { font-size: 11px; font-weight: normal; color: #94a3b8; }
    .text-content { background-color: #f8fafc; padding: 12px 15px; border-radius: 6px; font-size: 13.5px; color: #334155; line-height: 1.5; margin-top: 10px;}

    /* Banner Diagnóstico */
    .diag-container { padding: 0 30px 20px 30px; }
    .banner-diag { background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 15px 20px; width: 100%; }
    .diag-title { margin: 0; font-size: 13px; color: #166534; font-weight: bold; }
    .diag-text { margin: 4px 0 0 0; font-size: 12px; color: #15803d; }
    .diag-status { float: right; font-weight: 900; color: #14532d; letter-spacing: 1px;}

    /* Call to Action (ZONA DE BOTONES) */
    .cta-section { padding: 10px 30px 40px 30px; text-align: center; }
    .cta-card { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 40px 20px; background-color: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.03); }
    .cta-icon-wrapper { background-color: #e0e7ff; width: 50px; height: 50px; border-radius: 25px; margin: 0 auto 15px auto; line-height: 50px; text-align: center; }
    .cta-icon { color: #3730a3; font-size: 22px; font-weight: bold; }
    .cta-title { margin: 0 0 10px 0; font-size: 19px; color: #0f172a; font-weight: 900;}
    .cta-desc { margin: 0 0 25px 0; font-size: 14px; color: #64748b; line-height: 1.5; }

    /* ---------------------------------------------------- */
    /* BOTONES BULLETPROOF (La solución a tu imagen) */
    /* ---------------------------------------------------- */
    .btn-container { display: inline-block; margin: 0 8px; }
    
    /* El padding va en la celda (td), no en el enlace (a) */
    .btn-accept-cell { background-color: #2f7d32; border-radius: 6px; padding: 14px 28px; }
    .btn-accept-link { color: #ffffff !important; text-decoration: none; font-weight: bold; font-size: 14px; display: block; }
    
    .btn-reject-cell { background-color: #eef2ff; border-radius: 6px; padding: 14px 28px; border: 1px solid #dbeafe; }
    .btn-reject-link { color: #dc2626 !important; text-decoration: none; font-weight: bold; font-size: 14px; display: block; }
    /* ---------------------------------------------------- */

    /* Footer Sistema */
    .footer-system { padding: 0 30px 30px 30px; }
    .sys-card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px 20px; width: 100%; }
    .sys-text { font-size: 13px; color: #475569; }
    .sys-link { font-size: 13px; font-weight: bold; color: #2563eb; text-decoration: none; }

    /* Textos Legales */
    .legal-footer { max-width: 650px; margin: 25px auto 0 auto; text-align: left; padding: 0 10px; }
    .legal-text { font-size: 10px; color: #94a3b8; line-height: 1.6; }
    .legal-strong { color: #64748b; font-weight: bold; }
</style>