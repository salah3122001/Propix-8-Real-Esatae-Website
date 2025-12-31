<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class VerificationController extends Controller
{
    use ApiResponse;

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return $this->error(__('Your verification link is invalid.'), 403);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->success(null, __('Email already verified.'));
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return $this->success($user, __('Email verified successfully.'));
    }

    public function resend(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return $this->error(__('Email already verified.'), 400);
        }

        $user->sendEmailVerificationNotification();

        return $this->success($user, __('Verification link sent to your email.'));
    }
}
