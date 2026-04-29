<x-filament-panels::page>
<div x-data="{
    activeTab: 'general',
    showCharts: false,
    init() {
        this.$nextTick(() => { this.showCharts = true; this.initCharts(); });
    },
    initCharts() {
        this.$nextTick(() => {
            this.renderDonutChart();
            this.renderBarChart();
            this.renderLineChart();
            this.renderRegionChart();
            this.renderChart14d();
            this.renderPrioridadChart();
            this.renderResolucionChart();
            this.renderEscuelasChart();
        });
    },
    renderDonutChart() {
        const el = document.getElementById('donutChart');
        if (!el) return;
        const ctx = el.getContext('2d');
        if (el._chartInstance) el._chartInstance.destroy();
        el._chartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pendientes', 'En Proceso', 'Resueltos', 'Cerrados'],
                datasets: [{
                    data: [{{ $this->resumen['pendiente'] }}, {{ $this->resumen['en_proceso'] }}, {{ $this->resumen['resuelto'] }}, {{ $this->resumen['cerrado'] }}],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#6b7280'],
                    borderWidth: 0,
                    hoverOffset: 8,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17,24,39,.9)',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: (c) => ` ${c.label}: ${c.parsed} (${Math.round(c.parsed / Math.max(c.dataset.data.reduce((a,b)=>a+b,0),1) * 100)}%)`
                        }
                    }
                },
                animation: { animateRotate: true, duration: 1200, easing: 'easeOutQuart' }
            }
        });
    },
    renderBarChart() {
        const el = document.getElementById('barChart');
        if (!el) return;
        const ctx = el.getContext('2d');
        if (el._chartInstance) el._chartInstance.destroy();
        const tipos = @js($this->porTipo);
        const nombres = { fisica:'Física', psicologica:'Psicológica', verbal:'Verbal', sexual:'Sexual', ciberacoso:'Ciberacoso', discriminacion:'Discriminación', otro:'Otro' };
        const colores = { fisica:'#ef4444', psicologica:'#f97316', verbal:'#eab308', sexual:'#ec4899', ciberacoso:'#3b82f6', discriminacion:'#8b5cf6', otro:'#6b7280' };
        el._chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: tipos.map(t => nombres[t.tipo] || t.tipo),
                datasets: [{
                    label: 'Casos',
                    data: tipos.map(t => t.total),
                    backgroundColor: tipos.map(t => (colores[t.tipo] || '#6b7280') + 'cc'),
                    borderColor: tipos.map(t => colores[t.tipo] || '#6b7280'),
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17,24,39,.9)',
                        padding: 12, cornerRadius: 10,
                        callbacks: { label: (c) => ` ${c.parsed.y} casos` }
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,.06)' } },
                    x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            }
        });
    },
    renderLineChart() {
        const el = document.getElementById('lineChart');
        if (!el) return;
        const ctx = el.getContext('2d');
        if (el._chartInstance) el._chartInstance.destroy();
        const meses = @js($this->porMes);
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(99,102,241,.25)');
        gradient.addColorStop(1, 'rgba(99,102,241,.01)');
        el._chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: meses.map(m => m.mes),
                datasets: [{
                    label: 'Casos',
                    data: meses.map(m => m.total),
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17,24,39,.9)',
                        padding: 12, cornerRadius: 10,
                        callbacks: { label: (c) => ` ${c.parsed.y} casos` }
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,.06)' } },
                    x: { ticks: { font: { size: 10 }, maxRotation: 45 }, grid: { display: false } }
                },
                animation: { duration: 1200, easing: 'easeOutQuart' }
            }
        });
    },
    renderRegionChart() {
        const el = document.getElementById('regionChart');
        if (!el) return;
        const ctx = el.getContext('2d');
        if (el._chartInstance) el._chartInstance.destroy();
        const regiones = @js($this->porRegion);
        el._chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: regiones.map(r => r.region.charAt(0).toUpperCase() + r.region.slice(1).toLowerCase()),
                datasets: [{
                    label: 'Casos',
                    data: regiones.map(r => r.total),
                    backgroundColor: 'rgba(20,184,166,.7)',
                    borderColor: '#14b8a6',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17,24,39,.9)',
                        padding: 12, cornerRadius: 10,
                    }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,.06)' } },
                    y: { ticks: { font: { size: 11 } }, grid: { display: false } }
                },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            }
        });
    },
    renderChart14d() {
        const el = document.getElementById('chart14d');
        if (!el) return;
        const ctx = el.getContext('2d');
        if (el._chartInstance) el._chartInstance.destroy();
        const datos = @js($this->diasData ?? []);
        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(20,184,166,.3)');
        gradient.addColorStop(1, 'rgba(20,184,166,.01)');
        el._chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: datos.map(d => d.fecha),
                datasets: [{
                    label: 'Casos',
                    data: datos.map(d => d.casos),
                    borderColor: '#14b8a6',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#14b8a6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: 'rgba(17,24,39,.9)', padding: 10, cornerRadius: 8,
                        callbacks: { label: (c) => ` ${c.parsed.y} casos` } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: 'rgba(0,0,0,.05)' } },
                    x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                },
                animation: { duration: 900, easing: 'easeOutQuart' }
            }
        });
    },
    renderPrioridadChart() {
        const el = document.getElementById('prioridadChart');
        if (!el) return;
        const ctx = el.getContext('2d');
        if (el._chartInstance) el._chartInstance.destroy();
        const datos = @js($this->distribucionPrioridad ?? []);
        const labels = { baja: 'Baja', normal: 'Normal', alta: 'Alta', urgente: 'Urgente' };
        const colors = { baja: '#6b7280', normal: '#3b82f6', alta: '#f97316', urgente: '#ef4444' };
        el._chartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: datos.map(d => labels[d.prioridad] || d.prioridad),
                datasets: [{ data: datos.map(d => d.total),
                    backgroundColor: datos.map(d => colors[d.prioridad] || '#6b7280'),
                    borderWidth: 0, hoverOffset: 6, borderRadius: 3 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '68%',
                plugins: { legend: { position: 'bottom', labels: { padding: 12, font: { size: 11 } } },
                    tooltip: { backgroundColor: 'rgba(17,24,39,.9)', padding: 10, cornerRadius: 8 } }
            }
        });
    },
    renderResolucionChart() {
        const el = document.getElementById('resolucionChart');
        if (!el) return;
        const ctx = el.getContext('2d');
        if (el._chartInstance) el._chartInstance.destroy();
        const datos = @js($this->tasasResolucion ?? []);
        el._chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: datos.map(d => d.mes),
                datasets: [{
                    label: 'Tasa %',
                    data: datos.map(d => d.tasa),
                    backgroundColor: datos.map(d => d.tasa >= 70 ? 'rgba(16,185,129,.7)' : d.tasa >= 40 ? 'rgba(245,158,11,.7)' : 'rgba(239,68,68,.7)'),
                    borderRadius: 6, borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false },
                    tooltip: { backgroundColor: 'rgba(17,24,39,.9)', padding: 10, cornerRadius: 8,
                        callbacks: { label: (c) => ` ${c.parsed.y}% resolución` } } },
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%', font: { size: 10 } }, grid: { color: 'rgba(0,0,0,.05)' } },
                    x: { ticks: { font: { size: 10 }, maxRotation: 45 }, grid: { display: false } }
                }
            }
        });
    },
    renderEscuelasChart() {
        const el = document.getElementById('escuelasChart');
        if (!el) return;
        const ctx = el.getContext('2d');
        if (el._chartInstance) el._chartInstance.destroy();
        const datos = @js($this->topEscuelas ?? []);
        el._chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: datos.map(d => d.escuela.length > 28 ? d.escuela.substring(0,26)+'…' : d.escuela),
                datasets: [{
                    label: 'Casos',
                    data: datos.map(d => d.total),
                    backgroundColor: 'rgba(139,92,246,.7)',
                    borderColor: '#8b5cf6', borderWidth: 1.5,
                    borderRadius: 6, borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false },
                    tooltip: { backgroundColor: 'rgba(17,24,39,.9)', padding: 10, cornerRadius: 8,
                        callbacks: { label: (c) => ` ${c.parsed.x} casos` } } },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: 'rgba(0,0,0,.05)' } },
                    y: { ticks: { font: { size: 10 } }, grid: { display: false } }
                }
            }
        });
    }
}"
x-init="init()"
wire:key="reportes-panel"
>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

<style>
    /* Animations */
    @keyframes fadeUp   { from { opacity:0; transform:translateY(20px) } to { opacity:1; transform:translateY(0) } }
    @keyframes scaleIn  { from { opacity:0; transform:scale(.92) }      to { opacity:1; transform:scale(1) } }
    @keyframes slideR   { from { opacity:0; transform:translateX(-12px)} to { opacity:1; transform:translateX(0) } }
    @keyframes countUp  { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
    @keyframes pulse-ring { 0% { box-shadow:0 0 0 0 rgba(99,102,241,.4) } 70% { box-shadow:0 0 0 10px rgba(99,102,241,0) } 100% { box-shadow:0 0 0 0 rgba(99,102,241,0) } }
    @keyframes shimmer  { 0% { background-position:-200% 0 } 100% { background-position:200% 0 } }

    .anim-fade   { animation: fadeUp .6s ease both }
    .anim-scale  { animation: scaleIn .5s ease both }
    .anim-slide  { animation: slideR .5s ease both }
    .anim-count  { animation: countUp .4s ease both }
    .d1 { animation-delay:.05s } .d2 { animation-delay:.10s } .d3 { animation-delay:.15s }
    .d4 { animation-delay:.20s } .d5 { animation-delay:.25s } .d6 { animation-delay:.30s }
    .d7 { animation-delay:.35s } .d8 { animation-delay:.40s }

    .kpi-card { transition: all .25s ease }
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -8px rgba(0,0,0,.12) }

    .chart-card { transition: all .2s ease }
    .chart-card:hover { box-shadow: 0 8px 30px -4px rgba(0,0,0,.1) }

    .glass { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px) }

    .shimmer-bg {
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.08) 50%, transparent 100%);
        background-size: 200% 100%;
        animation: shimmer 3s infinite;
    }

    .tab-active {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        box-shadow: 0 4px 15px -3px rgba(99,102,241,.4);
    }

    /* Progress bars */
    .progress-bar { transition: width 1.2s cubic-bezier(.25,.46,.45,.94) }

    /* Print */
    @media print {
        .no-print { display:none !important }
        .anim-fade,.anim-scale,.anim-slide,.anim-count { animation:none !important; opacity:1 !important }
        table thead tr { background:#4F46E5 !important; -webkit-print-color-adjust:exact; print-color-adjust:exact }
        table thead th  { color:white !important }
    }
</style>

{{-- ═══════════════════════════════════════════
     HEADER BANNER
═══════════════════════════════════════════ --}}
<div class="relative overflow-hidden rounded-2xl mb-8 bg-gradient-to-br from-indigo-600 via-indigo-500 to-violet-600 shadow-xl no-print anim-fade">
    {{-- Decorative elements --}}
    <div class="absolute inset-0 opacity-[.07]" style="background-image:radial-gradient(circle at 25% 50%,white 1px,transparent 1px),radial-gradient(circle at 75% 20%,white 1px,transparent 1px);background-size:32px 32px"></div>
    <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-white/5"></div>
    <div class="absolute -left-8 -bottom-8 w-32 h-32 rounded-full bg-white/5"></div>
    <div class="shimmer-bg absolute inset-0 pointer-events-none"></div>

    <div class="relative px-8 py-7 flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-white/15 glass flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-white tracking-tight">Dashboard de Reportes</h2>
                    <p class="text-indigo-200 text-sm">Centro de análisis y estadísticas</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-white/10 glass text-white text-sm px-4 py-2 rounded-xl">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ now()->isoFormat('D [de] MMMM [de] Y') }}
            </div>
            <div class="flex items-center gap-2 bg-emerald-500/20 glass text-emerald-100 text-sm px-4 py-2 rounded-xl">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                En vivo
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     FILTERS
═══════════════════════════════════════════ --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8 no-print anim-fade d1">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-900/40 dark:to-violet-900/40 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">Filtros de Reporte</h3>
                <p class="text-xs text-gray-400">Ajusta los parámetros para refinar los datos</p>
            </div>
        </div>
        @if($fecha_inicio || $fecha_fin || $tipo_violencia || $estado || $prioridad || $region_filtro)
            <button wire:click="limpiarFiltros"
                    class="text-xs text-red-500 hover:text-red-700 font-semibold flex items-center gap-1.5 transition-all px-4 py-2 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 border border-transparent hover:border-red-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar filtros
            </button>
        @endif
    </div>
    <form wire:submit.prevent class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {{ $this->form }}
    </form>
    {{-- Quick Date Presets --}}
    <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
        <span class="text-xs text-gray-400 font-medium self-center mr-1">Acceso rápido:</span>
        @foreach(['hoy' => 'Hoy', 'semana' => 'Esta semana', 'mes' => 'Este mes', 'anio' => 'Este año'] as $preset => $label)
            <button wire:click="setPreset('{{ $preset }}')"
                    class="text-xs px-3 py-1.5 rounded-lg font-semibold border transition-all
                    {{ ($preset === 'hoy' && $fecha_inicio === today()->toDateString() && $fecha_inicio === $fecha_fin)
                        || ($preset === 'semana' && $fecha_inicio === today()->subDays(6)->toDateString() && $fecha_fin === today()->toDateString())
                        || ($preset === 'mes' && $fecha_inicio === today()->startOfMonth()->toDateString())
                        || ($preset === 'anio' && $fecha_inicio === today()->startOfYear()->toDateString())
                        ? 'bg-indigo-600 text-white border-indigo-600 shadow'
                        : 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-700 dark:hover:bg-indigo-900/20' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>

{{-- ═══════════════════════════════════════════
     KPI CARDS
═══════════════════════════════════════════ --}}
@php
    $r = array_replace([
        'total' => 0,
        'pendiente' => 0,
        'en_proceso' => 0,
        'resuelto' => 0,
        'cerrado' => 0,
        'anonimos' => 0,
        'sin_asignar' => 0,
        'urgentes' => 0,
        'sla_vencido' => 0,
        'tasa_resolucion' => 0,
    ], is_array($this->resumen) ? $this->resumen : []);
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    {{-- Total Cases --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 p-6 shadow-lg kpi-card anim-scale d1" style="animation:pulse-ring 3s ease-in-out infinite">
        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-white/10"></div>
        <div class="absolute right-2 bottom-2 w-16 h-16 rounded-full bg-white/5"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-indigo-200 text-xs font-bold uppercase tracking-widest">Total Casos</span>
            </div>
            <div class="text-4xl font-black text-white anim-count d2">{{ number_format($r['total']) }}</div>
            <p class="text-indigo-200/80 text-xs mt-2">Con filtros aplicados</p>
        </div>
    </div>

    {{-- Resolution Rate --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 p-6 shadow-lg kpi-card anim-scale d2">
        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-white/10"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-emerald-200 text-xs font-bold uppercase tracking-widest">Resolución</span>
            </div>
            <div class="text-4xl font-black text-white anim-count d3">{{ $r['tasa_resolucion'] }}%</div>
            <div class="w-full bg-white/20 rounded-full h-2 mt-3">
                <div class="bg-white h-2 rounded-full progress-bar" style="width:{{ $r['tasa_resolucion'] }}%"></div>
            </div>
        </div>
    </div>

    {{-- Unassigned --}}
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg kpi-card anim-scale d3
        {{ $r['sin_asignar'] > 0 ? 'bg-gradient-to-br from-amber-500 to-orange-600' : 'bg-gradient-to-br from-gray-400 to-gray-500' }}">
        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-white/10"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <span class="text-white/80 text-xs font-bold uppercase tracking-widest">Sin Asignar</span>
            </div>
            <div class="text-4xl font-black text-white anim-count d4">{{ $r['sin_asignar'] }}</div>
            <p class="text-white/70 text-xs mt-2">Pendientes de asignación</p>
        </div>
    </div>

    {{-- Anonymous --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-500 to-purple-700 p-6 shadow-lg kpi-card anim-scale d4">
        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-white/10"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <span class="text-violet-200 text-xs font-bold uppercase tracking-widest">Anónimos</span>
            </div>
            <div class="text-4xl font-black text-white anim-count d5">{{ $r['anonimos'] }}</div>
            <p class="text-violet-200/80 text-xs mt-2">{{ $r['total'] > 0 ? round(($r['anonimos'] / $r['total']) * 100, 1) : 0 }}% del total</p>
        </div>
    </div>

</div>

{{-- Segunda fila KPIs: Urgentes, SLA Vencido, Escalados, En proceso --}}
@php $alertas = $this->alertasCriticas; @endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
    {{-- Urgentes --}}
    <div class="flex items-center gap-4 rounded-xl p-4 shadow-sm border kpi-card anim-fade d5
        {{ $alertas['urgentes'] > 0 ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
            {{ $alertas['urgentes'] > 0 ? 'bg-red-500 shadow-red-200 shadow' : 'bg-gray-400' }}">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <div>
            <div class="text-2xl font-black {{ $alertas['urgentes'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400' }}">{{ $alertas['urgentes'] }}</div>
            <div class="text-xs font-semibold {{ $alertas['urgentes'] > 0 ? 'text-red-500 dark:text-red-400' : 'text-gray-400' }}">Urgentes activos</div>
        </div>
    </div>
    {{-- SLA Vencido --}}
    <div class="flex items-center gap-4 rounded-xl p-4 shadow-sm border kpi-card anim-fade d6
        {{ $alertas['sla_vencido'] > 0 ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
            {{ $alertas['sla_vencido'] > 0 ? 'bg-orange-500 shadow-orange-200 shadow' : 'bg-gray-400' }}">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <div class="text-2xl font-black {{ $alertas['sla_vencido'] > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-400' }}">{{ $alertas['sla_vencido'] }}</div>
            <div class="text-xs font-semibold {{ $alertas['sla_vencido'] > 0 ? 'text-orange-500 dark:text-orange-400' : 'text-gray-400' }}">SLA vencido</div>
        </div>
    </div>
    {{-- Escalados --}}
    <div class="flex items-center gap-4 rounded-xl p-4 shadow-sm border kpi-card anim-fade d7
        {{ $alertas['escalados'] > 0 ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
            {{ $alertas['escalados'] > 0 ? 'bg-amber-500 shadow-amber-200 shadow' : 'bg-gray-400' }}">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
            </svg>
        </div>
        <div>
            <div class="text-2xl font-black {{ $alertas['escalados'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400' }}">{{ $alertas['escalados'] }}</div>
            <div class="text-xs font-semibold {{ $alertas['escalados'] > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-gray-400' }}">Escalados activos</div>
        </div>
    </div>
    {{-- Sin atención +48h --}}
    <div class="flex items-center gap-4 rounded-xl p-4 shadow-sm border kpi-card anim-fade d8
        {{ $alertas['sin_atencion_48h'] > 0 ? 'bg-rose-50 dark:bg-rose-900/20 border-rose-200 dark:border-rose-800' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
            {{ $alertas['sin_atencion_48h'] > 0 ? 'bg-rose-500 shadow-rose-200 shadow' : 'bg-gray-400' }}">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
        </div>
        <div>
            <div class="text-2xl font-black {{ $alertas['sin_atencion_48h'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-400' }}">{{ $alertas['sin_atencion_48h'] }}</div>
            <div class="text-xs font-semibold {{ $alertas['sin_atencion_48h'] > 0 ? 'text-rose-500 dark:text-rose-400' : 'text-gray-400' }}">Sin atención +48h</div>
        </div>
    </div>
</div>

{{-- Banner alertas críticas --}}
@if($alertas['urgentes'] > 0 || $alertas['sla_vencido'] > 0)
<div class="relative overflow-hidden rounded-xl mb-8 border border-red-200 dark:border-red-900 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 px-5 py-4 flex flex-wrap items-center gap-4 anim-fade no-print">
    <div class="w-8 h-8 rounded-lg bg-red-500 flex items-center justify-center flex-shrink-0 shadow shadow-red-300 dark:shadow-red-900">
        <svg class="w-4 h-4 text-white animate-pulse" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
    </div>
    <div class="flex-1">
        <p class="text-sm font-bold text-red-700 dark:text-red-300">Atención requerida</p>
        <p class="text-xs text-red-600/80 dark:text-red-400 mt-0.5">
            @if($alertas['urgentes'] > 0)<span class="font-semibold">{{ $alertas['urgentes'] }} caso(s) urgente(s)</span>@endif
            @if($alertas['urgentes'] > 0 && $alertas['sla_vencido'] > 0) · @endif
            @if($alertas['sla_vencido'] > 0)<span class="font-semibold">{{ $alertas['sla_vencido'] }} caso(s) con SLA vencido</span>@endif
            requieren atención inmediata.
        </p>
    </div>
    <a href="{{ route('filament.admin.resources.casos.index', ['tableFilters[estado][value]' => 'pendiente']) }}"
       class="text-xs font-bold text-red-600 dark:text-red-300 px-3 py-1.5 rounded-lg border border-red-300 dark:border-red-700 hover:bg-red-100 dark:hover:bg-red-900/30 transition-all whitespace-nowrap">
        Ver casos →
    </a>
</div>
@endif
            :class="activeTab === 'table' ? 'tab-active' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
            class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 whitespace-nowrap flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        Tabla de Datos
    </button>
    <button @click="activeTab = 'export'"
            :class="activeTab === 'export' ? 'tab-active' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
            class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 whitespace-nowrap flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Exportar
    </button>
    <button @click="activeTab = 'psicologos'; $nextTick(() => initCharts())"
            :class="activeTab === 'psicologos' ? 'tab-active' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
            class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 whitespace-nowrap flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Psicólogos
    </button>
</div>

{{-- ═══════════════════════════════════════════
     TAB: GENERAL
═══════════════════════════════════════════ --}}
<div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

    {{-- Status Distribution with Donut --}}
    @php
        $estadosData = [
            ['label' => 'Pendientes', 'val' => $r['pendiente'],  'color' => 'amber',   'hex' => '#f59e0b', 'icon' => '⏳'],
            ['label' => 'En Proceso', 'val' => $r['en_proceso'], 'color' => 'blue',    'hex' => '#3b82f6', 'icon' => '🔄'],
            ['label' => 'Resueltos',  'val' => $r['resuelto'],   'color' => 'emerald', 'hex' => '#10b981', 'icon' => '✅'],
            ['label' => 'Cerrados',   'val' => $r['cerrado'],    'color' => 'gray',    'hex' => '#6b7280', 'icon' => '🔒'],
        ];
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6 chart-card anim-fade d4">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-900/40 dark:to-violet-900/40 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Distribución por Estado</h3>
                <p class="text-xs text-gray-400">Desglose de casos según su estado actual</p>
            </div>
        </div>
        <div class="flex flex-col lg:flex-row items-center gap-8">
            {{-- Chart.js Donut --}}
            <div class="relative flex-shrink-0" style="width:200px;height:200px;">
                <canvas id="donutChart" width="200" height="200"></canvas>
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="text-center">
                        <span class="text-3xl font-black text-gray-800 dark:text-white">{{ $r['total'] }}</span>
                        <br><span class="text-xs text-gray-400">total</span>
                    </div>
                </div>
            </div>

            {{-- Legend + Bars --}}
            <div class="flex-1 w-full space-y-4">
                @foreach($estadosData as $e)
                    @php $pct = $r['total'] > 0 ? round(($e['val'] / $r['total']) * 100, 1) : 0; @endphp
                    <div class="anim-slide d{{ $loop->index + 3 }}">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:{{ $e['hex'] }}"></span>
                                <span class="font-medium text-sm text-gray-700 dark:text-gray-300">{{ $e['icon'] }} {{ $e['label'] }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium text-gray-400 tabular-nums">{{ $pct }}%</span>
                                <span class="font-black text-lg tabular-nums" style="color:{{ $e['hex'] }}">{{ $e['val'] }}</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                            <div class="h-2.5 rounded-full progress-bar" style="background:{{ $e['hex'] }};width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Assignments Summary --}}
    @php
        $asig = array_replace([
            'total' => 0,
            'activas' => 0,
            'finalizadas' => 0,
            'canceladas' => 0,
        ], is_array($this->asignacionesResumen) ? $this->asignacionesResumen : []);
    @endphp
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6 chart-card anim-fade d5">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/40 dark:to-cyan-900/40 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Estado de Asignaciones</h3>
                <p class="text-xs text-gray-400">Resumen global del sistema de asignaciones</p>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Total',       'val' => $asig['total'],       'from' => 'from-indigo-500', 'to' => 'to-indigo-600', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['label' => 'Activas',     'val' => $asig['activas'],     'from' => 'from-emerald-500','to' => 'to-emerald-600', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7'],
                ['label' => 'Finalizadas', 'val' => $asig['finalizadas'], 'from' => 'from-blue-500',   'to' => 'to-blue-600',   'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Canceladas',  'val' => $asig['canceladas'],  'from' => 'from-red-500',    'to' => 'to-red-600',    'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ] as $idx => $a)
                <div class="rounded-xl bg-gradient-to-br {{ $a['from'] }} {{ $a['to'] }} p-5 text-center kpi-card anim-scale d{{ $idx + 2 }}">
                    <div class="w-10 h-10 rounded-lg bg-white/15 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $a['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-black text-white">{{ $a['val'] }}</div>
                    <div class="text-white/80 text-xs mt-1 font-medium">{{ $a['label'] }}</div>
                    @if($asig['total'] > 0)
                        <div class="text-white/50 text-xs mt-0.5">{{ round(($a['val'] / max($asig['total'], 1)) * 100, 1) }}%</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Psychologists + Regions --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Psychologist Load --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 chart-card anim-fade d6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/40 dark:to-indigo-900/40 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-200">Carga por Psicólogo</h3>
                    <p class="text-xs text-gray-400">Distribución de asignaciones activas</p>
                </div>
            </div>
            @php
                $psicologos = $this->porPsicologo;
                $maxCarga = collect($psicologos)->map(fn ($p) => (int) ($p['total'] ?? $p['asignaciones_activas'] ?? $p['activas'] ?? 0))->max() ?: 1;
            @endphp
            @if(empty($psicologos))
                <div class="flex flex-col items-center py-12 text-gray-300 gap-3">
                    <svg class="w-14 h-14 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm text-gray-400">Sin psicólogos registrados</span>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($psicologos as $idx => $p)
                        @php
                            $activas = (int) ($p['activas'] ?? $p['asignaciones_activas'] ?? 0);
                            $finalizadas = (int) ($p['finalizadas'] ?? 0);
                            $totalCarga = (int) ($p['total'] ?? ($activas + $finalizadas));
                            $pct = round(($totalCarga / $maxCarga) * 100);
                            $nombre = trim((string) ($p['nombre'] ?? 'Sin nombre'));
                        @endphp
                        <div class="anim-slide d{{ min($idx + 2, 8) }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center text-white text-xs font-black flex-shrink-0 shadow-sm">
                                        {{ strtoupper(mb_substr($nombre, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-sm text-gray-700 dark:text-gray-300 truncate max-w-[140px]">{{ $nombre }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs flex-shrink-0">
                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 font-bold">{{ $activas }}A</span>
                                    <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 font-bold">{{ $finalizadas }}F</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-indigo-400 to-violet-500 h-2.5 rounded-full progress-bar" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Regions --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 chart-card anim-fade d6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-50 to-emerald-50 dark:from-teal-900/40 dark:to-emerald-900/40 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-200">Casos por Región</h3>
                    <p class="text-xs text-gray-400">Distribución geográfica de casos</p>
                </div>
            </div>
            @php
                $regiones = $this->porRegion;
                $maxReg = collect($regiones)->max('total') ?: 1;
            @endphp
            @if(empty($regiones))
                <div class="flex flex-col items-center py-12 text-gray-300 gap-3">
                    <svg class="w-14 h-14 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm text-gray-400">Sin información geográfica</span>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($regiones as $idx => $reg)
                        @php $pct = round(($reg['total'] / $maxReg) * 100); @endphp
                        <div class="anim-slide d{{ min($idx + 2, 8) }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-semibold text-sm text-gray-700 dark:text-gray-300 capitalize">{{ ucfirst(strtolower($reg['region'])) }}</span>
                                <span class="text-sm font-black text-teal-600 dark:text-teal-400 tabular-nums">{{ $reg['total'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-teal-400 to-emerald-500 h-2.5 rounded-full progress-bar" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Top Escuelas --}}
    @php $escuelas = $this->topEscuelas; $maxEsc = collect($escuelas)->max('total') ?: 1; @endphp
    @if(!empty($escuelas))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6 chart-card anim-fade d7">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-50 to-purple-50 dark:from-violet-900/40 dark:to-purple-900/40 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Top Escuelas con más casos</h3>
                <p class="text-xs text-gray-400">Instituciones con mayor número de reportes</p>
            </div>
        </div>
        <div class="space-y-3">
            @foreach($escuelas as $idx => $esc)
                @php $pct = round(($esc['total'] / $maxEsc) * 100); @endphp
                <div class="anim-slide d{{ min($idx + 2, 8) }}">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-lg bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-300 text-xs font-black flex items-center justify-center">{{ $idx + 1 }}</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate max-w-xs">{{ $esc['escuela'] }}</span>
                        </div>
                        <span class="text-sm font-black text-violet-600 dark:text-violet-400 tabular-nums ml-3">{{ $esc['total'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-violet-400 to-purple-500 h-2 rounded-full progress-bar" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Distribución por Prioridad --}}
    @php $prioridades = $this->distribucionPrioridad; @endphp
    @if(!empty($prioridades))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6 chart-card anim-fade d8">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/40 dark:to-red-900/40 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Distribución por Prioridad</h3>
                <p class="text-xs text-gray-400">Escala de criticidad de los casos activos</p>
            </div>
        </div>
        @php
            $priColores = ['baja' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'normal' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300', 'alta' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300', 'urgente' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300'];
            $priHex    = ['baja' => '#6b7280', 'normal' => '#3b82f6', 'alta' => '#f97316', 'urgente' => '#ef4444'];
            $maxPri    = collect($prioridades)->max('total') ?: 1;
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($prioridades as $pri)
                @php $pct = round(($pri['total'] / $maxPri) * 100); @endphp
                <div class="rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $priColores[$pri['prioridad']] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($pri['prioridad']) }}
                        </span>
                        <div class="text-right">
                            <span class="text-xl font-black" style="color:{{ $priHex[$pri['prioridad']] ?? '#6b7280' }}">{{ $pri['total'] }}</span>
                            <span class="text-xs text-gray-400 ml-1">{{ $pri['porcentaje'] }}%</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                        <div class="h-2 rounded-full progress-bar" style="background:{{ $priHex[$pri['prioridad']] ?? '#6b7280' }};width:{{ $pct }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- ═══════════════════════════════════════════
     TAB: CHARTS
═══════════════════════════════════════════ --}}
<div x-show="activeTab === 'charts'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

    @php
        $tipoNombre = [
            'fisica' => 'Física', 'psicologica' => 'Psicológica', 'verbal' => 'Verbal',
            'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso', 'discriminacion' => 'Discriminación', 'otro' => 'Otro',
        ];
        $colorTipo = [
            'fisica' => '#ef4444', 'psicologica' => '#f97316', 'verbal' => '#eab308',
            'sexual' => '#ec4899', 'ciberacoso' => '#3b82f6', 'discriminacion' => '#8b5cf6', 'otro' => '#6b7280',
        ];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Bar Chart: By Type --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 chart-card">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/40 dark:to-orange-900/40 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-200">Por Tipo de Violencia</h3>
                    <p class="text-xs text-gray-400">Distribución según clasificación</p>
                </div>
            </div>
            @if(empty($this->porTipo))
                <div class="flex flex-col items-center py-16 text-gray-300 gap-3">
                    <svg class="w-14 h-14 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-sm text-gray-400">Sin datos para mostrar</span>
                </div>
            @else
                <div style="height:320px"><canvas id="barChart"></canvas></div>
                {{-- Legend below chart --}}
                <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    @foreach($this->porTipo as $t)
                        <div class="flex items-center gap-1.5 text-xs">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $colorTipo[$t['tipo']] ?? '#6b7280' }}"></span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $tipoNombre[$t['tipo']] ?? $t['tipo'] }}: <strong>{{ $t['total'] }}</strong> ({{ $t['porcentaje'] }}%)</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Line Chart: Monthly Evolution --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 chart-card">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-900/40 dark:to-violet-900/40 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-200">Evolución Mensual</h3>
                    <p class="text-xs text-gray-400">Tendencia de casos en el tiempo</p>
                </div>
            </div>
            @if(empty($this->porMes))
                <div class="flex flex-col items-center py-16 text-gray-300 gap-3">
                    <svg class="w-14 h-14 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm text-gray-400">Sin datos para mostrar</span>
                </div>
            @else
                <div style="height:320px"><canvas id="lineChart"></canvas></div>
            @endif
        </div>

    </div>

    {{-- Region Horizontal Bar Chart --}}
    @if(!empty($this->porRegion))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6 chart-card">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-50 to-emerald-50 dark:from-teal-900/40 dark:to-emerald-900/40 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Casos por Región</h3>
                <p class="text-xs text-gray-400">Distribución geográfica en gráfico de barras</p>
            </div>
        </div>
        <div style="height:{{ max(count($this->porRegion) * 45, 200) }}px"><canvas id="regionChart"></canvas></div>
    </div>
    @endif

    {{-- Actividad últimos 14 días --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6 chart-card">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-50 to-cyan-50 dark:from-teal-900/40 dark:to-cyan-900/40 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Actividad — Últimos 14 días</h3>
                <p class="text-xs text-gray-400">Nuevos casos por día en las últimas dos semanas</p>
            </div>
        </div>
        <div style="height:200px"><canvas id="chart14d"></canvas></div>
    </div>

    {{-- Tasa de Resolución por Mes --}}
    @if(!empty($this->tasasResolucion))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6 chart-card">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/40 dark:to-green-900/40 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Tasa de Resolución por Mes</h3>
                <p class="text-xs text-gray-400">Porcentaje de casos resueltos/cerrados por mes <span class="text-emerald-500 font-semibold">≥70% bueno</span> · <span class="text-amber-500 font-semibold">40–70% regular</span> · <span class="text-red-500 font-semibold">&lt;40% crítico</span></p>
            </div>
        </div>
        <div style="height:260px"><canvas id="resolucionChart"></canvas></div>
    </div>
    @endif

    {{-- Distribución prioridad donut --}}
    @if(!empty($this->distribucionPrioridad))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6 chart-card">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/40 dark:to-red-900/40 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Distribución por Prioridad</h3>
                <p class="text-xs text-gray-400">Proporción de casos según nivel de criticidad</p>
            </div>
        </div>
        <div style="height:240px"><canvas id="prioridadChart"></canvas></div>
    </div>
    @endif

    {{-- Top Escuelas Chart --}}
    @if(!empty($this->topEscuelas))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6 chart-card">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-50 to-purple-50 dark:from-violet-900/40 dark:to-purple-900/40 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Top Escuelas</h3>
                <p class="text-xs text-gray-400">Instituciones con mayor número de casos reportados</p>
            </div>
        </div>
        <div style="height:{{ max(count($this->topEscuelas) * 40, 200) }}px"><canvas id="escuelasChart"></canvas></div>
    </div>
    @endif

</div>

{{-- ═══════════════════════════════════════════
     TAB: TABLE
═══════════════════════════════════════════ --}}
<div x-show="activeTab === 'table'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

    @php
        $tipoNombreT = [
            'fisica' => 'Física', 'psicologica' => 'Psicológica', 'verbal' => 'Verbal',
            'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso', 'discriminacion' => 'Discriminación', 'otro' => 'Otro',
        ];
        $colorTipoT = [
            'fisica'         => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'psicologica'    => 'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
            'verbal'         => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
            'sexual'         => 'bg-pink-50 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300',
            'ciberacoso'     => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'discriminacion' => 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
            'otro'           => 'bg-gray-50 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
        ];
        $estadoBadge = [
            'pendiente'  => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            'en_proceso' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            'resuelto'   => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            'cerrado'    => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
        ];
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-700 dark:to-slate-700 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-200">Casos Recientes</h3>
                    <p class="text-xs text-gray-400">Últimos 20 registros con filtros aplicados</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-2 text-xs font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 dark:text-indigo-300 px-4 py-2 rounded-xl">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                {{ $r['total'] }} registros totales
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-slate-50 dark:from-gray-900/50 dark:to-slate-900/50">
                        <th class="px-5 py-4 text-left font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">Código</th>
                        <th class="px-5 py-4 text-left font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">Tipo</th>
                        <th class="px-5 py-4 text-left font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">Estado</th>
                        <th class="px-5 py-4 text-left font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">Denunciante</th>
                        <th class="px-5 py-4 text-left font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">Asignado</th>
                        <th class="px-5 py-4 text-left font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">Región</th>
                        <th class="px-5 py-4 text-left font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @forelse($this->ultimosCasos as $caso)
                        <tr class="hover:bg-indigo-50/40 dark:hover:bg-indigo-900/10 transition-colors duration-150 group">
                            <td class="px-5 py-4">
                                <span class="font-mono font-black text-indigo-600 dark:text-indigo-400 text-xs tracking-wider bg-indigo-50 dark:bg-indigo-900/20 px-2.5 py-1 rounded-lg">
                                    {{ $caso->codigo_caso }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $colorTipoT[$caso->tipo_violencia] ?? $colorTipoT['otro'] }}">
                                    {{ $tipoNombreT[$caso->tipo_violencia] ?? $caso->tipo_violencia }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $estadoBadge[$caso->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $caso->estado)) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-600 dark:text-gray-400">
                                @if($caso->es_anonimo)
                                    <span class="inline-flex items-center gap-1.5 text-purple-600 dark:text-purple-400 font-bold text-xs">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Anónimo
                                    </span>
                                @else
                                    <span class="text-sm">{{ $caso->denunciante?->name ?? '—' }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($caso->asignado)
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 flex-shrink-0 shadow-sm shadow-emerald-200"></span>
                                        <span class="text-gray-700 dark:text-gray-300 text-sm font-medium">{{ $caso->asignado->name }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-amber-500 dark:text-amber-400 font-bold text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        Sin asignar
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-500 dark:text-gray-400 text-xs capitalize font-medium">
                                {{ $caso->region ? ucfirst(strtolower($caso->region)) : '—' }}
                            </td>
                            <td class="px-5 py-4 text-gray-400 dark:text-gray-500 text-xs tabular-nums font-medium">
                                {{ $caso->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-20 text-center">
                                <div class="inline-flex flex-col items-center gap-4 text-gray-300">
                                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-400">No hay casos registrados</p>
                                        <p class="text-xs text-gray-300 mt-1">Ajusta los filtros para ver resultados</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════
     TAB: EXPORT
═══════════════════════════════════════════ --}}
<div x-show="activeTab === 'export'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-900/40 dark:to-violet-900/40 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </div>
            <h3 class="text-xl font-black text-gray-800 dark:text-white">Exportar Reporte</h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">
                Descarga los <strong class="text-indigo-600 dark:text-indigo-400">{{ $r['total'] }}</strong> casos filtrados en el formato que necesites
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 max-w-3xl mx-auto">

            {{-- CSV --}}
            <button wire:click="exportarCsv" wire:loading.attr="disabled" wire:target="exportarCsv"
                    class="group relative overflow-hidden rounded-2xl border-2 border-emerald-200 dark:border-emerald-800 bg-gradient-to-b from-white to-emerald-50 dark:from-gray-800 dark:to-emerald-900/20 p-6 text-center transition-all duration-300 hover:border-emerald-400 hover:shadow-lg hover:shadow-emerald-100/50 dark:hover:shadow-emerald-900/30 hover:-translate-y-1">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-200/50 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6M12 10V4m0 0a2 2 0 012 2v0M12 4a2 2 0 00-2 2v0M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1"/>
                    </svg>
                </div>
                <span wire:loading.remove wire:target="exportarCsv">
                    <h4 class="font-bold text-gray-800 dark:text-white text-lg">CSV</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Compatible con Excel, Google Sheets</p>
                </span>
                <span wire:loading wire:target="exportarCsv" class="inline-flex items-center gap-2 text-emerald-600 font-semibold">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Generando...
                </span>
            </button>

            {{-- Excel --}}
            <button wire:click="exportarExcel" wire:loading.attr="disabled" wire:target="exportarExcel"
                    class="group relative overflow-hidden rounded-2xl border-2 border-indigo-200 dark:border-indigo-800 bg-gradient-to-b from-white to-indigo-50 dark:from-gray-800 dark:to-indigo-900/20 p-6 text-center transition-all duration-300 hover:border-indigo-400 hover:shadow-lg hover:shadow-indigo-100/50 dark:hover:shadow-indigo-900/30 hover:-translate-y-1">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-indigo-200/50 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span wire:loading.remove wire:target="exportarExcel">
                    <h4 class="font-bold text-gray-800 dark:text-white text-lg">Excel</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato .xls con estilos</p>
                </span>
                <span wire:loading wire:target="exportarExcel" class="inline-flex items-center gap-2 text-indigo-600 font-semibold">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Generando...
                </span>
            </button>

            {{-- PDF / Print --}}
            <button onclick="window.print()"
                    class="group relative overflow-hidden rounded-2xl border-2 border-rose-200 dark:border-rose-800 bg-gradient-to-b from-white to-rose-50 dark:from-gray-800 dark:to-rose-900/20 p-6 text-center transition-all duration-300 hover:border-rose-400 hover:shadow-lg hover:shadow-rose-100/50 dark:hover:shadow-rose-900/30 hover:-translate-y-1">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-rose-200/50 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </div>
                <h4 class="font-bold text-gray-800 dark:text-white text-lg">Imprimir / PDF</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Guardar como PDF desde el diálogo</p>
            </button>

        </div>

        {{-- Info note --}}
        <div class="mt-8 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 max-w-3xl mx-auto">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <p class="font-semibold text-gray-600 dark:text-gray-300 mb-1">Nota sobre exportaciones</p>
                    <p>Los reportes incluyen todos los datos filtrados: código, tipo de violencia, estado, prioridad, denunciante, psicólogo asignado, región, provincia, escuela, fechas, SLA vencido y escalado. Los archivos CSV y Excel se generan con codificación UTF-8 para correcta visualización de caracteres especiales.</p>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════
     TAB: PSICÓLOGOS
═══════════════════════════════════════════ --}}
<div x-show="activeTab === 'psicologos'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

    @php
        $psics = $this->porPsicologo;
        $dispColor = [
            'disponible'  => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            'ocupado'     => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            'no_disponible' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        ];
        $dispLabel = ['disponible' => 'Disponible', 'ocupado' => 'Ocupado', 'no_disponible' => 'No disponible'];
        $maxTotal  = collect($psics)->map(fn ($p) => (int)($p['total'] ?? 0))->max() ?: 1;
    @endphp

    {{-- Summary cards --}}
    @php
        $totalPsics    = count($psics);
        $totalActivas  = array_sum(array_column($psics, 'activas'));
        $totalFin      = array_sum(array_column($psics, 'finalizadas'));
        $disponibles   = collect($psics)->where('disponibilidad', 'disponible')->count();
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label' => 'Psicólogos activos', 'val' => $totalPsics,   'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'from' => 'from-indigo-500', 'to' => 'to-indigo-700'],
            ['label' => 'Disponibles ahora',  'val' => $disponibles,  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'from-emerald-500', 'to' => 'to-emerald-700'],
            ['label' => 'Asignaciones activas','val' => $totalActivas, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',                                                'from' => 'from-amber-500',   'to' => 'to-amber-700'],
            ['label' => 'Casos finalizados',   'val' => $totalFin,    'icon' => 'M5 13l4 4L19 7',                                                            'from' => 'from-blue-500',    'to' => 'to-blue-700'],
        ] as $idx => $kpi)
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $kpi['from'] }} {{ $kpi['to'] }} p-5 shadow-lg kpi-card anim-scale d{{ $idx + 1 }}">
                <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-white/10"></div>
                <div class="relative z-10">
                    <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="text-3xl font-black text-white">{{ $kpi['val'] }}</div>
                    <div class="text-white/75 text-xs mt-1 font-medium">{{ $kpi['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Psicólogos detailed table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-200">Detalle por Psicólogo</h3>
            <p class="text-xs text-gray-400 mt-0.5">Rendimiento y carga de trabajo individual</p>
        </div>
        @if(empty($psics))
            <div class="py-16 text-center">
                <p class="text-gray-400 text-sm">Sin psicólogos registrados en el sistema.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/40">
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Psicólogo</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Especialidad</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Disponibilidad</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Activas</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Finalizadas</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Carga</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @foreach($psics as $idx => $p)
                            @php
                                $activas    = (int) ($p['activas'] ?? 0);
                                $finalizadas= (int) ($p['finalizadas'] ?? 0);
                                $total      = (int) ($p['total'] ?? ($activas + $finalizadas));
                                $nombre     = trim((string) ($p['nombre'] ?? 'Sin nombre'));
                                $pct        = round(($total / $maxTotal) * 100);
                                $disp       = $p['disponibilidad'] ?? null;
                            @endphp
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-colors">
                                <td class="px-5 py-4 text-xs text-gray-400 tabular-nums">{{ $idx + 1 }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center text-white text-xs font-black flex-shrink-0 shadow-sm">
                                            {{ strtoupper(mb_substr($nombre, 0, 2)) }}
                                        </div>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $nombre }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $p['especialidad'] ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($disp)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $dispColor[$disp] ?? 'bg-gray-100 text-gray-500' }}">
                                            {{ $dispLabel[$disp] ?? ucfirst($disp) }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-black text-sm">{{ $activas }}</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-black text-sm">{{ $finalizadas }}</span>
                                </td>
                                <td class="px-5 py-4 text-center font-black text-gray-700 dark:text-gray-200">{{ $total }}</td>
                                <td class="px-5 py-4 min-w-[120px]">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                            <div class="bg-gradient-to-r from-indigo-400 to-violet-500 h-2 rounded-full progress-bar" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-400 tabular-nums w-7 text-right">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Efficiency comparison: activas vs finalizadas horizontal bars --}}
    @if(!empty($psics))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-900/40 dark:to-violet-900/40 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Comparativa: Activas vs Finalizadas</h3>
                <p class="text-xs text-gray-400">Proporción de eficiencia por profesional</p>
            </div>
            <div class="flex items-center gap-4 ml-auto text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-emerald-400"></span> Activas</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-blue-400"></span> Finalizadas</span>
            </div>
        </div>
        <div class="space-y-4">
            @foreach($psics as $p)
                @php
                    $activas    = (int) ($p['activas'] ?? 0);
                    $finalizadas= (int) ($p['finalizadas'] ?? 0);
                    $totPsic    = $activas + $finalizadas ?: 1;
                    $pctAct     = round(($activas / $totPsic) * 100);
                    $pctFin     = round(($finalizadas / $totPsic) * 100);
                    $nombre     = trim((string) ($p['nombre'] ?? 'Sin nombre'));
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 truncate max-w-xs">{{ $nombre }}</span>
                        <span class="text-xs text-gray-400 tabular-nums">{{ $activas }}A / {{ $finalizadas }}F</span>
                    </div>
                    <div class="flex h-3 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                        @if($activas > 0)
                        <div class="bg-emerald-400 transition-all" style="width:{{ $pctAct }}%" title="Activas: {{ $activas }}"></div>
                        @endif
                        @if($finalizadas > 0)
                        <div class="bg-blue-400 transition-all" style="width:{{ $pctFin }}%" title="Finalizadas: {{ $finalizadas }}"></div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

</div>
</x-filament-panels::page>
