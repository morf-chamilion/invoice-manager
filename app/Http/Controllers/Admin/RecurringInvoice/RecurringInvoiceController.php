<?php

namespace App\Http\Controllers\Admin\RecurringInvoice;

use App\Enums\InvoiceStatus;
use App\Exceptions\RedirectResponseException;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\RecurringInvoice\RecurringInvoiceCustomerStoreRequest;
use App\Http\Requests\Admin\RecurringInvoice\RecurringInvoiceStoreRequest;
use App\Http\Requests\Admin\RecurringInvoice\RecurringInvoiceUpdateRequest;
use App\Http\Resources\Admin\RecurringInvoice\RecurringInvoiceIndexResource;
use App\Messages\RecurringInvoiceMessage;
use App\Models\RecurringInvoice;
use App\RoutePaths\Admin\RecurringInvoice\RecurringInvoiceRoutePath;
use App\Services\RecurringInvoiceService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecurringInvoiceController extends AdminBaseController
{
    public function __construct(
        private RecurringInvoiceRoutePath $recurringInvoiceRoutePath,
        private RecurringInvoiceService $recurringInvoiceService,
        private RecurringInvoiceMessage $recurringInvoiceMessage,
    ) {
        parent::__construct(service: $recurringInvoiceService);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): RecurringInvoiceIndexResource|Renderable
    {
        if (! Auth::user()?->vendor?->id) {
            return abort(403);
        }

        if ($request->ajax()) {
            $attributes = (object) $request->only(
                [
                    'draw',
                    'columns',
                    'order',
                    'start',
                    'length',
                    'search',
                    'date_start',
                    'date_end',
                    'status',
                    'payment_status',
                    'number',
                    'customer',
                    'company',
                ]
            );

            $request->merge([
                'recordsAll' => $this->recurringInvoiceService->getAllInvoices(),
                'recordsFiltered' => $this->recurringInvoiceService->getAllWithFilter(
                    filterColumns: ['id', 'number'],
                    filterQuery: $attributes,
                ),
            ]);

            return RecurringInvoiceIndexResource::make($attributes);
        }

        $columns = $this->tableColumns(
            prefixes: [],
            columns: ['number', 'customer', 'date', 'due_date', 'total_price', 'payment_status']
        );

        $this->registerBreadcrumb();

        $this->sharePageData([
            'title' => $this->getActionTitle(),
            'createTitle' => $this->getActionTitle('create'),
        ]);

        return view($this->recurringInvoiceRoutePath::INDEX, [
            'customers' => $this->recurringInvoiceService->getAllCustomers(),
            'create' => route($this->recurringInvoiceRoutePath::CREATE),
            'columnNames' => $columns,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Renderable
    {
        if (! Auth::user()?->vendor?->id) {
            return abort(403);
        }

        $this->registerBreadcrumb(
            parentRouteName: $this->recurringInvoiceRoutePath::INDEX,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->recurringInvoiceRoutePath::CREATE, [
            'customers' => $this->recurringInvoiceService->getAllCustomers(),
        ]);
    }

    /**
     * Show the resource.
     */
    public function show(RecurringInvoice $recurringInvoice): Renderable
    {
        if ($recurringInvoice->vendor->id !== Auth::user()?->vendor?->id) {
            return abort(403);
        }

        $this->registerBreadcrumb(
            parentRouteName: $this->recurringInvoiceRoutePath::INDEX,
            routeParameter: $recurringInvoice->id,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
            'editPage' => $recurringInvoice->status != InvoiceStatus::COMPLETED ? [
                'url' => route($this->recurringInvoiceRoutePath::EDIT, $recurringInvoice->id),
                'title' => 'Edit Recurring Invoice',
            ] : [],
        ]);

        return view($this->recurringInvoiceRoutePath::SHOW, [
            'recurringInvoice' => $recurringInvoice,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RecurringInvoiceStoreRequest $request): RedirectResponse|RedirectResponseException
    {
        $attributes = $request->getAttributes();

        $created = $this->recurringInvoiceService->createRecurringInvoice($attributes);

        throw_if(! $created, RedirectResponseException::class, $this->recurringInvoiceMessage->createFailed());

        return redirect()->route($this->recurringInvoiceRoutePath::EDIT, $created)->with([
            'message' => $this->recurringInvoiceMessage->createSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RecurringInvoice $recurringInvoice): Renderable|RedirectResponse
    {
        if ($recurringInvoice->vendor->id !== Auth::user()?->vendor?->id) {
            return abort(403);
        }

        $this->registerBreadcrumb(
            parentRouteName: $this->recurringInvoiceRoutePath::INDEX,
            routeParameter: $recurringInvoice->id,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
            'showPage' => [
                'url' => route($this->recurringInvoiceRoutePath::SHOW, $recurringInvoice->id),
                'title' => 'Show Invoice',
            ],
        ]);

        if ($recurringInvoice->status === InvoiceStatus::COMPLETED) {
            return redirect()->route($this->recurringInvoiceRoutePath::SHOW, $recurringInvoice);
        }

        return view($this->recurringInvoiceRoutePath::EDIT, [
            'customers' => $this->recurringInvoiceService->getAllCustomers(),
            'recurringInvoice' => $recurringInvoice,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RecurringInvoice $recurringInvoice, RecurringInvoiceUpdateRequest $request): JsonResponse|RedirectResponse|RedirectResponseException
    {
        $updated = $this->recurringInvoiceService->updateRecurringInvoice($recurringInvoice->id, $request->getAttributes());

        throw_if(! $updated, RedirectResponseException::class, $this->recurringInvoiceMessage->updateFailed());

        return redirect()->route($this->recurringInvoiceRoutePath::EDIT, $recurringInvoice)->with([
            'message' => $this->recurringInvoiceMessage->updateSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RecurringInvoice $recurringInvoice): JsonResponse|RedirectResponseException
    {
        $deleted = $this->recurringInvoiceService->deleteRecurringInvoice($recurringInvoice->id);

        throw_if(! $deleted, RedirectResponseException::class, $this->recurringInvoiceMessage->deleteFailed());

        return $this->jsonResponse()->message($this->recurringInvoiceMessage->deleteSuccess())
            ->success();
    }

    /**
     * Get all customers.
     */
    public function customerIndex(): JsonResponse
    {
        $customers = $this->recurringInvoiceService->getAllCustomers();

        if (! $customers) {
            return $this->jsonResponse()
                ->message($this->recurringInvoiceMessage->getAllCustomersFailed())
                ->error();
        }

        return $this->jsonResponse()
            ->message($this->recurringInvoiceMessage->getAllCustomersSuccess())
            ->body($customers->toArray())
            ->success();
    }

    /**
     * Store customer in storage.
     */
    public function customerStore(RecurringInvoiceCustomerStoreRequest $request): JsonResponse
    {
        $attributes = $request->getAttributes();

        $created = $this->recurringInvoiceService->storeCustomer($attributes);

        if (! $created) {
            return $this->jsonResponse()
                ->message($this->recurringInvoiceMessage->createCustomerFailed())
                ->error();
        }

        return $this->jsonResponse()
            ->message($this->recurringInvoiceMessage->createCustomerSuccess())
            ->body($created->toArray())
            ->success();
    }
}
