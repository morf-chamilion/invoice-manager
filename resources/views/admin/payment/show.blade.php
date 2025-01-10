<x-default-layout :model="$payment">

    <form method="POST" action="{{ route(PaymentRoutePath::UPDATE, $payment) }}" autocomplete="off" id="resource_form">
        @method('put')
        @csrf

        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-lg-row-fluid mb-10 mb-lg-0" id="resource_form_fieldset">
                <div class="card">
                    <div class="card-header">
                        <header>
                            <h2 class="text-lg mt-8 font-medium text-gray-900">
                                {{ __($pageData['title']) }}
                            </h2>
                        </header>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 mb-8">
                                <x-input-label for="date" :value="__('Payment ID')" />
                                <p>{{ $payment->number }}</p>
                            </div>

                            <div class="col-lg-3 mb-8">
                                <x-input-label for="date" :value="__('Date')" />
                                <p>{{ $payment->readableDate }}</p>
                            </div>

                            <div class="col-lg-3 mb-8">
                                <x-input-label for="customer_id" :value="__('Customer')" />
                                <p>{{ $payment->customer->name }}</p>
                            </div>

                            <div class="col-lg-3 mb-8">
                                <x-input-label for="invoice_id" :value="__('Invoice')" />
                                <div>
                                    <a href="{{ route(InvoiceRoutePath::EDIT, $payment->invoice->id) }}"
                                        target="_blank">
                                        {{ $payment->invoice->number }}
                                    </a>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <x-input-label for="method" :value="__('Method')" />
                                <p>{!! PaymentMethod::toBadge($payment->method) !!}</p>
                            </div>

                            <div class="col-lg-3">
                                <x-input-label for="amount" :value="__('Amount')" />
                                <p>{{ $payment->readableAmount }}</p>
                            </div>

                            <div class="col-lg-3">
                                <x-input-label for="notes" :value="__('Notes')" />
                                <p>{{ old('notes', $payment->notes) }}</p>
                            </div>

                            @if ($payment->getFirstMedia('reference_receipt'))
                                <div class="col-lg-3">
                                    <x-input-label for="reference_receipt" :value="__('Reference Receipt')" />
                                    <div>
                                        <a href="{{ $payment->getFirstMedia('reference_receipt')->getFullUrl() }}"
                                            class="btn btn-icon btn-light w-50" download>
                                            <i class="fas fa-file-download me-2"></i>
                                            {{ __('Download') }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if (!empty($payment->data))
                    <div class="card payment-card">
                        <div class="card-header">
                            <header>
                                <h2 class="text-lg mt-8 font-medium text-gray-800">
                                    {{ __('Card Payment Receipt') }}
                                </h2>
                            </header>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @isset($payment->data['reference'])
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Reference') }}
                                        </label>
                                        <p style="font-size: 12px;">{{ $payment->data['reference'] }}</p>
                                    </div>
                                @else
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Transaction ID') }}
                                        </label>
                                        <p style="font-size: 12px;">{{ $payment->data['transaction_id'] }}</p>
                                    </div>
                                @endisset
                                @if ($payment->customer->name)
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Billed To') }}
                                        </label>
                                        <p>
                                            {{ $payment->customer->name }}
                                        </p>
                                    </div>
                                @endif
                                @if ($payment->method)
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Payment Method') }}
                                        </label>
                                        <div>
                                            {!! PaymentMethod::toBadge($payment->method) !!}
                                        </div>
                                    </div>
                                @endif
                                @isset($payment->data['amount'])
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Amount') }}
                                        </label>
                                        <p>
                                            {{ MoneyHelper::print($payment->data['amount']) }}
                                        </p>
                                    </div>
                                @endisset
                                @if ($payment->date)
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Date') }}
                                        </label>
                                        <p>{{ $payment->date->format('Y-m-d') }}</p>
                                    </div>
                                @endif
                                @if ($payment->status)
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <label class="col-form-label font-weight-bold d-block">
                                            {{ __('Status') }}
                                        </label>
                                        {!! PaymentStatus::toBadge($payment->status) !!}
                                    </div>
                                @endif
                                @if ($payment->getFirstMedia('reference_receipt'))
                                    <div class="col-sm-12 col-md-4 col-lg-3">
                                        <label class="col-form-label font-weight-bold">
                                            {{ __('Reference Receipt') }}
                                        </label>
                                        <a href="{{ $payment->getFirstMedia('reference_receipt')->getFullUrl() }}"
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
            </div>
        </div>
    </form>
</x-default-layout>
