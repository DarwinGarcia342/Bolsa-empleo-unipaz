<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\JobPosting;
use App\Models\User;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'company_name' => ['required', 'string', 'max:255'],
            'nit' => ['nullable', 'regex:/^[0-9.\-]*$/', 'unique:companies,nit'],
            'sector' => ['required', 'string', 'max:100'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $company = DB::transaction(function () use ($validated): Company {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'company',
            ]);

            return Company::create([
                'user_id' => $user->id,
                'company_name' => $validated['company_name'],
                'nit' => $validated['nit'] ?? null,
                'sector' => $validated['sector'],
                'contact_person' => $validated['contact_person'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'description' => $validated['description'],
                'status' => 'pending',
            ]);
        });

        return response()->json([
            'message' => 'Registro recibido. La empresa debe ser aprobada antes de publicar vacantes.',
            'data' => $this->companyData($company->load('user')),
        ], 201);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $company = $this->company($request);
        $applications = Application::whereHas('jobPosting', fn ($query) => $query->where('company_id', $company->id));

        return response()->json([
            'data' => [
                'company' => $this->companyData($company),
                'jobs_total' => JobPosting::where('company_id', $company->id)->count(),
                'jobs_active' => JobPosting::where('company_id', $company->id)->where('status', 'active')->count(),
                'applications_total' => (clone $applications)->count(),
                'applications_pending' => (clone $applications)->where('status', 'pending')->count(),
                'recent_applications' => (clone $applications)->with(['user.studentProfile', 'jobPosting'])
                    ->latest()->take(8)->get()->map(fn (Application $application) => $this->applicationData($application)),
            ],
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->companyData($this->company($request))]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $company = $this->company($request);
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'sector' => ['required', 'string', 'max:100'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'nit' => ['nullable', 'regex:/^[0-9.\-]*$/', 'unique:companies,nit,'.$company->id],
        ]);

        $data = collect($validated)->except('logo')->all();
        if ($request->hasFile('logo')) {
            $newLogoPath = $request->file('logo')->store('logos', 'public');
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $data['logo_path'] = $newLogoPath;
        }

        $company->update($data);

        return response()->json(['data' => $this->companyData($company->refresh())]);
    }

    public function jobs(Request $request): JsonResponse
    {
        $company = $this->company($request);
        $jobs = JobPosting::where('company_id', $company->id)
            ->withCount('applications')->latest()->paginate(12);
        $jobs->getCollection()->transform(fn (JobPosting $job) => $this->jobData($job));

        return response()->json($jobs);
    }

    public function storeJob(Request $request): JsonResponse
    {
        $company = $this->approvedCompany($request);
        $validated = $request->validate($this->jobRules(true));
        $job = JobPosting::create($this->jobPayload($validated, $company->id));

        return response()->json([
            'message' => 'Vacante publicada correctamente.',
            'data' => $this->jobData($job->load('company')),
        ], 201);
    }

    public function showJob(Request $request, JobPosting $jobPosting): JsonResponse
    {
        $this->authorizeJob($request, $jobPosting);

        return response()->json([
            'data' => $this->jobData($jobPosting->load('company')->loadCount('applications')),
        ]);
    }

    public function updateJob(Request $request, JobPosting $jobPosting): JsonResponse
    {
        $company = $this->approvedCompany($request);
        $this->authorizeJob($request, $jobPosting);
        $validated = $request->validate($this->jobRules(false));
        $jobPosting->update($this->jobPayload($validated, $company->id));

        return response()->json([
            'message' => 'Vacante actualizada correctamente.',
            'data' => $this->jobData($jobPosting->refresh()->load('company')->loadCount('applications')),
        ]);
    }

    public function closeJob(Request $request, JobPosting $jobPosting): JsonResponse
    {
        $this->approvedCompany($request);
        $this->authorizeJob($request, $jobPosting);
        $jobPosting->update(['status' => 'closed']);

        return response()->json(['message' => 'Vacante cerrada correctamente.']);
    }

    public function applications(Request $request): JsonResponse
    {
        $company = $this->company($request);
        $filters = $request->validate([
            'status' => ['nullable', 'in:pending,reviewed,interview,accepted,rejected'],
            'job' => ['nullable', 'integer'],
        ]);

        $query = Application::with(['user.studentProfile', 'jobPosting'])
            ->whereHas('jobPosting', function ($builder) use ($company, $filters): void {
                $builder->where('company_id', $company->id);
                if (! empty($filters['job'])) {
                    $builder->whereKey($filters['job']);
                }
            });

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $applications = $query->latest()->paginate(15);
        $applications->getCollection()->transform(fn (Application $application) => $this->applicationData($application));

        return response()->json($applications);
    }

    public function showApplication(Request $request, Application $application): JsonResponse
    {
        $this->authorizeApplication($request, $application);

        return response()->json([
            'data' => $this->applicationData($application->load(['user.studentProfile', 'jobPosting'])),
        ]);
    }

    public function updateApplication(Request $request, Application $application): JsonResponse
    {
        $this->authorizeApplication($request, $application);
        $validated = $request->validate([
            'status' => ['required', 'in:pending,reviewed,interview,accepted,rejected'],
            'company_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $application->update([
            ...$validated,
            'reviewed_at' => now(),
        ]);
        $application->user->notify(new ApplicationStatusNotification($application));

        return response()->json([
            'message' => 'Estado de postulación actualizado.',
            'data' => $this->applicationData($application->refresh()->load(['user.studentProfile', 'jobPosting'])),
        ]);
    }

    public function reports(Request $request): JsonResponse
    {
        $company = $this->company($request);
        $applications = Application::whereHas('jobPosting', fn ($query) => $query->where('company_id', $company->id));

        return response()->json([
            'data' => [
                'jobs_total' => JobPosting::where('company_id', $company->id)->count(),
                'jobs_active' => JobPosting::where('company_id', $company->id)->where('status', 'active')->count(),
                'applications_total' => (clone $applications)->count(),
                'applications_pending' => (clone $applications)->where('status', 'pending')->count(),
                'applications_accepted' => (clone $applications)->where('status', 'accepted')->count(),
                'applications_by_status' => (clone $applications)->selectRaw('status, COUNT(*) as total')
                    ->groupBy('status')->pluck('total', 'status'),
                'jobs_performance' => JobPosting::where('company_id', $company->id)
                    ->withCount('applications')->orderByDesc('applications_count')->take(10)->get()
                    ->map(fn (JobPosting $job) => $this->jobData($job)),
            ],
        ]);
    }

    private function company(Request $request): Company
    {
        $company = $request->user()->company;
        abort_unless($company, 404, 'No se encontró el perfil de empresa.');

        return $company;
    }

    private function approvedCompany(Request $request): Company
    {
        $company = $this->company($request);
        abort_unless($company->isApproved(), 403, 'La empresa debe ser aprobada antes de gestionar vacantes.');

        return $company;
    }

    private function authorizeJob(Request $request, JobPosting $jobPosting): void
    {
        abort_unless((int) $jobPosting->company_id === (int) $this->company($request)->id, 404);
    }

    private function authorizeApplication(Request $request, Application $application): void
    {
        abort_unless(
            (int) $application->jobPosting?->company_id === (int) $this->company($request)->id,
            404
        );
    }

    private function jobRules(bool $creating): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'requirements' => ['required', 'string', 'max:3000'],
            'responsibilities' => ['required', 'string', 'min:20', 'max:3000'],
            'area' => ['required', 'string', 'max:100'],
            'contract_type' => ['required', 'string', 'max:100'],
            'modality' => ['required', 'in:onsite,remote,hybrid'],
            'location' => ['required', 'string', 'max:150'],
            'salary_range' => ['nullable', 'string', 'max:100'],
            'salary_negotiable' => ['sometimes', 'boolean'],
            'positions' => ['required', 'integer', 'min:1', 'max:100'],
            'deadline' => $creating ? ['required', 'date', 'after:today'] : ['required', 'date'],
            'requires_cv' => ['sometimes', 'boolean'],
            'programs_targeted' => ['nullable', 'string', 'max:500'],
            ...($creating ? [] : ['status' => ['required', 'in:active,paused,closed']]),
        ];
    }

    private function jobPayload(array $validated, int $companyId): array
    {
        return [
            ...$validated,
            'company_id' => $companyId,
            'salary_negotiable' => (bool) ($validated['salary_negotiable'] ?? false),
            'requires_cv' => (bool) ($validated['requires_cv'] ?? false),
            'status' => $validated['status'] ?? 'active',
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
            'account' => $company->relationLoaded('user') && $company->user ? [
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
            'location' => $job->location,
            'salary_range' => $job->salary_range,
            'salary_negotiable' => $job->salary_negotiable,
            'positions' => $job->positions,
            'deadline' => $job->deadline?->toDateString(),
            'status' => $job->status,
            'requires_cv' => $job->requires_cv,
            'programs_targeted' => $job->programs_targeted,
            'applications_count' => $job->applications_count ?? null,
        ];
    }

    private function applicationData(Application $application): array
    {
        $student = $application->user;
        $profile = $student?->studentProfile;

        return [
            'id' => $application->id,
            'status' => $application->status,
            'status_label' => $application->status_label,
            'cover_letter' => $application->cover_letter,
            'company_notes' => $application->company_notes,
            'reviewed_at' => $application->reviewed_at,
            'created_at' => $application->created_at,
            'cv_url' => $application->cv_path ? Storage::disk('public')->url($application->cv_path) : null,
            'student' => $student ? [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'profile' => $profile ? [
                    'student_code' => $profile->student_code,
                    'program' => $profile->program,
                    'semester' => $profile->semester,
                    'phone' => $profile->phone,
                    'about' => $profile->about,
                    'linkedin' => $profile->linkedin,
                ] : null,
            ] : null,
            'job' => $application->jobPosting ? [
                'id' => $application->jobPosting->id,
                'title' => $application->jobPosting->title,
            ] : null,
        ];
    }
}
