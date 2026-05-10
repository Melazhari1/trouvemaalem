<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Artisan;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class ReviewController extends Controller
{
    public function submit(Request $request, int $id): JsonResponse
    {
        $artisan = Artisan::findOrFail($id);

        $validated = $request->validate([
            'rating'          => ['required', 'integer', 'min:1', 'max:5'],
            'comment'         => ['required', 'string', 'min:10', 'max:500'],
            'name'            => ['nullable', 'string', 'max:255'],
            'email'           => ['nullable', 'email', 'max:255'],
            'recaptcha_token' => ['nullable', 'string'],
        ]);

        $this->validateRecaptcha($request->input('recaptcha_token'));
        $this->enforceRateLimit($request, $artisan->id, $validated['email'] ?? null);

        Review::create([
            'artisan_id'         => $artisan->id,
            'user_id'            => null,
            'rating'             => $validated['rating'],
            'comment'            => $validated['comment'],
            'status'             => 'pending',
            'submitted_by_name'  => $validated['name'] ?? 'Anonymous',
            'submitted_by_email' => $validated['email'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('app.review_submitted'),
        ]);
    }

    private function enforceRateLimit(Request $request, int $artisanId, ?string $email): void
    {
        $key = 'review:' . $artisanId . ':' . ($email ? md5(strtolower($email)) : $request->ip());

        if (RateLimiter::tooManyAttempts($key, 1)) {
            abort(429, __('app.review_rate_limit'));
        }

        RateLimiter::hit($key, 86400);
    }

    private function validateRecaptcha(?string $token): void
    {
        $secretKey = AdminSetting::get('recaptcha_secret_key');

        if (! $secretKey) {
            return;
        }

        if (! $token) {
            abort(422, __('app.recaptcha_failed'));
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $secretKey,
            'response' => $token,
        ])->json();

        if (! ($response['success'] ?? false) || ($response['score'] ?? 0) < 0.5) {
            abort(422, __('app.recaptcha_failed'));
        }
    }
}
