<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LawReference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LawReferenceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'category' => ['nullable', 'string', 'max:255'],
        ]);
        $role = $request->user()->role;
        $laws = LawReference::where('is_active', true)->get()
            ->filter(fn (LawReference $law) => $law->isApplicableToRole($role))
            ->when(! empty($filters['category']), fn ($items) => $items->where('category', $filters['category']))
            ->values()
            ->map(fn (LawReference $law) => $this->lawData($law));

        return response()->json(['data' => $laws]);
    }

    public function show(Request $request, LawReference $lawReference): JsonResponse
    {
        abort_unless(
            $lawReference->is_active && $lawReference->isApplicableToRole($request->user()->role),
            404
        );

        return response()->json(['data' => $this->lawData($lawReference)]);
    }

    private function lawData(LawReference $law): array
    {
        return [
            'id' => $law->id,
            'title' => $law->title,
            'description' => $law->description,
            'applicable_to' => $law->applicable_to,
            'category' => $law->category,
            'law_number' => $law->law_number,
            'publication_date' => $law->publication_date?->toDateString(),
            'relevant_articles' => $law->relevant_articles,
            'implementation_notes' => $law->implementation_notes,
        ];
    }
}
