<x-default-layout :model="$invoice">

    <form method="POST" action="{{ route(InvoiceRoutePath::UPDATE, $invoice) }}" autocomplete="off" id="resource_form">
        @method('put')
        @csrf

        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10" id="resource_form_fieldset">

                @if ($invoice->payment_status !== InvoicePaymentStatus::PENDING)
                    <div class="mb-8 card payment-card">
                        <div class="card-header">
                            <header>
                                <h2 class="text-lg mt-8 font-medium text-gray-800">
                                    {{ __('Payment Receipt') }}
                                </h2>
                            </header>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @isset($invoice->payment_data['reference'])
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Payment Reference') }}
                                        </label>
                                        <p style="font-size: 12px;">{{ $invoice->payment_data['reference'] }}</p>
                                    </div>
                                @else
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Transaction ID') }}
                                        </label>
                                        <p style="font-size: 12px;">{{ $invoice->payment_data['transaction_id'] }}</p>
                                    </div>
                                @endisset
                                @if ($invoice->customer->name)
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Billed To') }}
                                        </label>
                                        <p>
                                            {{ $invoice->customer->name }}
                                        </p>
                                    </div>
                                @endif
                                @if ($invoice->payment_method)
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Payment Method') }}
                                        </label>
                                        <div>
                                            {!! InvoicePaymentMethod::toBadge($invoice->payment_method) !!}
                                        </div>
                                    </div>
                                @endif
                                @isset($invoice->payment_data['amount'])
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Amount') }}
                                        </label>
                                        <p>
                                            {{ MoneyHelper::print($invoice->payment_data['amount']) }}
                                        </p>
                                    </div>
                                @endisset
                                @if ($invoice->payment_date)
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Payment Date') }}
                                        </label>
                                        <p>{{ $invoice->payment_date }}</p>
                                    </div>
                                @endif
                                <div class="col-sm-12 col-md-4 col-lg-2">
                                    <label class="col-form-label font-weight-bold d-block">
                                        {{ __('Payment Status') }}
                                    </label>
                                    {!! InvoicePaymentStatus::toBadge($invoice->payment_status) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body print-section">
                        @include('pdf.partials.invoice-content')
                    </div>
                </div>
            </div>

            <x-form-metadata :model="$invoice">
                <div class="mb-4">
                    <button type="button" class="btn btn-icon btn-primary w-100" id="invoice_download"
                        data-url="{{ route(InvoiceRoutePath::DOWNLOAD, $invoice) }}">
                        <i class="fas fa-file-download"></i>
                        <span class="ms-2">{{ __('Invoice PDF Download') }}</span>
                    </button>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-icon btn-secondary w-100" onclick="invoicePrint()">
                        <i class="fas fa-print"></i>
                        <span class="ms-2">{{ __('Print Invoice') }}</span>
                    </button>
                </div>
            </x-form-metadata>
        </div>
    </form>

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
