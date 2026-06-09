<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Empleo - {{ $company->company_name }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px solid #004b87; padding-bottom: 10px; margin-bottom: 20px; }
        .title { color: #004b87; font-size: 26px; font-weight: bold; }
        .section { margin-top: 25px; }
        .section-title { background: #f0f4f8; padding: 8px 12px; font-size: 16px; font-weight: bold; color: #004b87; border-left: 5px solid #004b87; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; font-size: 11px; }
        th { background-color: #f8fafc; color: #475569; text-transform: uppercase; }
        .stats-container { margin-top: 15px; width: 100%; }
        .stat-box { display: inline-block; width: 30%; border: 1px solid #e2e8f0; border-radius: 4px; padding: 15px; text-align: center; margin-right: 2%; }
        .stat-value { font-size: 22px; font-weight: bold; color: #004b87; }
        .stat-label { font-size: 10px; text-transform: uppercase; color: #64748b; margin-top: 5px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Bolsa de Empleo UNIPAZ</div>
        <div style="font-size: 14px; color: #64748b;">REPORTE DE GESTIÓN DE TALENTO HUMANO</div>
    </div>

    <div class="section">
        <div class="section-title">Información de la Organización</div>
        <p style="font-size: 13px;">
            <strong>Empresa:</strong> {{ $company->company_name }}<br>
            <strong>NIT:</strong> {{ $company->nit }}<br>
            <strong>Sector Económico:</strong> {{ $company->sector }}<br>
            <strong>Fecha de Emisión:</strong> {{ now()->translatedFormat('d \d\e F \d\e Y') }}
        </p>
    </div>

    <div class="section">
        <div class="section-title">Indicadores Clave</div>
        <div class="stats-container">
            <div class="stat-box">
                <div class="stat-value">{{ $stats['jobs_total'] }}</div>
                <div class="stat-label">Total de Vacantes</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $stats['applications_total'] }}</div>
                <div class="stat-label">Postulaciones Recibidas</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $stats['applications_accepted'] }}</div>
                <div class="stat-label">Candidatos Seleccionados</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detalle de Vacantes y Rendimiento</div>
        <table>
            <thead>
                <tr>
                    <th>Título de la Oferta</th>
                    <th>Estado</th>
                    <th style="text-align: center;">Postulaciones</th>
                    <th>Fecha Límite</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobsPerformance as $job)
                <tr>
                    <td style="font-weight: bold;">{{ $job->title }}</td>
                    <td>{{ $job->status == 'active' ? 'Activa' : ($job->status == 'paused' ? 'Pausada' : 'Cerrada') }}</td>
                    <td style="text-align: center;">{{ $job->applications_count }}</td>
                    <td>{{ $job->deadline ? date('d/m/Y', strtotime($job->deadline)) : 'Sin definir' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Documento generado automáticamente por el Portal de Empleo de la Universidad de la Paz (UNIPAZ).<br>
        Barrancabermeja, Colombia - {{ date('Y') }}
    </div>
</body>
</html>