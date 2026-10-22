@extends('layouts.app')
@section('title', 'Perfil de Administrador')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-person-gear me-2 text-primary"></i>Perfil de administrador
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nombre</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Correo institucional</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <hr>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nueva contraseña</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       autocomplete="new-password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                       autocomplete="new-password">
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Guardar cambios
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
