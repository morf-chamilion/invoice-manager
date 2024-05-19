<?php

namespace App\Http\Controllers\Front\Page;

use App\Enums\PageStatus;
use App\Http\Controllers\Front\FrontBaseController;
use App\Providers\AdminServiceProvider;
use App\RoutePaths\Front\Page\PageRoutePath;
use App\Services\PageService;
use App\Services\SettingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Exception;

class PageController extends FrontBaseController
{
    public function __construct(
        private SettingService $settingService,
        private PageService $pageService,
    ) {
        parent::__construct(settingService: $settingService);
    }

    /**
     * Show the dynamic runtime resource.
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        $page = $this->pageService->getPageBySlug($request->segment(1));

        if (!AdminServiceProvider::getAuthUser() && $page->status === PageStatus::INACTIVE) {
            throw new HttpException(403, 'Access Forbidden');
        }

        $this->sharePageData($this->pageService->pageSettingModule($page))
            ->pageIdentifier(
                'template-' . $this->pageService->getTemplateName($page->admin_template)
            );

        try {
            return view($page->front_template);
        } catch (Exception $e) {
            $errors = new MessageBag(['default' => $e->getMessage()]);

            return redirect()->route(PageRoutePath::HOME)->withErrors($errors);
        }
    }
}
