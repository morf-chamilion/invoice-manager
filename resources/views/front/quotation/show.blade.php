<x-front-layout>
    <section class="invoice show">
        <div class="container">
            @isset($quotation)
                <div class="invoice-wrapper">
                    @include('pdf.partials.quotation-content')
                </div>
            @else
                <div class="alert alert-info">
                    {{ __('Quotation Not Found') }}
                </div>
            @endisset
        </div>
    </section>
</x-front-layout>
