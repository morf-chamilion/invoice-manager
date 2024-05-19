<?php

namespace App\Http\Controllers\Front\Auth;

use App\Enums\SettingModule;
use App\Http\Controllers\Front\FrontBaseController;
use App\Http\Requests\Front\Auth\EmailVerificationRequest;
use App\Messages\AuthMessage;
use App\RoutePaths\Front\Auth\AuthRoutePath;
use App\Services\SettingService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Support\Renderable;

class VerifyEmailController extends FrontBaseController
{
    public function __construct(
        private SettingService $settingService,
        private AuthMessage $authMessage,
    ) {
        parent::__construct(settingService: $settingService);
    }

    /**
     * Mark the customer's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): Renderable
    {
        $this->sharePageData(SettingModule::GENERAL);

        if ($request->customer->hasVerifiedEmail()) {
            return view(AuthRoutePath::VERIFICATION_SHOW)->with([
                'message' => $this->authMessage->mailAlreadyVerified(),
                'verified' => true,
            ]);
        }

        if ($request->customer->markEmailAsVerified()) {
            event(new Verified($request->customer));
        }

        return view(AuthRoutePath::VERIFICATION_SHOW)->with([
            'message' => $this->authMessage->mailVerifySuccess(),
            'verified' => true,
        ]);
    }
}
