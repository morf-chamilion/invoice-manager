<x-front-auth-layout>
    @if (session('message'))
        <div class="row justify-content-center">
            <div class="col-sm-12 col-lg-6">
                <div class="alert alert-info">
                    {{ session('message') }}
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route(FrontAuthRoutePath::LOGIN) }}" method="POST" class="form w-100">
        @csrf

        <div class="text-center mb-11">
            <h1 class="text-dark fw-bolder mb-3">
                {{ __('Verification') }}
            </h1>
            <div class="text-gray-500 fw-medium fs-6">
                {{ __('Customer Account Registration') }}
            </div>
        </div>

        <div class="text-center">
            @isset($message)
                <p class="fw-medium">
                    {{ $message }}
                </p>
            @else
                <p>
                    {{ __('Verification status is unkown.') }}
                </p>
            @endisset
        </div>

        @if (isset($verified) && $verified)
            <div class="separator my-12"></div>

            <div class="d-grid mb-10">
                <a href="{{ route(FrontAuthRoutePath::LOGIN) }}" class="btn btn-primary">
                    {{ __('Login') }}
                </a>
            </div>
        @endif
    </form>
</x-front-auth-layout>
