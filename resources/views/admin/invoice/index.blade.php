<x-default-layout>
    <div class="card mb-5">
        <div class="card-body pb-3">
            <form action="{{ url()->current() }}" method="POST" id="datatable_index">
                @csrf
                <div class="row">
                    <div class="col-sm-12 col-lg-3 mb-5">
                        <x-input-label for="date_range" :value="__('Date Range')" />
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <x-input-date id="date_range" name="date_range" :value="old('date_range')" multiple />
                            <x-input-error :messages="$errors->get('date_range')" />
                        </div>
                    </div>

                    <div class="col-sm-12 col-lg-3 mb-5">
                        <div class="form-group">
                            <x-input-label for="status" :value="__('Status')" />
                            <x-input-select name="status" data-hide-search="true" data-placeholder="Select Status">
                                @foreach (InvoiceStatus::toSelectOptions() as $option)
                                    <option value="{{ $option->value }}">
                                        {{ $option->name }}
                                    </option>
                                @endforeach
                                <option value="past_due_date">
                                    {{ __('Past Due Date') }}
                                </option>
                            </x-input-select>
                            <x-input-error :messages="$errors->get('status')" />
                        </div>
                    </div>

                    <div class="col-sm-12 col-lg-3 mb-5">
                        <div class="form-group">
                            <x-input-label for="payment_status" :value="__('Payment Status')" />
                            <x-input-select name="payment_status" data-hide-search="true"
                                data-placeholder="Select Payment Status">
                                @foreach (InvoicePaymentStatus::toSelectOptions() as $option)
                                    <option value="{{ $option->value }}">
                                        {{ $option->name }}
                                    </option>
                                @endforeach
                            </x-input-select>
                            <x-input-error :messages="$errors->get('payment_status')" />
                        </div>
                    </div>

                    <div class="col-sm-12 col-lg-3 mb-5">
                        <div class="form-group">
                            <x-input-label for="number" :value="__('Number')" />
                            <x-input-text id="number" name="number" type="text" :value="old('number')" />
                            <x-input-error :messages="$errors->get('number')" />
                        </div>
                    </div>

                    @if ($customers->isNotEmpty())
                        <div class="col-sm-12 col-lg-3 mb-5">
                            <div class="form-group">
                                <x-input-label for="customer" :value="__('Customer')" />
                                <x-input-select id="customer" name="customer" name="customer"
                                    data-placeholder="Select Customer">
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </x-input-select>
                                <x-input-error :messages="$errors->get('customer')" />
                            </div>
                        </div>
                    @endif

                    <div class="col-sm-12 col-lg-3 mb-5">
                        <div class="form-group">
                            <x-input-label for="company" :value="__('Company')" />
                            <x-input-text id="company" name="company" type="text" :value="old('company')" />
                            <x-input-error :messages="$errors->get('company')" />
                        </div>
                    </div>

                    <div class="col-sm text-end mb-5">
                        <div class="d-flex justify-content-md-end align-items-end h-100 gap-5">
                            <button type="reset" class="btn btn-sm btn-light-danger">
                                <i class="fas fa-minus-circle"></i> {{ __('Reset Filter') }}
                            </button>
                            <button type="submit" class="btn btn-sm btn-light-primary">
                                <i class="fas fa-filter"></i> {{ __('Filter Records') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-body">
            @include('admin.partials.data-table')
        </div>
    </div>

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
