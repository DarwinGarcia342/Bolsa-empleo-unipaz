<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobPosting;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('home')->with('error', 'Perfil de empresa no encontrado.');
        }

        $totalVacantes    = JobPosting::where('company_id', $company->id)->count();
        $activeVacantes   = JobPosting::where('company_id', $company->id)->where('status', 'active')->count();
        $totalPostulaciones = Application::whereHas('jobPosting', fn($q) => $q->where('company_id', $company->id))->count();
        $pendingPostulaciones = Application::whereHas('jobPosting', fn($q) => $q->where('company_id', $company->id))
            ->where('status', 'pending')->count();

        $recentApplications = Application::whereHas('jobPosting', fn($q) => $q->where('company_id', $company->id))
            ->with(['user.studentProfile', 'jobPosting'])
            ->latest()
            ->take(8)
            ->get();

        return view('company.dashboard', compact(
            'user', 'company', 'totalVacantes', 'activeVacantes',
            'totalPostulaciones', 'pendingPostulaciones', 'recentApplications'
        ));
    }

    public function profile()
    {
        $company = Auth::user()->company;
        return view('company.profile', compact('company'));
    }

    public function updateProfile(Request $request)
    {
        $company = Auth::user()->company;

        $request->validate([
            'company_name'   => 'required|string|max:255',
            'sector'         => 'required|string|max:100',
            'contact_person' => 'required|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:255',
            'description'    => 'nullable|string|max:2000',
            'website'        => 'nullable|url|max:255',
            'logo'           => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'nit'            => 'nullable|string|max:20',
        ]);

        $data = $request->only([
            'company_name', 'sector', 'contact_person',
            'phone', 'address', 'description', 'website', 'nit'
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($data);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    // ─── Gestión de postulaciones ─────────────────────────────────
    public function applications(Request $request)
    {
        $company = Auth::user()->company;

        $query = Application::whereHas('jobPosting', fn($q) => $q->where('company_id', $company->id))
            ->with(['user.studentProfile', 'jobPosting']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('job')) {
            $query->where('job_posting_id', $request->job);
        }

        $applications = $query->latest()->paginate(15)->withQueryString();
        $vacantes = JobPosting::where('company_id', $company->id)->get();

        return view('company.applications', compact('applications', 'vacantes'));
    }

    public function updateApplicationStatus(Request $request, Application $application)
    {
        // Verificar que la postulación pertenezca a una vacante de esta empresa
        $company = Auth::user()->company;
        if ($application->jobPosting->company_id !== $company->id) {
            abort(403);
        }

        $request->validate([
            'status'        => 'required|in:pending,reviewed,interview,accepted,rejected',
            'company_notes' => 'nullable|string|max:500',
        ]);

        $application->update([
            'status'        => $request->status,
            'company_notes' => $request->company_notes,
            'reviewed_at'   => now(),
        ]);

        // Notificar al estudiante sobre el cambio de estado
        $application->user->notify(new ApplicationStatusNotification($application));

        return back()->with('success', 'Estado de postulación actualizado.');
    }
}
