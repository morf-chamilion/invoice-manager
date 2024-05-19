@props(['name', 'id', 'disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'tinymce-editor tox-target']) !!} name="{{ $name }}" id="{{ $id }}">{{ $slot }}</textarea>
