<x-front-layout>
    <section class="checkout">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="theme-card">
                        <div class="card-body">
                            <div class="content-wrapper">
                                <h2>Checkout</h2>
                            </div>
                            <div class="items">
                                <div class="row">
                                    <div class="col-sm-12 col-lg-6">
                                        <div class="item">
                                            <label class="col-form-label">Billing Name</label>
                                            <p>{{ $invoice->customer->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-lg-6">
                                        <div class="item">
                                            <label class="col-form-label">Total Amount</label>
                                            <p>{{ $invoice->readableTotalPrice }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="btn-row">
                                <form action="{{ route(FrontCheckoutRoutePath::DESTROY) }}" method="POST">
                                    @method('delete')
                                    @csrf

                                    <button class="theme-btn secondary">Cancel</button>
                                </form>
                                <form action="{{ $checkoutGatewayUrl }}" method="POST">
                                    @foreach ($checkoutFields as $checkoutField)
                                        {!! $checkoutField !!}
                                    @endforeach

                                    <button class="theme-btn">
                                        Proceed To Payment
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-front-layout>
