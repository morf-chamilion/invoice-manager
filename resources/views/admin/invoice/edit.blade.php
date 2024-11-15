<x-default-layout :model="$invoice">

    <form method="POST" action="{{ route(InvoiceRoutePath::UPDATE, $invoice) }}" autocomplete="off" id="resource_form">
        @method('put')
        @csrf

        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10" id="resource_form_fieldset">
                <div class="card">
                    <div class="card-header align-items-center">
                        <header>
                            <h2 class="m-0 text-lg font-medium text-gray-900">
                                {{ __($pageData['title']) }}: <code class="fs-3">{{ $invoice->number }}</code>
                            </h2>
                        </header>
                    </div>
                    <div class="card-body">
                        <div class="mb-8 row">
                            <div class="col-lg-6">
                                <div>
                                    <x-input-label for="date" :value="__('Date')" required />
                                    <x-input-date id="date" name="date" :value="old('date', $invoice->date)"
                                        data-locale-format="YYYY-MM-DD" required />
                                    <x-input-error :messages="$errors->get('date')" />
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div>
                                    <x-input-label for="due_date" :value="__('Due Date')" required />
                                    <x-input-date id="due_date" name="due_date" :value="old('due_date', $invoice->due_date)"
                                        data-locale-format="YYYY-MM-DD" required />
                                    <x-input-error :messages="$errors->get('due_date')" />
                                </div>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="customer_id" :value="__('Customer')" required />
                            <x-input-select name="customer_id" data-placeholder="Select Customer" required>
                                @if ($customers->isNotEmpty())
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" @selected($customer->id == old('customer_id', $invoice->customer_id))>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </x-input-select>
                            <x-input-error :messages="$errors->get('customer_id')" />
                        </div>
                    </div>
                </div>

                <div class="card mt-8">
                    <div class="card-header align-items-center">
                        <header>
                            <h2 class="m-0 text-lg font-medium text-gray-900">
                                {{ __('Invoice') }}
                            </h2>
                        </header>
                    </div>
                    <div class="card-body">
                        <div class="mb-8 rounded border p-8" id="invoiceItemForm">
                            <div class="row">
                                <div class="col-sm-12 col-xl-6">
                                    <div class="mb-4 form-group">
                                        <div class="itinerary-type" id="customItem">
                                            <x-input-label :value="__('Item')" />
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
                                            type="button" id="addInvoiceItemBtn">
                                            <i class="la la-plus"></i> {{ __('Add Item') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $items = old('invoice_items', $invoice->formattedInvoiceItems);
                            $totalPrice = 0;

                            if (!empty($items)) {
                                $totalPrice = \is_array($items)
                                    ? array_sum(
                                        array_column($items, 'amount') -
                                            old('discount_value', $invoice->discount_value),
                                    )
                                    : $items->sum('amount') - old('discount_value', $invoice->discount_value);
                            }
                        @endphp

                        <div class="table-wrapper">
                            <table id="invoiceData"
                                class="table table-rounded table-row-bordered table-responsive border gy-4 gs-4 mb-8">
                                <thead>
                                    <tr class="fw-bold fs-7 text-gray-500 text-uppercase">
                                        <th id="type" class="d-none">{{ __('Type') }}</th>
                                        <th id="item">{{ __('Item') }}</th>
                                        <th id="description">{{ __('Description') }}</th>
                                        <th id="quantity" width="10%" class="text-end">{{ __('Qty') }}</th>
                                        <th id="unit_price" width="15%" class="text-end">{{ __('Unit Price') }}</th>
                                        <th id="amount" width="15%" class="text-end">{{ __('Amount') }}</th>
                                        <th id="actions" width="150px" class="text-end">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody data-repeater-list="invoice_items" class="draggable-zone">
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
                                    <tr>
                                        <td colspan="4" class="text-right border-right-0">
                                            <span class="font-weight-bold h5">{{ __('Discount') }}</span>
                                        </td>
                                        <td colspan="1" class="border-right-0">
                                            <input type="number" class="form-control text-end" name="discount_value"
                                                id="discountValue" min="0" placeholder="0"
                                                value="{{ old('discount_value', $invoice->discount_value) }}" />
                                        </td>
                                        <td colspan="1" class="border-right-0">
                                            <select class="form-select" name="discount_type" id="discountType">
                                                <option value="fixed" @selected(old('discount_type', $invoice->discount_type) === 'fixed')>
                                                    {{ __('Fixed') }}
                                                </option>
                                                <option value="percentage" @selected(old('discount_type', $invoice->discount_type) === 'percentage')>
                                                    {{ __('Percent') }}
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
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
                        </div>

                        <script>
                            let items = @json(!empty($items) ? $items : []);
                        </script>

                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <x-input-textarea name="notes" id="notes">
                                {{ old('notes', $invoice->notes) }}
                            </x-input-textarea>
                            <x-input-error :messages="$errors->get('notes')" />
                        </div>
                    </div>
                </div>

                <div class="card mt-8">
                    <div class="card-header">
                        <header>
                            <h2 class="text-lg mt-8 font-medium text-gray-800">
                                {{ __('Payment') }}
                            </h2>
                        </header>
                    </div>
                    <div class="card-body">
                        <div class="mb-8">
                            <x-input-label for="payment_method" :value="__('Payment Method')" required />
                            <x-input-select name="payment_method" data-placeholder="Select Payment Method"
                                data-hide-search="true" required>
                                @foreach (InvoicePaymentMethod::toSelectOptions() as $option)
                                    <option value="{{ $option->value }}" @selected($option->value == old('payment_method', $invoice->payment_method->value))>
                                        {{ $option->name }}
                                    </option>
                                @endforeach
                            </x-input-select>
                            <x-input-error :messages="$errors->get('payment_method')" />
                        </div>

                        <div class="col-lg-12 mb-8">
                            <x-input-label for="payment_date" :value="__('Payment Date')" />
                            <x-input-date id="payment_date" name="payment_date" :value="old('payment_date', $invoice->payment_date?->format('Y-m-d'))"
                                data-locale-format="YYYY-MM-DD" data-init-empty="true" />
                            <x-input-error :messages="$errors->get('payment_date')" />
                        </div>

                        <div class="col-lg-12">
                            <x-input-label for="payment_reference" :value="__('Payment Reference')" />
                            <x-input-textarea id="payment_reference"
                                name="payment_reference">{{ old('payment_reference', optional($invoice->payment_data)['payment_reference']) }}</x-input-textarea>
                            <x-input-error :messages="$errors->get('payment_reference')" />
                        </div>

                        <div class="col-lg-12 mt-8">
                            <x-input-label for="payment_reference_receipt" :value="__('Payment Reference Receipt')" />
                            <x-input-file id="payment_reference_receipt" name="payment_reference_receipt"
                                :fileMaxSize="2" :value="$invoice->paymentReferenceReceipt" />
                            <x-input-error :messages="$errors->get('payment_reference_receipt')" />
                        </div>

                        <div class="col-lg-12">
                            <x-input-label for="payment_link" :value="__('Payment Link')" required />
                            <div class="input-group">
                                <x-input-text id="payment_link" name="payment_link" :value="old('payment_link', $invoice->checkout_link)" required
                                    disabled />
                                <button id="payment_link_btn" class="btn btn-secondary"
                                    type="button">{!! getIcon('copy', 'text-dark') !!}</button>
                            </div>

                            <x-input-error :messages="$errors->get('payment_link')" />
                        </div>
                    </div>
                </div>

            </div>

            <x-form-metadata :model="$invoice" type="Update">
                <div class="mb-8">
                    <x-input-label for="status" required>Status</x-input-label>
                    <x-input-select name="status" data-placeholder="Select Status" data-hide-search="true" required>
                        @foreach (InvoiceStatus::toSelectOptions() as $option)
                            @if ($option->value !== InvoiceStatus::COMPLETED->value)
                                <option value="{{ $option->value }}" @selected($option->value == old('status', $invoice->status->value))>
                                    {{ $option->name }}
                                </option>
                            @endif
                        @endforeach
                    </x-input-select>
                    <x-input-error :messages="$errors->get('status')" />
                </div>
            </x-form-metadata>
        </div>
    </form>

    @push('header')
        <style>
            .table-wrapper {
                overflow-x: auto;
            }

            table th,
            table td {
                min-width: 150px;
            }
        </style>
    @endpush

    @push('footer')
        <script>
            const ITEM_TYPE_CUSTOM = {{ InvoiceItemType::CUSTOM }};
        </script>

        @env('local')
        <script>
            {!! file_get_contents(resource_path('js/admin/invoice.js')) !!}
        </script>
        @endenv

        @env('production')
        <script src="{{ asset('assets/js/admin/invoice.js') }}"></script>
        @endenv
    @endpush
</x-default-layout>
