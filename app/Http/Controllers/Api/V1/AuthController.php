<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! $user->password || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son correctas.'],
            ]);
        }

        if (! $user->active) {
            return response()->json([
                'message' => 'Tu cuenta ha sido desactivada. Contacta al administrador.',
            ], 403);
        }

        $token = $user->createToken($credentials['device_name'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->userData($user),
        ]);
    }

    public function google(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_token' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($validated['id_token']);
        } catch (Exception) {
            return response()->json(['message' => 'El token de Google no es válido.'], 401);
        }

        $googleData = $googleUser->getRaw();
        $email = strtolower((string) $googleUser->getEmail());

        if (! ($googleData['email_verified'] ?? false) || ! str_ends_with($email, '@unipaz.edu.co')) {
            return response()->json([
                'message' => 'Se requiere una cuenta Google verificada con correo @unipaz.edu.co.',
            ], 403);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if ($user && ! $user->isStudent()) {
            return response()->json([
                'message' => 'Esta cuenta no está registrada como estudiante.',
            ], 403);
        }

        if ($user && ! $user->active) {
            return response()->json([
                'message' => 'Tu cuenta ha sido desactivada. Contacta al administrador.',
            ], 403);
        }

        if (! $user) {
            $user = DB::transaction(function () use ($googleUser, $email): User {
                $student = User::create([
                    'name' => $googleUser->getName() ?: $email,
                    'email' => $email,
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]);

                StudentProfile::create([
                    'user_id' => $student->id,
                    'program' => 'Sin especificar',
                ]);

                return $student;
            });
        } else {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

        return response()->json([
            'token' => $user->createToken($validated['device_name'])->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $this->userData($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->userData($request->user()),
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
            'avatar_url' => $user->avatar_url,
            'student_profile' => $user->studentProfile ? [
                'student_code' => $user->studentProfile->student_code,
                'program' => $user->studentProfile->program,
                'semester' => $user->studentProfile->semester,
                'phone' => $user->studentProfile->phone,
                'about' => $user->studentProfile->about,
                'linkedin' => $user->studentProfile->linkedin,
                'cv_url' => $user->studentProfile->cv_path
                    ? Storage::disk('public')->url($user->studentProfile->cv_path)
                    : null,
            ] : null,
            'company' => $user->company ? [
                'id' => $user->company->id,
                'company_name' => $user->company->company_name,
                'sector' => $user->company->sector,
                'status' => $user->company->status,
                'logo_url' => $user->company->logo_url,
            ] : null,
        ];
    }
}
