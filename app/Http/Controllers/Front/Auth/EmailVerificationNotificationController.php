<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Providers\CustomerServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if (CustomerServiceProvider::getAuthUser()->hasVerifiedEmail()) {
            return redirect()->intended(
                route(name: CustomerServiceProvider::DASHBOARD_ROUTE, absolute: false)
            );
        }

        CustomerServiceProvider::getAuthUser()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
