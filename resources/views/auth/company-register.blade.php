@extends('layouts.app')
@section('title', 'Registro de Empresa')

@push('styles')
<style>
    .register-page {
        min-height: calc(100vh - 150px);
        background: linear-gradient(160deg, #eef0f9 0%, #f4f5f7 60%, #e6f7ed 100%);
        padding: 2.5rem 0;
    }
    .register-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 4px 32px rgba(39,52,117,.1);
        overflow: hidden;
    }
    .register-header {
        background: linear-gradient(135deg, #273475 0%, #1d2659 100%);
        padding: 2rem 2rem 1.75rem;
        position: relative;
        overflow: hidden;
    }
    .register-header::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 160px; height: 160px;
        background: rgba(0,150,63,.12);
        border-radius: 50%;
    }
    .register-header > * { position: relative; z-index: 1; }
    .register-body { padding: 2rem; background: #fff; }

    .section-divider {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: 1.5rem 0 1.25rem;
    }
    .section-divider .label {
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: #273475;
        white-space: nowrap;
    }
    .section-divider .line {
        flex: 1;
        height: 1px;
        background: #eef0f9;
    }
    .section-divider .dot {
        width: 6px; height: 6px;
        background: #00963F;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .info-alert {
        background: #eef0f9;
        border: 1.5px solid #c7d2fe;
        border-radius: 10px;
        padding: .85rem 1.1rem;
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        font-size: .83rem;
        color: #273475;
    }
    .info-alert i { font-size: 1rem; flex-shrink: 0; margin-top: .05rem; }

    .btn-register {
        background: #00963F;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: .85rem;
        font-weight: 700;
        font-size: 1rem;
        width: 100%;
        transition: background .18s, transform .12s;
    }
    .btn-register:hover { background: #007832; color: #fff; transform: translateY(-1px); }
</style>
@endpush

@section('content')
<div class="register-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="register-card">

                    {{-- Header --}}
                    <div class="register-header">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="{{ asset('images/LogoWhite_.png') }}"
                                 alt="UNIPAZ"
                                 style="height: 58px; width: auto;">
                        </div>
                        <h5 class="text-white fw-bold mb-1 mt-2">
                            <i class="bi bi-building-add me-2" style="color:#6ee7a8;"></i>Registro de Empresa
                        </h5>
                        <p style="color:rgba(255,255,255,.65); font-size:.85rem; margin:0;">
                            Conecta tu empresa con el talento universitario de la región del Magdalena Medio.
                        </p>
                    </div>

                    {{-- Body --}}
                    <div class="register-body">

                        <div class="info-alert mb-4">
                            <i class="bi bi-shield-check" style="color:#273475;"></i>
                            <span>
                                Tu empresa será <strong>verificada por el equipo de UNIPAZ</strong> antes de poder publicar vacantes.
                                Recibirás un correo cuando sea aprobada. El proceso toma menos de 24 horas hábiles.
                            </span>
                        </div>

                        <form method="POST" action="{{ route('company.register.store') }}">
                            @csrf

                            {{-- Datos de acceso --}}
                            <div class="section-divider">
                                <span class="dot"></span>
                                <span class="label">Datos de acceso</span>
                                <span class="line"></span>
                            </div>

                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre del responsable <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}"
                                           placeholder="Juan Pérez"
                                           required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                                    <input type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}"
                                           placeholder="correo@empresa.com"
                                           required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                                    <input type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Mínimo 8 caracteres"
                                           minlength="8" required>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation"
                                           class="form-control"
                                           placeholder="Repite la contraseña"
                                           required>
                                </div>
                            </div>

                            {{-- Información empresa --}}
                            <div class="section-divider mt-3">
                                <span class="dot"></span>
                                <span class="label">Información de la empresa</span>
                                <span class="line"></span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Nombre de la empresa <span class="text-danger">*</span></label>
                                    <input type="text" name="company_name"
                                           class="form-control @error('company_name') is-invalid @enderror"
                                           value="{{ old('company_name') }}"
                                           placeholder="Razón social o nombre comercial"
                                           required>
                                    @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">NIT <span class="text-muted fw-normal">(opcional)</span></label>
                                    <input type="text" name="nit"
                                           class="form-control"
                                           value="{{ old('nit') }}"
                                           placeholder="900.123.456-7">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sector económico <span class="text-danger">*</span></label>
                                    <select name="sector" class="form-select @error('sector') is-invalid @enderror" required>
                                        <option value="">Seleccionar sector...</option>
                                        @foreach([
                                            'Tecnología e informática',
                                            'Petróleo y energía',
                                            'Salud',
                                            'Educación',
                                            'Comercio y retail',
                                            'Construcción',
                                            'Transporte y logística',
                                            'Agroindustria',
                                            'Financiero',
                                            'Servicios profesionales',
                                            'Otro'
                                        ] as $sector)
                                            <option value="{{ $sector }}" @selected(old('sector') === $sector)>
                                                {{ $sector }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sector')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono de contacto <span class="text-danger">*</span></label>
                                    <input type="text" name="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone') }}"
                                           placeholder="+57 300 000 0000"
                                           required>
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Persona de contacto <span class="text-danger">*</span></label>
                                    <input type="text" name="contact_person"
                                           class="form-control @error('contact_person') is-invalid @enderror"
                                           value="{{ old('contact_person') }}"
                                           placeholder="Nombre completo"
                                           required>
                                    @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Dirección en Barrancabermeja</label>
                                    <input type="text" name="address"
                                           class="form-control"
                                           value="{{ old('address') }}"
                                           placeholder="Calle 50 # 20-15">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción de la empresa</label>
                                    <textarea name="description" class="form-control" rows="3"
                                              placeholder="Cuéntanos a qué se dedica tu empresa, misión y valores...">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn-register">
                                    <i class="bi bi-check-circle-fill me-2"></i>Registrar mi empresa
                                </button>
                            </div>

                            <p class="text-center mt-3 mb-0" style="font-size:.83rem; color:#9ca3af;">
                                ¿Ya tienes cuenta?
                                <a href="{{ route('login') }}" style="color:#273475; font-weight:600;">Inicia sesión aquí</a>
                            </p>
                        </form>
                    </div>
                </div>

                {{-- Nota footer --}}
                <div class="text-center mt-3">
                    <small style="color:#9ca3af;">
                        <i class="bi bi-shield-lock me-1" style="color:#00963F;"></i>
                        Plataforma oficial UNIPAZ · Vigilada Ministerio de Educación
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
