<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Prevención de Violencia Escolar</title>
    <meta name="description" content="Plataforma segura y confidencial para la prevención, denuncia y seguimiento de situaciones de violencia escolar.">

    <!-- Google Fonts -->
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

<!-- ── Footer ── -->
<footer>
    <p>© {{ date('Y') }} Sistema de Prevención de Violencia Escolar · Todos los derechos reservados.</p>
    <p style="margin-top:.4rem;">Plataforma confidencial y segura · Construida con Laravel &amp; Filament</p>
</footer>

</body>
</html>
