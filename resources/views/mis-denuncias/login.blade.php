<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Consultar Mis Denuncias — Sistema de Violencia Escolar</title>
    <meta name="description" content="Inicia sesión para consultar el estado de tus denuncias registradas en el sistema.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-dark:      #0d0f14;
            --bg-card:      #141720;
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .bg-glow {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% -10%, rgba(99,102,241,0.15) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 110%, rgba(139,92,246,0.12) 0%, transparent 60%);
        }

        .login-wrapper {
            position: relative; z-index: 1;
            width: 100%; max-width: 440px;
        }

        /* ── Brand ── */
        .brand {
            display: flex; align-items: center; gap: .75rem;
            justify-content: center; margin-bottom: 2rem;
        }
        .brand-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 20px rgba(99,102,241,0.4);
        }
        .brand-name {
            font-size: 1.1rem; font-weight: 800; color: var(--text-primary);
        }
        .brand-tagline {
            font-size: .75rem; color: var(--text-muted);
        }

        /* ── Card ── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 2.25rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }

        .card-header {
            text-align: center; margin-bottom: 2rem;
        }
        .module-badge {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .3rem .9rem; border-radius: 999px;
            background: rgba(99,102,241,0.12); border: 1px solid rgba(99,102,241,0.3);
            color: #818cf8; font-size: .72rem; font-weight: 700;
            letter-spacing: .06em; text-transform: uppercase;
            margin-bottom: 1rem;
        }
        .card-title {
            font-size: 1.5rem; font-weight: 900; letter-spacing: -.02em;
            color: var(--text-primary); margin-bottom: .4rem;
        }
        .card-sub {
            font-size: .875rem; color: var(--text-muted); line-height: 1.6;
        }

        /* ── Role pills ── */
        .roles-label {
            font-size: .72rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--text-muted);
            margin-bottom: .6rem;
        }
        .roles-row {
            display: flex; gap: .5rem; margin-bottom: 1.5rem;
        }
        .role-pill {
            flex: 1; display: flex; align-items: center; justify-content: center;
            gap: .4rem; padding: .6rem .75rem; border-radius: 10px;
            border: 2px solid var(--border);
            background: rgba(255,255,255,0.03);
            font-size: .8rem; font-weight: 600; color: var(--text-muted);
            cursor: pointer; transition: all .2s; font-family: inherit;
        }
        .role-pill:hover { border-color: var(--border-hover); color: var(--text-primary); }
        .role-pill.alumno  { --rc: rgba(245,158,11,.6);  --rb: rgba(245,158,11,.08); }
        .role-pill.docente { --rc: rgba(59,130,246,.6);  --rb: rgba(59,130,246,.08); }
        .role-pill.active  { border-color: var(--rc); background: var(--rb); color: var(--text-primary); }

        /* ── Alerts ── */
        .alert {
            padding: .8rem 1rem; border-radius: 10px;
            margin-bottom: 1.25rem; font-size: .85rem; line-height: 1.5;
        }
        .alert-error   { background: rgba(239,68,68,.08);  border: 1px solid rgba(239,68,68,.2);  color: #f87171; }
        .alert-warning { background: rgba(245,158,11,.08); border: 1px solid rgba(245,158,11,.2); color: #fbbf24; }
        .alert-success { background: rgba(16,185,129,.08); border: 1px solid rgba(16,185,129,.2); color: #34d399; }

        /* ── Form fields ── */
        .field { margin-bottom: 1rem; }
        .field-label {
            display: block; font-size: .75rem; font-weight: 700;
            letter-spacing: .05em; text-transform: uppercase;
            color: var(--text-muted); margin-bottom: .45rem;
        }
        .field-wrap { position: relative; }
        .field-icon {
            position: absolute; left: .875rem; top: 50%; transform: translateY(-50%);
            color: var(--text-muted); pointer-events: none;
        }
        .field-wrap input {
            width: 100%; padding: .75rem 1rem .75rem 2.6rem;
            background: rgba(255,255,255,0.05); border: 1px solid var(--border);
            border-radius: 10px; color: var(--text-primary);
            font-size: .9rem; font-family: inherit; outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .field-wrap input::placeholder { color: var(--text-muted); opacity: .6; }
        .field-wrap input:focus {
            border-color: rgba(99,102,241,.5);
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        }
        .field-wrap input.err { border-color: rgba(239,68,68,.5); }
        .field-err { font-size: .75rem; color: #f87171; margin-top: .3rem; }

        /* ── Toggle password visibility ── */
        .btn-eye {
            position: absolute; right: .875rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--text-muted);
            cursor: pointer; padding: 0; display: flex; align-items: center;
            transition: color .2s;
        }
        .btn-eye:hover { color: var(--text-primary); }

        /* ── Options row ── */
        .options-row {
            display: flex; align-items: center; justify-content: space-between;
            margin: .75rem 0 1.25rem;
        }
        .check-label {
            display: flex; align-items: center; gap: .5rem;
            font-size: .82rem; color: var(--text-muted); cursor: pointer;
        }
        .check-label input[type="checkbox"] { width: 15px; height: 15px; accent-color: #6366f1; }
        .link-forgot { font-size: .8rem; color: #818cf8; text-decoration: none; }
        .link-forgot:hover { text-decoration: underline; }

        /* ── Submit ── */
        .btn-submit {
            width: 100%; padding: .9rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none; border-radius: 10px;
            color: #fff; font-size: .95rem; font-weight: 700;
            font-family: inherit; cursor: pointer;
            transition: all .25s; box-shadow: 0 4px 18px rgba(99,102,241,.3);
            display: flex; align-items: center; justify-content: center; gap: .5rem;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(99,102,241,.45); }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* ── Back link ── */
        .back-link {
            display: flex; align-items: center; justify-content: center;
            gap: .4rem; margin-top: 1.5rem;
            font-size: .82rem; color: var(--text-muted); text-decoration: none;
            transition: color .2s;
        }
        .back-link:hover { color: var(--text-primary); }

        /* ── Access denied card ── */
        .denied-card {
            text-align: center; padding: 2rem;
        }
        .denied-icon { font-size: 3rem; margin-bottom: 1rem; }
        .denied-title { font-size: 1.1rem; font-weight: 800; margin-bottom: .5rem; color: var(--text-primary); }
        .denied-text { font-size: .875rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem; }
        .btn-back {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .65rem 1.5rem; border-radius: 10px;
            border: 1px solid var(--border); background: transparent;
            color: var(--text-muted); font-size: .875rem; font-weight: 600;
            text-decoration: none; transition: all .2s; font-family: inherit; cursor: pointer;
        }
        .btn-back:hover { border-color: var(--border-hover); color: var(--text-primary); }

        .divider { height: 1px; background: var(--border); margin: 1.5rem 0; }

        .info-chips {
            display: flex; gap: .5rem; flex-wrap: wrap; justify-content: center;
            margin-top: 1.25rem;
        }
        .chip {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .3rem .7rem; border-radius: 999px;
            background: rgba(255,255,255,0.04); border: 1px solid var(--border);
            font-size: .72rem; color: var(--text-muted);
        }
        .chip svg { color: #818cf8; }
    </style>
</head>
<body>
<div class="bg-glow"></div>

<div class="login-wrapper">

    <!-- Brand -->
    <div class="brand">
        <div class="brand-icon">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <div>
            <div class="brand-name">SeguridadEscolar</div>
            <div class="brand-tagline">Sistema de Atención a Casos de Violencia</div>
        </div>
    </div>

    <div class="card">

        @if(isset($rolNoPermitido) && $rolNoPermitido)

            {{-- ── Usuario de otro rol autenticado ── --}}
            <div class="denied-card">
                <div class="denied-icon">🚫</div>
                <div class="denied-title">Acceso Restringido</div>
                <p class="denied-text">
                    Este módulo es exclusivo para <strong style="color:var(--text-primary)">alumnos y docentes</strong>.<br>
                    Tu perfil (<strong style="color:var(--text-primary)">{{ ucfirst($rolNoPermitido) }}</strong>)
                    debe ingresar por el acceso correspondiente.
                </p>
                <a href="{{ url('/dashboard') }}" class="btn-submit" style="text-decoration:none; margin-bottom:.75rem;">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Ir a Mi Panel
                </a>
                <form method="POST" action="{{ route('mis-denuncias.salir') }}" style="margin-top:.75rem;">
                    @csrf
                    <button type="submit" class="btn-back" style="width:100%; justify-content:center;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Cerrar Sesión y Cambiar Cuenta
                    </button>
                </form>
            </div>

        @else

            {{-- ── Formulario de login ── --}}
            <div class="card-header">
                <div class="module-badge">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Consultar Denuncias
                </div>
                <h1 class="card-title">Ingresa a tu cuenta</h1>
                <p class="card-sub">Consulta el estado de tus denuncias registradas.</p>
            </div>

            {{-- Status (logout, etc.) --}}
            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            {{-- Error de autenticación --}}
            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $err)
                        <p>{{ $err }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Selector de rol (visual, no funcional en backend) --}}
            <div class="roles-label">Soy...</div>
            <div class="roles-row">
                <button type="button" id="pill-alumno" class="role-pill alumno active"
                        onclick="selectRole('alumno')">
                    🎓 Alumno
                </button>
                <button type="button" id="pill-docente" class="role-pill docente"
                        onclick="selectRole('docente')">
                    📚 Docente
                </button>
            </div>

            <form method="POST" action="{{ route('mis-denuncias.login.post') }}" id="loginForm" novalidate>
                @csrf

                <div class="field">
                    <label class="field-label" for="email">Correo Electrónico</label>
                    <div class="field-wrap">
                        <span class="field-icon">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="tucorreo@escuela.edu"
                            required
                            autocomplete="email"
                            autofocus
                            class="{{ $errors->has('email') ? 'err' : '' }}"
                        >
                    </div>
                    @error('email')
                        <p class="field-err">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label class="field-label" for="password">Contraseña</label>
                    <div class="field-wrap">
                        <span class="field-icon">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                            class="{{ $errors->has('password') ? 'err' : '' }}"
                        >
                        <button type="button" class="btn-eye" id="togglePwd" title="Mostrar/ocultar contraseña">
                            <svg id="eyeIcon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="field-err">{{ $message }}</p>
                    @enderror
                </div>

                <div class="options-row">
                    <label class="check-label">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Recordarme</span>
                    </label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link-forgot">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Ingresar
                </button>
            </form>

            <div class="info-chips">
                <span class="chip">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Acceso seguro
                </span>
                <span class="chip">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Solo para alumnos y docentes
                </span>
            </div>

        @endif

    </div>

    <a href="/" class="back-link">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Volver al inicio
    </a>
</div>

<script>
    // ── Selector visual de rol ──────────────────────────────────────────────
    function selectRole(role) {
        document.querySelectorAll('.role-pill').forEach(p => p.classList.remove('active'));
        document.getElementById('pill-' + role).classList.add('active');
    }

    // ── Mostrar/ocultar contraseña ─────────────────────────────────────────
    const togglePwd = document.getElementById('togglePwd');
    const pwdInput  = document.getElementById('password');
    const eyeIcon   = document.getElementById('eyeIcon');

    if (togglePwd) {
        togglePwd.addEventListener('click', () => {
            const isText = pwdInput.type === 'text';
            pwdInput.type = isText ? 'password' : 'text';
            eyeIcon.innerHTML = isText
                ? `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                   <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`
                : `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
        });
    }

    // ── Deshabilitar botón en submit para evitar doble envío ───────────────
    const form      = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    if (form) {
        form.addEventListener('submit', () => {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="animation:spin .8s linear infinite">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Verificando...`;
            }
        });
    }
</script>
<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
</body>
</html>
