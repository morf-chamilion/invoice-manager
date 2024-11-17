<div class="modal fade" tabindex="-1" id="customer_create">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    {{ __('Create & Assign New Customer') }}
                </h3>

                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body">
                <div class="mb-4">
                    <x-input-label for="customer_name" :value="__('Name')" required />
                    <x-input-text id="customer_name" name="customer_name" type="text" :value="old('customer_name')" required />
                    <x-input-error :messages="$errors->get('customer_name')" />
                </div>

                <div class="mb-4">
                    <x-input-label for="customer_email" :value="__('Email')" required />
                    <x-input-text id="customer_email" name="customer_email" type="email" :value="old('customer_email')" required />
                    <x-input-error :messages="$errors->get('customer_email')" />
                </div>

                <div class="mb-4">
                    <x-input-label for="customer_phone" :value="__('Phone')" />
                    <x-input-text id="customer_phone" name="customer_phone" type="text" :value="old('customer_phone')" />
                    <x-input-error :messages="$errors->get('customer_phone')" />
                </div>

                <div class="mb-4">
                    <x-input-label for="customer_address" :value="__('Address')" />
                    <x-input-textarea id="customer_address"
                        name="customer_address">{{ old('customer_address') }}</x-input-textarea>
                    <x-input-error :messages="$errors->get('customer_address')" />
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    {{ __('Close') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    {{ __('Create') }}
                </button>
            </div>
        </div>
    </div>
</div>
