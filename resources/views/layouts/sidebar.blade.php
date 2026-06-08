<aside class="flex flex-col w-64 h-screen px-5 py-8 overflow-y-auto bg-white border-r rtl:border-r-0 rtl:border-l dark:bg-gray-900 dark:border-gray-700">
    <a href="/" class="flex items-center gap-2 px-3 text-2xl font-bold tracking-tight text-blue-600 dark:text-blue-400">
        UNIPAZ
    </a>

    <div class="flex flex-col justify-between flex-1 mt-6">
        <nav class="flex-1 -mx-3 space-y-3">
            @auth
                {{-- Menú para Estudiantes --}}
                @if(auth()->user()->role === 'student')
                    <div class="space-y-3">
                        <label class="px-3 text-xs text-gray-500 uppercase dark:text-gray-400">Estudiante</label>
                        
                        <a class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700 {{ request()->routeIs('student.dashboard') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('student.dashboard') }}">
                            <span class="mx-2 text-sm font-medium">Dashboard</span>
                        </a>

                        <a class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700 {{ request()->routeIs('student.jobs*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('student.jobs') }}">
                            <span class="mx-2 text-sm font-medium">Explorar Vacantes</span>
                        </a>

                        <a class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700 {{ request()->routeIs('student.applications') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('student.applications') }}">
                            <span class="mx-2 text-sm font-medium">Mis Postulaciones</span>
                        </a>
                    </div>
                @endif

                {{-- Menú para Empresas --}}
                @if(auth()->user()->role === 'company')
                    <div class="space-y-3">
                        <label class="px-3 text-xs text-gray-500 uppercase dark:text-gray-400">Empresa</label>

                        <a class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700 {{ request()->routeIs('company.dashboard') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('company.dashboard') }}">
                            <span class="mx-2 text-sm font-medium">Panel Principal</span>
                        </a>

                        <a class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700 {{ request()->routeIs('company.jobs.index') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('company.jobs.index') }}">
                            <span class="mx-2 text-sm font-medium">Mis Ofertas</span>
                        </a>

                        <a class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700 {{ request()->routeIs('company.jobs.create') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('company.jobs.create') }}">
                            <span class="mx-2 text-sm font-medium">Publicar Vacante</span>
                        </a>
                    </div>
                @endif

                {{-- Menú para Administradores --}}
                @if(auth()->user()->role === 'admin')
                    <div class="space-y-3">
                        <label class="px-3 text-xs text-gray-500 uppercase dark:text-gray-400">Administrador</label>

                        <a class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('admin.dashboard') }}">
                            <span class="mx-2 text-sm font-medium">Panel Admin</span>
                        </a>

                        <a class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700 {{ request()->routeIs('admin.companies*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('admin.companies.index') }}">
                            <span class="mx-2 text-sm font-medium">Validar Empresas</span>
                            @if(isset($pendingCompanies) && $pendingCompanies->count() > 0)
                                <span class="px-2 py-0.5 ml-auto text-xs font-bold text-white bg-red-500 rounded-full">{{ $pendingCompanies->count() }}</span>
                            @endif
                        </a>
                    </div>
                @endif

                <div class="pt-4 border-t dark:border-gray-700">
                    <label class="px-3 text-xs text-gray-500 uppercase dark:text-gray-400">Cuenta</label>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700">
                            <span class="mx-2 text-sm font-medium">Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            @endauth
        </nav>

        @auth
            <div class="mt-6">
                <div class="flex items-center gap-x-2">
                    <img class="object-cover rounded-full h-9 w-9" src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" alt="Avatar">
                    <div class="truncate">
                        <h1 class="text-sm font-semibold text-gray-700 dark:text-white capitalize">{{ auth()->user()->name }}</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</aside>