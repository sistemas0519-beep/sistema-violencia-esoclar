<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mis Denuncias — Sistema de Violencia Escolar</title>
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
            --accent:       #6366f1;
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

        /* ── Header / Nav ── */
        header {
            position: sticky; top: 0; z-index: 50;
            background: rgba(13,15,20,0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 0 1.5rem;
        }
        .header-inner {
            max-width: 900px; margin: 0 auto;
            display: flex; align-items: center; justify-content: space-between;
            height: 60px;
        }
        .header-brand {
            display: flex; align-items: center; gap: .6rem;
            text-decoration: none;
        }
        .header-brand-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
        }
        .header-brand-name { font-size: .9rem; font-weight: 800; color: var(--text-primary); }

        .header-user {
            display: flex; align-items: center; gap: .75rem;
        }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; font-weight: 800; color: #fff;
            flex-shrink: 0;
        }
        .user-info { display: flex; flex-direction: column; }
        .user-name { font-size: .82rem; font-weight: 700; color: var(--text-primary); }
        .user-role { font-size: .7rem; color: var(--text-muted); text-transform: capitalize; }

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
            max-width: 900px; margin: 0 auto;
            padding: 2.5rem 1.5rem;
        }

        /* ── Page title ── */
        .page-header { margin-bottom: 2rem; }
        .page-badge {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .28rem .85rem; border-radius: 999px;
            background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.3);
            color: #818cf8; font-size: .7rem; font-weight: 700;
            letter-spacing: .06em; text-transform: uppercase; margin-bottom: .75rem;
        }
        .page-title {
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 900; letter-spacing: -.02em;
            color: var(--text-primary); margin-bottom: .35rem;
        }
        .page-title span {
            background: linear-gradient(135deg, #818cf8, #c084fc);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .page-sub { font-size: .875rem; color: var(--text-muted); line-height: 1.6; }

        /* ── Stats bar ── */
        .stats-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 1rem; margin-bottom: 2rem;
        }
        @media (max-width: 600px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }

        .stat-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 14px; padding: 1.1rem 1.25rem;
        }
        .stat-label { font-size: .7rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--text-muted); margin-bottom: .4rem; }
        .stat-value { font-size: 1.75rem; font-weight: 900; line-height: 1; }
        .stat-total     { color: #818cf8; }
        .stat-pendiente { color: #fbbf24; }
        .stat-proceso   { color: #60a5fa; }
        .stat-resuelto  { color: #34d399; }

        /* ── Filters ── */
        .filters-row {
            display: flex; gap: .75rem; flex-wrap: wrap;
            margin-bottom: 1.5rem; align-items: center;
        }
        .filter-input {
            flex: 1; min-width: 180px;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 10px; padding: .6rem 1rem;
            color: var(--text-primary); font-size: .875rem; font-family: inherit;
            outline: none; transition: border-color .2s, box-shadow .2s;
        }
        .filter-input::placeholder { color: var(--text-muted); opacity: .6; }
        .filter-input:focus { border-color: rgba(99,102,241,.5); box-shadow: 0 0 0 3px rgba(99,102,241,.1); }

        .filter-select {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 10px; padding: .6rem .9rem;
            color: var(--text-primary); font-size: .875rem; font-family: inherit;
            outline: none; cursor: pointer; transition: border-color .2s;
        }
        .filter-select:focus { border-color: rgba(99,102,241,.5); }
        .filter-select option { background: #141720; }

        .btn-filter {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .6rem 1.25rem; border-radius: 10px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff; font-size: .875rem; font-weight: 600;
            border: none; cursor: pointer; font-family: inherit; transition: all .2s;
        }
        .btn-filter:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(99,102,241,.35); }

        .btn-clear {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .6rem 1rem; border-radius: 10px;
            background: transparent; border: 1px solid var(--border);
            color: var(--text-muted); font-size: .875rem; font-weight: 600;
            cursor: pointer; font-family: inherit; transition: all .2s; text-decoration: none;
        }
        .btn-clear:hover { border-color: var(--border-hover); color: var(--text-primary); }

        /* ── Case cards ── */
        .casos-list { display: flex; flex-direction: column; gap: 1rem; }

        .caso-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 16px; padding: 1.5rem;
            transition: border-color .25s, transform .2s, box-shadow .2s;
            text-decoration: none; display: block;
        }
        .caso-card:hover {
            border-color: rgba(99,102,241,.4);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
        }

        .caso-top {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: .6rem; margin-bottom: 1rem;
        }
        .caso-code {
            font-family: 'Courier New', monospace;
            font-weight: 700; font-size: .95rem; letter-spacing: .04em;
            color: #818cf8;
            background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2);
            padding: .2rem .7rem; border-radius: 7px;
        }

        .badge {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .22rem .7rem; border-radius: 999px;
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

        .caso-desc {
            font-size: .85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: .85rem;
        }

        .caso-meta {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: .5rem;
        }
        @media (max-width: 500px) { .caso-meta { grid-template-columns: 1fr 1fr; } }

        .meta-item { display: flex; flex-direction: column; gap: .15rem; }
        .meta-label {
            font-size: .65rem; font-weight: 700; letter-spacing: .06em;
            text-transform: uppercase; color: var(--text-muted);
        }
        .meta-value { font-size: .8rem; color: var(--text-primary); font-weight: 600; }

        .caso-footer {
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 1rem; padding-top: .85rem; border-top: 1px solid var(--border);
            flex-wrap: wrap; gap: .5rem;
        }
        .caso-time { font-size: .75rem; color: var(--text-muted); display: flex; align-items: center; gap: .35rem; }
        .link-detalle {
            display: inline-flex; align-items: center; gap: .35rem;
            font-size: .8rem; font-weight: 700; color: #818cf8; text-decoration: none;
            transition: color .2s;
        }
        .link-detalle:hover { color: #c084fc; }

        /* ── Empty ── */
        .empty-state {
            text-align: center; padding: 4rem 2rem;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 18px;
        }
        .empty-icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-title { font-size: 1.05rem; font-weight: 800; margin-bottom: .4rem; }
        .empty-sub { font-size: .875rem; color: var(--text-muted); line-height: 1.7; }

        /* ── Pagination ── */
        .pagination {
            display: flex; align-items: center; justify-content: center;
            gap: .75rem; margin-top: 2rem; flex-wrap: wrap;
        }
        .page-btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .5rem 1rem; border-radius: 9px;
            background: rgba(99,102,241,.1); border: 1px solid rgba(99,102,241,.25);
            color: #818cf8; text-decoration: none; font-size: .82rem; font-weight: 600;
            transition: all .2s;
        }
        .page-btn:hover { background: rgba(99,102,241,.2); border-color: rgba(99,102,241,.45); transform: translateY(-1px); }
        .page-btn.disabled { opacity: .35; pointer-events: none; }
        .page-info {
            font-size: .8rem; color: var(--text-muted);
            padding: .5rem .85rem; border-radius: 9px;
            background: rgba(255,255,255,.04); border: 1px solid var(--border);
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .header-user .user-info { display: none; }
            main { padding: 1.5rem 1rem; }
        }
    </style>
</head>
<body>
<div class="bg-glow"></div>

<!-- Header -->
<header>
    <div class="header-inner">
        <a href="{{ route('mis-denuncias.login') }}" class="header-brand">
            <div class="header-brand-icon">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <span class="header-brand-name">SeguridadEscolar</span>
        </a>

        <div class="header-user">
            <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div class="user-info">
                <span class="user-name">{{ $user->name }}</span>
                <span class="user-role">{{ ucfirst($user->rol) }}</span>
            </div>
            <form method="POST" action="{{ route('mis-denuncias.salir') }}">
                @csrf
                <button type="submit" class="btn-logout" title="Cerrar sesión">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Salir
                </button>
            </form>
        </div>
    </div>
</header>

<main>

    <!-- Page header -->
    <div class="page-header">
        <div class="page-badge">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Módulo de Consulta
        </div>
        <h1 class="page-title">Mis <span>Denuncias</span></h1>
        <p class="page-sub">Consulta el estado y seguimiento de todos tus casos registrados.</p>
    </div>

    @if(session('success'))
        <div style="padding:.8rem 1rem; border-radius:10px; margin-bottom:1.5rem; background:rgba(16,185,129,.08); border:1px solid rgba(16,185,129,.2); color:#34d399; font-size:.875rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total</div>
            <div class="stat-value stat-total">{{ $stats->total ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pendientes</div>
            <div class="stat-value stat-pendiente">{{ $stats->pendientes ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">En Proceso</div>
            <div class="stat-value stat-proceso">{{ $stats->en_proceso ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Resueltos</div>
            <div class="stat-value stat-resuelto">{{ $stats->resueltos ?? 0 }}</div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('mis-denuncias.lista') }}" id="filterForm">
        <div class="filters-row">
            <input
                type="text"
                name="q"
                class="filter-input"
                placeholder="Buscar por código (ej. VIO-2026-...)"
                value="{{ request('q') }}"
                autocomplete="off"
            >
            <select name="estado" class="filter-select">
                <option value="">Todos los estados</option>
                <option value="pendiente"  {{ request('estado') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                <option value="en_proceso" {{ request('estado') === 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                <option value="resuelto"   {{ request('estado') === 'resuelto'   ? 'selected' : '' }}>Resuelto</option>
                <option value="cerrado"    {{ request('estado') === 'cerrado'    ? 'selected' : '' }}>Cerrado</option>
            </select>
            <button type="submit" class="btn-filter">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filtrar
            </button>
            @if(request('q') || request('estado'))
                <a href="{{ route('mis-denuncias.lista') }}" class="btn-clear">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Limpiar
                </a>
            @endif
        </div>
    </form>

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
        $estadoDesc = [
            'pendiente'  => 'Tu caso ha sido recibido y está en espera de asignación.',
            'en_proceso' => 'Un profesional está atendiendo activamente tu caso.',
            'resuelto'   => 'El caso ha sido atendido y marcado como resuelto.',
            'cerrado'    => 'El expediente ha sido cerrado.',
        ];
    @endphp

    <!-- Cases list -->
    @if($casos->isNotEmpty())
        <div class="casos-list">
            @foreach($casos as $caso)
                <a href="{{ route('mis-denuncias.detalle', $caso->codigo_caso) }}" class="caso-card">
                    <div class="caso-top">
                        <span class="caso-code">{{ $caso->codigo_caso }}</span>
                        <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                            <span class="badge badge-{{ $caso->estado }}">
                                <span class="badge-dot dot-{{ $caso->estado }}"></span>
                                {{ $estadoLabels[$caso->estado] ?? $caso->estado }}
                            </span>
                            <span class="badge badge-{{ $caso->prioridad }}">
                                {{ ucfirst($caso->prioridad) }}
                            </span>
                        </div>
                    </div>

                    <p class="caso-desc">{{ $estadoDesc[$caso->estado] ?? '' }}</p>

                    <div class="caso-meta">
                        <div class="meta-item">
                            <span class="meta-label">Tipo</span>
                            <span class="meta-value">{{ $tipoLabels[$caso->tipo_violencia] ?? $caso->tipo_violencia }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Institución</span>
                            <span class="meta-value">{{ Str::limit($caso->escuela_nombre ?? '—', 30) }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Asignado a</span>
                            <span class="meta-value">{{ $caso->asignado?->name ?? 'Sin asignar' }}</span>
                        </div>
                    </div>

                    <div class="caso-footer">
                        <span class="caso-time">
                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Registrado: {{ $caso->created_at->format('d/m/Y') }}
                            &nbsp;·&nbsp;
                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Actualizado {{ $caso->updated_at->diffForHumans() }}
                        </span>
                        <span class="link-detalle">
                            Ver detalle
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($casos->hasPages())
            <div class="pagination">
                @if($casos->onFirstPage())
                    <span class="page-btn disabled">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Anterior
                    </span>
                @else
                    <a href="{{ $casos->previousPageUrl() }}" class="page-btn">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Anterior
                    </a>
                @endif

                <span class="page-info">{{ $casos->currentPage() }} / {{ $casos->lastPage() }}</span>

                @if($casos->hasMorePages())
                    <a href="{{ $casos->nextPageUrl() }}" class="page-btn">
                        Siguiente
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span class="page-btn disabled">
                        Siguiente
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        @endif

    @else
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <p class="empty-title">
                @if(request('q') || request('estado'))
                    No se encontraron denuncias con ese filtro
                @else
                    No tienes denuncias registradas
                @endif
            </p>
            <p class="empty-sub">
                @if(request('q') || request('estado'))
                    Prueba con otros criterios de búsqueda o
                    <a href="{{ route('mis-denuncias.lista') }}" style="color:#818cf8;">limpia los filtros</a>.
                @else
                    Cuando registres una denuncia aparecerá aquí con su estado en tiempo real.
                @endif
            </p>
        </div>
    @endif

</main>
</body>
</html>
