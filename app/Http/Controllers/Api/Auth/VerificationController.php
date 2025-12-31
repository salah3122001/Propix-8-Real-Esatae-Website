<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Http\Resources\UserResource;

class VerificationController extends Controller
{
    use ApiResponse;

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return $this->error(__('api.verification.invalid_link'), 403);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->success(new UserResource($user), __('api.verification.already_verified'));
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return $this->success(new UserResource($user), __('api.verification.success'));
    }

    public function resend(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return $this->error(__('api.verification.already_verified'), 400);
        }

        $user->sendEmailVerificationNotification();

        return $this->success(new UserResource($user), __('api.verification.sent'));
    }
}
