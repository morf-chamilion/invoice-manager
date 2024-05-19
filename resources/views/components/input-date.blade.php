<input {!! $attributes->merge(['class' => 'datetime form-control']) !!} name="{{ $name }}" id="{{ $id }}"
    @if (!empty($multiple)) data-type="multiple" @else data-type="single" @endif
    @if (!empty($value)) value="{{ $value }}" @endif>
