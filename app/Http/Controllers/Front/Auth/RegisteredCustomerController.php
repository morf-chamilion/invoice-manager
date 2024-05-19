<?php

namespace App\Http\Controllers\Front\Auth;

use App\Enums\SettingModule;
use App\Http\Controllers\Front\FrontBaseController;
use App\Http\Requests\Front\Customer\CustomerStoreRequest;
use App\Messages\AuthMessage;
use App\RoutePaths\Front\Auth\AuthRoutePath;
use App\Services\CustomerService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredCustomerController extends FrontBaseController
{
    public function __construct(
        private SettingService $settingService,
        private CustomerService $customerService,
        private AuthMessage $authMessage,
    ) {
        parent::__construct(settingService: $settingService);
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $this->sharePageData(SettingModule::HOME);

        return view(AuthRoutePath::REGISTER);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(CustomerStoreRequest $request): ValidationException|RedirectResponse
    {
        $customer = $this->customerService->createCustomer([
            ...$request->getAttributes(),
            'notification' => true,
        ]);

        Auth::login($customer);

        return redirect()->route(AuthRoutePath::LOGIN)->with([
            'message' => $this->authMessage->mailVerifyNotice(),
            'status' => true,
        ]);
    }
}
