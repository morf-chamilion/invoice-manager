<?php

namespace App\Http\Controllers\Admin\Vendor;

use App\Exceptions\RedirectResponseException;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Vendor\VendorSettingUpdateRequest;
use App\Messages\VendorMessage;
use App\Models\Vendor;
use App\RoutePaths\Admin\Vendor\VendorRoutePath;
use Illuminate\Contracts\Support\Renderable;
use App\Services\VendorService;
use Illuminate\Http\RedirectResponse;

class VendorInvoiceSettingController extends AdminBaseController
{
    public function __construct(
        private VendorRoutePath $vendorRoutePath,
        private VendorService $vendorService,
        private VendorMessage $vendorMessage,
    ) {
        parent::__construct(service: $vendorService);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor): Renderable
    {
        $this->registerBreadcrumb(
            routeParameter: $vendor->id,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->vendorRoutePath::INVOICE_SETTING_EDIT, [
            'vendor' => $vendor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Vendor $vendor, VendorSettingUpdateRequest $request): RedirectResponse|RedirectResponseException
    {
        $updated = $this->vendorService->updateVendor($vendor->id, $request->getAttributes());

        throw_if(!$updated, RedirectResponseException::class, $this->vendorMessage->updateFailed());

        return redirect()->route($this->vendorRoutePath::INVOICE_SETTING_EDIT, $vendor)->with([
            'message' => $this->vendorMessage->updateSuccess(),
            'status' => true,
        ]);
    }
}
