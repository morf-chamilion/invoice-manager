<x-front-layout>
    <section class="invoice show">
        <div class="container">
            @isset($quotation)
                <div class="invoice-wrapper">
                    @include('pdf.partials.quotation-content')
                </div>

                @if ($quotation->vendor?->quotation_terms_of_service)
                    <div class="invoice-wrapper mt-8">
                        <p style="margin-top: 6px;">{!! $quotation->vendor?->invoice_terms_of_service !!}</p>
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    {{ __('Quotation Not Found') }}
                </div>
            @endisset
        </div>
    </section>
</x-front-layout>
