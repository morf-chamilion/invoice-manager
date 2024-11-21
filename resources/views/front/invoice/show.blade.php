<x-front-layout>
    <section class="invoice show">
        <div class="container">
            @isset($invoice)
                <div class="invoice-wrapper">
                    @include('pdf.partials.invoice-content')
                </div>

                @if ($invoice->vendor?->invoice_terms_of_service)
                    <div class="invoice-wrapper mt-8">
                        <p style="margin-top: 6px;">{!! $invoice->vendor?->invoice_terms_of_service !!}</p>
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    {{ __('Invoice Not Found') }}
                </div>
            @endisset
        </div>
    </section>
</x-front-layout>
