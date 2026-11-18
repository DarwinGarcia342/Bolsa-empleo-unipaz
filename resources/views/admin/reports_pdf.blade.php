<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte administrativo - Bolsa de Empleo UNIPAZ</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333; font-size: 11px; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px solid #004b87; padding-bottom: 10px; margin-bottom: 20px; }
        .title { color: #004b87; font-size: 22px; font-weight: bold; }
        .subtitle { color: #64748b; font-size: 12px; margin-top: 4px; }
        .section { margin-top: 22px; }
        .section-title { background: #f0f4f8; padding: 7px 10px; font-size: 13px; font-weight: bold; color: #004b87; border-left: 4px solid #004b87; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d8e0e8; padding: 7px 8px; text-align: left; }
        th { background: #f8fafc; color: #475569; }
        .stat-value { color: #004b87; font-size: 16px; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 7px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Bolsa de Empleo UNIPAZ</div>
        <div class="subtitle">REPORTE GENERAL DE ADMINISTRACIÓN</div>
        <div>Fecha de emisión: {{ now()->format('d/m/Y') }}</div>
    </div>

    <div class="section">
        <div class="section-title">Indicadores generales</div>
        <table>
            <tbody>
                <tr>
                    <th>Estudiantes registrados</th>
                    <td class="stat-value">{{ $stats['students_total'] }}</td>
                    <th>Empresas aprobadas</th>
                    <td class="stat-value">{{ $stats['companies_approved'] }}</td>
                </tr>
                <tr>
                    <th>Vacantes activas</th>
                    <td class="stat-value">{{ $stats['jobs_active'] }}</td>
                    <th>Vacantes cerradas</th>
                    <td class="stat-value">{{ $stats['jobs_closed'] }}</td>
                </tr>
                <tr>
                    <th>Postulaciones totales</th>
                    <td class="stat-value">{{ $stats['applications_total'] }}</td>
                    <th>Postulaciones aceptadas</th>
                    <td class="stat-value">{{ $stats['applications_accepted'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Empresas por estado</div>
        <table>
            <thead>
                <tr><th>Estado</th><th>Cantidad</th></tr>
            </thead>
            <tbody>
                @foreach(['approved' => 'Aprobadas', 'pending' => 'Pendientes', 'rejected' => 'Rechazadas'] as $key => $label)
                    <tr><td>{{ $label }}</td><td>{{ $companiesByStatus[$key] ?? 0 }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Postulaciones por estado</div>
        <table>
            <thead>
                <tr><th>Estado</th><th>Cantidad</th></tr>
            </thead>
            <tbody>
                @foreach([
                    'pending' => 'Pendientes',
                    'reviewed' => 'En revisión',
                    'interview' => 'Entrevistas',
                    'accepted' => 'Aceptadas',
                    'rejected' => 'No seleccionadas',
                ] as $key => $label)
                    <tr><td>{{ $label }}</td><td>{{ $applicationsByStatus[$key] ?? 0 }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Vacantes por área</div>
        <table>
            <thead>
                <tr><th>Área</th><th>Cantidad</th></tr>
            </thead>
            <tbody>
                @forelse($jobsByArea as $item)
                    <tr><td>{{ $item->area ?: 'Sin especificar' }}</td><td>{{ $item->total }}</td></tr>
                @empty
                    <tr><td colspan="2">No hay vacantes registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        Documento generado automáticamente por el Portal de Empleo de la Universidad de la Paz (UNIPAZ).
    </div>
</body>
</html>
