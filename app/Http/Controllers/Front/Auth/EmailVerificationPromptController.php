<?php

namespace App\Http\Controllers\Front\Auth;

use App\Enums\SettingModule;
use App\Http\Controllers\Front\FrontBaseController;
use App\Providers\CustomerServiceProvider;
use App\RoutePaths\Front\Customer\CustomerRoutePath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends FrontBaseController
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $this->sharePageData(SettingModule::GENERAL);

        return CustomerServiceProvider::getAuthUser()->hasVerifiedEmail()
            ? redirect()->intended(CustomerServiceProvider::DASHBOARD_ROUTE)
            : view(CustomerRoutePath::DASHBOARD_SHOW)->with([
                'customer' => CustomerServiceProvider::getAuthUser(),
            ]);
    }
}
