@extends('layouts.app')
@section('title', 'Reportes de Empresa')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-bar-chart-line text-primary me-2"></i>Reportes de {{ $company->company_name }}</h4>
        <a href="{{ route('company.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Vacantes', $stats['jobs_total'], 'briefcase', 'primary'],
            ['Activas', $stats['jobs_active'], 'check-circle', 'success'],
            ['Postulaciones', $stats['applications_total'], 'file-earmark-person', 'warning'],
            ['Pendientes', $stats['applications_pending'], 'clock', 'secondary'],
            ['Aceptadas', $stats['applications_accepted'], 'award', 'success'],
        ] as [$label, $value, $icon, $color])
            <div class="col-md col-sm-6">
                <div class="card h-100 text-center">
                    <div class="card-body py-3">
                        <i class="bi bi-{{ $icon }} text-{{ $color }} fs-3"></i>
                        <div class="fs-2 fw-bold text-{{ $color }}">{{ $value }}</div>
                        <small class="text-muted">{{ $label }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header bg-transparent fw-semibold">Postulaciones por estado</div>
                <div class="card-body">
                    @php $total = $applicationsByStatus->sum(); @endphp
                    @foreach([
                        'pending' => ['Pendiente', 'bg-secondary'],
                        'reviewed' => ['En revision', 'bg-info'],
                        'interview' => ['Entrevista', 'bg-warning'],
                        'accepted' => ['Aceptada', 'bg-success'],
                        'rejected' => ['No seleccionada', 'bg-danger'],
                    ] as $key => [$label, $color])
                        @php $count = $applicationsByStatus[$key] ?? 0; $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
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
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header bg-transparent fw-semibold">Vacantes con mas postulaciones</div>
                <div class="card-body">
                    @forelse($jobsPerformance as $job)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div class="me-3">
                                <div class="fw-semibold">{{ $job->title }}</div>
                                <small class="text-muted">{{ $job->status }} · {{ $job->deadline?->format('d/m/Y') }}</small>
                            </div>
                            <span class="badge rounded-pill bg-primary">{{ $job->applications_count }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">Aun no hay datos para reportar.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
