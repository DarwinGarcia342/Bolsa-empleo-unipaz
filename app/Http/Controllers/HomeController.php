<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobPosting;
use App\Models\STUDENTS;
use App\Models\User;
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
        
        // 2. CONTA EN LA BASE DE DATOS LOS ESTUDIANTES
        // Si en tu tabla de usuarios tienes un rol para estudiantes, puedes filtrarlo, por ejemplo:
        // $totalstudents = User::where('role', 'student')->count();
        $totalstudents  = User::count(); 

        // 3. PASA LA VARIABLE EXACTA A LA VISTA
        return view('home', compact('latestJobs', 'totalJobs', 'totalCompanies', 'totalstudents'));
    }
}