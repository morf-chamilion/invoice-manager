<input type="file" id="{{ $id }}" name="{{ $name }}"
    {{ $attributes->merge(['class' => $attributes->get('multiple') ? 'pond-multiple' : 'pond-single'])->except('value') }} />

@push('footer')
    <script>
        window.AppFilePond.create(
            '{{ $id }}',
            @json(is_array($value) ? $value : ($value ? $value->toArray() : null)),
            {{ $fileMaxSize ?? 'undefined' }},
            {{ $fileMaxCount ?? 'undefined' }},
            {{ $mimeType ?? 'undefined' }}
        );
    </script>
@endpush
