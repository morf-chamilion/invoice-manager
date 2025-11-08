<x-default-layout>
    <form method="post" action="{{ route(RecurringInvoiceRoutePath::STORE) }}" autocomplete="off" id="resource_form">
        @method('post')
        @csrf

        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10" id="resource_form_fieldset">
                <div class="card">
                    <div class="card-header align-items-center">
                        <header class="d-flex w-100 justify-content-between">
                            <h2 class="text-lg mt-8 font-medium text-gray-900">
                                {{ __($pageData['title']) }}
                            </h2>
                        </header>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-8 col-lg-6">
                                <x-input-label for="start_date" :value="__('Start Date')" required />
                                <x-input-date id="start_date" name="start_date" :value="old('start_date')"
                                    data-locale-format="YYYY-MM-DD" required />
                                <x-input-error :messages="$errors->get('start_date')" />
                            </div>

                            <div class="mb-8 col-lg-6">
                                <x-input-label for="due_after_days" :value="__('Due After (Days)')" required />
                                <x-input-text type="number" id="due_after_days" name="due_after_days" min="0"
                                    step="1" value="{{ old('due_after_days', 30) }}" required />
                                <x-input-error :messages="$errors->get('due_after_days')" />
                            </div>

                            <div class="mb-8 col-lg-8">
                                <x-input-label for="customer_id" :value="__('Customer')" required />
                                <x-input-select name="customer_id" data-placeholder="Select Customer" required>
                                    @if ($customers->isNotEmpty())
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" @selected($customer->id == old('customer_id'))>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </x-input-select>
                                <x-input-error :messages="$errors->get('customer_id')" />
                            </div>

                            <div class="mb-8 col-lg-4">
                                <button class="btn font-weight-bolder btn-light-primary w-100 mt-8" type="button"
                                    data-bs-toggle="modal" data-bs-target="#customer_create">
                                    <i class="fas fa-user-plus"></i> {{ __('Create New Customer') }}
                                </button>
                            </div>

                            <div class="mb-8 col-lg-4">
                                <x-input-label for="frequency" :value="__('Frequency')" required />
                                <x-input-select id="frequency" name="frequency" data-hide-search="true"
                                    data-placeholder="{{ __('Select Frequency') }}" required>
                                    <option></option>
                                    @foreach (RecurringInvoiceFrequency::toSelectOptions() as $option)
                                        <option value="{{ $option->value }}" @selected(old('frequency') === $option->value)>
                                            {{ __($option->name) }}
                                        </option>
                                    @endforeach
                                </x-input-select>
                                <x-input-error :messages="$errors->get('frequency')" />
                            </div>

                            <div class="mb-8 col-lg-4">
                                <x-input-label for="end_condition_type" :value="__('End Condition Type')" />
                                <x-input-select id="end_condition_type" name="end_condition_type"
                                    data-placeholder="{{ __('Select End Condition Type') }}" data-hide-search="true">
                                    @foreach (RecurringInvoiceEndType::toSelectOptions() as $option)
                                        <option value="{{ $option->value }}" @selected(old('end_condition_type', RecurringInvoiceEndType::DATE->value) === $option->value)>
                                            {{ __($option->name) }}
                                        </option>
                                    @endforeach
                                </x-input-select>
                                <x-input-error :messages="$errors->get('end_condition_type')" />
                            </div>

                            <div class="mb-8 col-lg-4" id="endDateGroup">
                                <x-input-label for="end_date" :value="__('End Date')" />
                                <x-input-date id="end_date" name="end_date" :value="old('end_date')"
                                    data-locale-format="YYYY-MM-DD" />
                                <x-input-error :messages="$errors->get('end_date')" />
                            </div>

                            <div class="mb-8 col-lg-4 d-none" id="endCountGroup">
                                <x-input-label for="end_count" :value="__('End Count')" />
                                <x-input-text type="number" id="end_count" name="end_count" min="1"
                                    step="1" value="{{ old('end_count') }}" />
                                <x-input-error :messages="$errors->get('end_count')" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-8">
                    <div class="card-header align-items-center">
                        <header>
                            <h2 class="m-0 text-lg font-medium text-gray-900">
                                {{ __('Schedule') }}
                            </h2>
                        </header>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-8 form-group">
                                    <x-input-label :value="__('Next Run Date')" />
                                    <div class="form-control form-control-solid" id="next_run_date_display">—</div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-8 form-group">
                                    <x-input-label :value="__('Last Run Date')" />
                                    <div class="form-control form-control-solid" id="last_run_date_display">—</div>
                                </div>
                            </div>
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
                                        <x-input-text type="number" name="quantity" id="quantity"
                                            min="0" />
                                    </div>
                                </div>

                                <div class="col-sm-12 col-xl-5">
                                    <div class="mb-4 form-group">
                                        <x-input-label :value="__('Unit Price')" />
                                        <div class="input-group">
                                            <x-input-text type="number" name="unit_price" id="unit_price"
                                                min="0" step="0.01" />
                                            <span
                                                class="input-group-text">{{ auth()->user()->vendor?->currency }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-xl-4">
                                    <div class="form-group text-right">
                                        <button class="btn font-weight-bolder btn-light-primary w-100 mt-8"
                                            type="button" id="addRecurringInvoiceItemBtn">
                                            <i class="la la-plus"></i> {{ __('Add Item') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $items = old('invoice_items');
                            $totalPrice = 0;

                            if (!empty($items)) {
                                $totalPrice = \is_array($items)
                                    ? array_sum(array_column($items, 'amount') - old('discount_value'))
                                    : $items->sum('amount') - old('discount_value');
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
                                        <th id="unit_price" width="15%" class="text-end">{{ __('Unit Price') }}
                                        </th>
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
                                                value="{{ old('discount_value') }}" />
                                        </td>
                                        <td colspan="1" class="border-right-0">
                                            <select class="form-select" name="discount_type" id="discountType">
                                                <option value="fixed" @selected(old('discount_type') === 'fixed')>
                                                    {{ __('Fixed') }}
                                                </option>
                                                <option value="percentage" @selected(old('discount_type') === 'percentage')>
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
                                                <span>{{ auth()->user()->vendor?->currency }}</span>
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
                                {{ old('notes') }}
                            </x-input-textarea>
                            <x-input-error :messages="$errors->get('notes')" />
                        </div>
                    </div>
                </div>

            </div>

            <x-form-metadata type="Create">
                <div class="mb-8">
                    <x-input-label for="status" required>Status</x-input-label>
                    <x-input-select name="status" data-placeholder="Select Status" data-hide-search="true" required>
                        @foreach (RecurringInvoiceStatus::toSelectOptions() as $option)
                            <option value="{{ $option->value }}" @selected($option->value == old('status', RecurringInvoiceStatus::ACTIVE->value))>
                                {{ $option->name }}
                            </option>
                        @endforeach
                    </x-input-select>
                    <x-input-error :messages="$errors->get('status')" />
                </div>

                <div class="separator separator-dashed mb-8"></div>

                <div class="mb-8">
                    <div class="d-flex gap-4 align-items-center justify-content-between">
                        <x-input-label for="send_automatically" :value="__('Send Automatically')" />
                        <x-input-checkbox id="send_automatically" name="send_automatically" :value="old('send_automatically', false)"
                            :value="true" />
                    </div>
                    <p class="text-muted">
                        {{ __('The recurring invoice will be sent automatically.') }}
                    </p>
                    <x-input-error :messages="$errors->get('send_automatically')" />
                </div>
            </x-form-metadata>
        </div>
    </form>

    @include('admin.invoice.partials.customer-create')

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
            const ITEM_TYPE_CUSTOM = {{ RecurringInvoiceItemType::CUSTOM }};
        </script>

        @env('local')
            <script>
                {!! file_get_contents(resource_path('js/admin/recurring-invoice.js')) !!}
            </script>
        @endenv

        @env('production')
            <script src="{{ asset('assets/js/admin/recurring-invoice.js') }}"></script>
        @endenv
    @endpush
</x-default-layout>
