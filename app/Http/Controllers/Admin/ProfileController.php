<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProfileUpdateRequest;
use App\RoutePaths\Admin\AdminRoutePath;
use App\RoutePaths\Admin\Auth\AuthRoutePath;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends AdminBaseController
{
    public function __construct(
        private UserService $userService,
    ) {
        parent::__construct(service: $userService);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $this->registerBreadcrumb();

        $this->sharePageData([
            'title' => 'Profile Edit',
        ]);

        return view(AdminRoutePath::PROFILE_EDIT, [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route(AdminRoutePath::PROFILE_EDIT)->with([
            'message' => 'Profile updated successfully',
            'status' => true,
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route(AuthRoutePath::LOGIN);
    }
}
