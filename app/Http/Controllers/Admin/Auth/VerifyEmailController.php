<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\EmailVerificationRequest;
use App\Messages\AuthMessage;
use App\RoutePaths\Admin\Auth\AuthRoutePath;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Support\Renderable;

class VerifyEmailController extends Controller
{
    public function __construct(
        private AuthMessage $authMessage,
    ) {
    }

    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): Renderable
    {
        if ($request->user->hasVerifiedEmail()) {
            return view(AuthRoutePath::VERIFICATION_SHOW)->with([
                'message' => $this->authMessage->mailAlreadyVerified(),
                'verified' => true,
            ]);
        }

        if ($request->user->markEmailAsVerified()) {
            event(new Verified($request->user));
        }

        return view(AuthRoutePath::VERIFICATION_SHOW)->with([
            'message' => $this->authMessage->mailVerifySuccess(),
            'verified' => true,
        ]);
    }
}
