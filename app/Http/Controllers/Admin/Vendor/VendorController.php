<?php

namespace App\Http\Controllers\Admin\Vendor;

use App\Exceptions\RedirectResponseException;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Vendor\VendorSettingGeneralUpdateRequest;
use App\Http\Requests\Admin\Vendor\VendorSettingInvoiceUpdateRequest;
use App\Http\Requests\Admin\Vendor\VendorSettingQuotationUpdateRequest;
use App\Http\Requests\Admin\Vendor\VendorStoreRequest;
use App\Http\Requests\Admin\Vendor\VendorUpdateRequest;
use App\Http\Resources\Admin\Vendor\VendorIndexResource;
use App\Messages\VendorMessage;
use App\Models\Vendor;
use App\RoutePaths\Admin\Vendor\VendorRoutePath;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Services\VendorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class VendorController extends AdminBaseController
{
    public function __construct(
        private VendorRoutePath $vendorRoutePath,
        private VendorService $vendorService,
        private VendorMessage $vendorMessage,
    ) {
        parent::__construct(service: $vendorService);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): VendorIndexResource|Renderable
    {
        if ($request->ajax()) {
            $attributes = (object) $request->only(
                ['draw', 'columns', 'order', 'start', 'length', 'search']
            );

            $request->merge([
                'recordsAll' => $this->vendorService->getAllVendors(),
                'recordsFiltered' => $this->vendorService->getAllWithFilter(
                    filterColumns: ['id', 'name', 'status'],
                    filterQuery: $attributes,
                ),
            ]);

            return VendorIndexResource::make($attributes);
        }

        $columns = $this->tableColumns(
            ['name', 'currency']
        );

        $this->registerBreadcrumb();

        $this->sharePageData([
            'title' => $this->getActionTitle(),
            'createTitle' => $this->getActionTitle('create'),
        ]);

        return view($this->vendorRoutePath::INDEX, [
            'create' => route($this->vendorRoutePath::CREATE),
            'columnNames' => $columns,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Renderable
    {
        $this->registerBreadcrumb(
            parentRouteName: $this->vendorRoutePath::INDEX,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->vendorRoutePath::CREATE);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VendorStoreRequest $request): RedirectResponse|RedirectResponseException
    {
        $created = $this->vendorService->createVendor($request->getAttributes());

        throw_if(!$created, RedirectResponseException::class, $this->vendorMessage->createFailed());

        $this->registerBreadcrumb();

        return redirect()->route($this->vendorRoutePath::EDIT, $created)->with([
            'message' => $this->vendorMessage->createSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor): Renderable
    {
        $this->registerBreadcrumb(
            parentRouteName: $this->vendorRoutePath::INDEX,
            routeParameter: $vendor->id,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->vendorRoutePath::EDIT, [
            'vendor' => $vendor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Vendor $vendor, VendorUpdateRequest $request): RedirectResponse|RedirectResponseException
    {
        $updated = $this->vendorService->updateVendor($vendor->id, $request->getAttributes());

        throw_if(!$updated, RedirectResponseException::class, $this->vendorMessage->updateFailed());

        return redirect()->route($this->vendorRoutePath::EDIT, $vendor)->with([
            'message' => $this->vendorMessage->updateSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor): JsonResponse|RedirectResponseException
    {
        $deleted = $this->vendorService->deleteVendor($vendor->id);

        throw_if(!$deleted, RedirectResponseException::class, $this->vendorMessage->deleteFailed());

        return $this->jsonResponse()->message($this->vendorMessage->deleteSuccess())
            ->success();
    }

    /**
     * Show the form for editing the specified resource settings.
     */
    public function editGeneralSettings(Vendor $vendor): Renderable
    {
        if ($vendor->id !== auth()->user()->vendor?->id) {
            return abort(403);
        }

        $this->registerBreadcrumb(
            routeParameter: $vendor->id,
        );

        $this->sharePageData([
            'title' => 'General Settings',
        ]);

        return view($this->vendorRoutePath::GENERAL_SETTING_EDIT, [
            'vendor' => $vendor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateGeneralSettings(Vendor $vendor, VendorSettingGeneralUpdateRequest $request): RedirectResponse|RedirectResponseException
    {
        $updated = $this->vendorService->updateVendorSettings(
            $vendor->id,
            $request->getAttributes(),
            $this->vendorRoutePath::GENERAL_SETTING_UPDATE,
        );

        throw_if(!$updated, RedirectResponseException::class, $this->vendorMessage->updateFailed());

        return redirect()->route($this->vendorRoutePath::GENERAL_SETTING_EDIT, $vendor)->with([
            'message' => $this->vendorMessage->updateSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource settings.
     */
    public function editQuotationSettings(Vendor $vendor): Renderable
    {
        if ($vendor->id !== auth()->user()->vendor?->id) {
            return abort(403);
        }

        $this->registerBreadcrumb(
            routeParameter: $vendor->id,
        );

        $this->sharePageData([
            'title' => 'Quotation Settings',
        ]);

        return view($this->vendorRoutePath::QUOTATION_SETTING_EDIT, [
            'vendor' => $vendor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateQuotationSettings(Vendor $vendor, VendorSettingQuotationUpdateRequest $request): RedirectResponse|RedirectResponseException
    {
        $updated = $this->vendorService->updateVendorSettings($vendor->id, $request->getAttributes());

        throw_if(!$updated, RedirectResponseException::class, $this->vendorMessage->updateFailed());

        return redirect()->route($this->vendorRoutePath::QUOTATION_SETTING_EDIT, $vendor)->with([
            'message' => $this->vendorMessage->updateSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource settings.
     */
    public function editInvoiceSettings(Vendor $vendor): Renderable
    {
        if ($vendor->id !== auth()->user()->vendor?->id) {
            return abort(403);
        }

        $this->registerBreadcrumb(
            routeParameter: $vendor->id,
        );

        $this->sharePageData([
            'title' => 'Invoice Settings',
        ]);

        return view($this->vendorRoutePath::INVOICE_SETTING_EDIT, [
            'vendor' => $vendor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateInvoiceSettings(Vendor $vendor, VendorSettingInvoiceUpdateRequest $request): RedirectResponse|RedirectResponseException
    {
        $updated = $this->vendorService->updateVendorSettings($vendor->id, $request->getAttributes());

        throw_if(!$updated, RedirectResponseException::class, $this->vendorMessage->updateFailed());

        return redirect()->route($this->vendorRoutePath::INVOICE_SETTING_EDIT, $vendor)->with([
            'message' => $this->vendorMessage->updateSuccess(),
            'status' => true,
        ]);
    }
}
