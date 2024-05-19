<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Providers\AdminServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if (AdminServiceProvider::getAuthUser()->hasVerifiedEmail()) {
            return redirect()->intended(AdminServiceProvider::DASHBOARD_ROUTE);
        }

        AdminServiceProvider::getAuthUser()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
