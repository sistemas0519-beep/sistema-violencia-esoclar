<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consultar Expediente — Sistema de Violencia Escolar</title>
    <meta name="description" content="Consulta el estado de tu expediente ingresando tu número de caso o tu nombre completo.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-dark:      #0d0f14;
            --bg-card:      #141720;
            --bg-card-hover:#1a1f2e;
            --border:       rgba(255,255,255,0.07);
            --border-hover: rgba(255,255,255,0.15);
            --text-primary: #f0f2f8;
            --text-muted:   #8b92a9;
            --accent:       #6366f1;
            --accent-glow:  rgba(99,102,241,0.25);
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .bg-glow {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% -10%, rgba(99,102,241,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 110%, rgba(139,92,246,0.10) 0%, transparent 60%);
        }

        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 2rem;
            background: rgba(13,15,20,0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }
        .nav-brand {
            display: flex; align-items: center; gap: .65rem;
            font-weight: 700; font-size: 1rem; color: var(--text-primary);
            text-decoration: none;
        }
        .nav-brand-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
        }
        .nav-actions { display: flex; gap: .75rem; }
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .5rem 1.25rem; border-radius: 8px;
            font-size: .875rem; font-weight: 500;
            text-decoration: none; transition: all .2s ease; cursor: pointer; border: none;
        }
        .btn-ghost {
            color: var(--text-muted); background: transparent; border: 1px solid var(--border);
        }
        .btn-ghost:hover { color: var(--text-primary); border-color: var(--border-hover); background: rgba(255,255,255,0.04); }
        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: 1px solid rgba(99,102,241,0.5);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.35); }

        .page-wrap {
            position: relative; z-index: 1;
            min-height: 100vh;
            padding: 7rem 1.5rem 4rem;
            max-width: 720px;
            margin: 0 auto;
        }

        /* ── Page header ── */
        .page-badge {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .3rem .9rem; border-radius: 999px;
            background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.3);
            color: #818cf8; font-size: .78rem; font-weight: 600;
            letter-spacing: .05em; text-transform: uppercase;
            margin-bottom: 1.25rem;
        }
        .page-title {
            font-size: clamp(1.75rem, 5vw, 2.5rem);
            font-weight: 900; letter-spacing: -.03em; line-height: 1.15;
            margin-bottom: .75rem;
        }
        .page-title span {
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .page-sub {
            color: var(--text-muted); font-size: .95rem; line-height: 1.7;
            max-width: 560px; margin-bottom: 2.5rem;
        }

        /* ── Search card ── */
        .search-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .tipo-tabs {
            display: flex; gap: .5rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: 12px; padding: .3rem;
            margin-bottom: 1.5rem;
        }
        .tipo-tab {
            flex: 1; padding: .5rem 1rem;
            border: none; background: transparent;
            color: var(--text-muted); font-size: .875rem; font-weight: 500;
            border-radius: 9px; cursor: pointer;
            transition: all .2s;
        }
        .tipo-tab.active {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff; box-shadow: 0 2px 10px rgba(99,102,241,0.3);
        }
        .tipo-tab:hover:not(.active) { color: var(--text-primary); background: rgba(255,255,255,0.06); }

        .search-label {
            display: block; font-size: .825rem; font-weight: 600;
            color: var(--text-muted); letter-spacing: .04em; text-transform: uppercase;
            margin-bottom: .6rem;
        }
        .search-input-wrap {
            position: relative; display: flex; gap: .75rem; align-items: stretch;
        }
        .search-input {
            flex: 1;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: .8rem 1.1rem;
            color: var(--text-primary);
            font-size: .95rem; font-family: inherit;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .search-input::placeholder { color: var(--text-muted); }
        .search-input:focus { border-color: rgba(99,102,241,0.5); box-shadow: 0 0 0 3px rgba(99,102,241,0.12); }
        .search-btn {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .8rem 1.5rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff; border: none; border-radius: 12px;
            font-size: .875rem; font-weight: 600; cursor: pointer;
            transition: transform .2s, box-shadow .2s;
            white-space: nowrap;
        }
        .search-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.35); }

        .hint-text {
            margin-top: .6rem; font-size: .78rem; color: var(--text-muted); line-height: 1.5;
        }

        .error-msg {
            margin-top: .75rem; padding: .7rem 1rem;
            background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2);
            border-radius: 10px; color: #f87171; font-size: .85rem;
        }

        /* ── Results ── */
        .results-header {
            font-size: .8rem; font-weight: 600;
            color: var(--text-muted); letter-spacing: .06em; text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .caso-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: border-color .25s, transform .25s;
        }
        .caso-card:hover { border-color: var(--border-hover); transform: translateY(-2px); }

        .caso-header {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: .75rem; margin-bottom: 1rem;
        }
        .caso-code {
            font-family: 'Courier New', monospace;
            font-weight: 700; font-size: 1rem;
            color: #818cf8;
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.2);
            padding: .25rem .75rem; border-radius: 8px;
            letter-spacing: .05em;
        }

        .badge {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .25rem .75rem; border-radius: 999px;
            font-size: .75rem; font-weight: 600;
        }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; }

        .badge-pendiente  { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.25); }
        .badge-en_proceso { background: rgba(59,130,246,0.15);  color: #60a5fa; border: 1px solid rgba(59,130,246,0.25); }
        .badge-resuelto   { background: rgba(16,185,129,0.15);  color: #34d399; border: 1px solid rgba(16,185,129,0.25); }
        .badge-cerrado    { background: rgba(107,114,128,0.15); color: #9ca3af; border: 1px solid rgba(107,114,128,0.25); }

        .dot-pendiente  { background: #fbbf24; }
        .dot-en_proceso { background: #60a5fa; animation: pulse 1.5s infinite; }
        .dot-resuelto   { background: #34d399; }
        .dot-cerrado    { background: #9ca3af; }

        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

        .caso-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: .75rem; margin-top: .75rem;
        }
        @media (max-width: 480px) { .caso-grid { grid-template-columns: 1fr; } }

        .caso-field { display: flex; flex-direction: column; gap: .2rem; }
        .caso-field-label {
            font-size: .7rem; font-weight: 600;
            color: var(--text-muted); letter-spacing: .06em; text-transform: uppercase;
        }
        .caso-field-value { font-size: .875rem; color: var(--text-primary); font-weight: 500; }

        .timeline {
            margin-top: 1rem; padding-top: 1rem;
            border-top: 1px solid var(--border);
            font-size: .78rem; color: var(--text-muted);
            display: flex; gap: 1rem; flex-wrap: wrap;
        }
        .timeline span { display: flex; align-items: center; gap: .35rem; }

        /* ── Empty / No results ── */
        .empty-state {
            text-align: center; padding: 3rem 1.5rem;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 16px;
        }
        .empty-icon { font-size: 2.5rem; margin-bottom: 1rem; }
        .empty-title { font-size: 1rem; font-weight: 700; margin-bottom: .4rem; }
        .empty-sub { font-size: .85rem; color: var(--text-muted); line-height: 1.6; }

        /* ── Info note ── */
        .info-note {
            display: flex; gap: .75rem; align-items: flex-start;
            background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.2);
            border-radius: 12px; padding: 1rem 1.25rem;
            margin-top: 1.5rem; font-size: .82rem; color: var(--text-muted); line-height: 1.6;
        }
        .info-note svg { flex-shrink: 0; color: #818cf8; margin-top: .1rem; }

        footer {
            position: relative; z-index: 1;
            border-top: 1px solid var(--border);
            padding: 2rem 1.5rem; text-align: center;
            color: var(--text-muted); font-size: .8rem;
        }

        /* ── Paginación ── */
        .pagination-wrap {
            display: flex; align-items: center; justify-content: center;
            gap: .75rem; margin-top: 1.75rem;
        }
        .page-btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .55rem 1.1rem; border-radius: 10px;
            font-size: .85rem; font-weight: 600;
            background: rgba(99,102,241,0.12);
            border: 1px solid rgba(99,102,241,0.25);
            color: #818cf8;
            text-decoration: none;
            transition: all .2s;
        }
        .page-btn:hover { background: rgba(99,102,241,0.22); border-color: rgba(99,102,241,0.45); transform: translateY(-1px); }
        .page-btn-disabled {
            opacity: .35; cursor: default; pointer-events: none;
        }
        .page-info {
            font-size: .82rem; color: var(--text-muted);
            padding: .55rem .9rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="bg-glow"></div>

<!-- Nav -->
<nav>
    <a href="/" class="nav-brand">
        <div class="nav-brand-icon">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <span>SeguridadEscolar</span>
    </a>
    <div class="nav-actions">
        @auth
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Mi Panel</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-ghost">Iniciar Sesión</a>
        @endauth
        <a href="/" class="btn btn-ghost">← Inicio</a>
    </div>
</nav>

<div class="page-wrap">

    <!-- Header -->
    <div class="page-badge">
        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        Consulta de Expediente
    </div>
    <h1 class="page-title">Consulta el estado<br>de tu <span>expediente</span></h1>
    <p class="page-sub">
        Ingresa tu número de expediente o tu nombre completo para conocer el estado actual de tu caso.
        Solo se muestran casos no anónimos cuando se busca por nombre.
    </p>

    <!-- Search form -->
    <div class="search-card">
        @if($errors->any())
            <div class="error-msg" style="margin-bottom:1.25rem;">
                @foreach($errors->all() as $err)
                    <p>{{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form method="GET" action="{{ route('consultar.expediente') }}" id="searchForm">

            <!-- Type selector tabs -->
            <div class="tipo-tabs">
                <button type="button" class="tipo-tab {{ (!$tipo || $tipo === 'codigo') ? 'active' : '' }}"
                        onclick="setTipo('codigo', this)">
                    🔢 Por número de expediente
                </button>
                <button type="button" class="tipo-tab {{ $tipo === 'nombre' ? 'active' : '' }}"
                        onclick="setTipo('nombre', this)">
                    👤 Por nombre del estudiante
                </button>
            </div>

            <input type="hidden" name="tipo" id="tipoInput" value="{{ $tipo ?? 'codigo' }}">

            <label class="search-label" id="searchLabel" for="busquedaInput">
                {{ ($tipo === 'nombre') ? 'Nombre del estudiante' : 'Número de expediente' }}
            </label>
            <div class="search-input-wrap">
                <input
                    type="text"
                    id="busquedaInput"
                    name="busqueda"
                    class="search-input"
                    placeholder="{{ ($tipo === 'nombre') ? 'Ej. Juan Pérez García' : 'Ej. VIO-2026-XXXXXX' }}"
                    value="{{ old('busqueda', $busqueda ?? '') }}"
                    autocomplete="off"
                    autofocus
                    required
                >
                <button type="submit" class="search-btn">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Consultar
                </button>
            </div>
            <p class="hint-text" id="hintText">
                {{ ($tipo === 'nombre')
                    ? 'Solo se muestran casos identificados (no anónimos). Se retornan hasta 10 resultados.'
                    : 'El código de expediente tiene el formato VIO-AÑO-CÓDIGO (ej. VIO-2026-ABCDE1). Lo recibiste al momento de registrar tu caso.' }}
            </p>
        </form>

        <div class="info-note">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>
                Esta consulta es pública para facilitar el seguimiento. Si tu caso fue registrado como
                <strong style="color:var(--text-primary)">anónimo</strong>, no aparecerá en búsquedas por nombre
                para proteger tu privacidad. Puedes buscarlo directamente por su código de expediente.
            </span>
        </div>
    </div>

    <!-- Results -->
    @if($buscado)
        @php
            $tipoLabels = [
                'fisica'        => 'Física',
                'psicologica'   => 'Psicológica',
                'verbal'        => 'Verbal',
                'sexual'        => 'Sexual',
                'ciberacoso'    => 'Ciberacoso',
                'discriminacion'=> 'Discriminación',
                'otro'          => 'Otro',
            ];
            $estadoLabels = [
                'pendiente'  => 'Pendiente',
                'en_proceso' => 'En Proceso',
                'resuelto'   => 'Resuelto',
                'cerrado'    => 'Cerrado',
            ];
            $estadoDesc = [
                'pendiente'  => 'Tu caso ha sido recibido y está en espera de ser asignado a un profesional.',
                'en_proceso' => 'Tu caso está siendo atendido activamente por un profesional de apoyo.',
                'resuelto'   => 'El caso ha sido atendido y marcado como resuelto por el equipo.',
                'cerrado'    => 'El expediente ha sido cerrado.',
            ];
        @endphp

        @if($resultados->isNotEmpty())
            <p class="results-header">
                {{ $resultados->total() }} resultado{{ $resultados->total() !== 1 ? 's' : '' }} encontrado{{ $resultados->total() !== 1 ? 's' : '' }}
                para «{{ $busqueda }}»
                @if($resultados->lastPage() > 1)
                    &mdash; página {{ $resultados->currentPage() }} de {{ $resultados->lastPage() }}
                @endif
            </p>

            @foreach($resultados as $caso)
                <div class="caso-card">
                    <div class="caso-header">
                        <span class="caso-code">{{ $caso->codigo_caso }}</span>
                        <span class="badge badge-{{ $caso->estado }}">
                            <span class="badge-dot dot-{{ $caso->estado }}"></span>
                            {{ $estadoLabels[$caso->estado] ?? $caso->estado }}
                        </span>
                    </div>

                    <!-- Estado descriptivo -->
                    <p style="font-size:.875rem; color:var(--text-muted); line-height:1.6; margin-bottom:.5rem;">
                        {{ $estadoDesc[$caso->estado] ?? '' }}
                    </p>

                    <div class="caso-grid">
                        <div class="caso-field">
                            <span class="caso-field-label">Tipo de incidente</span>
                            <span class="caso-field-value">{{ $tipoLabels[$caso->tipo_violencia] ?? $caso->tipo_violencia }}</span>
                        </div>
                        <div class="caso-field">
                            <span class="caso-field-label">Prioridad</span>
                            <span class="caso-field-value" style="text-transform: capitalize;">{{ $caso->prioridad }}</span>
                        </div>
                        <div class="caso-field">
                            <span class="caso-field-label">Institución educativa</span>
                            <span class="caso-field-value">{{ $caso->escuela_nombre ?? '—' }}</span>
                        </div>
                        <div class="caso-field">
                            <span class="caso-field-label">Ubicación</span>
                            <span class="caso-field-value">
                                {{ implode(', ', array_filter([$caso->distrito, $caso->provincia, $caso->region])) ?: '—' }}
                            </span>
                        </div>
                    </div>

                    <div class="timeline">
                        <span>
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Registrado: {{ $caso->created_at->format('d/m/Y H:i') }}
                        </span>
                        <span>
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Última actualización: {{ $caso->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @endforeach

            @if($resultados->hasPages())
                <div class="pagination-wrap">
                    @if($resultados->onFirstPage())
                        <span class="page-btn page-btn-disabled">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            Anterior
                        </span>
                    @else
                        <a href="{{ $resultados->previousPageUrl() }}" class="page-btn">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            Anterior
                        </a>
                    @endif

                    <span class="page-info">{{ $resultados->currentPage() }} / {{ $resultados->lastPage() }}</span>

                    @if($resultados->hasMorePages())
                        <a href="{{ $resultados->nextPageUrl() }}" class="page-btn">
                            Siguiente
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span class="page-btn page-btn-disabled">
                            Siguiente
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    @endif
                </div>
            @endif

        @else
            <div class="empty-state">
                <div class="empty-icon">🔍</div>
                <p class="empty-title">No se encontraron resultados</p>
                <p class="empty-sub">
                    @if($tipo === 'codigo')
                        No existe ningún expediente con el código <strong style="color:var(--text-primary)">{{ $busqueda }}</strong>.<br>
                        Verifica que el código sea correcto (formato: VIO-AÑO-CÓDIGO).
                    @else
                        No se encontraron casos identificados para el nombre <strong style="color:var(--text-primary)">{{ $busqueda }}</strong>.<br>
                        Recuerda que los casos anónimos no aparecen en búsquedas por nombre.
                    @endif
                </p>
            </div>
        @endif
    @endif

</div>

<footer>
    <p>© {{ date('Y') }} Sistema de Prevención de Violencia Escolar · Todos los derechos reservados.</p>
    <p style="margin-top:.4rem;">Plataforma confidencial y segura · Construida con Laravel &amp; Filament</p>
</footer>

<script>
    function setTipo(valor, btn) {
        document.getElementById('tipoInput').value = valor;

        document.querySelectorAll('.tipo-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');

        const input  = document.getElementById('busquedaInput');
        const label  = document.getElementById('searchLabel');
        const hint   = document.getElementById('hintText');

        if (valor === 'nombre') {
            input.placeholder = 'Ej. Juan Pérez García';
            label.textContent  = 'Nombre del estudiante';
            hint.textContent   = 'Solo se muestran casos identificados (no anónimos). Se retornan hasta 10 resultados.';
        } else {
            input.placeholder = 'Ej. VIO-2026-XXXXXX';
            label.textContent  = 'Número de expediente';
            hint.textContent   = 'El código tiene el formato VIO-AÑO-CÓDIGO (ej. VIO-2026-ABCDE1). Lo recibiste al registrar tu caso.';
        }
        input.value = '';
        input.focus();
    }
</script>

</body>
</html>
