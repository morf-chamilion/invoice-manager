@props(['name', 'id', 'value', 'label', 'required' => false, 'checked' => false, 'attributes' => []])

<div {{ $attributes->merge(['class' => 'form-check form-check-custom mt-2', 'required' => $required]) }}>
    <input class="form-check-input" type="radio" value="{{ $value }}" id="{{ $value }}"
        name="{{ $name }}" {{ $checked ? 'checked' : '' }} />
    <label class="form-check-label" for="{{ $value }}">
        {{ $label }}
    </label>
</div>
