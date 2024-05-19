@props(['name', 'id', 'required' => false, 'attributes' => []])

<select name="{{ $name }}" id="{{ $id ?? $name }}"
    {{ $attributes->merge(['class' => 'form-select', 'data-control' => $attributes->get('data-has-icon') === 'true' ? ' select2-icon' : 'select2', 'required' => $required]) }}
    data-allow-clear="true">
    <option></option>
    {{ $slot }}
</select>
