@props(['name', 'settings', 'title' => false, 'context' => false])

<!--begin::Accordion Card-->
<div class="mb-5 card">

    <!--begin::Header-->
    <div class="card-header justify-content-start align-items-center cursor-pointer collapsed" data-bs-toggle="collapse"
        data-bs-target="#kt_accordion_{{ $name }}">
        <span class="accordion-icon">
            <i class="ki-duotone ki-arrow-right fs-4"><span class="path1"></span><span class="path2"></span></i>
        </span>
        <h3 class="fs-4 fw-semibold mb-0 mx-4">
            @if ($title)
                {{ $title }}
            @else
                {{ str()->of($name)->replace('_', ' ')->title() }}
            @endif
        </h3>
        @if ($context)
            <span class="text-muted fw-semibold font-size-sm">({{ $context }})</span>
        @endif
    </div>
    <!--end::Header-->

    <!--begin::Body-->
    <div id="kt_accordion_{{ $name }}" class="fs-6 collapse px-10 pt-10" data-bs-parent="#kt_accordion_mail">

        @php $fieldName = __($name . '_subject') @endphp
        <div class="mb-8">
            <x-input-label for="{{ $fieldName }}" :value="__('Subject')" />
            <x-input-text id="{{ $fieldName }}" name="{{ $fieldName }}" type="text" :value="old($fieldName, $settings->get($fieldName))" />
            <x-input-error class="mt-2" :messages="$errors->get($fieldName)" />
        </div>

        @php $fieldName = __($name . '_template') @endphp
        <div class="mb-8">
            <x-input-label for="{{ $fieldName }}" :value="__('Body')" />
            <x-input-editor name="{{ $fieldName }}" id="{{ $fieldName }}">
                {{ old($fieldName, $settings->get($fieldName)) }}
            </x-input-editor>
            <x-input-error class="mt-2" :messages="$errors->get($fieldName)" />
            {{ $slot }}
        </div>
    </div>
    <!--end::Body-->

</div>
<!--end::Accordion Card-->
