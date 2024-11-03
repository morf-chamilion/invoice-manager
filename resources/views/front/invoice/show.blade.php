<x-front-layout>
    <section class="invoice show">
        <div class="container">
            @isset($invoice)
                <div class="invoice-wrapper">
                    @include('pdf.partials.invoice-content')
                </div>
            @else
                <div class="alert alert-info">
                    {{ __('Invoice Not Found') }}
                </div>
            @endisset
        </div>
    </section>
</x-front-layout>
