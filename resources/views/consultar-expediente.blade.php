<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Consultar Expediente — Sistema de Violencia Escolar</title>
    <meta name="description" content="Consulta el estado de tu expediente ingresando tu número de caso o tu nombre completo.">

    <!-- Bunny Fonts (GDPR-friendly, CSP-safe) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet">

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

        /* ── Mode tabs ── */
        .mode-tabs {
            display: flex; gap: .4rem; margin-bottom: 2rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 14px; padding: .35rem;
        }
        .mode-tab {
            flex: 1; padding: .65rem 1rem;
            border: none; background: transparent;
            color: var(--text-muted); font-size: .875rem; font-weight: 500;
            border-radius: 10px; cursor: pointer; font-family: inherit;
            transition: all .2s; display: flex; align-items: center; justify-content: center; gap: .45rem;
        }
        .mode-tab.active {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff; box-shadow: 0 2px 12px rgba(99,102,241,0.35);
        }
        .mode-tab:hover:not(.active) { color: var(--text-primary); background: rgba(255,255,255,0.05); }

        /* ── Section visibility ── */
        .search-section { display: block; }
        .search-section.hidden { display: none; }
        .login-section { display: none; }
        .login-section.visible { display: block; }

        /* ── Login ── */
        .login-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 20px; padding: 2rem; margin-bottom: 1.5rem;
        }
        .login-logged-in {
            text-align: center; padding: 3rem 2rem;
            background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.2);
            border-radius: 20px; margin-bottom: 1.5rem;
        }
        .logged-icon { font-size: 3rem; margin-bottom: 1rem; }
        .login-logged-in h3 { font-size: 1.25rem; font-weight: 800; margin-bottom: .5rem; }
        .login-logged-in p { color: var(--text-muted); font-size: .9rem; margin-bottom: 1.75rem; }
        .btn-goto-panel {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .8rem 2rem; border-radius: 10px;
            background: linear-gradient(135deg, #10b981, #0d9488);
            color: #fff; font-size: 1rem; font-weight: 700;
            text-decoration: none; transition: all .25s;
            box-shadow: 0 4px 18px rgba(16,185,129,0.28);
        }
        .btn-goto-panel:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(16,185,129,0.4); }
        .profiles-section-title {
            font-size: .78rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
            color: var(--text-muted); margin-bottom: .75rem;
        }
        .profiles-grid-login {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: .6rem; margin-bottom: 1.5rem;
        }
        @media (max-width: 480px) { .profiles-grid-login { grid-template-columns: 1fr; } }
        .profile-card-login {
            display: flex; align-items: center; gap: .65rem;
            padding: .7rem .85rem; border-radius: 12px;
            background: rgba(255,255,255,0.03); border: 2px solid var(--border);
            cursor: pointer; transition: all .2s ease;
            text-align: left; font-family: inherit; width: 100%;
        }
        .profile-card-login:hover {
            border-color: var(--border-hover); background: rgba(255,255,255,0.06);
            transform: translateY(-1px);
        }
        .profile-card-login.active {
            border-color: var(--pc, rgba(99,102,241,.6));
            background: var(--pc-bg, rgba(99,102,241,.08));
        }
        .profile-icon-login {
            width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 1rem;
        }
        .profile-name-login { font-size: .82rem; font-weight: 700; color: var(--text-primary); }
        .profile-desc-login { font-size: .71rem; color: var(--text-muted); margin-top: .1rem; }
        .login-field { margin-bottom: 1rem; }
        .login-field label { display: block; font-size: .78rem; font-weight: 600; color: var(--text-muted); letter-spacing: .04em; text-transform: uppercase; margin-bottom: .45rem; }
        .login-field-wrap { position: relative; }
        .login-field-icon { position: absolute; left: .85rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; }
        .login-field-wrap input {
            width: 100%; padding: .75rem 1rem .75rem 2.5rem;
            background: rgba(255,255,255,0.05); border: 1px solid var(--border);
            border-radius: 10px; color: var(--text-primary); font-size: .9rem;
            font-family: inherit; outline: none; transition: border-color .2s, box-shadow .2s;
        }
        .login-field-wrap input::placeholder { color: var(--text-muted); opacity: .6; }
        .login-field-wrap input:focus { border-color: rgba(99,102,241,.5); box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
        .login-field-wrap input.err { border-color: rgba(239,68,68,.5); }
        .login-err-msg { font-size: .75rem; color: #f87171; margin-top: .3rem; }
        .login-check-row { display: flex; align-items: center; justify-content: space-between; margin: 1rem 0; }
        .login-check-label { display: flex; align-items: center; gap: .5rem; cursor: pointer; font-size: .82rem; color: var(--text-muted); }
        .login-check-label input[type="checkbox"] { width: 15px; height: 15px; accent-color: #6366f1; }
        .login-forgot { font-size: .8rem; color: #818cf8; text-decoration: none; }
        .login-forgot:hover { text-decoration: underline; }
        .btn-login-submit {
            width: 100%; padding: .85rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none; border-radius: 10px;
            color: #fff; font-size: .95rem; font-weight: 600;
            font-family: inherit; cursor: pointer;
            transition: all .25s; box-shadow: 0 4px 18px rgba(99,102,241,.28);
        }
        .btn-login-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(99,102,241,.42); }
        .login-divider { display: flex; align-items: center; gap: 1rem; margin: 1.2rem 0; color: var(--text-muted); font-size: .75rem; }
        .login-divider::before, .login-divider::after { content:''; flex:1; height:1px; background: var(--border); }
        .login-register-row { text-align: center; font-size: .82rem; color: var(--text-muted); }
        .login-register-row a { color: #818cf8; text-decoration: none; font-weight: 600; }
        .login-register-row a:hover { text-decoration: underline; }
        .login-status { padding: .7rem 1rem; border-radius: 10px; margin-bottom: 1rem; background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.25); color: #34d399; font-size: .82rem; }
        .login-alert { padding: .75rem 1rem; border-radius: 10px; margin-bottom: 1rem; background: rgba(239,68,68,.08); border: 1px solid rgba(239,68,68,.2); color: #f87171; font-size: .85rem; }
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

    <!-- ── Mode switcher ── -->
    <div class="mode-tabs">
        <button type="button" class="mode-tab active" id="tabBtnConsultar" onclick="switchTab('consultar')">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Consultar Expediente
        </button>
        <button type="button" class="mode-tab" id="tabBtnLogin" onclick="switchTab('login')">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Iniciar Sesión
        </button>
    </div>

    <!-- ── Tab: Consultar Expediente ── -->
    <div id="tab-consultar" class="search-section">

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

            {{-- Paginación superior --}}
            @if($resultados->hasPages())
                <div class="pagination-wrap" style="margin-top:.5rem; margin-bottom:1.25rem;">
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

    </div>{{-- /tab-consultar --}}

    <!-- ══════════════════════════════════════════════════════════════
         Tab: Iniciar Sesión
         Permite acceder con perfiles: alumno, docente, psicologo/apoyo, admin
    ═══════════════════════════════════════════════════════════════════ -->
    <div id="tab-login" class="login-section">

        @auth
            @php $authUser = auth()->user(); @endphp
            @if(in_array($authUser->rol, ['alumno', 'docente']))
                {{-- ── Vista personal de expedientes (alumno / docente) ── --}}
                <div style="margin-bottom:1.5rem;">
                    <div class="page-badge" style="margin-bottom:.75rem; display:inline-flex;">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Mis Expedientes
                    </div>
                    <h2 style="font-size:1.2rem; font-weight:800; margin-bottom:.4rem; color:var(--text-primary);">
                        Bienvenido, <span style="background:linear-gradient(135deg,#818cf8,#c084fc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">{{ $authUser->name }}</span>
                    </h2>
                    <p style="color:var(--text-muted); font-size:.85rem; margin-bottom:0; line-height:1.6;">
                        Consulta el estado actual de tus denuncias. También puedes buscar por número de expediente.
                    </p>
                </div>

                {{-- Búsqueda por código --}}
                <div class="search-card" style="margin-bottom:1.5rem;">
                    <form method="GET" action="{{ route('consultar.expediente') }}" id="miConsultaForm">
                        <input type="hidden" name="tab" value="login">
                        <input type="hidden" name="tipo" value="codigo">
                        <label class="search-label" for="miCodigoInput">Número de expediente</label>
                        <div class="search-input-wrap">
                            <input
                                type="text"
                                id="miCodigoInput"
                                name="busqueda"
                                class="search-input"
                                placeholder="Ej. VIO-2026-XXXXXX"
                                value="{{ ($buscado && $esMiConsulta) ? old('busqueda', $busqueda ?? '') : '' }}"
                                autocomplete="off">
                            <button type="submit" class="search-btn">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar
                            </button>
                        </div>
                        @if($buscado && $esMiConsulta && $resultados && $resultados->isEmpty())
                            <div class="error-msg" style="margin-top:.75rem;">
                                No se encontró ningún expediente con ese código en tu cuenta.
                            </div>
                        @endif
                    </form>
                </div>

                {{-- Lista de expedientes --}}
                @php
                    $tipoLabels2 = [
                        'fisica' => 'Física', 'psicologica' => 'Psicológica', 'verbal' => 'Verbal',
                        'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso', 'bullying' => 'Bullying',
                        'cyberbullying' => 'Cyberbullying', 'discriminacion' => 'Discriminación', 'otro' => 'Otro',
                    ];
                    $estadoLabels2 = [
                        'pendiente'  => 'Pendiente', 'en_proceso' => 'En Proceso',
                        'resuelto'   => 'Resuelto',  'cerrado'    => 'Cerrado',
                    ];
                    $estadoDesc2 = [
                        'pendiente'  => 'Tu caso ha sido recibido y está en espera de ser asignado a un profesional.',
                        'en_proceso' => 'Tu caso está siendo atendido activamente por un profesional de apoyo.',
                        'resuelto'   => 'El caso ha sido atendido y marcado como resuelto por el equipo.',
                        'cerrado'    => 'El expediente ha sido cerrado.',
                    ];
                    $casosAMostrar = ($buscado && $esMiConsulta && $resultados && $resultados->isNotEmpty())
                        ? $resultados
                        : $misCasos;
                    $esResultadoBusqueda = ($buscado && $esMiConsulta && $resultados && $resultados->isNotEmpty());
                @endphp

                @if($casosAMostrar && $casosAMostrar->isNotEmpty())
                    <p class="results-header">
                        @if($esResultadoBusqueda)
                            Resultado encontrado para «{{ $busqueda }}»
                        @else
                            {{ $casosAMostrar->count() }} expediente{{ $casosAMostrar->count() !== 1 ? 's' : '' }} registrado{{ $casosAMostrar->count() !== 1 ? 's' : '' }}
                        @endif
                    </p>
                    @foreach($casosAMostrar as $caso)
                        <div class="caso-card">
                            <div class="caso-header">
                                <span class="caso-code">{{ $caso->codigo_caso }}</span>
                                <span class="badge badge-{{ $caso->estado }}">
                                    <span class="badge-dot dot-{{ $caso->estado }}"></span>
                                    {{ $estadoLabels2[$caso->estado] ?? $caso->estado }}
                                </span>
                            </div>
                            <p style="font-size:.875rem; color:var(--text-muted); line-height:1.6; margin-bottom:.5rem;">
                                {{ $estadoDesc2[$caso->estado] ?? '' }}
                            </p>
                            <div class="caso-grid">
                                <div class="caso-field">
                                    <span class="caso-field-label">Tipo de incidente</span>
                                    <span class="caso-field-value">{{ $tipoLabels2[$caso->tipo_violencia] ?? $caso->tipo_violencia }}</span>
                                </div>
                                <div class="caso-field">
                                    <span class="caso-field-label">Prioridad</span>
                                    <span class="caso-field-value" style="text-transform:capitalize;">{{ $caso->prioridad }}</span>
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
                @else
                    <div class="empty-state">
                        <div class="empty-icon">📋</div>
                        <p class="empty-title">No tienes expedientes registrados</p>
                        <p class="empty-sub">Cuando registres una denuncia, aparecerá aquí con su estado actual.</p>
                    </div>
                @endif

                {{-- Cerrar sesión --}}
                <div style="text-align:center; margin-top:2rem; padding-top:1.25rem; border-top:1px solid var(--border);">
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="background:transparent; border:1px solid var(--border); color:var(--text-muted); padding:.5rem 1.25rem; border-radius:8px; font-size:.85rem; cursor:pointer; font-family:inherit; transition:all .2s;" onmouseover="this.style.color='var(--text-primary)'" onmouseout="this.style.color='var(--text-muted)'">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>

            @else
                {{-- Admin / Psicólogo / Asistente → acceso al panel completo --}}
                <div class="login-logged-in">
                    <div class="logged-icon">✅</div>
                    <h3>Sesión activa</h3>
                    <p>
                        Has iniciado sesión como
                        <strong style="color:var(--text-primary)">{{ $authUser->name }}</strong>
                        ({{ $authUser->rol }}).
                    </p>
                    <a href="{{ url('/dashboard') }}" class="btn-goto-panel">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Ir a mi Panel
                    </a>
                </div>
            @endif
        @else
            <div class="login-card">

                {{-- Status (password reset, etc.) --}}
                @if(session('status'))
                    <div class="login-status">{{ session('status') }}</div>
                @endif

                {{-- Errores de autenticación --}}
                @if($errors->has('email') || $errors->has('password'))
                    <div class="login-alert">
                        @foreach($errors->only(['email', 'password']) as $err)
                            <p>{{ $err }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Selector de perfil -->
                <div class="profiles-section-title">Selecciona tu perfil de acceso</div>
                <div class="profiles-grid-login">

                    <button type="button" class="profile-card-login"
                            style="--pc:rgba(245,158,11,.6);--pc-bg:rgba(245,158,11,.08);"
                            onclick="selectLoginProfile('alumno@escuela.edu','password',this)">
                        <div class="profile-icon-login" style="background:rgba(245,158,11,.15)">🎓</div>
                        <div>
                            <div class="profile-name-login">Alumno</div>
                            <div class="profile-desc-login">Seguimiento de tu denuncia</div>
                        </div>
                    </button>

                    <button type="button" class="profile-card-login"
                            style="--pc:rgba(59,130,246,.6);--pc-bg:rgba(59,130,246,.08);"
                            onclick="selectLoginProfile('docente@escuela.edu','password',this)">
                        <div class="profile-icon-login" style="background:rgba(59,130,246,.15)">📚</div>
                        <div>
                            <div class="profile-name-login">Docente / Tutor</div>
                            <div class="profile-desc-login">Reportar y hacer seguimiento</div>
                        </div>
                    </button>

                    <button type="button" class="profile-card-login"
                            style="--pc:rgba(16,185,129,.6);--pc-bg:rgba(16,185,129,.08);"
                            onclick="selectLoginProfile('psicologo@escuela.edu','password',this)">
                        <div class="profile-icon-login" style="background:rgba(16,185,129,.15)">🧠</div>
                        <div>
                            <div class="profile-name-login">Psicólogo / Apoyo</div>
                            <div class="profile-desc-login">Panel de casos y sesiones</div>
                        </div>
                    </button>

                    <button type="button" class="profile-card-login"
                            style="--pc:rgba(239,68,68,.6);--pc-bg:rgba(239,68,68,.08);"
                            onclick="selectLoginProfile('admin@escuela.edu','password',this)">
                        <div class="profile-icon-login" style="background:rgba(239,68,68,.15)">🛡️</div>
                        <div>
                            <div class="profile-name-login">Administrador</div>
                            <div class="profile-desc-login">Panel de administración</div>
                        </div>
                    </button>

                </div>

                <!-- Formulario de login -->
                <form method="POST" action="{{ route('consultar.login') }}" id="loginFormExpediente">
                    @csrf

                    <div class="login-field">
                        <label for="login_email">Correo Electrónico</label>
                        <div class="login-field-wrap">
                            <span class="login-field-icon">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <input type="email" id="login_email" name="email"
                                   value="{{ old('email') }}"
                                   placeholder="tu@correo.edu"
                                   required autocomplete="username"
                                   class="{{ $errors->has('email') ? 'err' : '' }}">
                        </div>
                        @error('email') <p class="login-err-msg">{{ $message }}</p> @enderror
                    </div>

                    <div class="login-field">
                        <label for="login_password">Contraseña</label>
                        <div class="login-field-wrap">
                            <span class="login-field-icon">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input type="password" id="login_password" name="password"
                                   placeholder="••••••••"
                                   required autocomplete="current-password"
                                   class="{{ $errors->has('password') ? 'err' : '' }}">
                        </div>
                        @error('password') <p class="login-err-msg">{{ $message }}</p> @enderror
                    </div>

                    <div class="login-check-row">
                        <label class="login-check-label">
                            <input type="checkbox" name="remember" id="login_remember">
                            <span>Recordarme</span>
                        </label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="login-forgot">¿Olvidaste tu contraseña?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn-login-submit">Iniciar Sesión →</button>

                    @if(Route::has('register'))
                        <div class="login-divider">o</div>
                        <div class="login-register-row">
                            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
                        </div>
                    @endif
                </form>

            </div>
        @endauth

    </div>{{-- /tab-login --}}

</div>

<footer>
    <p>© {{ date('Y') }} Sistema de Prevención de Violencia Escolar · Todos los derechos reservados.</p>
    <p style="margin-top:.4rem;">Plataforma confidencial y segura · Construida con Laravel &amp; Filament</p>
</footer>

<script>
/* ── Tab switcher ── */
function switchTab(tab) {
    var elConsultar  = document.getElementById('tab-consultar');
    var elLogin      = document.getElementById('tab-login');
    var btnConsultar = document.getElementById('tabBtnConsultar');
    var btnLogin     = document.getElementById('tabBtnLogin');

    if (tab === 'login') {
        elConsultar.classList.add('hidden');
        elLogin.classList.add('visible');
        btnLogin.classList.add('active');
        btnConsultar.classList.remove('active');
    } else {
        elConsultar.classList.remove('hidden');
        elLogin.classList.remove('visible');
        btnConsultar.classList.add('active');
        btnLogin.classList.remove('active');
    }

    /* Actualizar URL sin recargar */
    try {
        var url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url.toString());
    } catch (e) {}
}

function selectLoginProfile(email, pass, card) {
    document.querySelectorAll('.profile-card-login').forEach(function(c) {
        c.classList.remove('active');
    });
    card.classList.add('active');
    document.getElementById('login_email').value    = email;
    document.getElementById('login_password').value = pass;
}

/* Inicialización: activar tab según URL o errores de auth */
(function () {
    var hasLoginErrors = @json($errors->has('email') || $errors->has('password'));
    var tabParam = '';
    try {
        tabParam = new URL(window.location.href).searchParams.get('tab') || '';
    } catch (e) {}

    var esMiConsulta = @json($esMiConsulta);
    if (tabParam === 'login' || hasLoginErrors || esMiConsulta) {
        switchTab('login');
    }
})();
</script>

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

{{-- ═══════════════════════════════════════════════════════════════════════════
     AUTO-REFRESH POR INACTIVIDAD
     Solo se activa cuando el usuario ha realizado una búsqueda ($buscado=true).

     Métricas del sistema:
       · CheckSessionTimeout::TIMEOUT_MINUTES = 30 min  (sesión autenticada)
       · SESSION_LIFETIME = 30 min
       · Umbral de inactividad pública = 5 min  (1/6 del ciclo de sesión)
         → apropiado para terminal/quiosco de consulta pública

     Funcionalidades:
       · Detección de inactividad: mousemove, click, keydown, scroll, touch, wheel
       · Sincronización cross-tab via BroadcastChannel + localStorage fallback
       · Intercepta XHR/fetch pendientes → pospone el refresh
       · Detecta modales/dropdowns abiertos → pospone el refresh
       · Banner de aviso con cuenta regresiva (30 s) antes de refrescar
       · Logging server-side via POST /log-auto-refresh (ActividadSistema)
       · Logging local en localStorage (últimas 20 entradas)
══════════════════════════════════════════════════════════════════════════════ --}}
<script>
(function () {
    'use strict';

    // ── Configuración ─────────────────────────────────────────────────────────
    // INACTIVITY_TIMEOUT: derivado de SESSION_LIFETIME(30min)/6 = 5 min.
    // Para ajustar, modificar solo este valor (en segundos).
    var CONFIG = {
        INACTIVITY_TIMEOUT : 300,               // segundos (5 min) antes del aviso
        WARNING_COUNTDOWN  : 30,                // segundos de cuenta regresiva visible
        STORAGE_KEY        : 'sve_expediente_actividad', // clave localStorage cross-tab
        CHANNEL_NAME       : 'sve_expediente_refresh',  // BroadcastChannel id
        LOG_ENDPOINT       : '/log-auto-refresh',
        CSRF_TOKEN         : (document.querySelector('meta[name="csrf-token"]') || {}).content || '',
        REFRESH_URL        : '/consultar-expediente',
    };

    // ── ¿Hay resultados visibles? Solo activar tras una búsqueda ─────────────
    var BUSQUEDA_ACTIVA = @json($buscado);
    if (!BUSQUEDA_ACTIVA) return;

    // ── Estado interno ────────────────────────────────────────────────────────
    var inactivityTimer  = null;
    var countdownTimer   = null;
    var countdownSeconds = CONFIG.WARNING_COUNTDOWN;
    var pendingRequests  = 0;
    var bannerEl         = null;
    var refreshChannel   = null;
    var refreshScheduled = false;

    // ── Interceptar XHR para contar solicitudes pendientes ───────────────────
    var origOpen = XMLHttpRequest.prototype.open;
    var origSend = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.open = function () {
        this._sveTracked = true;
        return origOpen.apply(this, arguments);
    };
    XMLHttpRequest.prototype.send = function () {
        if (this._sveTracked) {
            pendingRequests++;
            this.addEventListener('loadend', function () {
                pendingRequests = Math.max(0, pendingRequests - 1);
            });
        }
        return origSend.apply(this, arguments);
    };

    // Interceptar fetch
    var origFetch = window.fetch;
    window.fetch = function () {
        pendingRequests++;
        var args = arguments;
        return origFetch.apply(this, args).finally(function () {
            pendingRequests = Math.max(0, pendingRequests - 1);
        });
    };

    // ── BroadcastChannel (sincronización entre pestañas) ─────────────────────
    if ('BroadcastChannel' in window) {
        try {
            refreshChannel = new BroadcastChannel(CONFIG.CHANNEL_NAME);
            refreshChannel.onmessage = function (e) {
                if (!e.data) return;
                if (e.data.type === 'activity') {
                    // Otra pestaña tuvo actividad → resetear sin propagar de vuelta
                    resetTimerSilent();
                }
                if (e.data.type === 'refresh') {
                    // Otra pestaña ejecutó el refresh → seguirla
                    doRefresh(false);
                }
            };
        } catch (_) {}
    }

    // ── Detectar modales / dropdowns activos ─────────────────────────────────
    function hasActiveModal() {
        if (document.querySelector('dialog[open]'))               return true;
        if (document.querySelector('[aria-expanded="true"]'))     return true;
        if (document.querySelector('[data-headlessui-state="open"]')) return true;
        if (document.querySelector('.dropdown-open, .modal-open')) return true;
        // El formulario de búsqueda tiene foco → no interrumpir
        var focused = document.activeElement;
        if (focused && (focused.tagName === 'INPUT' || focused.tagName === 'TEXTAREA' || focused.tagName === 'SELECT')) {
            return true;
        }
        return false;
    }

    // ── Crear banner de aviso ─────────────────────────────────────────────────
    function crearBanner() {
        if (bannerEl) return;
        bannerEl = document.createElement('div');
        bannerEl.id = 'sve-auto-refresh-banner';
        bannerEl.setAttribute('role', 'alert');
        bannerEl.setAttribute('aria-live', 'assertive');
        bannerEl.style.cssText = [
            'position:fixed', 'bottom:1.5rem', 'left:50%', 'transform:translateX(-50%)',
            'z-index:99999', 'display:flex', 'align-items:center', 'gap:.875rem',
            'padding:.875rem 1.25rem', 'border-radius:12px',
            'max-width:500px', 'width:calc(100% - 3rem)',
            'background:#141720', 'border:1px solid rgba(99,102,241,0.45)',
            'box-shadow:0 8px 32px rgba(0,0,0,0.5),0 0 0 1px rgba(99,102,241,0.08)',
            'font-family:Inter,sans-serif', 'font-size:.875rem', 'color:#f0f2f8',
            'animation:sveBannerIn .3s cubic-bezier(.4,0,.2,1)',
        ].join(';');

        bannerEl.innerHTML =
            '<style>' +
            '@keyframes sveBannerIn{from{opacity:0;transform:translateX(-50%) translateY(.75rem)}to{opacity:1;transform:translateX(-50%) translateY(0)}}' +
            '#sve-auto-refresh-banner .svebi{flex-shrink:0;width:36px;height:36px;border-radius:8px;background:rgba(99,102,241,.15);display:flex;align-items:center;justify-content:center}' +
            '#sve-auto-refresh-banner .svetx{flex:1;min-width:0}' +
            '#sve-auto-refresh-banner .svetitle{font-weight:600;margin-bottom:.15rem}' +
            '#sve-auto-refresh-banner .svesub{color:#8b92a9;font-size:.8rem}' +
            '#sve-auto-refresh-banner .svecnt{font-size:1.1rem;font-weight:700;color:#818cf8;min-width:2.25rem;text-align:center}' +
            '#sve-auto-refresh-banner .svedis{flex-shrink:0;padding:.35rem .85rem;border-radius:6px;border:1px solid rgba(255,255,255,.12);background:transparent;color:#8b92a9;cursor:pointer;font-size:.8rem;font-family:inherit;transition:all .15s}' +
            '#sve-auto-refresh-banner .svedis:hover{background:rgba(255,255,255,.06);color:#f0f2f8;border-color:rgba(255,255,255,.22)}' +
            '</style>' +
            '<div class="svebi">' +
                '<svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#818cf8" stroke-width="2">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>' +
                '</svg>' +
            '</div>' +
            '<div class="svetx">' +
                '<div class="svetitle">Pantalla inactiva</div>' +
                '<div class="svesub">La consulta se reiniciará en</div>' +
            '</div>' +
            '<div class="svecnt" id="sve-ar-cnt">' + CONFIG.WARNING_COUNTDOWN + 's</div>' +
            '<button class="svedis" id="sve-ar-dismiss" type="button">Continuar</button>';

        document.body.appendChild(bannerEl);
        document.getElementById('sve-ar-dismiss').addEventListener('click', cancelarCuentaRegresiva);
    }

    function quitarBanner() {
        if (bannerEl) { bannerEl.remove(); bannerEl = null; }
    }

    // ── Ejecutar el refresh ───────────────────────────────────────────────────
    function doRefresh(notifyOtherTabs) {
        if (refreshScheduled) return;
        refreshScheduled = true;
        quitarBanner();
        registrarEvento();
        if (notifyOtherTabs && refreshChannel) {
            try { refreshChannel.postMessage({ type: 'refresh' }); } catch (_) {}
        }
        window.location.href = CONFIG.REFRESH_URL;
    }

    // ── Iniciar cuenta regresiva ──────────────────────────────────────────────
    function iniciarCuentaRegresiva() {
        // Posponer si hay solicitudes pendientes o un elemento interactivo activo
        if (pendingRequests > 0 || hasActiveModal()) {
            resetTimerSilent();
            return;
        }
        countdownSeconds = CONFIG.WARNING_COUNTDOWN;
        crearBanner();
        countdownTimer = setInterval(function () {
            countdownSeconds--;
            var el = document.getElementById('sve-ar-cnt');
            if (el) el.textContent = countdownSeconds + 's';
            if (countdownSeconds <= 0) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                doRefresh(true);
            }
        }, 1000);
    }

    // ── Cancelar cuenta regresiva (usuario sigue activo) ─────────────────────
    function cancelarCuentaRegresiva() {
        clearInterval(countdownTimer);
        countdownTimer = null;
        quitarBanner();
    }

    // ── Resetear temporizador con propagación cross-tab ───────────────────────
    function resetTimer() {
        clearTimeout(inactivityTimer);
        cancelarCuentaRegresiva();
        // Sincronizar con otras pestañas
        try { localStorage.setItem(CONFIG.STORAGE_KEY, String(Date.now())); } catch (_) {}
        if (refreshChannel) {
            try { refreshChannel.postMessage({ type: 'activity' }); } catch (_) {}
        }
        inactivityTimer = setTimeout(iniciarCuentaRegresiva, CONFIG.INACTIVITY_TIMEOUT * 1000);
    }

    // ── Resetear sin propagar (evita bucles cross-tab) ────────────────────────
    function resetTimerSilent() {
        clearTimeout(inactivityTimer);
        cancelarCuentaRegresiva();
        inactivityTimer = setTimeout(iniciarCuentaRegresiva, CONFIG.INACTIVITY_TIMEOUT * 1000);
    }

    // ── Escuchar eventos de actividad del usuario ─────────────────────────────
    var ACTIVITY_EVENTS = ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'touchmove', 'wheel', 'click'];
    ACTIVITY_EVENTS.forEach(function (evt) {
        document.addEventListener(evt, resetTimer, { passive: true, capture: false });
    });

    // ── Sincronización cross-tab via localStorage (fallback) ─────────────────
    window.addEventListener('storage', function (e) {
        if (e.key === CONFIG.STORAGE_KEY) resetTimerSilent();
    });

    // ── Reactivar al volver a la pestaña ─────────────────────────────────────
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') resetTimer();
    });

    // ── Logging server-side vía ActividadSistema ──────────────────────────────
    function registrarEvento() {
        try {
            var payload = {
                tipo   : 'auto_refresh_expediente',
                url    : window.location.href.substring(0, 500),
                agente : navigator.userAgent.substring(0, 200),
                ts     : new Date().toISOString(),
            };
            // sendBeacon: funciona incluso durante la descarga de la página
            if (navigator.sendBeacon && CONFIG.LOG_ENDPOINT && CONFIG.CSRF_TOKEN) {
                var fd = new FormData();
                Object.keys(payload).forEach(function (k) { fd.append(k, payload[k]); });
                fd.append('_token', CONFIG.CSRF_TOKEN);
                navigator.sendBeacon(CONFIG.LOG_ENDPOINT, fd);
            }
            // Log local para diagnóstico (últimas 20 entradas)
            try {
                var logs = JSON.parse(localStorage.getItem('sve_refresh_log') || '[]');
                logs.push({ ts: payload.ts, url: payload.url });
                if (logs.length > 20) logs.splice(0, logs.length - 20);
                localStorage.setItem('sve_refresh_log', JSON.stringify(logs));
            } catch (_) {}
        } catch (_) {}
    }

    // ── Arrancar el sistema de detección ─────────────────────────────────────
    resetTimer();

})();
</script>

</body>
</html>
