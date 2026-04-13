@extends('layouts.app')
@section('title', 'Publicar Vacante')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Publicar nueva vacante</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('company.jobs.store') }}">
                        @csrf

                        <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">INFORMACIÓN DEL CARGO</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Título del cargo *</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title') }}" placeholder="Ej: Desarrollador Web Junior" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Área / Perfil *</label>
                                <select name="area" class="form-select @error('area') is-invalid @enderror" required>
                                    <option value="">Seleccionar área...</option>
                                    @foreach(['Tecnología e informática','Administración de empresas','Contabilidad y finanzas','Ingeniería civil','Ingeniería ambiental','Petróleo y gas','Salud','Educación','Marketing y ventas','Recursos humanos','Derecho','Otro'] as $area)
                                        <option value="{{ $area }}" @selected(old('area') === $area)>{{ $area }}</option>
                                    @endforeach
                                </select>
                                @error('area')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Tipo de contrato *</label>
                                <select name="contract_type" class="form-select @error('contract_type') is-invalid @enderror" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach(['Término fijo','Indefinido','Prestación de servicios','Práctica / Pasantía','Aprendizaje SENA','Medio tiempo'] as $ct)
                                        <option value="{{ $ct }}" @selected(old('contract_type') === $ct)>{{ $ct }}</option>
                                    @endforeach
                                </select>
                                @error('contract_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Modalidad *</label>
                                <select name="modality" class="form-select" required>
                                    <option value="onsite" @selected(old('modality') === 'onsite')>Presencial</option>
                                    <option value="remote" @selected(old('modality') === 'remote')>Remoto</option>
                                    <option value="hybrid" @selected(old('modality') === 'hybrid')>Híbrido</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Ubicación *</label>
                                <input type="text" name="location" class="form-control"
                                       value="{{ old('location', 'Barrancabermeja') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Número de vacantes *</label>
                                <input type="number" name="positions" class="form-control" min="1" max="50"
                                       value="{{ old('positions', 1) }}" required>
                            </div>
                        </div>

                        <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">REMUNERACIÓN Y FECHA</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold">Rango salarial</label>
                                <input type="text" name="salary_range" class="form-control"
                                       value="{{ old('salary_range') }}" placeholder="Ej: $1.5M - $2M mensuales">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="salary_negotiable"
                                           id="salary_neg" value="1" @checked(old('salary_negotiable'))>
                                    <label class="form-check-label small" for="salary_neg">A convenir</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Fecha límite de postulación *</label>
                                <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror"
                                       value="{{ old('deadline') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">DESCRIPCIÓN</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Descripción del cargo *</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                          rows="4" placeholder="Describe las funciones principales del cargo..." required>{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Requisitos *</label>
                                <textarea name="requirements" class="form-control @error('requirements') is-invalid @enderror"
                                          rows="3" placeholder="Formación académica, experiencia, habilidades requeridas..." required>{{ old('requirements') }}</textarea>
                                @error('requirements')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Responsabilidades (opcional)</label>
                                <textarea name="responsibilities" class="form-control" rows="3"
                                          placeholder="¿Qué hará el candidato en su día a día?">{{ old('responsibilities') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Programas académicos preferidos</label>
                                <input type="text" name="programs_targeted" class="form-control"
                                       value="{{ old('programs_targeted') }}"
                                       placeholder="Ej: Ingeniería de Sistemas, Administración de Empresas">
                                <small class="text-muted">Separa los programas con comas</small>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requires_cv"
                                           id="requires_cv" value="1" @checked(old('requires_cv', true))>
                                    <label class="form-check-label small" for="requires_cv">
                                        Requerir hoja de vida para postularse
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>Publicar vacante
                            </button>
                            <a href="{{ route('company.jobs.index') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
