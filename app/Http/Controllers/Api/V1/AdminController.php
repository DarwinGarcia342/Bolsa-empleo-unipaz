<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\JobPosting;
use App\Models\User;
use App\Notifications\CompanyApprovedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $driver = config('database.default');
        $monthExpr = match ($driver) {
            'pgsql' => "TO_CHAR(created_at, 'MM')",
            'mysql' => "DATE_FORMAT(created_at, '%m')",
            default => "strftime('%m', created_at)",
        };
        $yearExpr = match ($driver) {
            'pgsql' => "TO_CHAR(created_at, 'YYYY')",
            'mysql' => "DATE_FORMAT(created_at, '%Y')",
            default => "strftime('%Y', created_at)",
        };

        return response()->json([
            'data' => [
                'students_total' => User::where('role', 'student')->count(),
                'companies_approved' => Company::where('status', 'approved')->count(),
                'companies_pending' => Company::where('status', 'pending')->count(),
                'jobs_active' => JobPosting::where('status', 'active')->count(),
                'applications_total' => Application::count(),
                'monthly_applications' => Application::selectRaw("{$monthExpr} as month, COUNT(*) as total")
                    ->whereRaw("{$yearExpr} = ?", [(string) now()->year])
                    ->groupBy('month')->orderBy('month')->get(),
                'top_areas' => JobPosting::active()->selectRaw('area, COUNT(*) as total')
                    ->groupBy('area')->orderByDesc('total')->take(5)->get(),
                'recent_companies' => Company::with('user')->latest()->take(5)->get()
                    ->map(fn (Company $company) => $this->companyData($company)),
                'recent_applications' => Application::with(['user', 'jobPosting.company'])
                    ->latest()->take(8)->get()->map(fn (Application $application) => $this->applicationData($application)),
            ],
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->userData($request->user())]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Perfil de administrador actualizado.',
            'data' => $this->userData($user->refresh()),
        ]);
    }

    public function companies(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:pending,approved,rejected'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Company::with('user');
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('company_name', 'like', "%{$search}%");
        }

        $companies = $query->latest()->paginate(15);
        $companies->getCollection()->transform(fn (Company $company) => $this->companyData($company));

        return response()->json($companies);
    }

    public function approveCompany(Company $company): JsonResponse
    {
        $company->update(['status' => 'approved']);
        $company->user?->notify(new CompanyApprovedNotification($company));

        return response()->json([
            'message' => 'Empresa aprobada.',
            'data' => $this->companyData($company->load('user')),
        ]);
    }

    public function rejectCompany(Company $company): JsonResponse
    {
        $company->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Empresa rechazada.',
            'data' => $this->companyData($company->load('user')),
        ]);
    }

    public function deleteCompany(Company $company): JsonResponse
    {
        $companyName = $company->company_name;
        DB::transaction(function () use ($company): void {
            $user = $company->user;
            $user?->tokens()->delete();
            $company->delete();
            $user?->delete();
        });

        return response()->json(['message' => "La empresa {$companyName} fue eliminada."]);
    }

    public function users(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'role' => ['nullable', 'in:admin,student,company'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $query = User::with(['studentProfile', 'company']);
        if (! empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn ($builder) => $builder->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $users = $query->latest()->paginate(20);
        $users->getCollection()->transform(fn (User $user) => $this->userData($user));

        return response()->json($users);
    }

    public function toggleUser(User $user): JsonResponse
    {
        abort_if($user->isAdmin(), 403, 'No se pueden desactivar cuentas de administrador.');
        $user->update(['active' => ! $user->active]);

        if (! $user->active) {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => $user->active ? 'Usuario activado.' : 'Usuario desactivado.',
            'data' => $this->userData($user),
        ]);
    }

    public function jobs(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:active,paused,closed'],
            'area' => ['nullable', 'string', 'max:100'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $query = JobPosting::with('company')->withCount('applications');
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['area'])) {
            $query->where('area', $filters['area']);
        }
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn ($builder) => $builder->where('title', 'like', "%{$search}%")
                ->orWhereHas('company', fn ($company) => $company->where('company_name', 'like', "%{$search}%")));
        }

        $jobs = $query->latest()->paginate(15);
        $jobs->getCollection()->transform(fn (JobPosting $job) => $this->jobData($job));

        return response()->json($jobs);
    }

    public function showJob(JobPosting $jobPosting): JsonResponse
    {
        $jobPosting->load(['company', 'applications.user.studentProfile'])->loadCount('applications');

        return response()->json([
            'data' => [
                ...$this->jobData($jobPosting),
                'applications' => $jobPosting->applications->map(fn (Application $application) => $this->applicationData($application)),
            ],
        ]);
    }

    public function toggleJob(JobPosting $jobPosting): JsonResponse
    {
        $status = $jobPosting->status === 'active' ? 'paused' : 'active';
        $jobPosting->update(['status' => $status]);

        return response()->json([
            'message' => $status === 'active' ? 'Vacante reanudada.' : 'Vacante pausada.',
            'data' => $this->jobData($jobPosting->refresh()->load('company')->loadCount('applications')),
        ]);
    }

    public function reports(): JsonResponse
    {
        return response()->json([
            'data' => [
                'students_total' => User::where('role', 'student')->count(),
                'companies_approved' => Company::where('status', 'approved')->count(),
                'jobs_active' => JobPosting::where('status', 'active')->count(),
                'jobs_closed' => JobPosting::where('status', 'closed')->count(),
                'applications_total' => Application::count(),
                'applications_accepted' => Application::where('status', 'accepted')->count(),
                'companies_by_status' => Company::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
                'applications_by_status' => Application::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
                'jobs_by_area' => JobPosting::selectRaw('area, COUNT(*) as total')->groupBy('area')->orderByDesc('total')->get(),
            ],
        ]);
    }

    private function userData(User $user): array
    {
        $user->loadMissing(['studentProfile', 'company']);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'active' => $user->active,
            'avatar_url' => $user->avatar_url,
            'student_profile' => $user->studentProfile ? [
                'student_code' => $user->studentProfile->student_code,
                'program' => $user->studentProfile->program,
                'semester' => $user->studentProfile->semester,
                'phone' => $user->studentProfile->phone,
            ] : null,
            'company' => $user->company ? $this->companyData($user->company) : null,
        ];
    }

    private function companyData(Company $company): array
    {
        return [
            'id' => $company->id,
            'company_name' => $company->company_name,
            'nit' => $company->nit,
            'sector' => $company->sector,
            'contact_person' => $company->contact_person,
            'phone' => $company->phone,
            'address' => $company->address,
            'description' => $company->description,
            'website' => $company->website,
            'status' => $company->status,
            'logo_url' => $company->logo_url,
            'user' => $company->relationLoaded('user') && $company->user ? [
                'id' => $company->user->id,
                'name' => $company->user->name,
                'email' => $company->user->email,
                'active' => $company->user->active,
            ] : null,
        ];
    }

    private function jobData(JobPosting $job): array
    {
        return [
            'id' => $job->id,
            'title' => $job->title,
            'description' => $job->description,
            'requirements' => $job->requirements,
            'responsibilities' => $job->responsibilities,
            'area' => $job->area,
            'contract_type' => $job->contract_type,
            'modality' => $job->modality,
            'schedule' => $job->schedule,
            'location' => $job->location,
            'salary_range' => $job->salary_range,
            'salary_negotiable' => $job->salary_negotiable,
            'positions' => $job->positions,
            'deadline' => $job->deadline?->toDateString(),
            'status' => $job->status,
            'requires_cv' => $job->requires_cv,
            'programs_targeted' => $job->programs_targeted,
            'applications_count' => $job->applications_count ?? null,
            'company' => $job->company ? [
                'id' => $job->company->id,
                'company_name' => $job->company->company_name,
                'sector' => $job->company->sector,
            ] : null,
        ];
    }

    private function applicationData(Application $application): array
    {
        return [
            'id' => $application->id,
            'status' => $application->status,
            'status_label' => $application->status_label,
            'cover_letter' => $application->cover_letter,
            'created_at' => $application->created_at,
            'student' => $application->user ? [
                'id' => $application->user->id,
                'name' => $application->user->name,
                'email' => $application->user->email,
                'program' => $application->user->studentProfile?->program,
            ] : null,
        ];
    }
}
