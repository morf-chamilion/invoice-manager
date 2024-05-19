@props(['value'])

<label
    {{ $attributes->merge(['class' => 'form-label' . (isset($attributes['required']) ? ' required' : '')]) }}>{{ $value ?? $slot }}</label>
