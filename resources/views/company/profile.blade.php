@extends('layouts.app')
@section('title', 'Perfil de Empresa')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- Encabezado con logo -->
            <div class="card mb-4">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center gap-4">
                        <img src="{{ $company->logo_url }}" class="rounded" width="80" height="80" style="object-fit:cover; border:2px solid #dee2e6;">
                        <div>
                            <h4 class="fw-bold mb-1">{{ $company->company_name }}</h4>
                            <p class="text-muted mb-1 small">{{ $company->sector }}</p>
                            <span class="badge {{ $company->isApproved() ? 'bg-success' : ($company->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ match($company->status) {
                                    'approved' => '✓ Aprobada por UNIPAZ',
                                    'pending'  => 'Pendiente de aprobación',
                                    'rejected' => 'Rechazada',
                                    default    => $company->status
                                } }}
                            </span>
                        </div>
                    </div>
                    @if($company->status === 'pending')
                        <div class="alert alert-warning mt-3 mb-0 small">
                            <i class="bi bi-clock me-2"></i>
                            Tu empresa está en proceso de verificación. Recibirás un correo cuando sea aprobada y podrás publicar vacantes.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Formulario de edición -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Actualizar información</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('company.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">DATOS DE LA EMPRESA</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">Nombre de la empresa *</label>
                                <input type="text" name="company_name"
                                       class="form-control @error('company_name') is-invalid @enderror"
                                       value="{{ old('company_name', $company->company_name) }}" required>
                                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">NIT</label>
                                <input type="text" name="nit" class="form-control"
                                       value="{{ old('nit', $company->nit) }}" placeholder="900.123.456-7">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Sector / Industria *</label>
                                <select name="sector" class="form-select @error('sector') is-invalid @enderror" required>
                                    @foreach([
                                        'Tecnología e informática','Administración de empresas','Petróleo y gas',
                                        'Construcción e infraestructura','Comercio y retail','Salud y bienestar',
                                        'Educación','Manufactura e industria','Logística y transporte',
                                        'Financiero y bancario','Recursos humanos','Marketing y publicidad','Otro'
                                    ] as $sector)
                                        <option value="{{ $sector }}" @selected(old('sector', $company->sector) === $sector)>
                                            {{ $sector }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sector')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Persona de contacto *</label>
                                <input type="text" name="contact_person"
                                       class="form-control @error('contact_person') is-invalid @enderror"
                                       value="{{ old('contact_person', $company->contact_person) }}" required>
                                @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Teléfono</label>
                                <input type="text" name="phone" class="form-control"
                                       value="{{ old('phone', $company->phone) }}" placeholder="+57 300 000 0000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Sitio web</label>
                                <input type="url" name="website" class="form-control @error('website') is-invalid @enderror"
                                       value="{{ old('website', $company->website) }}" placeholder="https://www.ejemplo.com">
                                @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Dirección</label>
                                <input type="text" name="address" class="form-control"
                                       value="{{ old('address', $company->address) }}"
                                       placeholder="Calle / Carrera, Barrio, Ciudad">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Descripción de la empresa</label>
                                <textarea name="description" class="form-control" rows="4"
                                          placeholder="Cuéntale a los estudiantes sobre tu empresa, misión y cultura...">{{ old('description', $company->description) }}</textarea>
                                <small class="text-muted">Máximo 2000 caracteres</small>
                            </div>
                        </div>

                        <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">LOGO DE LA EMPRESA</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Subir nuevo logo</label>
                                <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror"
                                       accept=".jpg,.jpeg,.png">
                                <small class="text-muted">JPG o PNG, máximo 2MB</small>
                                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            @if($company->logo_path)
                                <div class="col-md-6 d-flex align-items-center">
                                    <div>
                                        <p class="small text-muted mb-1">Logo actual:</p>
                                        <img src="{{ $company->logo_url }}" class="rounded" height="60" style="object-fit:contain;">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Guardar cambios
                            </button>
                            <a href="{{ route('company.dashboard') }}" class="btn btn-outline-secondary">
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
