<x-default-layout :model="$recurringInvoice">

    <div class="d-flex flex-column flex-lg-row">
        <div class="flex-lg-row-fluid">
            <div class="card">
                <div class="card-header align-items-center">
                    <header>
                        <h2 class="m-0 text-lg font-medium text-gray-900">
                            {{ __($pageData['title']) }}
                        </h2>
                    </header>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-5">
                                <div class="text-gray-500 mb-1">{{ __('Customer') }}</div>
                                <div class="fw-bold">{{ $recurringInvoice->customer->name }}</div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="mb-5">
                                <div class="text-gray-500 mb-1">{{ __('Start Date') }}</div>
                                <div class="fw-bold">{{ $recurringInvoice->readableStartDate }}</div>
                            </div>
                        </div>
                        @if ($recurringInvoice->end_date)
                            <div class="col-lg-2 col-md-6">
                                <div class="mb-5">
                                    <div class="text-gray-500 mb-1">{{ __('End Date') }}</div>
                                    <div class="fw-bold">
                                        {{ \Carbon\Carbon::parse($recurringInvoice->end_date)->format('d M Y') }}</div>
                                </div>
                            </div>
                        @endif
                        @if ($recurringInvoice->end_count)
                            <div class="col-lg-2 col-md-6">
                                <div class="mb-5">
                                    <div class="text-gray-500 mb-1">{{ __('End Count') }}</div>
                                    <div class="fw-bold">{{ $recurringInvoice->end_count }} {{ __('invoices') }}</div>
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-2 col-md-6">
                            <div class="mb-5">
                                <div class="text-gray-500 mb-1">{{ __('Frequency') }}</div>
                                <div class="fw-bold">
                                    {{ RecurringInvoiceFrequency::from($recurringInvoice->frequency)->getName() }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="mb-5">
                                <div class="text-gray-500 mb-1">{{ __('Status') }}</div>
                                <div>{!! RecurringInvoiceStatus::toBadge($recurringInvoice->status) !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-8">
                <div class="card-header align-items-center">
                    <header>
                        <h2 class="m-0 text-lg font-medium text-gray-900">
                            {{ __('Generated Invoices') }}
                        </h2>
                    </header>
                </div>
                <div class="card-body">
                    @if ($recurringInvoice->invoices->isNotEmpty())
                        <div class="table-wrapper">
                            <table
                                style="width: 100%; border-collapse: collapse; margin: 0 auto 6px; font-family: sans-serif">
                                <thead>
                                    <tr>
                                        <th align="left"
                                            style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3;">
                                            <p
                                                style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555250;">
                                                {{ __('Invoice Number') }}
                                            </p>
                                        </th>
                                        <th align="left"
                                            style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3;">
                                            <p
                                                style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555250;">
                                                {{ __('Date') }}
                                            </p>
                                        </th>
                                        <th align="left"
                                            style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3;">
                                            <p
                                                style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555250;">
                                                {{ __('Due Date') }}
                                            </p>
                                        </th>
                                        <th align="right"
                                            style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3; color: #555250; text-align: end;">
                                            <p
                                                style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase;">
                                                {{ __('Amount (:currency)', ['currency' => $recurringInvoice->vendor->currency]) }}
                                            </p>
                                        </th>
                                        <th
                                            style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3; text-align: center;">
                                            <p
                                                style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555250;">
                                                {{ __('Payment Status') }}
                                            </p>
                                        </th>
                                        <th
                                            style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3; text-align: center;">
                                            <p
                                                style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555250;">
                                                {{ __('Status') }}
                                            </p>
                                        </th>
                                        <th align="right"
                                            style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3; color: #555250; text-align: end;">
                                            <p
                                                style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase;">
                                                {{ __('Actions') }}
                                            </p>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recurringInvoice->invoices as $invoice)
                                        <tr>
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                                <p style="margin: 3px 0 0; font-size: 12px;">
                                                    {{ $invoice->number }}
                                                </p>
                                            </td>
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                                <p style="margin: 3px 0 0; font-size: 12px;">
                                                    {{ $invoice->readableDate }}
                                                </p>
                                            </td>
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                                <p style="margin: 3px 0 0; font-size: 12px;">
                                                    {{ $invoice->readableDueDate }}
                                                </p>
                                            </td>
                                            <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                                                <span
                                                    style="font-size: 12px;">{{ $invoice->readableTotalPrice }}</span>
                                            </td>
                                            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                                <span style="font-size: 12px;">
                                                    {!! InvoicePaymentStatus::toBadge($invoice->payment_status) !!}
                                                </span>
                                            </td>
                                            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                                <span style="font-size: 12px;">
                                                    {!! InvoiceStatus::toBadge($invoice->status) !!}
                                                </span>
                                            </td>
                                            <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                                                <span
                                                    style="font-size: 12px; display: flex; gap: 6px; justify-content: flex-end;">
                                                    <a href="{{ route(InvoiceRoutePath::SHOW, $invoice) }}"
                                                        class="btn btn-sm btn-icon btn-light-dark"
                                                        title="{{ __('View') }}">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p class="mb-0">{{ __('No invoices have been generated yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-8">
                <div class="card-header align-items-center">
                    <header>
                        <h2 class="m-0 text-lg font-medium text-gray-900">
                            {{ __('Scheduled Invoices') }}
                        </h2>
                    </header>
                </div>
                <div class="card-body">
                    @if ($scheduledInvoices->isNotEmpty())
                        <div class="table-wrapper">
                            <table
                                style="width: 100%; border-collapse: collapse; margin: 0 auto 6px; font-family: sans-serif">
                                <thead>
                                    <tr>
                                        <th align="left"
                                            style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3;">
                                            <p
                                                style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555250;">
                                                {{ __('Expected Date') }}
                                            </p>
                                        </th>
                                        <th align="left"
                                            style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3;">
                                            <p
                                                style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555250;">
                                                {{ __('Due Date') }}
                                            </p>
                                        </th>
                                        <th align="right"
                                            style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3; color: #555250; text-align: end;">
                                            <p
                                                style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase;">
                                                {{ __('Amount (:currency)', ['currency' => $recurringInvoice->vendor->currency]) }}
                                            </p>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($scheduledInvoices as $scheduledInvoice)
                                        <tr>
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                                <p style="margin: 3px 0 0; font-size: 12px;">
                                                    {{ $scheduledInvoice->date->format('d M Y') }}
                                                </p>
                                            </td>
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                                <p style="margin: 3px 0 0; font-size: 12px;">
                                                    {{ $scheduledInvoice->due_date->format('d M Y') }}
                                                </p>
                                            </td>
                                            <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                                                <span style="font-size: 12px;">
                                                    {{ $recurringInvoice->vendor->currency }}
                                                    {{ number_format($scheduledInvoice->amount, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p class="mb-0">{{ __('No invoices are scheduled to be generated.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-default-layout>
