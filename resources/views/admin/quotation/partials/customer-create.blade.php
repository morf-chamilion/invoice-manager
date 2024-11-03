@isset($fields)
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
                    @foreach ($fields as $name => $field)
                        <div class="mb-4">
                            <x-input-label for="{{ $name }}" :value="__($field['label'])" required />
                            @if ($field['type'] != 'textarea')
                                <x-input-text id="{{ $name }}" name="{{ $name }}"
                                    type="{{ $field['type'] }}" :value="old($name)" required />
                            @else
                                <x-input-textarea id="{{ $name }}" name="{{ $name }}"
                                    type="{{ $field['type'] }}" required>{{ old($name) }}</x-input-textarea>
                            @endif
                            <x-input-error :messages="$errors->get($name)" />
                        </div>
                    @endforeach
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
@endisset
