<x-default-layout :model="$payment">

    <form method="POST" action="{{ route(PaymentRoutePath::UPDATE, $payment) }}" autocomplete="off" id="resource_form">
        @method('put')
        @csrf

        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10" id="resource_form_fieldset">
                <div class="card">
                    <div class="card-header">
                        <header>
                            <h2 class="text-lg mt-8 font-medium text-gray-900">
                                {{ __($pageData['title']) }}
                            </h2>
                        </header>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 mb-8">
                                <x-input-label for="date" :value="__('Date')" required />
                                <x-input-date id="date" name="date" :value="old('date')"
                                    data-locale-format="YYYY-MM-DD" required />
                                <x-input-error :messages="$errors->get('date', $payment->date)" />
                            </div>

                            <div class="col-lg-6 mb-8">
                                <x-input-label for="customer_id" :value="__('Customer')" required />
                                <x-input-select name="customer_id" data-placeholder="Select Customer" required>
                                    @if ($customers->isNotEmpty())
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" @selected($customer->id == old('customer_id', $payment->customer->id))>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </x-input-select>
                                <x-input-error :messages="$errors->get('customer_id')" />
                            </div>

                            <div class="col-lg-6 mb-8">
                                <x-input-label for="invoice_id" :value="__('Invoice')" required />
                                <x-input-select name="invoice_id" data-placeholder="Select Invoice" required>
                                    @if ($invoices->isNotEmpty())
                                        @foreach ($invoices as $invoice)
                                            <option value="{{ $invoice->id }}" @selected($invoice->id == old('invoice_id', $payment?->invoice?->id))>
                                                {{ $invoice->number }} ({{ $invoice->readableTotalPrice }})
                                            </option>
                                        @endforeach
                                    @endif
                                </x-input-select>
                                <x-input-error :messages="$errors->get('invoice_id')" />
                            </div>

                            <div class="col-lg-6 mb-8">
                                <x-input-label for="method" :value="__('Method')" required />
                                <x-input-select name="method" data-placeholder="Select Method" data-hide-search="true"
                                    required>
                                    @foreach (PaymentMethod::toSelectOptions() as $option)
                                        <option value="{{ $option->value }}" @selected($option->value == old('method', PaymentMethod::CASH->value))>
                                            {{ $option->name }}
                                        </option>
                                    @endforeach
                                </x-input-select>
                                <x-input-error :messages="$errors->get('method')" />
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-8 form-group">
                                    <x-input-label for="amount" :value="__('Amount')" required />
                                    <div class="input-group">
                                        <x-input-text id="amount" name="amount" type="number" :value="old('amount', $payment->amount)"
                                            step="0.01" required />
                                        <span class="input-group-text">{{ $invoice->vendor->currency }}</span>
                                    </div>
                                    <x-input-error :messages="$errors->get('amount')" />
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <x-input-label for="notes" :value="__('Notes')" />
                                <x-input-textarea name="notes"
                                    id="notes">{{ old('notes', $payment->notes) }}</x-input-textarea>
                                <x-input-error :messages="$errors->get('notes')" />
                            </div>

                            <div class="col-lg-6">
                                <x-input-label for="reference_receipt" :value="__('Reference Receipt')" />
                                <x-input-file id="reference_receipt" name="reference_receipt" :fileMaxSize="2"
                                    mimeTypes="['application/pdf', 'image/*']" :value="$payment->referenceReceipt" />
                                <x-input-error :messages="$errors->get('reference_receipt')" />
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <x-form-metadata :model="$payment" type="Update">
                <div class="mb-10">
                    <x-input-label for="status" required>Status</x-input-label>
                    <x-input-select name="status" data-placeholder="Select Status" data-hide-search="true" required>
                        @foreach (PaymentStatus::toSelectOptions() as $option)
                            <option value="{{ $option->value }}" @selected($option->value == old('status', $payment->status->value))>
                                {{ $option->name }}
                            </option>
                        @endforeach
                    </x-input-select>
                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                </div>
            </x-form-metadata>

        </div>
    </form>
</x-default-layout>
