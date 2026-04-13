<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobPosting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $latestJobs = JobPosting::active()
            ->with('company')
            ->latest()
            ->take(6)
            ->get();

        $totalJobs      = JobPosting::active()->count();
        $totalCompanies = Company::where('status', 'approved')->count();

        return view('home', compact('latestJobs', 'totalJobs', 'totalCompanies'));
    }
}
