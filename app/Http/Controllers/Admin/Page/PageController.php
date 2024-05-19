<?php

namespace App\Http\Controllers\Admin\Page;

use App\Exceptions\RedirectResponseException;
use App\Http\Controllers\Admin\Setting\BaseSettingController;
use App\Http\Requests\Admin\Page\PageStoreRequest;
use App\Http\Requests\Admin\Page\PageUpdateRequest;
use App\Http\Resources\Admin\Page\PageIndexResource;
use App\Messages\PageMessage;
use App\Models\Page;
use App\RoutePaths\Admin\Page\PageRoutePath;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Services\PageService;
use App\Services\SettingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\MessageBag;

class PageController extends BaseSettingController
{
    public function __construct(
        private SettingService $settingService,
        private PageRoutePath $pageRoutePath,
        private PageService $pageService,
        private PageMessage $pageMessage,
    ) {
        parent::__construct(service: $pageService);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): PageIndexResource|Renderable
    {
        if ($request->ajax()) {
            $attributes = (object) $request->only(
                ['draw', 'columns', 'order', 'start', 'length', 'search']
            );

            $request->merge([
                'recordsAll' => $this->pageService->getAllPages(),
                'recordsFiltered' => $this->pageService->getAllWithFilter(
                    filterColumns: ['id', 'title', 'slug', 'status'],
                    filterQuery: $attributes,
                ),
            ]);

            return PageIndexResource::make($attributes);
        }

        $columns = $this->tableColumns(
            ['title', 'slug', 'template']
        );

        $this->registerBreadcrumb();

        $this->sharePageData([
            'title' => $this->getActionTitle(),
            'createTitle' => $this->getActionTitle('create'),
        ]);

        return view($this->pageRoutePath::INDEX, [
            'create' => route($this->pageRoutePath::CREATE),
            'columnNames' => $columns,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Renderable
    {
        $this->registerBreadcrumb(
            parentRouteName: $this->pageRoutePath::INDEX,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->pageRoutePath::CREATE, [
            'templates' => $this->pageService->getPageTemplates(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PageStoreRequest $request): RedirectResponse|RedirectResponseException
    {
        $created = $this->pageService->createPage($request->getAttributes());

        throw_if(!$created, RedirectResponseException::class, $this->pageMessage->createFailed());

        return redirect()->route($this->pageRoutePath::EDIT, $created)->with([
            'message' => $this->pageMessage->createSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page): Renderable|RedirectREsponse
    {
        $this->registerBreadcrumb(
            parentRouteName: $this->pageRoutePath::INDEX,
            routeParameter: $page->id,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
            'frontUrl' => route($this->pageService::pageRouteName($page)),
        ]);

        try {
            return view($page->admin_template, [
                'page' => $page,
                'templates' => $this->pageService->getPageTemplates(),
                'settings' => $this->settingService->module(
                    $this->pageService::pageSettingModule($page)
                ),
            ]);
        } catch (Exception $e) {
            $errors = new MessageBag(['default' => $e->getMessage()]);

            return redirect()->back()->withErrors($errors);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Page $page, PageUpdateRequest $request): RedirectResponse|RedirectResponseException
    {
        try {
            $this->pageService->updatePage($page->id, $request->getAttributes());

            $this->settingService
                ->module($this->pageService::pageSettingModule($page))
                ->storeAllSettings(
                    $request->except(
                        ['_token', '_method', ...$this->pageService->getAllColumns()]
                    ),
                );

            return redirect()->route($this->pageRoutePath::EDIT, $page)->with([
                'message' => $this->pageMessage->updateSuccess(),
                'status' => true,
            ]);
        } catch (RedirectResponseException $e) {
            throw new RedirectResponseException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page): JsonResponse|RedirectResponseException
    {
        $deleted = $this->pageService->deletePage($page->id);

        throw_if(!$deleted, RedirectResponseException::class, $this->pageMessage->deleteFailed());

        return $this->jsonResponse()->message($this->pageMessage->deleteSuccess())
            ->success();
    }
}
