<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Enums\SettingModule;
use App\Http\Controllers\Admin\Setting\BaseSettingController;
use App\Messages\SettingMessage;
use App\RoutePaths\Admin\Setting\PageSettingRoutePath;
use App\RoutePaths\Front\Page\PageRoutePath;
use App\Services\SettingService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;

class PageSettingController extends BaseSettingController
{
    public function __construct(
        private SettingService $settingService,
        private PageSettingRoutePath $pageSettingRoutePath,
        private SettingMessage $settingMessage,
    ) {
        parent::__construct(service: $settingService);
    }

    /**
     * Show the form for updating the resource.
     */
    public function home(): View
    {
        $module = SettingModule::HOME;
        $resourceTitle = $this->getActionTitle();

        $this->registerBreadcrumb(routeTitle: $resourceTitle);

        Session::put($this->sessionKey, $module);

        $this->sharePageData([
            'title' => $resourceTitle,
            'frontUrl' => route(PageRoutePath::HOME),
        ]);

        return view($this->pageSettingRoutePath::HOME, [
            'settings' => $this->settingService->module($module),
            'action' => $this->settingStoreRoute(),
        ]);
    }
}
