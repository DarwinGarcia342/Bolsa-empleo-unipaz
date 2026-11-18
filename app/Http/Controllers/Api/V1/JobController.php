<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobPosting;
use App\Notifications\NewApplicationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = JobPosting::active()->with('company');

        foreach (['area', 'modality', 'contract_type'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->query($filter));
            }
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($builder) use ($search): void {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('company', fn ($company) => $company->where('company_name', 'like', "%{$search}%"));
            });
        }

        $jobs = $query->latest()->paginate(12)->withQueryString();
        $jobs->getCollection()->transform(fn (JobPosting $job) => $this->jobData($job));

        return response()->json($jobs);
    }

    public function show(JobPosting $jobPosting): JsonResponse
    {
        abort_unless($jobPosting->isActive(), 404);
        $jobPosting->load('company');

        return response()->json([
            'data' => $this->jobData($jobPosting),
        ]);
    }

    public function favorite(Request $request, JobPosting $jobPosting): JsonResponse
    {
        abort_unless($jobPosting->isActive(), 404);

        $user = $request->user();
        $user->favoriteJobPostings()->syncWithoutDetaching([$jobPosting->id]);

        return response()->json(['data' => [
            'job_posting_id' => $jobPosting->id,
            'is_favorite' => true,
        ]]);
    }

    public function unfavorite(Request $request, JobPosting $jobPosting): JsonResponse
    {
        $request->user()->favoriteJobPostings()->detach($jobPosting->id);

        return response()->json([
            'data' => [
                'job_posting_id' => $jobPosting->id,
                'is_favorite' => false,
            ],
        ]);
    }

    public function favorites(Request $request): JsonResponse
    {
        $jobs = $request->user()->favoriteJobPostings()
            ->active()
            ->with('company')
            ->latest('student_favorite_job_postings.created_at')
            ->paginate(12);
        $jobs->getCollection()->transform(fn (JobPosting $job) => $this->jobData($job));

        return response()->json($jobs);
    }

    public function apply(Request $request, JobPosting $jobPosting): JsonResponse
    {
        $user = $request->user();

        abort_unless($jobPosting->isActive(), 404);

        if (Application::where('user_id', $user->id)
            ->where('job_posting_id', $jobPosting->id)
            ->exists()) {
            return response()->json([
                'message' => 'Ya te has postulado a esta vacante.',
            ], 409);
        }

        $cvRule = $jobPosting->requires_cv && ! $user->studentProfile?->cv_path
            ? 'required|file|mimes:pdf|max:20480'
            : 'nullable|file|mimes:pdf|max:20480';

        $validated = $request->validate([
            'cover_letter' => ['nullable', 'string', 'max:2000'],
            'cv' => $cvRule,
        ]);

        $cvPath = $request->hasFile('cv')
            ? $request->file('cv')->store('cvs', 'public')
            : $user->studentProfile?->cv_path;

        $application = Application::create([
            'user_id' => $user->id,
            'job_posting_id' => $jobPosting->id,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'cv_path' => $cvPath,
            'status' => 'pending',
        ]);

        $jobPosting->loadMissing('company.user');
        $jobPosting->company?->user?->notify(new NewApplicationNotification($application));

        return response()->json([
            'message' => 'Postulación creada correctamente.',
            'data' => $this->applicationData($application->load('jobPosting.company')),
        ], 201);
    }

    public function applications(Request $request): JsonResponse
    {
        $applications = Application::where('user_id', $request->user()->id)
            ->with('jobPosting.company')
            ->latest()
            ->paginate(12);
        $applications->getCollection()->transform(fn (Application $application) => $this->applicationData($application));

        return response()->json($applications);
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
            'requires_cv' => $job->requires_cv,
            'programs_targeted' => $job->programs_targeted,
            'company' => $job->company ? [
                'id' => $job->company->id,
                'name' => $job->company->company_name,
                'sector' => $job->company->sector,
                'logo_url' => $job->company->logo_url,
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
            'cv_url' => $application->cv_path
                ? Storage::disk('public')->url($application->cv_path)
                : null,
            'job' => $application->jobPosting
                ? $this->jobData($application->jobPosting)
                : null,
        ];
    }
}
