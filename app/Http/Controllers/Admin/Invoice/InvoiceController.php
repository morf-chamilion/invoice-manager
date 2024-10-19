<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Enums\InvoiceStatus;
use App\Exceptions\RedirectResponseException;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Invoice\InvoiceCustomerStoreRequest;
use App\Http\Requests\Admin\Invoice\InvoiceStoreRequest;
use App\Http\Requests\Admin\Invoice\InvoiceUpdateRequest;
use App\Http\Resources\Admin\Invoice\InvoiceIndexResource;
use App\Messages\InvoiceMessage;
use App\Models\Invoice;
use App\RoutePaths\Admin\Invoice\InvoiceRoutePath;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class InvoiceController extends AdminBaseController
{
	public function __construct(
		private InvoiceRoutePath $invoiceRoutePath,
		private InvoiceService $invoiceService,
		private InvoiceMessage $invoiceMessage,
	) {
		parent::__construct(service: $invoiceService);
	}

	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request): InvoiceIndexResource|Renderable
	{
		if (!auth()->user()->vendor?->id) {
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
					'company'
				]
			);

			$request->merge([
				'recordsAll' => $this->invoiceService->getAllInvoices(),
				'recordsFiltered' => $this->invoiceService->getAllWithCustomFilter(
					filterColumns: ['id', 'number'],
					filterQuery: $attributes,
				),
			]);

			return InvoiceIndexResource::make($attributes);
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

		return view($this->invoiceRoutePath::INDEX, [
			'customers' => $this->invoiceService->getAllCustomers(),
			'create' => route($this->invoiceRoutePath::CREATE),
			'columnNames' => $columns,
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): Renderable
	{
		if (!auth()->user()->vendor?->id) {
			return abort(403);
		}

		$this->registerBreadcrumb(
			parentRouteName: $this->invoiceRoutePath::INDEX,
		);

		$this->sharePageData([
			'title' => $this->getActionTitle(),
		]);

		return view($this->invoiceRoutePath::CREATE, [
			'customers' => $this->invoiceService->getAllCustomers(),
		]);
	}

	/**
	 * Show the resource.
	 */
	public function show(Invoice $invoice): Renderable
	{
		if ($invoice->vendor->id !== auth()->user()->vendor?->id) {
			return abort(403);
		}

		$this->registerBreadcrumb(
			parentRouteName: $this->invoiceRoutePath::INDEX,
			routeParameter: $invoice->id,
		);

		$this->sharePageData([
			'title' => $this->getActionTitle(),
			'editPage' => $invoice->status != InvoiceStatus::COMPLETED ? [
				'url' => route($this->invoiceRoutePath::EDIT, $invoice->id),
				'title' => 'Edit Invoice',
			] : [],
		]);

		return view($this->invoiceRoutePath::SHOW, [
			'invoice' => $invoice,
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(InvoiceStoreRequest $request): RedirectResponse|RedirectResponseException
	{
		$attributes = $request->getAttributes();

		$created = $this->invoiceService->createInvoice($attributes);

		throw_if(!$created, RedirectResponseException::class, $this->invoiceMessage->createFailed());

		return redirect()->route($this->invoiceRoutePath::EDIT, $created)->with([
			'message' => $this->invoiceMessage->createSuccess(),
			'status' => true,
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Invoice $invoice): Renderable|RedirectResponse
	{
		if ($invoice->vendor->id !== auth()->user()->vendor?->id) {
			return abort(403);
		}

		$this->registerBreadcrumb(
			parentRouteName: $this->invoiceRoutePath::INDEX,
			routeParameter: $invoice->id,
		);

		$this->sharePageData([
			'title' => $this->getActionTitle(),
			'showPage' => [
				'url' => route($this->invoiceRoutePath::SHOW, $invoice->id),
				'title' => 'Show Invoice',
			],
		]);

		if ($invoice->status === InvoiceStatus::COMPLETED) {
			return redirect()->route($this->invoiceRoutePath::SHOW, $invoice);
		}

		return view($this->invoiceRoutePath::EDIT, [
			'customers' => $this->invoiceService->getAllCustomers(),
			'invoice' => $invoice,
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Invoice $invoice, InvoiceUpdateRequest $request): JsonResponse|RedirectResponse|RedirectResponseException
	{
		$updated = $this->invoiceService->updateInvoice($invoice->id, $request->getAttributes());

		throw_if(!$updated, RedirectResponseException::class, $this->invoiceMessage->updateFailed());

		return redirect()->route($this->invoiceRoutePath::EDIT, $invoice)->with([
			'message' => $this->invoiceMessage->updateSuccess(),
			'status' => true,
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Invoice $invoice): JsonResponse|RedirectResponseException
	{
		$deleted = $this->invoiceService->deleteInvoice($invoice->id);

		throw_if(!$deleted, RedirectResponseException::class, $this->invoiceMessage->deleteFailed());

		return $this->jsonResponse()->message($this->invoiceMessage->deleteSuccess())
			->success();
	}

	/**
	 * Download the specified resource view.
	 */
	public function download(Invoice $invoice): Response
	{
		$fileName = $this->invoiceService->invoiceFileName($invoice);
		$view = $this->invoiceService->invoicePDF($invoice);

		return $view->download("{$fileName}.pdf");
	}

	/**
	 * Send invoice overdue notification.
	 */
	public function overdue(Invoice $invoice): JsonResponse
	{
		$notification = $this->invoiceService->invoiceOverdueMailNotification($invoice);

		if (!$notification) {
			return $this->jsonResponse()
				->message($this->invoiceMessage->overdueMailFailed())
				->error();
		}

		return $this->jsonResponse()
			->message($this->invoiceMessage->overdueMailSuccess())
			->success();
	}

	/**
	 * Get all customers.
	 */
	public function customerIndex(): JsonResponse
	{
		$customers = $this->invoiceService->getAllCustomers();

		if (!$customers) {
			return $this->jsonResponse()
				->message($this->invoiceMessage->getAllCustomersFailed())
				->error();
		}

		return $this->jsonResponse()
			->message($this->invoiceMessage->getAllCustomersSuccess())
			->body($customers->toArray())
			->success();
	}

	/**
	 * Store customer in storage.
	 */
	public function customerStore(InvoiceCustomerStoreRequest $request): JsonResponse
	{
		$attributes = $request->getAttributes();

		$created = $this->invoiceService->storeCustomer($attributes);

		if (!$created) {
			return $this->jsonResponse()
				->message($this->invoiceMessage->createCustomerFailed())
				->error();
		}

		return $this->jsonResponse()
			->message($this->invoiceMessage->createCustomerSuccess())
			->body($created->toArray())
			->success();
	}
}
