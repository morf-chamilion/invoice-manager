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
    <div id="kt_accordion_{{ $name }}" class="fs-6 collapse px-10 pt-10" data-bs-parent="#kt_accordion_setting">
        {{ $slot }}
    </div>
    <!--end::Body-->

</div>
<!--end::Accordion Card-->
