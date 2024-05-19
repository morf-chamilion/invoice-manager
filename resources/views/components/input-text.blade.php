@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => !empty($attributes->get('readonly')) ? 'form-control form-control-solid' : 'form-control',
]) !!}>
