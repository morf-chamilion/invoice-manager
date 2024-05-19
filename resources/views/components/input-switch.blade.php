@props(['name', 'id', 'value', 'required' => false, 'attributes' => []])

<div
    {{ $attributes->merge(['class' => 'form-check form-switch form-check-custom form-check-solid', 'required' => $required]) }}>
    <input type='hidden' value="0" name="{{ $name }}">
    <input class="form-check-input @if ($attributes->get('visualElement')) checkbox-visual-toggle @endif" type="checkbox"
        value="1" id="{{ $id }}" name="{{ $name }}"
        data-visual-element="{{ $attributes->get('visualElement') }}"
        @isset($value) @checked($value) @endisset />
</div>
