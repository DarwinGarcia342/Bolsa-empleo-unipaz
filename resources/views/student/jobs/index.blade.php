@extends('layouts.app')
@section('title', 'Vacantes disponibles')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-briefcase text-primary me-2"></i>Vacantes disponibles</h2>

    <!-- Filtros -->
    <form method="GET" class="card mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por cargo o empresa..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="area" class="form-select">
                        <option value="">Todas las áreas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area }}" @selected(request('area') === $area)>{{ $area }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="modality" class="form-select">
                        <option value="">Modalidad</option>
                        <option value="onsite" @selected(request('modality') === 'onsite')>Presencial</option>
                        <option value="remote" @selected(request('modality') === 'remote')>Remoto</option>
                        <option value="hybrid" @selected(request('modality') === 'hybrid')>Híbrido</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="contract_type" class="form-select">
                        <option value="">Tipo contrato</option>
                        <option value="Término fijo" @selected(request('contract_type') === 'Término fijo')>Término fijo</option>
                        <option value="Indefinido" @selected(request('contract_type') === 'Indefinido')>Indefinido</option>
                        <option value="Prestación de servicios" @selected(request('contract_type') === 'Prestación de servicios')>Prestación servicios</option>
                        <option value="Práctica" @selected(request('contract_type') === 'Práctica')>Práctica</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </form>

    <!-- Resultados -->
    <div class="row g-4">
        @forelse($jobPostings as $job)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 {{ in_array($job->id, $appliedIds) ? 'border-success' : '' }}">
                    @if(in_array($job->id, $appliedIds))
                        <div class="card-header bg-success text-white small py-1 text-center">
                            <i class="bi bi-check-circle me-1"></i>Ya postulado
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="{{ $job->company->logo_url }}" class="rounded border" width="50" height="50" style="object-fit:cover;">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $job->title }}</h6>
                                <small class="text-muted">{{ $job->company->company_name }}</small>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $job->area }}</span>
                            <span class="badge {{ $job->modality_badge }} text-white">
                                {{ match($job->modality) { 'onsite' => 'Presencial', 'remote' => 'Remoto', 'hybrid' => 'Híbrido', default => $job->modality } }}
                            </span>
                            <span class="badge bg-light text-dark border">{{ $job->contract_type }}</span>
                        </div>

                        <p class="small text-muted">{{ Str::limit($job->description, 100) }}</p>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success small fw-semibold">
                                <i class="bi bi-cash-stack me-1"></i>{{ $job->salary_label }}
                            </span>
                            <span class="text-danger small">
                                <i class="bi bi-clock me-1"></i>{{ $job->remaining_days }} días
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('student.jobs.show', $job) }}"
                           class="btn btn-primary btn-sm w-100">
                            Ver detalles y postularme
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-search fs-1 opacity-25"></i>
                <p class="mt-3">No se encontraron vacantes con los filtros seleccionados.</p>
                <a href="{{ route('student.jobs') }}" class="btn btn-outline-primary">Limpiar filtros</a>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $jobPostings->links() }}
    </div>
</div>
@endsection
