@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-danger validation-message']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
