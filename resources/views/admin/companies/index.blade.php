@extends('layouts.app')
@section('title', 'Gestión de Empresas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-building text-primary me-2"></i>Gestión de Empresas</h4>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
    </div>

    <!-- Filtros -->
    <form method="GET" class="card mb-4">
        <div class="card-body py-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Buscar empresa..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pendientes</option>
                        <option value="approved" @selected(request('status') === 'approved')>Aprobadas</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Rechazadas</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filtrar</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Empresa</th>
                        <th>NIT</th>
                        <th>Sector</th>
                        <th>Contacto</th>
                        <th>Registrada</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $company->logo_url }}" class="rounded" width="36" height="36" style="object-fit:cover;">
                                    <div>
                                        <p class="mb-0 fw-semibold small">{{ $company->company_name }}</p>
                                        <small class="text-muted">{{ $company->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><small>{{ $company->nit ?? '—' }}</small></td>
                            <td><small>{{ $company->sector }}</small></td>
                            <td>
                                <small>{{ $company->contact_person }}</small><br>
                                <small class="text-muted">{{ $company->phone }}</small>
                            </td>
                            <td><small>{{ $company->created_at->format('d/m/Y') }}</small></td>
                            <td>
                                <span class="badge {{ match($company->status) { 'approved' => 'bg-success', 'pending' => 'bg-warning text-dark', 'rejected' => 'bg-danger', default => 'bg-secondary' } }}">
                                    {{ match($company->status) { 'approved' => 'Aprobada', 'pending' => 'Pendiente', 'rejected' => 'Rechazada', default => $company->status } }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($company->status === 'pending')
                                        <form method="POST" action="{{ route('admin.companies.approve', $company) }}">
                                            @csrf
                                            <button class="btn btn-success btn-sm" title="Aprobar">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.companies.reject', $company) }}">
                                            @csrf
                                            <button class="btn btn-danger btn-sm" title="Rechazar"
                                                    onclick="return confirm('¿Rechazar esta empresa?')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @elseif($company->status === 'approved')
                                        <form method="POST" action="{{ route('admin.companies.reject', $company) }}">
                                            @csrf
                                            <button class="btn btn-outline-danger btn-sm" title="Revocar aprobación"
                                                    onclick="return confirm('¿Revocar la aprobación de esta empresa?')">
                                                <i class="bi bi-slash-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No se encontraron empresas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent">
            {{ $companies->links() }}
        </div>
    </div>
</div>
@endsection
