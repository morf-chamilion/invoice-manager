<?php

namespace App\Http\Controllers\Admin;

use App\Providers\AdminServiceProvider;
use App\RoutePaths\Admin\AdminRoutePath;
use App\Services\UserService;
use App\Services\VendorService;
use App\Services\InvoiceService;
use App\Services\CustomerService;
use App\Services\QuotationService;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;

class DashboardController extends AdminBaseController
{
    public function __construct(
        private UserService $userService,
        private VendorService $vendorService,
        private InvoiceService $invoiceService,
        private CustomerService $customerService,
        private QuotationService $quotationService,
        private PaymentService $paymentService,
    ) {
        parent::__construct(service: $userService);
    }

    public function index(): View
    {
        $this->registerBreadcrumb(routeTitle: 'Dashboard');

        $this->sharePageData([
            'title' => 'Dashboard',
        ]);

        return view(AdminRoutePath::DASHBOARD, [
            'isSuperAdmin' => AdminServiceProvider::isSuperAdmin(),
            'users' => $this->userService->getAllUsers(),
            'totalAmountDue' => $this->invoiceService->getTotalAmountDue(),
            'totalRevenueCollected' => $this->paymentService->getTotalRevenueCollected(),
            'invoicesThisMonth' => $this->invoiceService->getInvoicesSentThisMonth(),
            'conversionRate' => $this->quotationService->getConversionRate(),
            'revenueChartData' => $this->invoiceService->getRevenueChartData(),
            'invoiceStatusDistribution' => $this->invoiceService->getInvoiceStatusDistribution(),
        ]);
    }
}
