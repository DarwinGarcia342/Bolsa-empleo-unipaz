@extends('layouts.app')
@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-people text-primary me-2"></i>Gestión de Usuarios</h4>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
    </div>

    <!-- Filtros -->
    <form method="GET" class="card mb-4">
        <div class="card-body py-3">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Buscar por nombre o correo..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select form-select-sm">
                        <option value="">Todos los roles</option>
                        <option value="admin"   @selected(request('role') === 'admin')>Administrador</option>
                        <option value="company" @selected(request('role') === 'company')>Empresa</option>
                        <option value="student" @selected(request('role') === 'student')>Estudiante</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filtrar</button>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Resumen rápido -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-stat border-primary">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-2 text-primary"><i class="bi bi-mortarboard"></i></div>
                        <div>
                            <div class="fw-bold fs-5">{{ $users->where('role','student')->count() }}</div>
                            <small class="text-muted">Estudiantes en esta página</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat border-success">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-2 text-success"><i class="bi bi-building"></i></div>
                        <div>
                            <div class="fw-bold fs-5">{{ $users->where('role','company')->count() }}</div>
                            <small class="text-muted">Empresas en esta página</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat border-warning">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-2 text-warning"><i class="bi bi-person-x"></i></div>
                        <div>
                            <div class="fw-bold fs-5">{{ $users->where('active', false)->count() }}</div>
                            <small class="text-muted">Desactivados en esta página</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Registrado</th>
                        <th>Último acceso</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="{{ !$user->active ? 'table-warning' : '' }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $user->avatar_url }}" class="rounded-circle" width="36" height="36" style="object-fit:cover;">
                                    <div>
                                        <p class="mb-0 fw-semibold small">{{ $user->name }}</p>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ match($user->role) {
                                    'admin'   => 'bg-danger',
                                    'company' => 'bg-success',
                                    'student' => 'bg-primary',
                                    default   => 'bg-secondary'
                                } }}">
                                    {{ match($user->role) {
                                        'admin'   => 'Administrador',
                                        'company' => 'Empresa',
                                        'student' => 'Estudiante',
                                        default   => $user->role
                                    } }}
                                </span>
                            </td>
                            <td><small>{{ $user->created_at->format('d/m/Y') }}</small></td>
                            <td>
                                <small class="text-muted">
                                    {{ $user->updated_at->diffForHumans() }}
                                </small>
                            </td>
                            <td>
                                @if($user->active)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activo</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-dash-circle me-1"></i>Inactivo</span>
                                @endif
                            </td>
                            <td>
                                @if(!$user->isAdmin())
                                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}"
                                          onsubmit="return confirm('¿Confirmas cambiar el estado de este usuario?')">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm {{ $user->active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                            @if($user->active)
                                                <i class="bi bi-person-dash me-1"></i>Desactivar
                                            @else
                                                <i class="bi bi-person-check me-1"></i>Activar
                                            @endif
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-people fs-3 d-block mb-2"></i>
                                No se encontraron usuarios con estos filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
            <small class="text-muted">Total: {{ $users->total() }} usuarios</small>
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
