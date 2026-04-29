<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">

        {{-- ── Modal advertencia de inactividad ───────────────────────────── --}}
        @auth
        <div id="inactivity-modal"
             class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm"
             role="dialog" aria-modal="true" aria-labelledby="inactivity-title">
            <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center">
                <div class="flex justify-center mb-4">
                    <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-amber-100">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </span>
                </div>
                <h2 id="inactivity-title" class="text-lg font-bold text-gray-800 mb-2">
                    Sesión por expirar
                </h2>
                <p class="text-gray-500 text-sm mb-1">
                    Tu sesión cerrará automáticamente en
                </p>
                <p class="text-3xl font-mono font-bold text-amber-600 mb-4" id="countdown-display">02:00</p>
                <p class="text-gray-400 text-xs mb-6">
                    Si sigues activo, haz clic en "Continuar sesión" para permanecer conectado.
                </p>
                <div class="flex gap-3 justify-center">
                    <button id="btn-keep-session"
                            class="px-5 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                        Continuar sesión
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
        (function () {
            const TOTAL_SECONDS    = {{ (30 - 2) * 60 }}; // 28 minutos → mostrar aviso
            const WARNING_SECONDS  = 2 * 60;              // 2 minutos de cuenta regresiva
            const PING_URL         = '{{ url('/') }}';
            const LOGOUT_URL       = '{{ route('logout') }}';
            const CSRF_TOKEN       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            let idleTimer, warnTimer, countdownInterval;
            const modal = document.getElementById('inactivity-modal');
            const display = document.getElementById('countdown-display');
            const btnKeep = document.getElementById('btn-keep-session');

            function formatTime(s) {
                const m = String(Math.floor(s / 60)).padStart(2, '0');
                const sec = String(s % 60).padStart(2, '0');
                return `${m}:${sec}`;
            }

            function showWarning() {
                modal.classList.remove('hidden');
                let remaining = WARNING_SECONDS;
                display.textContent = formatTime(remaining);

                countdownInterval = setInterval(() => {
                    remaining--;
                    display.textContent = formatTime(remaining);
                    if (remaining <= 0) {
                        clearInterval(countdownInterval);
                        autoLogout();
                    }
                }, 1000);
            }

            function autoLogout() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = LOGOUT_URL;
                const csrf = document.createElement('input');
                csrf.type  = 'hidden'; csrf.name = '_token'; csrf.value = CSRF_TOKEN;
                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            }

            function resetTimers() {
                clearTimeout(idleTimer);
                clearTimeout(warnTimer);
                clearInterval(countdownInterval);
                modal.classList.add('hidden');

                idleTimer = setTimeout(showWarning, TOTAL_SECONDS * 1000);
            }

            // Mantener sesión activa (ping al servidor)
            btnKeep.addEventListener('click', () => {
                fetch(PING_URL, { credentials: 'same-origin' })
                    .then(() => resetTimers())
                    .catch(() => resetTimers());
            });

            // Reiniciar contador en actividad del usuario
            ['mousemove', 'keydown', 'mousedown', 'touchstart', 'scroll'].forEach(evt => {
                document.addEventListener(evt, resetTimers, { passive: true });
            });

            // Arrancar
            resetTimers();
        })();
        </script>
        @endauth

        <div class="min-h-screen bg-gray-50">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/80 backdrop-blur-sm border-b border-gray-100">
                    <div class="max-w-7xl mx-auto py-4 sm:py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
