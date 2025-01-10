<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Enums\PaymentStatus;
use App\Exceptions\RedirectResponseException;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Payment\PaymentStoreRequest;
use App\Http\Requests\Admin\Payment\PaymentUpdateRequest;
use App\Http\Resources\Admin\Payment\PaymentIndexResource;
use App\Messages\PaymentMessage;
use App\Models\Payment;
use App\RoutePaths\Admin\Payment\PaymentRoutePath;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PaymentController extends AdminBaseController
{
    public function __construct(
        private PaymentRoutePath $paymentRoutePath,
        private PaymentService $paymentService,
        private PaymentMessage $paymentMessage,
    ) {
        parent::__construct(service: $paymentService);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): PaymentIndexResource|Renderable
    {
        if ($request->ajax()) {
            $attributes = (object) $request->only(
                ['draw', 'columns', 'order', 'start', 'length', 'search']
            );

            $request->merge([
                'recordsAll' => $this->paymentService->getAllPayments(),
                'recordsFiltered' => $this->paymentService->getAllWithFilter(
                    filterColumns: ['id', 'status'],
                    filterQuery: $attributes,
                ),
            ]);

            return PaymentIndexResource::make($attributes);
        }

        $columns = $this->tableColumns(
            columns: ['number', 'customer', 'date', 'method', 'amount'],
            prefixes: [],
        );

        $this->registerBreadcrumb();

        $this->sharePageData([
            'title' => $this->getActionTitle(),
            'createTitle' => $this->getActionTitle('create'),
        ]);

        return view($this->paymentRoutePath::INDEX, [
            'create' => route($this->paymentRoutePath::CREATE),
            'columnNames' => $columns,
        ]);
    }


    /**
     * Show the resource.
     */
    public function show(Payment $payment): Renderable
    {
        if ($payment->vendor->id !== auth()->user()->vendor?->id) {
            return abort(403);
        }

        $this->registerBreadcrumb(
            parentRouteName: $this->paymentRoutePath::INDEX,
            routeParameter: $payment->id,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
            'editPage' => $payment->status != PaymentStatus::PAID ? [
                'url' => route($this->paymentRoutePath::EDIT, $payment->id),
                'title' => 'Edit Payment',
            ] : [],
        ]);

        return view($this->paymentRoutePath::SHOW, [
            'customers' => $this->paymentService->getAllCustomers(),
            'invoices' => $this->paymentService->getAllInvoices(),
            'payment' => $payment,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Renderable
    {
        $this->registerBreadcrumb(
            parentRouteName: $this->paymentRoutePath::INDEX,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->paymentRoutePath::CREATE, [
            'customers' => $this->paymentService->getAllCustomers(),
            'invoices' => $this->paymentService->getAllInvoices(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentStoreRequest $request): RedirectResponse|RedirectResponseException
    {
        $created = $this->paymentService->createPayment($request->getAttributes());

        throw_if(!$created, RedirectResponseException::class, $this->paymentMessage->createFailed());

        $this->registerBreadcrumb();

        return redirect()->route($this->paymentRoutePath::EDIT, $created)->with([
            'message' => $this->paymentMessage->createSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment): Renderable
    {
        $this->registerBreadcrumb(
            parentRouteName: $this->paymentRoutePath::INDEX,
            routeParameter: $payment->id,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->paymentRoutePath::EDIT, [
            'customers' => $this->paymentService->getAllCustomers(),
            'invoices' => $this->paymentService->getAllInvoices(),
            'payment' => $payment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Payment $payment, PaymentUpdateRequest $request): RedirectResponse|RedirectResponseException
    {
        $updated = $this->paymentService->updatePayment($payment->id, $request->getAttributes());

        throw_if(!$updated, RedirectResponseException::class, $this->paymentMessage->updateFailed());

        return redirect()->route($this->paymentRoutePath::EDIT, $payment)->with([
            'message' => $this->paymentMessage->updateSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment): JsonResponse|RedirectResponseException
    {
        $deleted = $this->paymentService->deletePayment($payment->id);

        throw_if(!$deleted, RedirectResponseException::class, $this->paymentMessage->deleteFailed());

        return $this->jsonResponse()->message($this->paymentMessage->deleteSuccess())
            ->success();
    }
}
