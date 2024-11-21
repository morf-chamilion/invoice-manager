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
                        <p>{{ $invoice->payment_date->format('Y-m-d') }}</p>
                    </div>
                @endif
                @if ($invoice->payment_status)
                    <div class="col-sm-12 col-md-4 col-lg-2">
                        <label class="col-form-label font-weight-bold d-block">
                            {{ __('Payment Status') }}
                        </label>
                        {!! InvoicePaymentStatus::toBadge($invoice->payment_status) !!}
                    </div>
                @endif
                @if ($invoice->getFirstMedia('payment_reference_receipt'))
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label class="col-form-label font-weight-bold">
                            {{ __('Payment Reference Receipt') }}
                        </label>
                        <a href="{{ $invoice->getFirstMedia('payment_reference_receipt')->getFullUrl() }}"
                            class="btn btn-icon btn-dark w-100" download>
                            <i class="fas fa-file-download me-2"></i>
                            {{ __('Download') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
