<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Casos — {{ now()->format('d/m/Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1f2937; background: #fff; padding: 30px; font-size: 13px; line-height: 1.5; }

        .header { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; padding: 28px 32px; border-radius: 12px; margin-bottom: 24px; }
        .header h1 { font-size: 22px; font-weight: 800; margin-bottom: 4px; }
        .header p { opacity: .85; font-size: 13px; }

        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
        .kpi-card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; text-align: center; }
        .kpi-card .value { font-size: 28px; font-weight: 900; color: #4f46e5; }
        .kpi-card .label { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; font-weight: 600; margin-top: 4px; }

        .section { margin-bottom: 24px; }
        .section-title { font-size: 15px; font-weight: 700; color: #374151; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb; }

        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        thead tr { background: #4f46e5; }
        thead th { color: white; padding: 10px 12px; text-align: left; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: .3px; }
        tbody td { padding: 9px 12px; border-bottom: 1px solid #f3f4f6; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody tr:hover { background: #eef2ff; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 700; }
        .badge-pendiente  { background: #fef3c7; color: #92400e; }
        .badge-en_proceso { background: #dbeafe; color: #1e40af; }
        .badge-resuelto   { background: #d1fae5; color: #065f46; }
        .badge-cerrado    { background: #f3f4f6; color: #4b5563; }

        .footer { margin-top: 30px; padding-top: 16px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 11px; color: #9ca3af; }

        @media print {
            body { padding: 15px; }
            .header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            thead tr { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Reporte de Casos — Violencia Escolar</h1>
    <p>Generado el {{ now()->format('d/m/Y H:i') }} · {{ $casos->count() }} registros</p>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="value">{{ $resumen['total'] }}</div>
        <div class="label">Total Casos</div>
    </div>
    <div class="kpi-card">
        <div class="value" style="color:#059669">{{ $resumen['tasa_resolucion'] }}%</div>
        <div class="label">Tasa Resolución</div>
    </div>
    <div class="kpi-card">
        <div class="value" style="color:#d97706">{{ $resumen['sin_asignar'] }}</div>
        <div class="label">Sin Asignar</div>
    </div>
    <div class="kpi-card">
        <div class="value" style="color:#7c3aed">{{ $resumen['anonimos'] }}</div>
        <div class="label">Anónimos</div>
    </div>
</div>

<div class="section">
    <div class="section-title">Detalle de Casos</div>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Anónimo</th>
                <th>Denunciante</th>
                <th>Asignado a</th>
                <th>Región</th>
                <th>Escuela</th>
                <th>Incidente</th>
                <th>Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($casos as $c)
                <tr>
                    <td style="font-family:monospace;font-weight:700;color:#4f46e5">{{ $c->codigo_caso }}</td>
                    <td>{{ $tipoLabel[$c->tipo_violencia] ?? $c->tipo_violencia }}</td>
                    <td>
                        <span class="badge badge-{{ $c->estado }}">
                            {{ $estadoLabel[$c->estado] ?? ucfirst($c->estado) }}
                        </span>
                    </td>
                    <td>{{ $c->es_anonimo ? 'Sí' : 'No' }}</td>
                    <td>{{ $c->es_anonimo ? 'Anónimo' : ($c->denunciante?->name ?? '—') }}</td>
                    <td>{{ $c->asignado?->name ?? 'Sin asignar' }}</td>
                    <td>{{ $c->region ?? '—' }}</td>
                    <td>{{ $c->escuela_nombre ?? '—' }}</td>
                    <td>{{ $c->fecha_incidente?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
    Sistema de Violencia Escolar · Reporte generado automáticamente · {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
