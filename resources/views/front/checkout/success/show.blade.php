<x-front-layout>
    <section class="checkout success">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="theme-card">
                        <div class="card-body">
                            <div class="card-body">
                                <div class="content-wrapper">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <h2>{{ __('Checkout Success!') }}</h2>
                                </div>
                                <div class="items">
                                    <div class="row">
                                        <div class="col-sm-12 col-lg-3">
                                            <div class="item">
                                                <label class="col-form-label">Transaction ID</label>
                                                <p style="font-size: 14px;">
                                                    {{ $invoice?->payment_data['transaction_id'] }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-lg-3">
                                            <div class="item">
                                                <label class="col-form-label">Billed To</label>
                                                <p>{{ $invoice?->customer->name }}</p>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-lg-3">
                                            <div class="item">
                                                <label class="col-form-label">Amount Paid</label>
                                                <p>{{ MoneyHelper::print($invoice?->payment_data['amount']) }}</p>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-lg-3">
                                            <div class="item">
                                                <label class="col-form-label">Payment Date</label>
                                                <p>{{ $invoice?->payment_date }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-row">
                                    <a href="{{ route(FrontPageRoutePath::HOME) }}" class="theme-btn">Go Back To
                                        Home</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</x-front-layout>
