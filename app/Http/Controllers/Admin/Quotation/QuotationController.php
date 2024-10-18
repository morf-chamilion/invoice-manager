<?php

namespace App\Http\Controllers\Admin\Quotation;

use App\Enums\QuotationStatus;
use App\Exceptions\RedirectResponseException;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Quotation\QuotationCustomerStoreRequest;
use App\Http\Requests\Admin\Quotation\QuotationStoreRequest;
use App\Http\Requests\Admin\Quotation\QuotationUpdateRequest;
use App\Http\Resources\Admin\Quotation\QuotationIndexResource;
use App\Messages\QuotationMessage;
use App\Models\Quotation;
use App\RoutePaths\Admin\Invoice\InvoiceRoutePath;
use App\RoutePaths\Admin\Quotation\QuotationRoutePath;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Services\QuotationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class QuotationController extends AdminBaseController
{
	public function __construct(
		private QuotationRoutePath $quotationRoutePath,
		private QuotationService $quotationService,
		private QuotationMessage $quotationMessage,
	) {
		parent::__construct(service: $quotationService);
	}

	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request): QuotationIndexResource|Renderable
	{
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
				'recordsAll' => $this->quotationService->getAllQuotations(),
				'recordsFiltered' => $this->quotationService->getAllWithCustomFilter(
					filterColumns: ['id', 'number'],
					filterQuery: $attributes,
				),
			]);

			return QuotationIndexResource::make($attributes);
		}

		$columns = $this->tableColumns(
			prefixes: [],
			columns: ['number', 'customer', 'date', 'total_price']
		);

		$this->registerBreadcrumb();

		$this->sharePageData([
			'title' => $this->getActionTitle(),
			'createTitle' => $this->getActionTitle('create'),
		]);

		return view($this->quotationRoutePath::INDEX, [
			'customers' => $this->quotationService->getAllCustomers(),
			'create' => route($this->quotationRoutePath::CREATE),
			'columnNames' => $columns,
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): Renderable
	{
		$this->registerBreadcrumb(
			parentRouteName: $this->quotationRoutePath::INDEX,
		);

		$this->sharePageData([
			'title' => $this->getActionTitle(),
		]);

		return view($this->quotationRoutePath::CREATE, [
			'customers' => $this->quotationService->getAllCustomers(),
		]);
	}

	/**
	 * Show the resource.
	 */
	public function show(Quotation $quotation): Renderable
	{
		$this->registerBreadcrumb(
			parentRouteName: $this->quotationRoutePath::INDEX,
			routeParameter: $quotation->id,
		);

		$this->sharePageData([
			'title' => $this->getActionTitle(),
			'editPage' => $quotation->status != QuotationStatus::COMPLETED ? [
				'url' => route($this->quotationRoutePath::EDIT, $quotation->id),
				'title' => 'Edit Quotation',
			] : [],
		]);

		return view($this->quotationRoutePath::SHOW, [
			'quotation' => $quotation,
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(QuotationStoreRequest $request): RedirectResponse|RedirectResponseException
	{
		$attributes = $request->getAttributes();

		$created = $this->quotationService->createQuotation($attributes);

		throw_if(!$created, RedirectResponseException::class, $this->quotationMessage->createFailed());

		return redirect()->route($this->quotationRoutePath::EDIT, $created)->with([
			'message' => $this->quotationMessage->createSuccess(),
			'status' => true,
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Quotation $quotation): Renderable|RedirectResponse
	{
		$this->registerBreadcrumb(
			parentRouteName: $this->quotationRoutePath::INDEX,
			routeParameter: $quotation->id,
		);

		$this->sharePageData([
			'title' => $this->getActionTitle(),
			'showPage' => [
				'url' => route($this->quotationRoutePath::SHOW, $quotation->id),
				'title' => 'Show Quotation',
			],
		]);

		if ($quotation->status === QuotationStatus::COMPLETED) {
			return redirect()->route($this->quotationRoutePath::SHOW, $quotation);
		}

		return view($this->quotationRoutePath::EDIT, [
			'customers' => $this->quotationService->getAllCustomers(),
			'quotation' => $quotation,
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Quotation $quotation, QuotationUpdateRequest $request): JsonResponse|RedirectResponse|RedirectResponseException
	{
		$updated = $this->quotationService->updateQuotation($quotation->id, $request->getAttributes());

		throw_if(!$updated, RedirectResponseException::class, $this->quotationMessage->updateFailed());

		return redirect()->route($this->quotationRoutePath::EDIT, $quotation)->with([
			'message' => $this->quotationMessage->updateSuccess(),
			'status' => true,
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Quotation $quotation): JsonResponse|RedirectResponseException
	{
		$deleted = $this->quotationService->deleteQuotation($quotation->id);

		throw_if(!$deleted, RedirectResponseException::class, $this->quotationMessage->deleteFailed());

		return $this->jsonResponse()->message($this->quotationMessage->deleteSuccess())
			->success();
	}

	/**
	 * Download the specified resource view.
	 */
	public function download(Quotation $quotation): Response
	{
		$fileName = $this->quotationService->quotationFileName($quotation);
		$view = $this->quotationService->quotationPDF($quotation);

		return $view->download("{$fileName}.pdf");
	}

	/**
	 * Get all customers.
	 */
	public function customerIndex(): JsonResponse
	{
		$customers = $this->quotationService->getAllCustomers();

		if (!$customers) {
			return $this->jsonResponse()
				->message($this->quotationMessage->getAllCustomersFailed())
				->error();
		}

		return $this->jsonResponse()
			->message($this->quotationMessage->getAllCustomersSuccess())
			->body($customers->toArray())
			->success();
	}

	/**
	 * Store customer in storage.
	 */
	public function customerStore(QuotationCustomerStoreRequest $request): JsonResponse
	{
		$attributes = $request->getAttributes();

		$created = $this->quotationService->storeCustomer($attributes);

		if (!$created) {
			return $this->jsonResponse()
				->message($this->quotationMessage->createCustomerFailed())
				->error();
		}

		return $this->jsonResponse()
			->message($this->quotationMessage->createCustomerSuccess())
			->body($created->toArray())
			->success();
	}

	/**
	 * Generate an invoice from the specified resource.
	 */
	public function invoiceGenerate(Quotation $quotation): JsonResponse
	{
		$invoice = $this->quotationService->generateInvoice($quotation);

		if (!$invoice) {
			return $this->jsonResponse()
				->message($this->quotationMessage->generateInvoiceFailed())
				->error();
		}

		return $this->jsonResponse()
			->message($this->quotationMessage->generateInvoiceSuccess())
			->body([
				...$invoice->toArray(),
				'redirect_url' => route(InvoiceRoutePath::SHOW, $invoice->id)
			])
			->success();
	}
}
