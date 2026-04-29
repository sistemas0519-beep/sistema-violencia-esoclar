<x-guest-layout>

<style>
    /* ── Rol cards ── */
    .rol-title {
        font-size: .75rem; font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase; color: #6b7394;
        margin-bottom: .75rem;
    }
    .rol-grid {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: .6rem; margin-bottom: 1.5rem;
    }
    .rol-card {
        display: flex; flex-direction: column; align-items: center; gap: .35rem;
        padding: .9rem .6rem; border-radius: 12px;
        background: rgba(255,255,255,0.03);
        border: 2px solid rgba(255,255,255,0.07);
        cursor: pointer; transition: all .2s ease;
        text-align: center; position: relative;
    }
    .rol-card:hover {
        border-color: rgba(255,255,255,0.18);
        background: rgba(255,255,255,0.06);
        transform: translateY(-2px);
    }
    .rol-card.selected {
        border-color: var(--rc, rgba(99,102,241,.7));
        background: var(--rc-bg, rgba(99,102,241,.08));
        box-shadow: 0 0 0 1px var(--rc, rgba(99,102,241,.3));
    }
    .rol-card .check-mark {
        position: absolute; top: .45rem; right: .5rem;
        width: 16px; height: 16px; border-radius: 50%;
        background: var(--rc, #6366f1);
        display: none; align-items: center; justify-content: center;
    }
    .rol-card.selected .check-mark { display: flex; }
    .rol-icon {
        width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: 1.15rem;
    }
    .rol-name  { font-size: .78rem; font-weight: 700; color: #d1d5e8; }
    .rol-sub   { font-size: .65rem; color: #5a6180; line-height: 1.3; }

    /* ── Error global del rol ── */
    .rol-error { font-size: .75rem; color: #f87171; margin-top: -.75rem; margin-bottom: .75rem; }

    /* ── Form fields (mismo estilo que login) ── */
    .field       { margin-bottom: 1rem; }
    .field label {
        display: block; font-size: .78rem; font-weight: 600;
        color: #9ca3b8; letter-spacing: .04em; text-transform: uppercase;
        margin-bottom: .45rem;
    }
    .field-wrap  { position: relative; }
    .field-icon  {
        position: absolute; left: .85rem; top: 50%; transform: translateY(-50%);
        color: #4b5370; pointer-events: none;
    }
    .field-wrap input {
        width: 100%; padding: .7rem 1rem .7rem 2.5rem;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.09);
        border-radius: 10px;
        color: #f0f2f8; font-size: .88rem;
        font-family: 'Inter', sans-serif; outline: none;
        transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .field-wrap input::placeholder { color: #3d4460; }
    .field-wrap input:focus {
        border-color: rgba(99,102,241,.6);
        background: rgba(99,102,241,.05);
        box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    }
    .field-wrap input.err { border-color: rgba(239,68,68,.5) !important; }
    .err-msg { font-size: .75rem; color: #f87171; margin-top: .35rem; }

    .btn-submit {
        width: 100%; padding: .82rem;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none; border-radius: 10px;
        color: #fff; font-size: .93rem; font-weight: 600;
        font-family: 'Inter', sans-serif; cursor: pointer;
        transition: all .25s ease;
        box-shadow: 0 4px 18px rgba(99,102,241,.28);
    }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(99,102,241,.42); }

    .login-row { text-align: center; font-size: .82rem; color: #8b92a9; margin-top: 1.1rem; }
    .login-row a { color: #818cf8; text-decoration: none; font-weight: 600; }
    .login-row a:hover { text-decoration: underline; }

    .divider {
        display: flex; align-items: center; gap: 1rem;
        margin: 1rem 0; color: #4b5370; font-size: .75rem;
    }
    .divider::before, .divider::after { content:''; flex:1; height:1px; background: rgba(255,255,255,0.07); }
</style>

<form method="POST" action="{{ route('register') }}" id="registerForm">
    @csrf

    {{-- ── Selector de tipo de perfil ── --}}
    <div class="rol-title">Selecciona tu tipo de perfil</div>

    <div class="rol-grid">

        <label class="rol-card" style="--rc: rgba(245,158,11,.7); --rc-bg: rgba(245,158,11,.08);" id="card-alumno">
            <input type="radio" name="rol" value="alumno" class="sr-only"
                   {{ old('rol') === 'alumno' ? 'checked' : '' }}
                   onchange="selectRol(this, 'card-alumno')">
            <div class="check-mark">
                <svg width="8" height="8" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="rol-icon" style="background:rgba(245,158,11,.15); color:#fbbf24">🎓</div>
            <div class="rol-name">Alumno</div>
            <div class="rol-sub">Reportar y dar seguimiento a incidentes</div>
        </label>

        <label class="rol-card" style="--rc: rgba(59,130,246,.7); --rc-bg: rgba(59,130,246,.08);" id="card-docente">
            <input type="radio" name="rol" value="docente" class="sr-only"
                   {{ old('rol') === 'docente' ? 'checked' : '' }}
                   onchange="selectRol(this, 'card-docente')">
            <div class="check-mark">
                <svg width="8" height="8" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="rol-icon" style="background:rgba(59,130,246,.15); color:#60a5fa">📚</div>
            <div class="rol-name">Docente / Tutor</div>
            <div class="rol-sub">Reportar incidentes observados</div>
        </label>

        <label class="rol-card" style="--rc: rgba(16,185,129,.7); --rc-bg: rgba(16,185,129,.08);" id="card-psicologo">
            <input type="radio" name="rol" value="psicologo" class="sr-only"
                   {{ old('rol') === 'psicologo' ? 'checked' : '' }}
                   onchange="selectRol(this, 'card-psicologo')">
            <div class="check-mark">
                <svg width="8" height="8" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="rol-icon" style="background:rgba(16,185,129,.15); color:#34d399">🧠</div>
            <div class="rol-name">Psicólogo</div>
            <div class="rol-sub">Atender y gestionar casos</div>
        </label>



    </div>

    @error('rol')
        <p class="rol-error">{{ $message }}</p>
    @enderror

    <div class="divider">datos personales</div>

    {{-- Nombre --}}
    <div class="field">
        <label for="name">Nombre completo</label>
        <div class="field-wrap">
            <span class="field-icon">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </span>
            <input type="text" id="name" name="name"
                   value="{{ old('name') }}" placeholder="Tu nombre"
                   required autofocus autocomplete="name"
                   class="{{ $errors->has('name') ? 'err' : '' }}">
        </div>
        @error('name') <p class="err-msg">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div class="field">
        <label for="email">Correo Electrónico</label>
        <div class="field-wrap">
            <span class="field-icon">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </span>
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}" placeholder="tu@correo.edu"
                   required autocomplete="username"
                   class="{{ $errors->has('email') ? 'err' : '' }}">
        </div>
        @error('email') <p class="err-msg">{{ $message }}</p> @enderror
    </div>

    {{-- Contraseña --}}
    <div class="field">
        <label for="password">Contraseña</label>
        <div class="field-wrap">
            <span class="field-icon">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </span>
            <input type="password" id="password" name="password"
                   placeholder="Mínimo 8 caracteres"
                   required autocomplete="new-password"
                   class="{{ $errors->has('password') ? 'err' : '' }}">
        </div>
        @error('password') <p class="err-msg">{{ $message }}</p> @enderror
    </div>

    {{-- Confirmar contraseña --}}
    <div class="field">
        <label for="password_confirmation">Confirmar Contraseña</label>
        <div class="field-wrap">
            <span class="field-icon">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </span>
            <input type="password" id="password_confirmation"
                   name="password_confirmation"
                   placeholder="Repite tu contraseña"
                   required autocomplete="new-password"
                   class="{{ $errors->has('password_confirmation') ? 'err' : '' }}">
        </div>
        @error('password_confirmation') <p class="err-msg">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="btn-submit">Crear Cuenta →</button>

    <div class="login-row" style="margin-top:.9rem;">
        ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
    </div>

</form>

<script>
    // Restaurar card seleccionada si hay old('rol') (tras error de validación)
    const oldRol = '{{ old("rol") }}';
    if (oldRol) selectRolByValue(oldRol);

    function selectRol(radio, cardId) {
        // quitar selección de todas
        document.querySelectorAll('.rol-card').forEach(c => c.classList.remove('selected'));
        // marcar la elegida
        document.getElementById(cardId).classList.add('selected');
    }

    function selectRolByValue(value) {
        const radio = document.querySelector(`input[name="rol"][value="${value}"]`);
        if (radio) {
            radio.checked = true;
            radio.closest('.rol-card').classList.add('selected');
        }
    }
</script>

</x-guest-layout>
