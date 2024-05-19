<x-front-customer-layout>
    <div class="card">
        <div class="card-body">

            @if ($invoices->isNotEmpty())
                <div class="d-md-flex flex-stack mb-5">
                    <div class="d-flex align-items-center position-relative my-1">
                        {!! getIcon('magnifier', 'ki-duotone fs-1 position-absolute ms-6') !!}
                        <input type="text" data-kt-docs-table-filter="search"
                            class="form-control form-control-solid w-250px ps-15" placeholder="Search" />
                    </div>
                </div>

                <table id="customer_invoice_index"
                    class="table table-bordered align-middle table-row-dashed dataTable fs-6 gy-2">
                    <thead>
                        <tr class="fw-semibold fs-6 text-muted">
                            <th class="id">Number</th>
                            <th class="due-date">Due Date</th>
                            <th class="price">Price</th>
                            <th class="status">Payment Status</th>
                            <th class="actions">Actions</th>

                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route(FrontCustomerRoutePath::INVOICE_SHOW, $invoice) }}">
                                        {{ $invoice->number }}
                                    </a>
                                </td>
                                <td>
                                    {{ $invoice->readableDueDate }}
                                </td>
                                <td>
                                    {{ $invoice->readableTotalPrice }}
                                </td>
                                <td>
                                    {!! InvoicePaymentStatus::toBadge($invoice->payment_status) !!}
                                </td>
                                <td>
                                    <a href="{{ route(FrontCustomerRoutePath::INVOICE_SHOW, $invoice) }}"
                                        class="btn btn-sm btn-primary me-2">
                                        <i class="fas fa-eye"></i> {{ __('View') }}
                                    </a>
                                    <a href="{{ route(FrontCustomerRoutePath::INVOICE_DOWNLOAD, $invoice) }}"
                                        target="_blank" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-download"></i> {{ __('Download') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            @else
                <div class="alert alert-info">
                    {{ __('No Invoices Found') }}
                </div>
            @endif
        </div>
    </div>
</x-front-customer-layout>
