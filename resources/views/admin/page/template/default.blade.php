@extends('admin.page.edit')

@section('template')
    <div class="card mt-8">
        <div class="card-body">
            <div class="mb-8">
                <x-input-label for="page_content" :value="__('Page Content')" />
                <x-input-editor name="page_content" id="page_content">
                    {{ old('page_content', $settings->get('page_content')) }}
                </x-input-editor>
                <x-input-error :messages="$errors->get('page_content')" />
            </div>
        </div>
    </div>
@endsection
