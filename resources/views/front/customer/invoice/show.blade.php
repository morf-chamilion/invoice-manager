<x-front-customer-layout>
    <div class="customer invoice show">
        <div class="theme-card">
            @isset($invoice)
                @include('pdf.partials.invoice-content')
            @else
                <div class="alert alert-info">
                    {{ __('Invoice Not Found') }}
                </div>
            @endisset
        </div>
    </div>
</x-front-customer-layout>
