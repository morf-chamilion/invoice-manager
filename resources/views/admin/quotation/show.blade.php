<x-default-layout :model="$quotation">

    <form method="POST" action="{{ route(QuotationRoutePath::UPDATE, $quotation) }}" autocomplete="off" id="resource_form">
        @method('put')
        @csrf

        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10" id="resource_form_fieldset">

                <div class="card">
                    <div class="card-body print-section">
                        @include('pdf.partials.quotation-content')
                    </div>
                </div>
            </div>

            <x-form-metadata :model="$quotation">
                <h4 class="form-label">{{ __('Quotation Tools') }}</h4>

                <div class="mb-4">
                    <button type="button" class="btn btn-icon btn-primary w-100" id="quotation_download"
                        data-url="{{ route(QuotationRoutePath::DOWNLOAD, $quotation) }}">
                        <i class="fas fa-file-download"></i>
                        <span class="ms-2">{{ __('Quotation PDF Download') }}</span>
                    </button>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-icon btn-secondary w-100" onclick="quotationPrint()">
                        <i class="fas fa-print"></i>
                        <span class="ms-2">{{ __('Print Quotation') }}</span>
                    </button>
                </div>

                <h4 class="form-label">{{ __('Quotation Actions') }}</h4>

                <div class="mb-4">
                    <a href="{{ $quotation->show_link }}" target="_blank" class="btn btn-icon btn-secondary w-100">
                        <i class="fa-solid fa-eye"></i>
                        <span class="ms-2">{{ __('View Quotation') }}</span>
                    </a>
                </div>
            </x-form-metadata>
        </div>
    </form>

    @push('header')
        <style>
            @media print {

                .app-header,
                .app-toolbar,
                .app-footer,
                .payment-card,
                #form_metadata_container {
                    display: none;
                }

                .print-section {
                    display: block !important;
                }
            }
        </style>

        <script>
            function quotationPrint() {
                document.body.classList.add('print-content-only');
                window.print();
            }
        </script>
    @endpush

    @push('footer')
        @env('local')
        <script>
            {!! file_get_contents(resource_path('js/admin/quotation.js')) !!}
        </script>
        @endenv

        @env('production')
        <script src="{{ asset('assets/js/admin/quotation.js') }}"></script>
        @endenv
    @endpush
</x-default-layout>
