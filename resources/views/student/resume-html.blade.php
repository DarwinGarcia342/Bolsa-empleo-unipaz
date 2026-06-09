@extends('layouts.app')
@section('title', 'Hoja de Vida HTML')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-start gap-3 border-bottom pb-4 mb-4">
                        <div>
                            <h2 class="fw-bold mb-1">{{ $user->name }}</h2>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            @if($profile?->program)
                                <span class="badge bg-primary">{{ $profile->program }}</span>
                            @endif
                            @if($profile?->semester)
                                <span class="badge bg-secondary">Semestre {{ $profile->semester }}</span>
                            @endif
                        </div>
                        <img src="{{ $user->avatar_url }}" class="rounded-circle border" width="88" height="88" style="object-fit:cover;">
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <h6 class="fw-bold text-uppercase text-muted small">Contacto</h6>
                            <p class="mb-1"><i class="bi bi-envelope me-2 text-primary"></i>{{ $user->email }}</p>
                            @if($profile?->phone)
                                <p class="mb-1"><i class="bi bi-telephone me-2 text-primary"></i>{{ $profile->phone }}</p>
                            @endif
                            @if($profile?->student_code)
                                <p class="mb-1"><i class="bi bi-person-badge me-2 text-primary"></i>{{ $profile->student_code }}</p>
                            @endif
                            @if($profile?->linkedin)
                                <p class="mb-1"><i class="bi bi-linkedin me-2 text-primary"></i><a href="{{ $profile->linkedin }}" target="_blank" rel="noopener">LinkedIn</a></p>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <h6 class="fw-bold text-uppercase text-muted small">Perfil profesional</h6>
                            <p class="text-muted">{{ $profile?->about ?: 'El estudiante aun no ha registrado una descripcion profesional.' }}</p>

                            <h6 class="fw-bold text-uppercase text-muted small mt-4">Formacion</h6>
                            <p class="mb-1 fw-semibold">{{ $profile?->program ?: 'Programa no registrado' }}</p>
                            <p class="text-muted small mb-0">Instituto Universitario de la Paz - UNIPAZ</p>
                        </div>
                    </div>

                    @if($profile?->cv_path)
                        <div class="alert alert-light border mt-4 mb-0 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Tambien hay un PDF cargado en el perfil.</span>
                            <a href="{{ Storage::url($profile->cv_path) }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                Ver PDF
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
