<?php

namespace App\Http\Controllers\Admin;

use App\RoutePaths\Admin\AdminRoutePath;
use App\Services\UserService;
use Illuminate\Contracts\View\View;

class DashboardController extends AdminBaseController
{
    public function __construct(
        private UserService $userService,
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
            'users' => $this->userService->getAllUsers(),
        ]);
    }
}
