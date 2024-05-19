<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Providers\AdminServiceProvider;
use App\RoutePaths\Admin\AdminRoutePath;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends AdminBaseController
{
    public function __construct(
        private UserService $userService,
    ) {
        parent::__construct(service: $userService);
    }

    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $this->registerBreadcrumb(routeTitle: 'Profile Edit');

        $this->sharePageData([
            'title' => 'Profile Edit',
        ]);

        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(AdminServiceProvider::DASHBOARD_ROUTE)
            : view(AdminRoutePath::PROFILE_EDIT)->with([
                'user' => $request->user(),
            ]);
    }
}
