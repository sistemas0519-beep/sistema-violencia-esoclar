<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Prevención de Violencia Escolar</title>
    <meta name="description" content="Plataforma segura y confidencial para la prevención, denuncia y seguimiento de situaciones de violencia escolar.">

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

        /* ── Background particles / glow ── */
        .bg-glow {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% -10%, rgba(99,102,241,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 110%, rgba(139,92,246,0.10) 0%, transparent 60%);
        }

        /* ── Nav ── */
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
            color: var(--text-muted);
            background: transparent;
            border: 1px solid var(--border);
        }
        .btn-ghost:hover { color: var(--text-primary); border-color: var(--border-hover); background: rgba(255,255,255,0.04); }
        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: 1px solid rgba(99,102,241,0.5);
            box-shadow: 0 4px 15px rgba(99,102,241,0.2);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.35); }

        /* ── Hero ── */
        .hero {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-align: center; padding: 6rem 1.5rem 4rem;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .35rem 1rem; border-radius: 999px;
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.3);
            color: #818cf8; font-size: .8rem; font-weight: 600;
            letter-spacing: .05em; text-transform: uppercase;
            margin-bottom: 1.75rem;
            animation: fadeUp .6s ease both;
        }
        .hero-badge-dot { width: 6px; height: 6px; border-radius: 50%; background: #818cf8; animation: pulse 1.8s ease infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }

        h1 {
            font-size: clamp(2.2rem, 6vw, 4rem);
            font-weight: 900; line-height: 1.1;
            letter-spacing: -.03em;
            max-width: 800px;
            animation: fadeUp .7s .1s ease both;
        }
        h1 span {
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 50%, #f472b6 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .hero-sub {
            margin-top: 1.5rem; max-width: 560px;
            font-size: 1.05rem; color: var(--text-muted); line-height: 1.7;
            animation: fadeUp .7s .2s ease both;
        }
        .hero-cta {
            margin-top: 2.5rem; display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;
            animation: fadeUp .7s .3s ease both;
        }
        .btn-lg { padding: .75rem 2rem; font-size: 1rem; border-radius: 10px; }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(20px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* ── Stats bar ── */
        .stats-bar {
            position: relative; z-index: 1;
            display: flex; justify-content: center; gap: 3rem; flex-wrap: wrap;
            padding: 2.5rem 1.5rem;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,0.02);
        }
        .stat { text-align: center; }
        .stat-num { font-size: 1.75rem; font-weight: 800; color: #818cf8; }
        .stat-label { font-size: .8rem; color: var(--text-muted); margin-top: .2rem; }

        /* ── Roles section ── */
        .section { position: relative; z-index: 1; padding: 5rem 1.5rem; max-width: 1100px; margin: 0 auto; }
        .section-label {
            text-align: center; font-size: .8rem; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            color: #818cf8; margin-bottom: .75rem;
        }
        h2 {
            text-align: center; font-size: clamp(1.6rem, 4vw, 2.4rem);
            font-weight: 800; letter-spacing: -.02em; margin-bottom: .75rem;
        }
        .section-sub {
            text-align: center; color: var(--text-muted);
            max-width: 500px; margin: 0 auto 3.5rem; line-height: 1.7; font-size: .95rem;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 1.25rem;
        }
        .role-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem 1.5rem;
            transition: all .3s ease;
            position: relative; overflow: hidden;
            cursor: default;
        }
        .role-card::before {
            content:''; position:absolute; inset:0; border-radius:16px;
            opacity:0; transition: opacity .3s ease;
            background: radial-gradient(ellipse at top left, var(--card-glow, rgba(99,102,241,.12)), transparent 70%);
        }
        .role-card:hover { border-color: var(--border-hover); transform: translateY(-4px); background: var(--bg-card-hover); }
        .role-card:hover::before { opacity:1; }

        .role-icon {
            width: 52px; height: 52px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; margin-bottom: 1.25rem;
        }
        .role-title { font-size: 1.05rem; font-weight: 700; margin-bottom: .5rem; }
        .role-desc  { font-size: .85rem; color: var(--text-muted); line-height: 1.65; }
        .role-badge {
            display: inline-block; margin-top: 1rem;
            padding: .2rem .7rem; border-radius: 999px;
            font-size: .72rem; font-weight: 600; letter-spacing: .04em;
        }

        /* Per-role colors */
        .role-admin  { --card-glow: rgba(239,68,68,.15); }
        .role-psi    { --card-glow: rgba(16,185,129,.15); }
        .role-doc    { --card-glow: rgba(59,130,246,.15); }
        .role-alum   { --card-glow: rgba(245,158,11,.15); }

        .icon-admin  { background: rgba(239,68,68,.15);   color: #f87171; }
        .icon-psi    { background: rgba(16,185,129,.15);  color: #34d399; }
        .icon-doc    { background: rgba(59,130,246,.15);  color: #60a5fa; }
        .icon-alum   { background: rgba(245,158,11,.15);  color: #fbbf24; }

        .badge-admin { background: rgba(239,68,68,.15);   color: #f87171;  border: 1px solid rgba(239,68,68,.3);   }
        .badge-psi   { background: rgba(16,185,129,.15);  color: #34d399;  border: 1px solid rgba(16,185,129,.3);  }
        .badge-doc   { background: rgba(59,130,246,.15);  color: #60a5fa;  border: 1px solid rgba(59,130,246,.3);  }
        .badge-alum  { background: rgba(245,158,11,.15);  color: #fbbf24;  border: 1px solid rgba(245,158,11,.3);  }

        /* ── Features grid ── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 3rem;
        }
        .feature {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 12px; padding: 1.5rem;
            display: flex; align-items: flex-start; gap: 1rem;
            transition: border-color .2s;
        }
        .feature:hover { border-color: var(--border-hover); }
        .feature-icon { font-size: 1.4rem; flex-shrink: 0; margin-top: .1rem; }
        .feature-title { font-size: .9rem; font-weight: 600; margin-bottom: .25rem; }
        .feature-desc  { font-size: .8rem; color: var(--text-muted); line-height: 1.5; }

        /* ── CTA Banner ── */
        .cta-banner {
            position: relative; z-index: 1;
            margin: 0 1.5rem 5rem;
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(139,92,246,0.15));
            border: 1px solid rgba(99,102,241,0.3);
            border-radius: 20px;
            padding: 3.5rem 2rem;
            text-align: center;
            overflow: hidden;
            max-width: 900px;
            margin-left: auto; margin-right: auto;
        }
        .cta-banner::before {
            content:''; position:absolute; inset:0;
            background: radial-gradient(ellipse at center, rgba(99,102,241,0.08), transparent 70%);
        }
        .cta-banner h3 { font-size: clamp(1.4rem,3vw,2rem); font-weight: 800; position:relative; }
        .cta-banner p  { color:var(--text-muted); margin:.75rem auto 2rem; max-width:480px; line-height:1.7; position:relative; }
        .cta-banner .btn-group { display:flex; gap:.75rem; flex-wrap:wrap; justify-content:center; position:relative; }

        /* ── Footer ── */
        footer {
            position: relative; z-index: 1;
            border-top: 1px solid var(--border);
            padding: 2rem 1.5rem;
            text-align: center;
            color: var(--text-muted);
            font-size: .8rem;
        }

        /* ── Consulta expediente (inline) ── */
        .exp-wrap {
            position: relative; z-index: 1;
            max-width: 720px; margin: 0 auto 5rem; padding: 0 1.5rem;
        }
        .exp-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.25rem 2rem;
        }
        .exp-card-header {
            display: flex; align-items: center; gap: .65rem; margin-bottom: .6rem;
        }
        .exp-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .exp-title { font-size: 1.1rem; font-weight: 800; }
        .exp-sub   { font-size: .875rem; color: var(--text-muted); line-height: 1.65; margin-bottom: 1.25rem; }

        .tipo-tabs { display: flex; gap: .5rem; margin-bottom: 1rem; flex-wrap: wrap; }
        .tipo-tab {
            flex: 1; min-width: 160px;
            padding: .55rem 1rem; border-radius: 10px;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            color: var(--text-muted); font-size: .85rem; font-weight: 500;
            cursor: pointer; font-family: inherit;
            transition: all .2s ease; text-align: center;
        }
        .tipo-tab.active {
            background: rgba(99,102,241,0.15);
            border-color: rgba(99,102,241,0.45);
            color: #818cf8;
        }
        .tipo-tab:hover:not(.active) { border-color: var(--border-hover); color: var(--text-primary); }

        .exp-input-wrap { display: flex; gap: .6rem; flex-wrap: wrap; }
        .exp-input {
            flex: 1; min-width: 200px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: .7rem 1rem;
            color: var(--text-primary); font-size: .9rem; font-family: inherit; outline: none;
            transition: border-color .2s;
        }
        .exp-input:focus { border-color: rgba(99,102,241,0.5); }
        .exp-btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .7rem 1.4rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff; border: none; border-radius: 10px;
            font-size: .875rem; font-weight: 600; cursor: pointer; font-family: inherit;
            transition: opacity .2s;
        }
        .exp-btn:hover { opacity: .9; }
        .exp-hint { margin-top: .55rem; font-size: .75rem; color: var(--text-muted); }

        /* Results */
        .results-header { font-size: .85rem; color: var(--text-muted); margin: 1.5rem 0 .75rem; }
        .caso-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.4rem 1.5rem;
            margin-bottom: .75rem;
            transition: border-color .2s;
        }
        .caso-card:hover { border-color: var(--border-hover); }
        .caso-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: .5rem; flex-wrap: wrap; gap: .5rem; }
        .caso-code  { font-weight: 700; font-size: .95rem; font-family: monospace; color: #818cf8; }
        .badge-estado {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .25rem .75rem; border-radius: 999px;
            font-size: .75rem; font-weight: 600;
        }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
        .badge-pendiente   { background: rgba(245,158,11,.15);  color:#fbbf24; border:1px solid rgba(245,158,11,.3); }
        .dot-pendiente     { background: #fbbf24; }
        .badge-en_proceso  { background: rgba(59,130,246,.15);  color:#60a5fa; border:1px solid rgba(59,130,246,.3); }
        .dot-en_proceso    { background: #60a5fa; }
        .badge-resuelto    { background: rgba(16,185,129,.15);  color:#34d399; border:1px solid rgba(16,185,129,.3); }
        .dot-resuelto      { background: #34d399; }
        .badge-cerrado     { background: rgba(107,114,128,.15); color:#9ca3af; border:1px solid rgba(107,114,128,.3); }
        .dot-cerrado       { background: #9ca3af; }

        .caso-estado-desc { font-size: .85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: .75rem; }
        .caso-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); gap: .5rem .75rem; margin-bottom: .75rem; }
        .caso-field-label { display: block; font-size: .72rem; color: var(--text-muted); margin-bottom: .15rem; }
        .caso-field-value { font-size: .85rem; font-weight: 500; }
        .caso-timeline { display: flex; flex-wrap: wrap; gap: .5rem 1.25rem; font-size: .75rem; color: var(--text-muted); border-top: 1px solid var(--border); padding-top: .75rem; }
        .caso-timeline span { display: flex; align-items: center; gap: .3rem; }

        .empty-state  { text-align: center; padding: 2rem 1rem; }
        .empty-icon   { font-size: 2.5rem; margin-bottom: .75rem; }
        .empty-title  { font-size: 1rem; font-weight: 700; margin-bottom: .4rem; }
        .empty-sub    { font-size: .85rem; color: var(--text-muted); line-height: 1.65; }

        .pagination-wrap { display: flex; align-items: center; justify-content: center; gap: .75rem; margin-top: 1.25rem; }
        .page-btn {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .45rem .9rem; border-radius: 8px; font-size: .8rem; font-weight: 500;
            background: rgba(255,255,255,0.05); border: 1px solid var(--border);
            color: var(--text-primary); text-decoration: none; transition: all .2s;
        }
        .page-btn:hover { background: rgba(255,255,255,0.08); border-color: var(--border-hover); }
        .page-btn-disabled { opacity: .4; cursor: default; pointer-events: none; }
        .page-info { font-size: .8rem; color: var(--text-muted); }

        @media (max-width: 640px) {
            nav { padding: .85rem 1.25rem; }
            .stats-bar { gap: 2rem; }
            .hero { padding-top: 5rem; }
        }
    </style>
</head>
<body>

<div class="bg-glow"></div>

<!-- ── Navbar ── -->
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
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Mi Panel
            </a>
        @else
            <a href="{{ route('login') }}" class="btn btn-ghost">Iniciar Sesión</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-primary">Registrarse</a>
            @endif
        @endauth
    </div>
</nav>

<!-- ── Hero ── -->
<section class="hero">
    <div class="hero-badge">
        <span class="hero-badge-dot"></span>
        Plataforma Confidencial y Segura
    </div>

    <h1>Cada voz importa.<br><span>Actuamos juntos</span> contra la violencia escolar.</h1>

    <p class="hero-sub">
        Un espacio seguro para reportar, dar seguimiento y prevenir situaciones de violencia en el entorno educativo.
        Confidencial, accesible y diseñado para proteger a toda la comunidad.
    </p>

    <div class="hero-cta">
        @auth
            <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg">Ir a mi Panel →</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Iniciar Sesión →</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-ghost btn-lg">Crear cuenta</a>
            @endif
        @endauth
    </div>
</section>

<!-- ── Stats ── -->
<div class="stats-bar">
    <div class="stat">
        <div class="stat-num">100%</div>
        <div class="stat-label">Confidencial</div>
    </div>
    <div class="stat">
        <div class="stat-num">4</div>
        <div class="stat-label">Roles de usuario</div>
    </div>
    <div class="stat">
        <div class="stat-num">24/7</div>
        <div class="stat-label">Disponible</div>
    </div>
    <div class="stat">
        <div class="stat-num">🔒</div>
        <div class="stat-label">Denuncia anónima</div>
    </div>
</div>

<!-- ── Roles section ── -->
<section class="section">
    <div class="section-label">Niveles de Usuario</div>
    <h2>¿Quiénes participan en el sistema?</h2>
    <p class="section-sub">
        Cada perfil tiene acceso a las herramientas que necesita para contribuir a un entorno escolar seguro.
    </p>

    <div class="roles-grid">

        <!-- Administrador -->
        <div class="role-card role-admin">
            <div class="role-icon icon-admin">🛡️</div>
            <div class="role-title">Administrador</div>
            <div class="role-desc">
                Gestión completa del sistema. Supervisa usuarios, roles, reportes y configuración general de la plataforma desde el panel administrativo.
            </div>
            <ul style="margin-top:.75rem; padding-left:1.1rem; font-size:.8rem; color:var(--text-muted); line-height:1.8;">
                <li>Gestión de usuarios y roles</li>
                <li>Acceso al panel Filament</li>
                <li>Visualización de todos los casos</li>
            </ul>
            <span class="role-badge badge-admin">Acceso total</span>
        </div>

        <!-- Psicólogo -->
        <div class="role-card role-psi">
            <div class="role-icon icon-psi">🧠</div>
            <div class="role-title">Psicólogo / Orientador</div>
            <div class="role-desc">
                Revisa, atiende y da seguimiento a los casos reportados. Registra intervenciones, actualiza estados y gestiona el bienestar estudiantil.
            </div>
            <ul style="margin-top:.75rem; padding-left:1.1rem; font-size:.8rem; color:var(--text-muted); line-height:1.8;">
                <li>Ver casos asignados y pendientes</li>
                <li>Registrar seguimientos</li>
                <li>Actualizar estado del caso</li>
            </ul>
            <span class="role-badge badge-psi">Atención de casos</span>
        </div>

        <!-- Docente -->
        <div class="role-card role-doc">
            <div class="role-icon icon-doc">📚</div>
            <div class="role-title">Docente / Tutor</div>
            <div class="role-desc">
                Puede reportar incidentes observados en el plantel. Actúa como primer respondedor para escalar situaciones al equipo de psicología.
            </div>
            <ul style="margin-top:.75rem; padding-left:1.1rem; font-size:.8rem; color:var(--text-muted); line-height:1.8;">
                <li>Reportar incidentes observados</li>
                <li>Acceso al formulario de denuncia</li>
                <li>Reportes confidenciales</li>
            </ul>
            <span class="role-badge badge-doc">Reportador</span>
        </div>

        <!-- Alumno -->
        <div class="role-card role-alum">
            <div class="role-icon icon-alum">🎓</div>
            <div class="role-title">Alumno</div>
            <div class="role-desc">
                Puede reportar situaciones de violencia de forma segura, eligiendo si hacerlo de forma anónima o identificada, sin miedo a represalias.
            </div>
            <ul style="margin-top:.75rem; padding-left:1.1rem; font-size:.8rem; color:var(--text-muted); line-height:1.8;">
                <li>Reportar incidentes sufridos</li>
                <li>Opción de anonimato total</li>
                <li>Formulario seguro y encriptado</li>
            </ul>
            <span class="role-badge badge-alum">Denunciante</span>
        </div>

    </div>

    <!-- Features -->
    <div class="features-grid">
        <div class="feature">
            <span class="feature-icon">🔐</span>
            <div>
                <div class="feature-title">Anonimato garantizado</div>
                <div class="feature-desc">El denunciante puede elegir reportar sin revelar su identidad en ningún momento.</div>
            </div>
        </div>
        <div class="feature">
            <span class="feature-icon">📋</span>
            <div>
                <div class="feature-title">Seguimiento completo</div>
                <div class="feature-desc">Cada caso tiene un historial detallado de intervenciones y cambios de estado.</div>
            </div>
        </div>
        <div class="feature">
            <span class="feature-icon">⚡</span>
            <div>
                <div class="feature-title">Respuesta rápida</div>
                <div class="feature-desc">Los psicólogos reciben notificaciones de casos nuevos y pueden actuar de inmediato.</div>
            </div>
        </div>
        <div class="feature">
            <span class="feature-icon">🏷️</span>
            <div>
                <div class="feature-title">Clasificación por tipo</div>
                <div class="feature-desc">Física, psicológica, verbal, sexual, ciberacoso, discriminación y más categorías.</div>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA Banner ── -->
<div class="cta-banner">
    <h3>¿Estás viviendo una situación de violencia?</h3>
    <p>No estás solo/a. Puedes reportarlo de forma completamente segura y confidencial. Nuestra comunidad está aquí para apoyarte.</p>
    <div class="btn-group">
        @auth
            <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg">Ir a mi Panel</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Acceder ahora</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-ghost btn-lg">Crear cuenta gratuita</a>
            @endif
        @endauth
    </div>
</div>

<!-- ── Consulta de expediente (inline) ── -->
<div class="exp-wrap" id="consulta">
    <div class="exp-card">
        <div class="exp-card-header">
            <div class="exp-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h3 class="exp-title">Consulta tu expediente</h3>
        </div>
        <p class="exp-sub">
            ¿Ya enviaste un reporte? Consulta el estado de tu caso por su código de expediente o por tu nombre.
        </p>

        @if($errors->any())
            <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:10px;padding:.85rem 1rem;margin-bottom:1rem;font-size:.85rem;color:#f87171;">
                @foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach
            </div>
        @endif

        <form method="GET" action="{{ url('/') }}#consulta" id="expForm">
            <div class="tipo-tabs">
                <button type="button"
                    class="tipo-tab {{ (!isset($tipo) || !$tipo || $tipo === 'codigo') ? 'active' : '' }}"
                    onclick="setExpTipo('codigo', this)">
                    🔢 Por código de expediente
                </button>
                <button type="button"
                    class="tipo-tab {{ (isset($tipo) && $tipo === 'nombre') ? 'active' : '' }}"
                    onclick="setExpTipo('nombre', this)">
                    👤 Por nombre del estudiante
                </button>
            </div>

            <input type="hidden" name="tipo" id="expTipoInput" value="{{ $tipo ?? 'codigo' }}">

            <div class="exp-input-wrap">
                <input
                    type="text"
                    id="expBusquedaInput"
                    name="busqueda"
                    class="exp-input"
                    placeholder="{{ (isset($tipo) && $tipo === 'nombre') ? 'Ej. Juan Pérez García' : 'Ej. VIO-2026-ABCDE1' }}"
                    value="{{ old('busqueda', $busqueda ?? '') }}"
                    autocomplete="off"
                    required
                >
                <button type="submit" class="exp-btn">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Consultar
                </button>
            </div>
            <p class="exp-hint" id="expHint">
                @if(isset($tipo) && $tipo === 'nombre')
                    Solo se muestran casos identificados (no anónimos).
                @else
                    Formato del código: VIO-AÑO-CÓDIGO (ej. VIO-2026-ABCDE1). Lo recibiste al registrar tu caso.
                @endif
            </p>
        </form>

        {{-- ── Resultados ── --}}
        @if(isset($buscado) && $buscado)
            @php
                $tipoLabels = [
                    'fisica'         => 'Física',
                    'psicologica'    => 'Psicológica',
                    'verbal'         => 'Verbal',
                    'sexual'         => 'Sexual',
                    'ciberacoso'     => 'Ciberacoso',
                    'discriminacion' => 'Discriminación',
                    'otro'           => 'Otro',
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

            @if($resultados && $resultados->isNotEmpty())
                <p class="results-header">
                    {{ $resultados->total() }} resultado{{ $resultados->total() !== 1 ? 's' : '' }}
                    para «{{ $busqueda }}»
                    @if($resultados->lastPage() > 1)
                        &mdash; página {{ $resultados->currentPage() }} de {{ $resultados->lastPage() }}
                    @endif
                </p>

                @foreach($resultados as $caso)
                    <div class="caso-card">
                        <div class="caso-header">
                            <span class="caso-code">{{ $caso->codigo_caso }}</span>
                            <span class="badge-estado badge-{{ $caso->estado }}">
                                <span class="badge-dot dot-{{ $caso->estado }}"></span>
                                {{ $estadoLabels[$caso->estado] ?? $caso->estado }}
                            </span>
                        </div>
                        <p class="caso-estado-desc">{{ $estadoDesc[$caso->estado] ?? '' }}</p>
                        <div class="caso-grid">
                            <div>
                                <span class="caso-field-label">Tipo de incidente</span>
                                <span class="caso-field-value">{{ $tipoLabels[$caso->tipo_violencia] ?? $caso->tipo_violencia }}</span>
                            </div>
                            <div>
                                <span class="caso-field-label">Prioridad</span>
                                <span class="caso-field-value" style="text-transform:capitalize;">{{ $caso->prioridad ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="caso-field-label">Institución educativa</span>
                                <span class="caso-field-value">{{ $caso->escuela_nombre ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="caso-field-label">Ubicación</span>
                                <span class="caso-field-value">
                                    {{ implode(', ', array_filter([$caso->distrito, $caso->provincia, $caso->region])) ?: '—' }}
                                </span>
                            </div>
                        </div>
                        <div class="caso-timeline">
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
                            <a href="{{ $resultados->previousPageUrl() }}#consulta" class="page-btn">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                Anterior
                            </a>
                        @endif
                        <span class="page-info">{{ $resultados->currentPage() }} / {{ $resultados->lastPage() }}</span>
                        @if($resultados->hasMorePages())
                            <a href="{{ $resultados->nextPageUrl() }}#consulta" class="page-btn">
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
                        @if(isset($tipo) && $tipo === 'codigo')
                            No existe ningún expediente con el código <strong style="color:var(--text-primary)">{{ $busqueda }}</strong>.<br>
                            Verifica que el código sea correcto (formato: VIO-AÑO-CÓDIGO).
                        @else
                            No se encontraron casos identificados para «<strong style="color:var(--text-primary)">{{ $busqueda }}</strong>».<br>
                            Los casos anónimos no aparecen en búsquedas por nombre.
                        @endif
                    </p>
                </div>
            @endif
        @endif
    </div>
</div>

<!-- ── Footer ── -->
<footer>
    <p>© {{ date('Y') }} Sistema de Prevención de Violencia Escolar · Todos los derechos reservados.</p>
    <p style="margin-top:.4rem;">Plataforma confidencial y segura · Construida con Laravel &amp; Filament</p>
</footer>

<script>
function setExpTipo(valor, btn) {
    document.getElementById('expTipoInput').value = valor;
    document.querySelectorAll('.tipo-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    const input = document.getElementById('expBusquedaInput');
    const hint  = document.getElementById('expHint');
    if (valor === 'nombre') {
        input.placeholder = 'Ej. Juan Pérez García';
        hint.textContent  = 'Solo se muestran casos identificados (no anónimos).';
    } else {
        input.placeholder = 'Ej. VIO-2026-ABCDE1';
        hint.textContent  = 'Formato del código: VIO-AÑO-CÓDIGO (ej. VIO-2026-ABCDE1). Lo recibiste al registrar tu caso.';
    }
    input.value = '';
    input.focus();
}
</script>

</body>
</html>
