@props(['disabled' => false, 'style' => 'height: 100px;'])

<textarea {{ $disabled ? 'disabled' : '' }} @if (!$attributes->get('rows')) style="{{ $style }}" @endif
    {!! $attributes->merge(['class' => 'form-control bg-transparent']) !!}>{{ $slot }}</textarea>
