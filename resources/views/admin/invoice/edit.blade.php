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

                        @if ($customers->isNotEmpty())
                            <div>
                                <x-input-label for="customer_id" :value="__('Customer')" required />
                                <x-input-select name="customer_id" data-placeholder="Select Customer" required>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" @selected($customer->id == old('customer_id', $invoice->customer_id))>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </x-input-select>
                                <x-input-error :messages="$errors->get('customer_id')" />
                            </div>
                        @endif
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
                        <div class="table-wrapper mb-8">
                            <table id="invoice"
                                class="table table-rounded table-row-bordered table-responsive border gy-4 gs-4">
                                <thead class="border">
                                    <tr class="fw-bold fs-7 text-gray-500 text-uppercase">
                                        <th style="width: 40%;">{{ __('Item') }}</th>
                                        <th class="text-end">{{ __('Quantity') }}</th>
                                        <th class="text-end">{{ __('Unit Price') }}</th>
                                        <th class="text-end">{{ __('Amount') }}</th>
                                        <th class="text-end">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="draggable-zone border" data-repeater-list="invoice_items"
                                    class="draggable-zone">
                                    @php
                                        $invoiceItems = old(
                                            'invoice_items',
                                            $invoice->invoiceItems->isNotEmpty()
                                                ? $invoice->invoiceItems->toArray()
                                                : [
                                                    [
                                                        'type' => InvoiceItemType::DESCRIPTION->value,
                                                        'content' => '',
                                                        'quantity' => '',
                                                        'unit_price' => '',
                                                        'amount' => '',
                                                    ],
                                                ],
                                        );
                                    @endphp
                                    @foreach ($invoiceItems as $index => $item)
                                        <tr class="draggable" data-repeater-item>
                                            <input type="hidden" name="type" id="type"
                                                value="{{ old('invoice_items.' . $index . '.' . 'type', isset($item['type']) ? $item['type'] : '') }}">
                                            <td style="width: 40%;"
                                                @if ($item['type'] === InvoiceItemType::HEADING->value) colspan="4" @endif>
                                                <input id="content" name="content" type="text" class="form-control"
                                                    style="height: 35px; border-radius: 4px;"
                                                    value="{{ old('invoice_items.' . $index . '.' . 'content', isset($item['content']) ? $item['content'] : '') }}" />
                                                <x-input-error :messages="$errors->get('invoice_items.' . $index . '.' . 'content')" />
                                            </td>
                                            <td class="@if ($item['type'] === InvoiceItemType::HEADING->value) d-none @endif">
                                                <input id="quantity" name="quantity" type="number"
                                                    class="form-control"
                                                    style="height: 35px; border-radius: 4px; text-align: end;"
                                                    value="{{ old('invoice_items.' . $index . '.' . 'quantity', isset($item['quantity']) ? $item['quantity'] : '') }}"
                                                    min="0" />
                                                <x-input-error :messages="$errors->get('invoice_items.' . $index . '.' . 'quantity')" />
                                            </td>
                                            <td class="@if ($item['type'] === InvoiceItemType::HEADING->value) d-none @endif">
                                                <input id="unit_price" name="unit_price" type="number"
                                                    class="form-control"
                                                    style="height: 35px; border-radius: 4px; text-align: end;"
                                                    value="{{ old('invoice_items.' . $index . '.' . 'unit_price', isset($item['unit_price']) ? $item['unit_price'] : '') }}"
                                                    min="0" step="0.01" />
                                                <x-input-error :messages="$errors->get('invoice_items.' . $index . '.' . 'unit_price')" />
                                            </td>
                                            <td class="@if ($item['type'] === InvoiceItemType::HEADING->value) d-none @endif">
                                                <input id="amount" name="amount" type="number"
                                                    class="form-control form-control-solid"
                                                    style="height: 35px; border-radius: 4px; text-align: end; padding-right: unset;"
                                                    value="{{ old('invoice_items.' . $index . '.' . 'amount', isset($item['amount']) ? $item['amount'] : '') }}"
                                                    readonly min="0" step="0.01" />
                                                <x-input-error :messages="$errors->get('invoice_items.' . $index . '.' . 'amount')" />
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-3 justify-content-end">
                                                    <button
                                                        class="btn btn-sm btn-icon btn-light-secondary draggable-handle"
                                                        title="Move" type="button" style="cursor: grab;">
                                                        <i class="fa-solid fa-arrows-alt text-dark"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-icon btn-light-danger" title="Delete"
                                                        type="button" data-repeater-delete>
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="border">
                                    <tr>
                                        <td colspan="6">
                                            <div class="d-flex flex-end gap-3">
                                                <button class="btn btn-sm btn-light-secondary text-dark" type="button"
                                                    data-repeater-create-custom="heading">
                                                    <i class="fa-solid fa-heading text-dark"></i>
                                                    {{ __('Add Heading') }}
                                                </button>
                                                <button class="btn btn-sm btn-light-primary" type="button"
                                                    data-repeater-create-custom="description">
                                                    <i class="fa-solid fa-plus"></i>
                                                    {{ __('Add Item') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-100">
                                        <td colspan="3">
                                            <h5>{{ __('Total Amount') }}</h5>
                                        </td>
                                        <td colspan="2" class="text-end">
                                            <h5>
                                                {{ MoneyHelper::currencyCode() }}
                                                <span id="total_price_show">{{ $invoice->total_price }}</span>
                                            </h5>
                                            <input type="hidden" id="total_price" name="total_price"
                                                value="0" />
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mb-4">
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
                            <x-input-label for="payment_reference_receipt" :value="__('Payment Reference Receipt')" required />
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

            table td {
                min-width: 150px;
            }
        </style>
    @endpush

    @push('footer')
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
