<?php

namespace App\View\Components;

use App\Core\Bootstrap\BootstrapFrontCustomer;
use App\Providers\CustomerServiceProvider;
use App\Services\CustomerService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FrontCustomerLayout extends Component
{
    public function __construct(
        private CustomerService $customerService,
    ) {
        app(BootstrapFrontCustomer::class)->init();
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('front.customer.main', [
            'routes' => $this->customerService->getRoutes(),
            'customer' => CustomerServiceProvider::getAuthUser(),
        ]);
    }
}
