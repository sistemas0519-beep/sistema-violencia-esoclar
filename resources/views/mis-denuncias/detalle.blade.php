<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detalle Denuncia {{ $caso->codigo_caso }} — Sistema de Violencia Escolar</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-dark:      #0d0f14;
            --bg-card:      #141720;
            --bg-card-alt:  #1a1f2e;
            --border:       rgba(255,255,255,0.07);
            --border-hover: rgba(255,255,255,0.14);
            --text-primary: #f0f2f8;
            --text-muted:   #8b92a9;
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
        }

        .bg-glow {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background:
                radial-gradient(ellipse 70% 40% at 15% -5%, rgba(99,102,241,0.1) 0%, transparent 55%),
                radial-gradient(ellipse 50% 35% at 85% 100%, rgba(139,92,246,0.08) 0%, transparent 55%);
        }

        /* ── Header ── */
        header {
            position: sticky; top: 0; z-index: 50;
            background: rgba(13,15,20,0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 0 1.5rem;
        }
        .header-inner {
            max-width: 860px; margin: 0 auto;
            display: flex; align-items: center; justify-content: space-between;
            height: 60px;
        }
        .back-btn {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .4rem .9rem; border-radius: 8px;
            border: 1px solid var(--border); background: transparent;
            color: var(--text-muted); font-size: .82rem; font-weight: 600;
            text-decoration: none; transition: all .2s;
        }
        .back-btn:hover { border-color: var(--border-hover); color: var(--text-primary); }
        .header-code {
            font-family: 'Courier New', monospace;
            font-size: .85rem; font-weight: 700; color: #818cf8;
            background: rgba(99,102,241,.1); border: 1px solid rgba(99,102,241,.2);
            padding: .25rem .7rem; border-radius: 7px;
        }
        .btn-logout {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .4rem .9rem; border-radius: 8px;
            border: 1px solid var(--border); background: transparent;
            color: var(--text-muted); font-size: .8rem; font-weight: 600;
            cursor: pointer; font-family: inherit; transition: all .2s;
        }
        .btn-logout:hover { border-color: var(--border-hover); color: var(--text-primary); }

        /* ── Content ── */
        main {
            position: relative; z-index: 1;
            max-width: 860px; margin: 0 auto;
            padding: 2.5rem 1.5rem;
        }

        /* ── Hero card ── */
        .hero-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 20px; padding: 2rem; margin-bottom: 1.5rem;
        }
        .hero-top {
            display: flex; align-items: flex-start; justify-content: space-between;
            flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;
        }
        .hero-code {
            font-family: 'Courier New', monospace;
            font-size: 1.35rem; font-weight: 900; letter-spacing: .04em;
            color: #818cf8;
        }
        .badges-row { display: flex; gap: .5rem; flex-wrap: wrap; }

        .badge {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .22rem .75rem; border-radius: 999px;
            font-size: .72rem; font-weight: 700;
        }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
        .badge-pendiente  { background: rgba(245,158,11,.12); color: #fbbf24; border: 1px solid rgba(245,158,11,.25); }
        .badge-en_proceso { background: rgba(59,130,246,.12);  color: #60a5fa; border: 1px solid rgba(59,130,246,.25); }
        .badge-resuelto   { background: rgba(16,185,129,.12);  color: #34d399; border: 1px solid rgba(16,185,129,.25); }
        .badge-cerrado    { background: rgba(107,114,128,.12); color: #9ca3af; border: 1px solid rgba(107,114,128,.25); }
        .dot-pendiente  { background: #fbbf24; }
        .dot-en_proceso { background: #60a5fa; animation: pulse 1.5s infinite; }
        .dot-resuelto   { background: #34d399; }
        .dot-cerrado    { background: #9ca3af; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

        .badge-urgente { background: rgba(239,68,68,.12); color: #f87171; border: 1px solid rgba(239,68,68,.25); }
        .badge-alta    { background: rgba(245,158,11,.12); color: #fbbf24; border: 1px solid rgba(245,158,11,.25); }
        .badge-media   { background: rgba(59,130,246,.12);  color: #60a5fa; border: 1px solid rgba(59,130,246,.25); }
        .badge-baja    { background: rgba(107,114,128,.12); color: #9ca3af; border: 1px solid rgba(107,114,128,.25); }

        /* ── Estado descriptivo ── */
        .estado-bar {
            display: flex; align-items: center; gap: .75rem;
            padding: .85rem 1.1rem; border-radius: 12px;
            margin-bottom: 1.5rem;
        }
        .estado-bar.pendiente  { background: rgba(245,158,11,.06); border: 1px solid rgba(245,158,11,.15); }
        .estado-bar.en_proceso { background: rgba(59,130,246,.06);  border: 1px solid rgba(59,130,246,.15); }
        .estado-bar.resuelto   { background: rgba(16,185,129,.06);  border: 1px solid rgba(16,185,129,.15); }
        .estado-bar.cerrado    { background: rgba(107,114,128,.06); border: 1px solid rgba(107,114,128,.15); }
        .estado-bar-icon { font-size: 1.25rem; flex-shrink: 0; }
        .estado-bar-text { font-size: .875rem; color: var(--text-muted); line-height: 1.5; }
        .estado-bar-text strong { color: var(--text-primary); }

        /* ── Info grid ── */
        .info-grid {
            display: grid; grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        @media (max-width: 520px) { .info-grid { grid-template-columns: 1fr; } }

        .info-item { display: flex; flex-direction: column; gap: .25rem; }
        .info-label {
            font-size: .68rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--text-muted);
        }
        .info-value { font-size: .9rem; color: var(--text-primary); font-weight: 600; }

        /* ── Section ── */
        .section { margin-bottom: 1.5rem; }
        .section-title {
            font-size: .7rem; font-weight: 800; letter-spacing: .1em;
            text-transform: uppercase; color: var(--text-muted);
            margin-bottom: .85rem; display: flex; align-items: center; gap: .5rem;
        }
        .section-title::after { content: ''; flex: 1; height: 1px; background: var(--border); }

        /* ── Description card ── */
        .desc-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;
        }
        .desc-text {
            font-size: .9rem; color: var(--text-muted); line-height: 1.75;
            white-space: pre-wrap;
        }

        /* ── Professional card ── */
        .prof-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;
        }
        .prof-row {
            display: flex; align-items: center; gap: .85rem;
        }
        .prof-avatar {
            width: 46px; height: 46px; border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #0d9488);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 800; color: #fff; flex-shrink: 0;
        }
        .prof-name { font-size: .95rem; font-weight: 800; margin-bottom: .2rem; }
        .prof-role { font-size: .78rem; color: var(--text-muted); text-transform: capitalize; }
        .prof-spec {
            display: inline-flex; align-items: center; gap: .35rem;
            margin-top: .4rem; font-size: .75rem; color: #60a5fa;
            background: rgba(59,130,246,.08); border: 1px solid rgba(59,130,246,.2);
            padding: .2rem .6rem; border-radius: 999px;
        }
        .no-prof {
            display: flex; align-items: center; gap: .75rem;
            padding: .85rem 1rem;
            background: rgba(245,158,11,.04); border: 1px solid rgba(245,158,11,.1);
            border-radius: 12px; font-size: .875rem; color: var(--text-muted);
        }
        .no-prof-icon { font-size: 1.25rem; }

        /* ── Timeline ── */
        .seguimientos-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 16px; padding: 1.5rem;
        }
        .timeline { position: relative; padding-left: 1.5rem; }
        .timeline::before {
            content: ''; position: absolute; left: .4rem; top: .3rem; bottom: .3rem;
            width: 2px; background: var(--border); border-radius: 2px;
        }
        .timeline-item {
            position: relative; padding-bottom: 1.5rem;
        }
        .timeline-item:last-child { padding-bottom: 0; }
        .timeline-dot {
            position: absolute; left: -1.2rem; top: .3rem;
            width: 10px; height: 10px; border-radius: 50%;
            background: #6366f1; border: 2px solid var(--bg-dark);
            box-shadow: 0 0 0 2px rgba(99,102,241,.4);
        }
        .timeline-date {
            font-size: .7rem; font-weight: 700; letter-spacing: .05em;
            text-transform: uppercase; color: var(--text-muted); margin-bottom: .3rem;
        }
        .timeline-accion {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .2rem .65rem; border-radius: 999px;
            font-size: .7rem; font-weight: 700;
            background: rgba(99,102,241,.1); border: 1px solid rgba(99,102,241,.25);
            color: #818cf8; margin-bottom: .4rem;
        }
        .timeline-notas {
            font-size: .85rem; color: var(--text-muted); line-height: 1.65;
        }
        .timeline-by { font-size: .72rem; color: var(--text-muted); margin-top: .25rem; }

        .empty-timeline {
            text-align: center; padding: 2rem 1rem;
            font-size: .875rem; color: var(--text-muted);
        }
        .empty-timeline-icon { font-size: 2rem; margin-bottom: .5rem; }

        /* ── Dates ── */
        .dates-row {
            display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem;
            padding-top: 1.25rem; border-top: 1px solid var(--border);
        }
        .date-item { display: flex; align-items: center; gap: .4rem; font-size: .78rem; color: var(--text-muted); }
        .date-item svg { color: #818cf8; flex-shrink: 0; }
    </style>
</head>
<body>
<div class="bg-glow"></div>

<!-- Header -->
<header>
    <div class="header-inner">
        <a href="{{ route('mis-denuncias.lista') }}" class="back-btn">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Mis Denuncias
        </a>

        <span class="header-code">{{ $caso->codigo_caso }}</span>

        <form method="POST" action="{{ route('mis-denuncias.salir') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Salir
            </button>
        </form>
    </div>
</header>

<main>

    @php
        $tipoLabels = [
            'fisica'        => 'Física',
            'psicologica'   => 'Psicológica',
            'verbal'        => 'Verbal',
            'sexual'        => 'Sexual',
            'ciberacoso'    => 'Ciberacoso',
            'discriminacion'=> 'Discriminación',
            'bullying'      => 'Bullying',
            'cyberbullying' => 'Cyberbullying',
            'otro'          => 'Otro',
        ];
        $estadoLabels = [
            'pendiente'  => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'resuelto'   => 'Resuelto',
            'cerrado'    => 'Cerrado',
        ];
        $estadoIcons = [
            'pendiente'  => '⏳',
            'en_proceso' => '🔄',
            'resuelto'   => '✅',
            'cerrado'    => '🔒',
        ];
        $estadoDescLarga = [
            'pendiente'  => 'Tu denuncia ha sido recibida y está en espera de ser asignada a un profesional. Te notificaremos en cuanto haya novedades.',
            'en_proceso' => 'Un profesional de apoyo está atendiendo activamente tu caso. Se registrarán seguimientos a medida que avance la atención.',
            'resuelto'   => 'Tu caso ha sido atendido satisfactoriamente. El equipo de apoyo marcó el expediente como resuelto.',
            'cerrado'    => 'El expediente ha sido cerrado por el equipo administrativo.',
        ];
        $accionLabels = [
            'primera_atencion' => 'Primera Atención',
            'seguimiento'      => 'Seguimiento',
            'cierre'           => 'Cierre',
            'escalado'         => 'Escalado',
            'derivacion'       => 'Derivación',
        ];
    @endphp

    <!-- Hero card -->
    <div class="hero-card">
        <div class="hero-top">
            <span class="hero-code">{{ $caso->codigo_caso }}</span>
            <div class="badges-row">
                <span class="badge badge-{{ $caso->estado }}">
                    <span class="badge-dot dot-{{ $caso->estado }}"></span>
                    {{ $estadoLabels[$caso->estado] ?? $caso->estado }}
                </span>
                <span class="badge badge-{{ $caso->prioridad }}">
                    Prioridad {{ ucfirst($caso->prioridad) }}
                </span>
                @if($caso->es_anonimo)
                    <span class="badge" style="background:rgba(107,114,128,.12); color:#9ca3af; border:1px solid rgba(107,114,128,.25);">Anónimo</span>
                @endif
            </div>
        </div>

        <!-- Estado descriptivo -->
        <div class="estado-bar {{ $caso->estado }}">
            <span class="estado-bar-icon">{{ $estadoIcons[$caso->estado] ?? '📋' }}</span>
            <span class="estado-bar-text">
                <strong>{{ $estadoLabels[$caso->estado] ?? $caso->estado }}:</strong>
                {{ $estadoDescLarga[$caso->estado] ?? '' }}
            </span>
        </div>

        <!-- Info grid -->
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Tipo de Violencia</span>
                <span class="info-value">{{ $tipoLabels[$caso->tipo_violencia] ?? $caso->tipo_violencia }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Fecha del Incidente</span>
                <span class="info-value">
                    {{ $caso->fecha_incidente ? $caso->fecha_incidente->format('d/m/Y') : 'No especificada' }}
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Institución Educativa</span>
                <span class="info-value">{{ $caso->escuela_nombre ?? '—' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Ubicación</span>
                <span class="info-value">
                    {{ implode(', ', array_filter([$caso->distrito, $caso->provincia, $caso->region])) ?: '—' }}
                </span>
            </div>
            @if($caso->codigo_modular)
                <div class="info-item">
                    <span class="info-label">Código Modular</span>
                    <span class="info-value">{{ $caso->codigo_modular }}</span>
                </div>
            @endif
            @if($caso->sla_limite)
                <div class="info-item">
                    <span class="info-label">Plazo de Atención</span>
                    <span class="info-value" style="{{ $caso->sla_vencido ? 'color:#f87171;' : 'color:#34d399;' }}">
                        {{ $caso->sla_vencido ? '⚠️ Vencido' : $caso->sla_limite->format('d/m/Y H:i') }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Timestamps -->
        <div class="dates-row">
            <span class="date-item">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Registrado: {{ $caso->created_at->format('d/m/Y \a\l\a\s H:i') }}
            </span>
            <span class="date-item">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Última actualización: {{ $caso->updated_at->diffForHumans() }}
            </span>
        </div>
    </div>

    <!-- Descripción -->
    @if($caso->descripcion)
        <div class="desc-card">
            <div class="section-title">Descripción del Caso</div>
            <p class="desc-text">{{ $caso->descripcion }}</p>
        </div>
    @endif

    <!-- Profesional asignado -->
    <div class="prof-card">
        <div class="section-title">Profesional Asignado</div>
        @if($caso->asignado)
            <div class="prof-row">
                <div class="prof-avatar">{{ strtoupper(substr($caso->asignado->name, 0, 1)) }}</div>
                <div>
                    <div class="prof-name">{{ $caso->asignado->name }}</div>
                    <div class="prof-role">{{ ucfirst($caso->asignado->rol) }}</div>
                    @if($caso->asignado->especialidad)
                        <span class="prof-spec">
                            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            {{ $caso->asignado->especialidad }}
                        </span>
                    @endif
                </div>
            </div>
        @else
            <div class="no-prof">
                <span class="no-prof-icon">⏳</span>
                <span>Tu caso aún no ha sido asignado a un profesional. Será asignado en breve según la prioridad del caso.</span>
            </div>
        @endif
    </div>

    <!-- Seguimientos / Historial -->
    <div class="seguimientos-card">
        <div class="section-title">Historial de Seguimiento</div>

        @if($caso->seguimientos->isNotEmpty())
            <div class="timeline">
                @foreach($caso->seguimientos as $seguimiento)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-date">
                            {{ $seguimiento->fecha_seguimiento
                                ? (is_string($seguimiento->fecha_seguimiento)
                                    ? \Carbon\Carbon::parse($seguimiento->fecha_seguimiento)->format('d/m/Y H:i')
                                    : $seguimiento->fecha_seguimiento->format('d/m/Y H:i'))
                                : $seguimiento->created_at->format('d/m/Y H:i') }}
                        </div>
                        @if($seguimiento->accion)
                            <span class="timeline-accion">
                                {{ $accionLabels[$seguimiento->accion] ?? ucfirst($seguimiento->accion) }}
                            </span>
                        @endif
                        @if($seguimiento->notas)
                            <p class="timeline-notas">{{ $seguimiento->notas }}</p>
                        @endif
                        @if($seguimiento->responsable)
                            <p class="timeline-by">Por: {{ $seguimiento->responsable->name }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-timeline">
                <div class="empty-timeline-icon">📝</div>
                <p>Aún no hay registros de seguimiento para este caso.</p>
                <p style="margin-top:.3rem; font-size:.78rem; opacity:.7;">Los seguimientos se añadirán a medida que el profesional intervenga.</p>
            </div>
        @endif
    </div>

</main>
</body>
</html>
