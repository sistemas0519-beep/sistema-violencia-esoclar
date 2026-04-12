<x-guest-layout>

<style>
    /* ── Profile cards ── */
    .profiles-title {
        font-size: .75rem; font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase; color: #6b7394;
        margin-bottom: .75rem;
    }
    .profiles-grid {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: .6rem; margin-bottom: 1.5rem;
    }
    .profile-card {
        display: flex; align-items: center; gap: .6rem;
        padding: .6rem .75rem; border-radius: 10px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.07);
        cursor: pointer; transition: all .2s ease;
        text-align: left;
    }
    .profile-card:hover {
        border-color: rgba(255,255,255,0.15);
        background: rgba(255,255,255,0.06);
        transform: translateY(-1px);
    }
    .profile-card.active {
        border-color: var(--pc, rgba(99,102,241,0.6));
        background: var(--pc-bg, rgba(99,102,241,0.08));
        box-shadow: 0 0 0 1px var(--pc, rgba(99,102,241,0.3));
    }
    .profile-icon {
        width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem;
    }
    .profile-info { min-width: 0; }
    .profile-name { font-size: .78rem; font-weight: 700; color: #d1d5e8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .profile-email { font-size: .68rem; color: #5a6180; margin-top: .05rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* ── Form fields ── */
    .field       { margin-bottom: 1.1rem; }
    .field label {
        display: block; font-size: .78rem; font-weight: 600;
        color: #9ca3b8; letter-spacing: .04em; text-transform: uppercase;
        margin-bottom: .45rem;
    }
    .field-wrap { position: relative; }
    .field-icon {
        position: absolute; left: .85rem; top: 50%; transform: translateY(-50%);
        color: #4b5370; pointer-events: none;
    }
    .field-wrap input {
        width: 100%; padding: .72rem 1rem .72rem 2.5rem;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.09);
        border-radius: 10px;
        color: #f0f2f8; font-size: .88rem;
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .field-wrap input::placeholder { color: #3d4460; }
    .field-wrap input:focus {
        border-color: rgba(99,102,241,0.6);
        background: rgba(99,102,241,0.05);
        box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
    }
    .field-wrap input.err { border-color: rgba(239,68,68,0.5) !important; }
    .err-msg { font-size: .75rem; color: #f87171; margin-top: .35rem; }

    .status-msg {
        padding: .7rem 1rem; border-radius: 10px; margin-bottom: 1rem;
        background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25);
        color: #34d399; font-size: .82rem;
    }

    .check-row {
        display: flex; align-items: center; justify-content: space-between;
        margin: 1rem 0;
    }
    .check-label { display: flex; align-items: center; gap: .5rem; cursor: pointer; }
    .check-label input[type="checkbox"] { width: 15px; height: 15px; border-radius: 4px; accent-color: #6366f1; }
    .check-label span { font-size: .8rem; color: #8b92a9; }
    .forgot-link { font-size: .8rem; color: #818cf8; text-decoration: none; transition: color .2s; }
    .forgot-link:hover { color: #a5b4fc; text-decoration: underline; }

    .btn-submit {
        width: 100%; padding: .82rem;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none; border-radius: 10px;
        color: #fff; font-size: .93rem; font-weight: 600;
        font-family: 'Inter', sans-serif; cursor: pointer;
        transition: all .25s ease;
        box-shadow: 0 4px 18px rgba(99,102,241,0.28);
        position: relative; overflow: hidden;
    }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(99,102,241,0.42); }
    .btn-submit:active { transform: translateY(0); }

    .divider {
        display: flex; align-items: center; gap: 1rem;
        margin: 1.2rem 0; color: #4b5370; font-size: .75rem;
    }
    .divider::before, .divider::after { content:''; flex:1; height:1px; background: rgba(255,255,255,0.07); }

    .register-row { text-align: center; font-size: .82rem; color: #8b92a9; margin-top: 1.1rem; }
    .register-row a { color: #818cf8; text-decoration: none; font-weight: 600; }
    .register-row a:hover { text-decoration: underline; }
</style>

{{-- Session Status --}}
@if(session('status'))
    <div class="status-msg">{{ session('status') }}</div>
@endif

{{-- Profile selector --}}
<div class="profiles-title">Selecciona tu perfil de acceso</div>
<div class="profiles-grid">

    <button type="button" class="profile-card"
            style="--pc: rgba(239,68,68,.6); --pc-bg: rgba(239,68,68,.08);"
            onclick="selectProfile('admin@escuela.edu', 'password', this)">
        <div class="profile-icon" style="background:rgba(239,68,68,.15)">🛡️</div>
        <div class="profile-info">
            <div class="profile-name">Administrador</div>
            <div class="profile-email">admin@escuela.edu</div>
        </div>
    </button>

    <button type="button" class="profile-card"
            style="--pc: rgba(16,185,129,.6); --pc-bg: rgba(16,185,129,.08);"
            onclick="selectProfile('psicologo@escuela.edu', 'password', this)">
        <div class="profile-icon" style="background:rgba(16,185,129,.15)">🧠</div>
        <div class="profile-info">
            <div class="profile-name">Psicólogo</div>
            <div class="profile-email">psicologo@escuela.edu</div>
        </div>
    </button>

    <button type="button" class="profile-card"
            style="--pc: rgba(59,130,246,.6); --pc-bg: rgba(59,130,246,.08);"
            onclick="selectProfile('docente@escuela.edu', 'password', this)">
        <div class="profile-icon" style="background:rgba(59,130,246,.15)">📚</div>
        <div class="profile-info">
            <div class="profile-name">Docente / Tutor</div>
            <div class="profile-email">docente@escuela.edu</div>
        </div>
    </button>

    <button type="button" class="profile-card"
            style="--pc: rgba(245,158,11,.6); --pc-bg: rgba(245,158,11,.08);"
            onclick="selectProfile('alumno@escuela.edu', 'password', this)">
        <div class="profile-icon" style="background:rgba(245,158,11,.15)">🎓</div>
        <div class="profile-info">
            <div class="profile-name">Alumno</div>
            <div class="profile-email">alumno@escuela.edu</div>
        </div>
    </button>

</div>

{{-- Login form --}}
<form method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf

    <div class="field">
        <label for="email">Correo Electrónico</label>
        <div class="field-wrap">
            <span class="field-icon">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </span>
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}"
                   placeholder="tu@correo.edu"
                   required autofocus autocomplete="username"
                   class="{{ $errors->has('email') ? 'err' : '' }}">
        </div>
        @error('email') <p class="err-msg">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="password">Contraseña</label>
        <div class="field-wrap">
            <span class="field-icon">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </span>
            <input type="password" id="password" name="password"
                   placeholder="••••••••"
                   required autocomplete="current-password"
                   class="{{ $errors->has('password') ? 'err' : '' }}">
        </div>
        @error('password') <p class="err-msg">{{ $message }}</p> @enderror
    </div>

    <div class="check-row">
        <label class="check-label">
            <input type="checkbox" name="remember" id="remember_me">
            <span>Recordarme</span>
        </label>
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="forgot-link">¿Olvidaste tu contraseña?</a>
        @endif
    </div>

    <button type="submit" class="btn-submit">Iniciar Sesión →</button>

    @if (Route::has('register'))
        <div class="divider">o</div>
        <div class="register-row">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
        </div>
    @endif
</form>

<script>
function selectProfile(email, pass, card) {
    // Quitar clase active de todas
    document.querySelectorAll('.profile-card').forEach(c => c.classList.remove('active'));
    // Activar la seleccionada
    card.classList.add('active');
    // Rellenar campos
    document.getElementById('email').value    = email;
    document.getElementById('password').value = pass;
}
</script>

</x-guest-layout>
