<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()->studentProfile;

        return response()->json([
            'data' => $profile ? $this->profileData($profile) : null,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'program' => ['required', 'string', 'max:150'],
            'semester' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
            'about' => ['nullable', 'string', 'max:1000'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'student_code' => ['nullable', 'string', 'max:30'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        $user = $request->user();
        $profile = $user->studentProfile;
        $data = collect($validated)->except('cv')->all();

        if ($request->hasFile('cv')) {
            $newCvPath = $request->file('cv')->store('cvs', 'public');

            if ($profile?->cv_path) {
                Storage::disk('public')->delete($profile->cv_path);
            }

            $data['cv_path'] = $newCvPath;
        }

        $profile = $user->studentProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return response()->json([
            'data' => $this->profileData($profile),
        ]);
    }

    private function profileData(StudentProfile $profile): array
    {
        return [
            'student_code' => $profile->student_code,
            'program' => $profile->program,
            'semester' => $profile->semester,
            'phone' => $profile->phone,
            'about' => $profile->about,
            'linkedin' => $profile->linkedin,
            'cv_url' => $profile->cv_path
                ? Storage::disk('public')->url($profile->cv_path)
                : null,
        ];
    }
}
