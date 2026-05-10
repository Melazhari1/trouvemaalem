<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\ContactSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactFormController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255'],
            'subject'          => ['required', 'string', 'min:5', 'max:255'],
            'message'          => ['required', 'string', 'min:10'],
            'recaptcha_token'  => ['nullable', 'string'],
        ]);

        $this->validateRecaptcha($request->input('recaptcha_token'));

        ContactSubmission::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'subject'    => $validated['subject'],
            'message'    => $validated['message'],
            'status'     => 'new',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('app.contact_success'),
        ]);
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
