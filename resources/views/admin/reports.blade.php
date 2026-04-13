@extends('layouts.app')
@section('title', 'Reportes y Estadísticas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-bar-chart-line text-primary me-2"></i>Reportes y Estadísticas</h4>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
    </div>

    <!-- KPIs principales -->
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-sm-6">
            <div class="card card-stat text-center border-primary h-100">
                <div class="card-body py-3">
                    <div class="fs-1 fw-bold text-primary">{{ $stats['students_total'] }}</div>
                    <small class="text-muted">Estudiantes registrados</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card card-stat text-center border-success h-100">
                <div class="card-body py-3">
                    <div class="fs-1 fw-bold text-success">{{ $stats['companies_approved'] }}</div>
                    <small class="text-muted">Empresas aprobadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card card-stat text-center border-info h-100">
                <div class="card-body py-3">
                    <div class="fs-1 fw-bold text-info">{{ $stats['jobs_active'] }}</div>
                    <small class="text-muted">Vacantes activas</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card card-stat text-center border-secondary h-100">
                <div class="card-body py-3">
                    <div class="fs-1 fw-bold text-secondary">{{ $stats['jobs_closed'] }}</div>
                    <small class="text-muted">Vacantes cerradas</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card card-stat text-center border-warning h-100">
                <div class="card-body py-3">
                    <div class="fs-1 fw-bold text-warning">{{ $stats['applications_total'] }}</div>
                    <small class="text-muted">Postulaciones totales</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card card-stat text-center border-success h-100">
                <div class="card-body py-3">
                    <div class="fs-1 fw-bold text-success">{{ $stats['applications_accepted'] }}</div>
                    <small class="text-muted">Postulaciones aceptadas</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Estado de empresas -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-building me-2 text-primary"></i>Empresas por estado
                </div>
                <div class="card-body">
                    @php
                        $companyTotal = $companiesByStatus->sum();
                    @endphp
                    @foreach(['approved' => ['Aprobadas','bg-success'], 'pending' => ['Pendientes','bg-warning'], 'rejected' => ['Rechazadas','bg-danger']] as $key => [$label, $color])
                        @php $count = $companiesByStatus[$key] ?? 0; $pct = $companyTotal > 0 ? round($count/$companyTotal*100) : 0; @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $label }}</span>
                                <strong>{{ $count }} ({{ $pct }}%)</strong>
                            </div>
                            <div class="progress" style="height:10px">
                                <div class="progress-bar {{ $color }}" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    <hr>
                    <p class="text-center text-muted small mb-0">Total: {{ $companyTotal }} empresas registradas</p>
                </div>
            </div>
        </div>

        <!-- Estado de postulaciones -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-file-earmark-person me-2 text-success"></i>Postulaciones por estado
                </div>
                <div class="card-body">
                    @php $appTotal = $applicationsByStatus->sum(); @endphp
                    @foreach([
                        'pending'   => ['Pendiente',   'bg-secondary'],
                        'reviewed'  => ['En revisión', 'bg-info'],
                        'interview' => ['Entrevista',  'bg-warning'],
                        'accepted'  => ['Aceptada',    'bg-success'],
                        'rejected'  => ['No selec.',   'bg-danger'],
                    ] as $key => [$label, $color])
                        @php $count = $applicationsByStatus[$key] ?? 0; $pct = $appTotal > 0 ? round($count/$appTotal*100) : 0; @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $label }}</span>
                                <strong>{{ $count }} ({{ $pct }}%)</strong>
                            </div>
                            <div class="progress" style="height:10px">
                                <div class="progress-bar {{ $color }}" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    <hr>
                    <p class="text-center text-muted small mb-0">Total: {{ $appTotal }} postulaciones</p>
                </div>
            </div>
        </div>

        <!-- Vacantes por área -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-tags me-2 text-info"></i>Vacantes por área
                </div>
                <div class="card-body">
                    @forelse($jobsByArea as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span class="text-truncate me-2" style="max-width:180px" title="{{ $item->area }}">
                                {{ $item->area ?? 'Sin especificar' }}
                            </span>
                            <span class="badge badge-area rounded-pill">{{ $item->total }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-center small mt-3">Sin datos aún.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Tasa de éxito -->
    @if($stats['applications_total'] > 0)
    <div class="card mt-4">
        <div class="card-body text-center py-4">
            @php
                $tasa = round($stats['applications_accepted'] / $stats['applications_total'] * 100, 1);
            @endphp
            <h5 class="fw-bold text-success mb-1">{{ $tasa }}%</h5>
            <p class="text-muted mb-0 small">Tasa de éxito — postulaciones que resultaron en contratación</p>
        </div>
    </div>
    @endif
</div>
@endsection
