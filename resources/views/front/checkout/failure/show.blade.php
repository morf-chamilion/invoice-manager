<x-front-layout>
    <section class="checkout failure">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="theme-card">
                        <div class="card-body">
                            <div class="content-wrapper">
                                <i class="fas fa-exclamation-circle"></i>
                                <h2>{{ __('Checkout Failed!') }}</h2>
                            </div>
                            <div class="alert alert-danger">
                                @if ($errors->any())
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <ul>
                                        <li>Unknown failure, no errors caught.</li>
                                    </ul>
                                @endif
                            </div>
                            <div class="btn-row">
                                <form action="{{ route(FrontCheckoutRoutePath::DESTROY) }}" method="POST">
                                    @method('delete')
                                    @csrf

                                    <button class="theme-btn secondary">Cancel</button>
                                </form>

                                @isset($sessionId)
                                    <form action="{{ route(FrontCheckoutRoutePath::SHOW, $sessionId) }}" method="GET">
                                        @csrf

                                        <button class="theme-btn">Retry Payment</button>
                                    </form>
                                @endisset
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-front-layout>
