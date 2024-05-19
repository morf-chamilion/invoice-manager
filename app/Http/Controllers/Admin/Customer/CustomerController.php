<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Exceptions\RedirectResponseException;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Customer\CustomerStoreRequest;
use App\Http\Requests\Admin\Customer\CustomerUpdateRequest;
use App\Http\Resources\Admin\Customer\CustomerIndexResource;
use App\Messages\CustomerMessage;
use App\Models\Customer;
use App\RoutePaths\Admin\Customer\CustomerRoutePath;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CustomerController extends AdminBaseController
{
    public function __construct(
        private CustomerRoutePath $customerRoutePath,
        private CustomerService $customerService,
        private CustomerMessage $customerMessage,
    ) {
        parent::__construct(service: $customerService);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): CustomerIndexResource|Renderable
    {
        if ($request->ajax()) {
            $attributes = (object) $request->only(
                ['draw', 'columns', 'order', 'start', 'length', 'search']
            );

            $request->merge([
                'recordsAll' => $this->customerService->getAllCustomers(),
                'recordsFiltered' => $this->customerService->getAllWithFilter(
                    filterColumns: ['id', 'name', 'email', 'phone', 'status'],
                    filterQuery: $attributes,
                ),
            ]);

            return CustomerIndexResource::make($attributes);
        }

        $columns = $this->tableColumns(
            ['name', 'email', 'phone',]
        );

        $this->registerBreadcrumb();

        $this->sharePageData([
            'title' => $this->getActionTitle(),
            'createTitle' => $this->getActionTitle('create'),
        ]);

        return view($this->customerRoutePath::INDEX, [
            'create' => route($this->customerRoutePath::CREATE),
            'columnNames' => $columns,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Renderable
    {
        $this->registerBreadcrumb(
            parentRouteName: $this->customerRoutePath::INDEX,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->customerRoutePath::CREATE);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerStoreRequest $request): RedirectResponse|RedirectResponseException
    {
        $created = $this->customerService->createCustomer($request->getAttributes());

        throw_if(!$created, RedirectResponseException::class, $this->customerMessage->createFailed());

        return redirect()->route($this->customerRoutePath::EDIT, $created)->with([
            'message' => $this->customerMessage->createSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): Renderable
    {
        $this->registerBreadcrumb(
            parentRouteName: $this->customerRoutePath::INDEX,
            routeParameter: $customer->id,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->customerRoutePath::EDIT, [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Customer $customer, CustomerUpdateRequest $request): RedirectResponse|RedirectResponseException
    {
        $updated = $this->customerService->updateCustomer($customer->id, $request->getAttributes());

        throw_if(!$updated, RedirectResponseException::class, $this->customerMessage->updateFailed());

        return redirect()->route($this->customerRoutePath::EDIT, $customer)->with([
            'message' => $this->customerMessage->updateSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): JsonResponse|RedirectResponseException
    {
        $deleted = $this->customerService->deleteCustomer($customer->id);

        throw_if(!$deleted, RedirectResponseException::class, $this->customerMessage->deleteFailed());

        return $this->jsonResponse()->message($this->customerMessage->deleteSuccess())
            ->success();
    }
}
