@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} @if (!$attributes->get('rows')) style="height: 100px;" @endif
    {!! $attributes->merge(['class' => 'form-control bg-transparent']) !!}>{{ $slot }}</textarea>
