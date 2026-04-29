<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SeguridadEscolar') }}</title>

    <!-- Bunny Fonts (GDPR-friendly, CSP-safe) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-dark:     #0d0f14;
            --bg-card:     #141720;
            --border:      rgba(255,255,255,0.07);
            --border-c:    rgba(255,255,255,0.12);
            --text-pri:    #f0f2f8;
            --text-muted:  #8b92a9;
            --accent:      #6366f1;
            --accent-2:    #8b5cf6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: var(--text-pri);
            min-height: 100vh;
            display: flex;
        }

        /* ── Left brand panel ── */
        .brand-panel {
            display: none;
            position: relative;
            overflow: hidden;
            background: linear-gradient(160deg, #0f1225 0%, #0d0f14 100%);
            border-right: 1px solid var(--border);
        }
        @media (min-width: 1024px) {
            .brand-panel { display: flex; flex-direction: column; justify-content: space-between; width: 45%; padding: 3rem; }
        }

        .bp-glow {
            position: absolute; inset: 0; pointer-events: none;
            background:
                radial-gradient(ellipse 70% 50% at 10% 20%, rgba(99,102,241,0.18) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 90% 80%, rgba(139,92,246,0.14) 0%, transparent 60%);
        }
        .bp-logo {
            position: relative; display: flex; align-items: center; gap: .75rem;
            font-size: 1.1rem; font-weight: 700;
        }
        .bp-logo-icon {
            width: 40px; height: 40px; border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 20px rgba(99,102,241,0.4);
        }

        .bp-content { position: relative; }
        .bp-title {
            font-size: 2.4rem; font-weight: 900; line-height: 1.15;
            letter-spacing: -.03em; margin-bottom: 1.25rem;
        }
        .bp-title span {
            background: linear-gradient(135deg, #818cf8, #c084fc, #f472b6);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .bp-desc { color: var(--text-muted); line-height: 1.7; font-size: .95rem; margin-bottom: 2.5rem; }

        .bp-features { display: flex; flex-direction: column; gap: .85rem; }
        .bp-feat {
            display: flex; align-items: center; gap: .85rem;
            padding: .85rem 1.1rem; border-radius: 12px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            font-size: .875rem;
        }
        .bp-feat-icon {
            width: 34px; height: 34px; border-radius: 8px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 1rem;
        }
        .bp-feat-label { font-weight: 600; color: var(--text-pri); }
        .bp-feat-sub   { font-size: .78rem; color: var(--text-muted); margin-top: .1rem; }

        .bp-footer { position: relative; font-size: .78rem; color: var(--text-muted); }

        /* ── Right form panel ── */
        .form-panel {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 2rem 1.5rem;
            background: var(--bg-dark);
            position: relative; overflow: hidden;
        }
        .form-panel::before {
            content: ''; position: absolute;
            top: -200px; right: -200px;
            width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.06), transparent 70%);
            pointer-events: none;
        }

        .form-box {
            position: relative; width: 100%; max-width: 420px;
            background: var(--bg-card);
            border: 1px solid var(--border-c);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4), 0 0 0 1px rgba(99,102,241,0.1);
        }

        .form-back {
            display: inline-flex; align-items: center; gap: .4rem;
            color: var(--text-muted); font-size: .82rem; text-decoration: none;
            margin-bottom: 2rem; transition: color .2s;
        }
        .form-back:hover { color: var(--text-pri); }

        .form-header { margin-bottom: 2rem; }
        .form-logo {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.25rem;
            box-shadow: 0 8px 25px rgba(99,102,241,0.3);
        }
        .form-title { font-size: 1.5rem; font-weight: 800; letter-spacing: -.02em; margin-bottom: .35rem; }
        .form-sub   { font-size: .875rem; color: var(--text-muted); }

        /* Slot content placeholder */
    </style>
</head>
<body>

    <!-- ── Brand panel (desktop only) ── -->
    <div class="brand-panel">
        <div class="bp-glow"></div>

        <div class="bp-logo">
            <div class="bp-logo-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            SeguridadEscolar
        </div>

        <div class="bp-content">
            <h2 class="bp-title">Tu voz es segura<br><span>aquí.</span></h2>
            <p class="bp-desc">
                Plataforma confidencial para la prevención y seguimiento de violencia escolar.
                Cada reporte es atendido por profesionales.
            </p>
            <div class="bp-features">
                <div class="bp-feat">
                    <div class="bp-feat-icon" style="background:rgba(99,102,241,.15);color:#818cf8">🔐</div>
                    <div>
                        <div class="bp-feat-label">Totalmente confidencial</div>
                        <div class="bp-feat-sub">Tu identidad está protegida en todo momento</div>
                    </div>
                </div>
                <div class="bp-feat">
                    <div class="bp-feat-icon" style="background:rgba(16,185,129,.15);color:#34d399">🧠</div>
                    <div>
                        <div class="bp-feat-label">Atención profesional</div>
                        <div class="bp-feat-sub">Psicólogos y orientadores revisarán tu caso</div>
                    </div>
                </div>
                <div class="bp-feat">
                    <div class="bp-feat-icon" style="background:rgba(245,158,11,.15);color:#fbbf24">⚡</div>
                    <div>
                        <div class="bp-feat-label">Respuesta rápida</div>
                        <div class="bp-feat-sub">Seguimiento en tiempo real de cada situación</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bp-footer">
            © {{ date('Y') }} SeguridadEscolar · Todos los derechos reservados
        </div>
    </div>

    <!-- ── Form panel ── -->
    <div class="form-panel">
        <div class="form-box">
            <a href="/" class="form-back">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al inicio
            </a>

            <div class="form-header">
                <div class="form-logo">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <div class="form-title">Bienvenido de vuelta</div>
                <div class="form-sub">Inicia sesión en tu cuenta para continuar</div>
            </div>

            {{ $slot }}
        </div>
    </div>

</body>
</html>
