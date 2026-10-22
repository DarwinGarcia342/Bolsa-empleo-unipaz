<?php

namespace App\Http\Controllers;

use App\Models\LawReference;
use Illuminate\Http\Request;

class LawReferenceController extends Controller
{
    public function index(Request $request)
    {
        $role = auth()->user()->role;
        $category = $request->query('category');
        
        $query = LawReference::where('is_active', true)->get()
            ->filter(fn($law) => $law->isApplicableToRole($role));

        if ($category) {
            $query = $query->filter(fn($law) => $law->category === $category);
        }

        $categories = LawReference::where('is_active', true)
            ->get()
            ->filter(fn($law) => $law->isApplicableToRole($role))
            ->pluck('category')
            ->unique();

        return view('laws.index', [
            'laws' => $query,
            'categories' => $categories,
            'selectedCategory' => $category,
            'role' => $role,
        ]);
    }

    public function show(LawReference $law)
    {
        $role = auth()->user()->role;
        
        if (!$law->isApplicableToRole($role)) {
            abort(403, 'No tienes acceso a esta ley.');
        }

        return view('laws.show', ['law' => $law]);
    }

    public function getByRole(string $role)
    {
        return LawReference::getByRole($role);
    }
}
