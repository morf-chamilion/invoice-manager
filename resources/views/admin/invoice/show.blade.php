<x-default-layout :model="$invoice">

    <form method="POST" action="{{ route(InvoiceRoutePath::UPDATE, $invoice) }}" autocomplete="off" id="resource_form">
        @method('put')
        @csrf

        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10" id="resource_form_fieldset">

                <div class="card">
                    <div class="card-body print-section">
                        @include('pdf.partials.invoice-content')
                    </div>
                </div>

                @if ($invoice->payments->isNotEmpty())
                    <div class="card mt-8 payment-card">
                        <div class="card-body">
                            <div class="table-wrapper">
                                <h5 class="mb-4">Payments</h5>
                                <table
                                    style="width: 100%; border-collapse: collapse; margin: 0 auto 6px; font-family: sans-serif">
                                    <thead>
                                        <tr>
                                            <th align="left"
                                                style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3;">
                                                <p
                                                    style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555250;">
                                                    {{ __('Reference') }}
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
                                                    {{ __('Method') }}
                                                </p>
                                            </th>
                                            <th align="right"
                                                style="border: 1px solid #ddd; padding: 8px; background-color: #f0f1f3; color: #555250; text-align: end;">
                                                <p
                                                    style="margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase;">
                                                    {{ __('Amount (:currency)', ['currency' => $invoice->vendor->currency]) }}
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
                                        @foreach ($invoice->payments as $payment)
                                            <tr>
                                                <td style="border: 1px solid #ddd; padding: 8px;">
                                                    <p style="margin: 3px 0 0; font-size: 12px;">
                                                        {{ $payment->number }}
                                                    </p>
                                                </td>
                                                <td style="border: 1px solid #ddd; padding: 8px;">
                                                    <p style="margin: 3px 0 0; font-size: 12px;">
                                                        {{ $payment->readableDate }}
                                                    </p>
                                                </td>
                                                <td style="border: 1px solid #ddd; padding: 8px;">
                                                    <span
                                                        style="font-size: 12px;">{{ $payment->method->getName() }}</span>
                                                </td>
                                                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                                                    <span style="font-size: 12px;">{{ $payment->readableAmount }}</span>
                                                </td>
                                                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                                                    <span
                                                        style="font-size: 12px; display: flex; gap: 6px; justify-content: flex-end;">
                                                        <button type="button"
                                                            class="btn btn-sm btn-icon btn-secondary view-payment"
                                                            data-bs-toggle="modal" data-bs-target="#payment_show"
                                                            data-payment-number="{{ $payment->number }}"
                                                            data-payment-date="{{ $payment->readableDate }}"
                                                            data-payment-amount="{{ $payment->readableAmount }}"
                                                            data-payment-notes="{{ $payment->notes }}"
                                                            data-payment-method="{{ PaymentMethod::toBadge($payment->method) }}"
                                                            data-payment-status="{{ PaymentStatus::toBadge($payment->status) }}"
                                                            data-payment-image="{{ $payment->getFirstMedia('reference_receipt')?->getFullUrl() }}">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($invoice->vendor?->invoice_terms_of_service)
                    <div style="page-break-before: always;"></div>

                    <div class="card mt-8">
                        <div class="card-body print-section">
                            <table style="font-family: sans-serif; margin-bottom: 30px;">
                                <tbody>
                                    <tr>
                                        <td>
                                            <p style="margin-top: 6px;">{!! $invoice->vendor?->invoice_terms_of_service !!}</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <x-form-metadata :model="$invoice">
                <h4 class="form-label">{{ __('Invoice Tools') }}</h4>

                <div class="mb-4">
                    <button type="button" class="btn btn-icon btn-primary w-100" id="invoice_download"
                        data-url="{{ route(InvoiceRoutePath::DOWNLOAD, $invoice) }}">
                        <i class="fas fa-file-download"></i>
                        <span class="ms-2">{{ __('PDF Download') }}</span>
                    </button>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-icon btn-secondary w-100" onclick="invoicePrint()">
                        <i class="fas fa-print"></i>
                        <span class="ms-2">{{ __('Print Document') }}</span>
                    </button>
                </div>

                <div class="mb-4">
                    <h4 class="form-label">{{ __('Invoice Preview') }}</h4>
                    <div class="d-flex gap-4">
                        <a href="{{ $invoice->show_link }}" target="_blank" class="btn btn-icon btn-secondary w-100">
                            <i class="fa-solid fa-eye"></i>
                            <span class="ms-2">{{ __('View') }}</span>
                        </a>
                        <div class="w-100">
                            <input type="hidden" id="payment_link" name="payment_link"
                                value="{{ old('payment_link', $invoice->show_link) }}" disabled />
                            <button class="btn btn-secondary w-100" id="payment_link_btn" type="button">
                                {!! getIcon('copy', 'text-dark') !!} {{ __('Link') }}
                            </button>
                        </div>
                    </div>
                </div>
            </x-form-metadata>
        </div>
    </form>

    @include('admin.invoice.partials.payment-view')

    @push('header')
        <style>
            @media print {

                .app-header,
                .app-toolbar,
                .app-footer,
                .payment-card,
                #form_metadata_container {
                    display: none;
                }

                .print-section {
                    display: block !important;
                }
            }
        </style>

        <script>
            function invoicePrint() {
                document.body.classList.add('print-content-only');
                window.print();
            }
        </script>
    @endpush

    @push('footer')
        @env('local')
        <script>
            {!! file_get_contents(resource_path('js/admin/invoice.js')) !!}
        </script>
        @endenv

        @env('production')
        <script src="{{ asset('assets/js/admin/invoice.js') }}"></script>
        @endenv
    @endpush
</x-default-layout>
