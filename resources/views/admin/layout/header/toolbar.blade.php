<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <!--begin::Toolbar container-->
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <!--begin::Breadcrumb-->
            {{ isset($model) ? Breadcrumbs::render(Route::currentRouteName(), $model) : Breadcrumbs::render(Route::currentRouteName()) }}
            <!--end::Breadcrumb-->
        </div>
        <!--end::Page title-->

        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @if (!empty($pageData['frontUrl']))
                <a href="{{ $pageData['frontUrl'] }}" target="_blank" class="btn btn-sm btn-dark">View Page</a>
            @endif
        </div>
        <!--end::Actions-->

    </div>
    <!--end::Toolbar container-->
</div>
<!--end::Toolbar-->
