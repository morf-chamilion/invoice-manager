<x-default-layout :model="$quotation">

    <form method="POST" action="{{ route(QuotationRoutePath::UPDATE, $quotation) }}" autocomplete="off" id="resource_form">
        @method('put')
        @csrf

        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10" id="resource_form_fieldset">
                <div class="card">
                    <div class="card-header align-items-center">
                        <header>
                            <h2 class="m-0 text-lg font-medium text-gray-900">
                                {{ __($pageData['title']) }}: <code class="fs-3">{{ $quotation->number }}</code>
                            </h2>
                        </header>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div>
                                    <x-input-label for="date" :value="__('Date')" required />
                                    <x-input-date id="date" name="date" :value="old('date', $quotation->date)"
                                        data-locale-format="YYYY-MM-DD" required />
                                    <x-input-error :messages="$errors->get('date')" />
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div>
                                    <x-input-label for="customer_id" :value="__('Customer')" required />
                                    <x-input-select name="customer_id" data-placeholder="Select Customer" required>
                                        @if ($customers->isNotEmpty())
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}" @selected($customer->id == old('customer_id', $quotation->customer_id))>
                                                    {{ $customer->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </x-input-select>
                                    <x-input-error :messages="$errors->get('customer_id')" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-8">
                    <div class="card-header align-items-center">
                        <header>
                            <h2 class="m-0 text-lg font-medium text-gray-900">
                                {{ __('Quotation') }}
                            </h2>
                        </header>
                    </div>
                    <div class="card-body">
                        <div class="mb-8 rounded border p-8" id="quotationItemForm">
                            <div class="row">
                                <div class="col-sm-12 col-xl-6">
                                    <div class="mb-4 form-group">
                                        <div class="itinerary-type" id="customItem">
                                            <x-input-label :value="__('Title')" />
                                            <x-input-text type="text" name="title" id="title" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-xl-6">
                                    <div class="mb-4 form-group">
                                        <x-input-label :value="__('Description')" />
                                        <x-input-text type="text" name="description" id="description" />
                                    </div>
                                </div>

                                <div class="col-sm-12 col-xl-3">
                                    <div class="mb-4 form-group">
                                        <x-input-label :value="__('Quantity')" />
                                        <x-input-text type="number" name="quantity" id="quantity" min="0" />
                                    </div>
                                </div>

                                <div class="col-sm-12 col-xl-5">
                                    <div class="mb-4 form-group">
                                        <x-input-label :value="__('Unit Price')" />
                                        <div class="input-group">
                                            <x-input-text type="number" name="unit_price" id="unit_price"
                                                min="0" step="0.01" />
                                            <span class="input-group-text">{{ MoneyHelper::currencyCode() }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-xl-4">
                                    <div class="form-group text-right">
                                        <button class="btn font-weight-bolder btn-light-primary w-100 mt-8"
                                            type="button" id="addQuotationItemBtn">
                                            <i class="la la-plus"></i> {{ __('Add Item') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $items = old('quotation_items', $quotation->formattedQuotationItems);
                            $totalPrice = 0;

                            if (!empty($items)) {
                                $totalPrice = \is_array($items)
                                    ? array_sum(array_column($items, 'amount'))
                                    : $items->sum('amount');
                            }
                        @endphp

                        <table id="quotationData"
                            class="table table-rounded table-row-bordered table-responsive border gy-4 gs-4 mb-8">
                            <thead>
                                <tr class="fw-bold fs-7 text-gray-500 text-uppercase">
                                    <th id="type" class="d-none">{{ __('Type') }}</th>
                                    <th id="item">{{ __('Item') }}</th>
                                    <th id="description">{{ __('Description') }}</th>
                                    <th id="quantity" width="10%" class="text-end">{{ __('Quantity') }}</th>
                                    <th id="unit_price" width="15%" class="text-end">{{ __('Unit Price') }}</th>
                                    <th id="amount" width="15%" class="text-end">{{ __('Amount') }}</th>
                                    <th id="actions" width="150px" class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody data-repeater-list="quotation_items" class="draggable-zone">
                                <tr data-repeater-item class="draggable">
                                    <td class="d-none">
                                        <input type="hidden" name="type_id">
                                        <x-input-text type="text" name="type" id="type"
                                            class="bg-none bg-body p-0 border-0" readonly />
                                    </td>
                                    <td>
                                        <input type="hidden" name="item_id">
                                        <x-input-text type="text" name="title" id="title"
                                            class="bg-body p-0 border-0" readonly />
                                    </td>
                                    <td>
                                        <x-input-text type="text" name="description" id="description"
                                            class="bg-body p-0 border-0" readonly />
                                    </td>
                                    <td>
                                        <x-input-text type="text" name="quantity" id="quantity"
                                            class="text-end bg-body p-0 border-0" min="1" readonly />
                                    </td>
                                    <td>
                                        <x-input-text type="text" name="unit_price" id="unit_price"
                                            class="text-end bg-body p-0 border-0" min="0" readonly />
                                    </td>
                                    <td>
                                        <x-input-text type="text" name="amount" id="amount"
                                            class="text-end bg-body p-0 border-0" min="0" readonly />
                                    </td>
                                    <td class="text-end">
                                        <button type="button"
                                            class="btn btn-sm btn-icon btn-light-secondary draggable-handle"
                                            title="Move">
                                            <i class="fa-solid fa-arrows-alt text-dark"></i>
                                        </button>

                                        <button type="button" data-repeater-delete=""
                                            class="btn btn-sm btn-icon btn-light-danger" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="border">
                                <tr class="bg-gray-100">
                                    <td colspan="5" class="text-right border-right-0 py-7"><span
                                            class="font-weight-bolder h3">{{ __('Total Amount') }}</span></td>
                                    <td colspan="2" class="border-right-0 py-7">
                                        <span class="font-weight-bolder h3 d-block text-end">
                                            <span>{{ MoneyHelper::currencyCode() }}</span>
                                            <span id="totalPrice">{{ MoneyHelper::format($totalPrice) }}</span>
                                        </span>
                                        <input type="hidden" name="total_price" id="totalPriceInput"
                                            value="{{ $totalPrice }}">
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <script>
                            let items = @json(!empty($items) ? $items : []);
                        </script>

                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <x-input-textarea name="notes" id="notes">
                                {{ old('notes', $quotation->notes) }}
                            </x-input-textarea>
                            <x-input-error :messages="$errors->get('notes')" />
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-3" id="form_metadata_container">
                <div data-kt-sticky="true" data-kt-sticky-name="form-metadata-card"
                    data-kt-sticky-width="{target: '#form_metadata_container'}" data-kt-sticky-top="100px"
                    data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                    <div class="card mb-8">
                        <div class="card-body">

                            <div class="mb-8">
                                <x-input-label for="status" required>Status</x-input-label>
                                <x-input-select name="status" data-placeholder="Select Status"
                                    data-hide-search="true" required>
                                    @foreach (QuotationStatus::toSelectOptions() as $option)
                                        @if ($option->value !== QuotationStatus::COMPLETED->value)
                                            <option value="{{ $option->value }}" @selected($option->value == old('status', $quotation->status->value))>
                                                {{ $option->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </x-input-select>
                                <x-input-error :messages="$errors->get('status')" />
                            </div>

                            @if (!empty($quotation->createdByUser) || !empty($quotation->updatedByUser))
                                <div class="separator separator-dashed mb-8"></div>
                            @endif

                            @if (!empty($quotation->createdByUser))
                                <h4 class="form-label">Created By</h4>
                                <div class="d-flex justify-content-between mb-4">
                                    <p class="text-muted">{{ $quotation->createdByUser->name }}</p>
                                    <p class="text-muted">{{ $quotation->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            @endif

                            @if (!empty($quotation->updatedByUser))
                                <h4 class="form-label">{{ __('Last Updated By') }}</h4>
                                <div class="d-flex justify-content-between mb-4">
                                    <p class="text-muted">{{ $quotation?->updatedByUser->name }}</p>
                                    <p class="text-muted">{{ $quotation?->updated_at->diffForHumans() }}</p>
                                </div>
                            @endif

                            @if (!empty($quotation->createdByUser) || !empty($quotation->updatedByUser))
                                <div class="separator separator-dashed mb-8"></div>
                            @endif

                            <div class="flex items-center gap-4">
                                <x-button-primary class="w-100">{{ __('Update') }}</x-button-primary>
                            </div>
                        </div>
                    </div>

                    <div class="card" data-kt-sticky="true" data-kt-sticky-name="form-metadata-card"
                        data-kt-sticky-width="{target: '#form_metadata_container'}" data-kt-sticky-top="100px"
                        data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                        <div class="card-body">
                            <div>
                                <p class="text-muted">Convert to an invoice with billing capabilities.</p>
                                <button type="button" class="btn btn-icon btn-light-info w-100"
                                    id="invoice_generate">
                                    <i class="fas fa-file-export"></i>
                                    <span class="ms-2">{{ __('Convert To Invoice') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

    @push('header')
        <style>
            .table-wrapper {
                overflow-x: auto;
            }

            table td {
                min-width: 150px;
            }
        </style>
    @endpush

    @push('footer')
        <script>
            const ITEM_TYPE_CUSTOM = {{ QuotationItemType::CUSTOM }};
        </script>

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
