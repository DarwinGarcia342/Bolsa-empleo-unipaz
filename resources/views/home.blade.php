@extends('layouts.app')
@section('title', 'Inicio')

@push('styles')
<style>
    /* ═══════════════════════════════════════
    LANDING PAGE — EMPLEA UNIPAZ
    Tema oscuro premium con acento esmeralda
    ═══════════════════════════════════════ */

    /* Wrapper global de la landing */
    .landing-wrapper {
        background: #0d1b4c;
        color: #fff;
    }

    /* ── HERO ── */
    .hero-section {
        background: linear-gradient(135deg, #0d1b4c 0%, #10235f 55%, #0e2060 100%);
        position: relative;
        overflow: hidden;
        padding: 5rem 0;
    }

    .hero-container-compact {
        max-width: 1140px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -120px;
        right: -120px;
        width: 560px;
        height: 560px;
        background: radial-gradient(circle, rgba(52, 211, 153, .09) 0%, transparent 68%);
        pointer-events: none;
    }

    .hero-section::after {
        content: '';
        position: absolute;
        bottom: -100px;
        left: -80px;
        width: 420px;
        height: 420px;
        background: radial-gradient(circle, rgba(52, 211, 153, .06) 0%, transparent 70%);
        pointer-events: none;
    }

    .hero-section .hero-container-compact {
        position: relative;
        z-index: 1;
    }

    /* Contenedor Flex para alinear simétricamente el contenido izquierdo */
    .hero-left-wrapper {
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        background: rgba(52, 211, 153, .12);
        border: 1px solid rgba(52, 211, 153, .28);
        color: #6ee7b7;
        border-radius: 24px;
        padding: .45rem 1.15rem;
        font-size: .78rem;
        font-weight: 600;
        letter-spacing: .5px;
        margin-bottom: 1.4rem;
        width: fit-content;
    }

    .hero-badge .dot {
        width: 7px;
        height: 7px;
        background: #34d399;
        border-radius: 50%;
        animation: pulse-dot 1.8s ease-in-out infinite;
        flex-shrink: 0;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: .45; transform: scale(.75); }
    }

    .hero-title {
        font-size: clamp(2.2rem, 5vw, 3.2rem);
        font-weight: 900;
        line-height: 1.15;
        color: #fff;
        letter-spacing: -.8px;
        margin-bottom: 1.25rem;
    }

    .hero-title .highlight {
        color: #34d399;
    }

    .hero-lead {
        font-size: 1.02rem;
        color: rgba(255, 255, 255, .78);
        line-height: 1.6;
        max-width: 520px;
        margin-bottom: 2rem;
    }

    /* Stat chips */
    .stat-chip {
        background: rgba(255, 255, 255, .08);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 14px;
        padding: 0.9rem 1.2rem;
        text-align: center;
        backdrop-filter: blur(6px);
        flex: 1;
        min-width: 100px;
    }

    .stat-chip .stat-number {
        font-size: 1.7rem;
        font-weight: 900;
        color: #fff;
        line-height: 1;
    }

    .stat-chip .stat-number span {
        color: #34d399;
    }

    .stat-chip .stat-label {
        font-size: .65rem;
        color: rgba(255, 255, 255, .52);
        margin-top: .3rem;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    /* Botones premium */
    .btn-hero-primary {
        background: #34d399;
        color: #0d1b4c;
        border: none;
        border-radius: 12px;
        padding: .8rem 1.5rem;
        font-weight: 700;
        font-size: .92rem;
        transition: all .22s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
    }

    .btn-hero-primary:hover {
        background: #10b981;
        color: #0d1b4c;
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(52, 211, 153, .35);
    }

    /* Tarjeta de búsqueda mejorada organizada con Grid interno */
    .search-card {
        background: rgba(255, 255, 255, .07);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 24px;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 2.2rem 2rem;
        height: 100%;
    }

    .search-card h3 {
        color: #fff;
        font-weight: 700;
        font-size: 1.35rem;
        margin-bottom: 1.5rem;
    }

    /* Pequeñas etiquetas sobre los selectores */
    .search-card .field-label {
        font-size: 0.73rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.6);
        margin-bottom: 0.4rem;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .search-card .form-control,
    .search-card .form-select {
        background: rgba(255, 255, 255, .1) !important;
        border: 1px solid rgba(255, 255, 255, .15) !important;
        color: #fff !important;
        border-radius: 12px !important;
        padding: .75rem 1rem !important;
        font-size: .88rem !important;
    }

    .search-card .form-control::placeholder {
        color: rgba(255, 255, 255, .45) !important;
    }

    .search-card .form-select option {
        background: #10235f;
        color: #fff;
    }

    .search-card .form-control:focus,
    .search-card .form-select:focus {
        border-color: rgba(52, 211, 153, .55) !important;
        box-shadow: 0 0 0 3px rgba(52, 211, 153, .14) !important;
        background: rgba(255, 255, 255, .13) !important;
    }

    .btn-search {
        background: #34d399;
        color: #0d1b4c;
        border: none;
        border-radius: 12px;
        padding: .85rem 1.5rem;
        font-weight: 700;
        font-size: .92rem;
        width: 100%;
        transition: all .2s;
        cursor: pointer;
    }

    .btn-search:hover {
        background: #10b981;
        box-shadow: 0 8px 24px rgba(52, 211, 153, .3);
    }

    /* Tarjeta de Empresas (Fila Inferior Dedicada) */
    .side-empresa-card {
        background: linear-gradient(135deg, #10235f 0%, #0e3855 100%);
        border: 1px solid rgba(52, 211, 153, .18);
        border-radius: 24px;
        padding: 2.2rem;
        margin-top: 3.5rem;
    }

    .side-empresa-card h4 {
        color: #fff;
        font-weight: 700;
        font-size: 1.35rem;
        margin-bottom: 0.5rem;
    }

    /* ── SECCIONES DE CONTENIDO ── */
    .section-dark {
        background: rgba(255, 255, 255, .025);
        padding: 4.5rem 0;
    }

    .section-darker {
        background: rgba(0, 0, 0, .12);
        padding: 4.5rem 0;
    }

    .section-tag {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: 1.8px;
        text-transform: uppercase;
        color: #34d399;
        margin-bottom: .5rem;
        display: block;
    }

    .section-title-dark {
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        line-height: 1.2;
    }

    /* ── CÓMO FUNCIONA ── */
    .how-step {
        text-align: center;
        padding: 1.75rem 1.25rem;
        background: rgba(255, 255, 255, .06);
        border: 1px solid rgba(255, 255, 255, .09);
        border-radius: 18px;
        transition: all .22s ease;
        height: 100%;
    }

    .how-step:hover {
        background: rgba(255, 255, 255, .1);
        border-color: rgba(52, 211, 153, .22);
        transform: translateY(-3px);
    }

    .how-step .step-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .how-step .step-number {
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: 1.3px;
        text-transform: uppercase;
        color: #34d399;
        margin-bottom: .4rem;
    }

    /* ── TARJETAS DE VACANTES ── */
    .job-card-dark {
        background: rgba(255, 255, 255, .07);
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: 18px;
        transition: all .22s ease;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .job-card-dark:hover {
        background: rgba(255, 255, 255, .11);
        border-color: rgba(52, 211, 153, .28);
        transform: translateY(-3px);
        box-shadow: 0 18px 44px rgba(0, 0, 0, .32);
    }

    .job-card-dark .card-body {
        padding: 1.35rem;
        flex: 1;
    }

    .job-card-dark .company-logo {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #fff;
        font-size: 1.2rem;
        border: 1.5px solid rgba(255, 255, 255, .12);
        background: rgba(255, 255, 255, .08);
    }

    .job-card-dark .job-title {
        font-size: .93rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.3;
    }

    .job-card-dark .company-name {
        font-size: .77rem;
        color: rgba(255, 255, 255, .55);
    }

    .job-card-dark .badge-location {
        font-size: .71rem;
        background: rgba(255, 255, 255, .1);
        color: rgba(255, 255, 255, .72);
        padding: .28rem .68rem;
        border-radius: 6px;
    }

    .job-card-dark .badge-modality {
        font-size: .71rem;
        font-weight: 600;
        padding: .28rem .68rem;
        border-radius: 6px;
    }

    .job-card-dark .badge-days {
        font-size: .71rem;
        background: rgba(234, 179, 8, .14);
        color: #fde68a;
        padding: .28rem .68rem;
        border-radius: 6px;
    }

    .job-card-dark .job-desc {
        font-size: .8rem;
        color: rgba(255, 255, 255, .58);
        line-height: 1.55;
        margin-top: .75rem;
    }

    .job-card-dark .salary-label {
        font-size: .83rem;
        font-weight: 700;
        color: #34d399;
    }

    .job-card-dark .card-footer-dark {
        padding: .9rem 1.35rem;
        border-top: 1px solid rgba(255, 255, 255, .07);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-card-detail {
        background: rgba(52, 211, 153, .13);
        color: #34d399;
        border: 1px solid rgba(52, 211, 153, .22);
        border-radius: 8px;
        font-weight: 600;
        font-size: .77rem;
        padding: .38rem .9rem;
        text-decoration: none;
    }

    .btn-ver-todas {
        background: rgba(255, 255, 255, .08);
        color: rgba(255, 255, 255, .8);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 10px;
        font-weight: 600;
        font-size: .82rem;
        padding: .5rem 1rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
    }
</style>
@endpush

@section('content')

<div class="landing-wrapper">

    {{-- HERO PRINCIPAL --}}
    <section class="hero-section">
        <div class="hero-container-compact">
            
            {{-- Fila superior del Hero --}}
            <div class="row g-4 align-items-stretch">

                {{-- Columna Izquierda: Mensaje de Bienvenida y Estadísticas --}}
                <div class="col-lg-6">
                    <div class="hero-left-wrapper">
                        <div class="hero-badge">
                            <span class="dot"></span>
                            Bolsa de empleo oficial · UNIPAZ Barrancabermeja
                        </div>

                        <h1 class="hero-title">
                            Conectamos talento<br>
                            universitario con<br>
                            <span class="highlight">oportunidades reales.</span>
                        </h1>

                        <p class="hero-lead">
                            Plataforma institucional para estudiantes, egresados y empresas del Distrito de Barrancabermeja.
                        </p>

                        {{-- CHIPS DE ESTADÍSTICAS --}}
                        <div class="d-flex gap-3 flex-wrap mt-2">
                            <div class="stat-chip">
                                <div class="stat-number">{{ $totalstudents }}</div>
                                <div class="stat-label">Estudiantes</div>
                            </div>
                            <div class="stat-chip">
                                <div class="stat-number">{{ $totalJobs }}<span>+</span></div>
                                <div class="stat-label">Vacantes activas</div>
                            </div>
                            <div class="stat-chip">
                                <div class="stat-number">{{ $totalCompanies }}</div>
                                <div class="stat-label">Empresas aliadas</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Columna Derecha: Buscador Completo Organizado --}}
                <div class="col-lg-6">
                    <div class="search-card">
                        <h3>Buscar oportunidades</h3>
                        <form action="{{ route('auth.google') }}" method="GET">
                            <div class="row g-3">
                                
                                {{-- Cargo / Palabra Clave --}}
                                <div class="col-12">
                                    <span class="field-label">Palabra clave</span>
                                    <input type="text" class="form-control" name="q" placeholder="Ej: Desarrollador Web Junior">
                                </div>

                                {{-- Área / Perfil --}}
                                <div class="col-12">
                                    <span class="field-label">Área / Perfil</span>
                                    <select class="form-select" name="area">
                                        <option value="">Seleccionar área...</option>
                                        <option value="tecnologia">Tecnología e informática</option>
                                        <option value="administracion">Administración de empresas</option>
                                        <option value="contabilidad">Contabilidad y finanzas</option>
                                        <option value="ingenieria_civil">Ingeniería civil</option>
                                        <option value="ingenieria_ambiental">Ingeniería ambiental</option>
                                        <option value="petroleo_gas">Petróleo y gas</option>
                                        <option value="salud">Salud</option>
                                        <option value="educacion">Educación</option>
                                        <option value="marketing_ventas">Marketing y ventas</option>
                                        <option value="recursos_humanos">Recursos humanos</option>
                                        <option value="derecho">Derecho</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>

                                {{-- Modalidad --}}
                                <div class="col-sm-6">
                                    <span class="field-label">Modalidad</span>
                                    <select class="form-select" name="modality">
                                        <option value="">Cualquiera</option>
                                        <option value="Presencial">Presencial</option>
                                        <option value="Remoto">Remoto</option>
                                        <option value="Hibrido">Híbrido</option>
                                    </select>
                                </div>

                                {{-- Ubicación --}}
                                <div class="col-sm-6">
                                    <span class="field-label">Ubicación</span>
                                    <input type="text" class="form-control" name="location" placeholder="Barrancabermeja">
                                </div>

                                {{-- Rango Salarial --}}
                                <div class="col-12">
                                    <span class="field-label">Rango Salarial Mínimo</span>
                                    <select class="form-select" name="salary_min">
                                        <option value="">Cualquier remuneración / A convenir</option>
                                        <option value="1000000">Desde $1.0M mensuales</option>
                                        <option value="1500000">Desde $1.5M mensuales</option>
                                        <option value="2000000">Desde $2.0M mensuales</option>
                                        <option value="3000000">Desde $3.0M o más</option>
                                    </select>
                                </div>

                                {{-- Botón de Acción --}}
                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn-search">
                                        <i class="bi bi-search me-2"></i>Buscar ahora
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

            </div>{{-- /row superior --}}

            {{-- Fila Inferior del Hero: Bloque de Empresas Independiente --}}
            <div class="row">
                <div class="col-12">
                    <div class="side-empresa-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <span class="section-tag">EMPRESAS Y MICROEMPRESAS</span>
                                <h4>¿Buscas talento universitario?</h4>
                                <p class="small mb-md-0 opacity-75" style="line-height: 1.5; font-size: 0.88rem;">
                                    Publica tus ofertas laborales <strong class="text-white">sin costo</strong> y conecta con los mejores estudiantes y egresados calificados del Instituto Universitario de la Paz en Barrancabermeja.
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="d-flex flex-column align-items-md-end gap-2">
                                    <a href="{{ route('company.register') }}" class="btn-hero-primary px-4 py-2">
                                        <i class="bi bi-building-add me-2"></i>Registrar mi empresa
                                    </a>
                                    <div class="opacity-50 w-100 text-center text-md-end" style="font-size: 0.72rem;">
                                        <i class="bi bi-check-circle me-1" style="color:#34d399;"></i>Aprobación de cuenta en 24 horas
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- /row inferior --}}

        </div>{{-- /container --}}
    </section>

    {{-- ── CÓMO FUNCIONA ── --}}
    <section class="section-darker">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-tag">¿Cómo funciona?</span>
                <h2 class="section-title-dark">Simple, rápido y gratuito</h2>
            </div>
            <div class="row g-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="how-step">
                        <div class="step-icon" style="background:rgba(16,35,95,.9);"><i class="bi bi-google" style="color:#34d399;"></i></div>
                        <div class="step-number">Paso 01</div>
                        <h5>Ingresa con tu correo</h5>
                        <p>Usa tu cuenta institucional <strong style="color:rgba(255,255,255,.85);">@unipaz.edu.co</strong> para acceder seguro.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="how-step">
                        <div class="step-icon" style="background:rgba(52,211,153,.1);"><i class="bi bi-person-badge" style="color:#34d399;"></i></div>
                        <div class="step-number">Paso 02</div>
                        <h5>Completa tu perfil</h5>
                        <p>Agrega tu programa académico, CV y habilidades para destacar ante las empresas.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="how-step">
                        <div class="step-icon" style="background:rgba(16,35,95,.9);"><i class="bi bi-search" style="color:#34d399;"></i></div>
                        <div class="step-number">Paso 03</div>
                        <h5>Explora vacantes</h5>
                        <p>Filtra por área, modalidad y ubicación para encontrar tu oportunidad ideal.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="how-step">
                        <div class="step-icon" style="background:rgba(52,211,153,.1);"><i class="bi bi-send-check" style="color:#34d399;"></i></div>
                        <div class="step-number">Paso 04</div>
                        <h5>Postúlate</h5>
                        <p>Envía tu postulación con un clic y haz seguimiento del proceso en tiempo real.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── VACANTES RECIENTES ── --}}
    <section class="section-dark pb-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <span class="section-tag">Oportunidades laborales</span>
                    <h2 class="section-title-dark">Vacantes recientes</h2>
                </div>
                <a href="{{ route('auth.google') }}" class="btn-ver-todas d-none d-sm-inline-flex">
                    Ver todas <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            
            <div class="row g-3">
                @forelse($latestJobs as $job)
                <div class="col-md-6 col-lg-4">
                    <div class="job-card-dark h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="company-logo">
                                    {{ strtoupper(substr($job->company->company_name ?? 'E', 0, 1)) }}
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="job-title">{{ $job->title }}</div>
                                    <div class="company-name">{{ $job->company->company_name ?? 'Empresa' }}</div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge-location"><i class="bi bi-geo-alt"></i>{{ $job->location }}</span>
                                <span class="badge-modality"><i class="bi bi-laptop"></i>{{ $job->modality ?? 'Presencial' }}</span>
                                @if($job->created_at->diffInDays() < 7)
                                <span class="badge-days"><i class="bi bi-clock"></i>Hace {{ $job->created_at->diffForHumans(null, true) }}</span>
                                @endif
                            </div>
                            @if($job->description)
                            <p class="job-desc">{{ Str::limit(strip_tags($job->description), 100) }}</p>
                            @endif
                        </div>
                        <div class="card-footer-dark">
                            @if($job->salary_min)
                            <span class="salary-label">
                                <i class="bi bi-currency-dollar"></i>${{ number_format($job->salary_min / 1000) }}k
                                @if($job->salary_max) - ${{ number_format($job->salary_max / 1000) }}k @endif
                            </span>
                            @else
                            <span class="salary-label"><i class="bi bi-chat-square-text"></i> A convenir</span>
                            @endif
                            <a href="{{ route('auth.google') }}" class="btn-card-detail">
                                Aplicar <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-briefcase opacity-25 d-block mb-2" style="font-size:3rem; color:#34d399;"></i>
                    <p style="color:rgba(255,255,255,.5);">Pronto habrá vacantes disponibles.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

</div>

@endsection