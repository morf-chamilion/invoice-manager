<!--begin::Danger Alert-->
<div id="kt_app_alert_container" class="app-container container-fluid">
    @if ($errors->any())
        <div class="alert alert-dismissible bg-danger d-flex flex-column flex-sm-row p-5 mb-5">
            <!--begin::Icon-->
            {!! getIcon('information-3', 'fs-2hx fs-md-1 text-light me-4 mb-5 mb-sm-0') !!}
            <!--end::Icon-->

            <!--begin::Wrapper-->
            <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                <!--begin::Title-->
                <h4 class="mb-2 text-light">{{ $errors->first() }}</h4>
                <!--end::Title-->

                <!--begin::Content-->
                <span>We've pinpointed the problems for you. Check the fields with error messages below.</span>
                <!--end::Content-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Close-->
            <button type="button"
                class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto"
                data-bs-dismiss="alert">
                <i class="ki-duotone ki-cross fs-1 text-light"><span class="path1"></span><span
                        class="path2"></span></i>
            </button>
            <!--end::Close-->
        </div>
    @endif
</div>
<!--end::Danger Alert-->

<!--begin::Success Alert-->
<div id="kt_app_alert_container" class="app-container container-fluid">
    @if (session('status'))
        <div class="alert alert-dismissible bg-success d-flex flex-column flex-sm-row p-5 mb-5 align-items-center">
            <!--begin::Icon-->
            {!! getIcon('check-square', 'fs-2hx fs-md-1 text-light me-4') !!}
            <!--end::Icon-->

            <!--begin::Wrapper-->
            <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                <!--begin::Title-->
                <h5 class="mb-0 mt-1 text-light">
                    @if (session('message'))
                        {{ session('message') }}
                    @else
                        Submission Completed Successfully
                    @endif
                </h5>
                <!--end::Title-->
            </div>
            <!--end::Wrapper-->
        </div>
    @elseif (session('status') === false)
        <div class="alert alert-dismissible bg-danger d-flex flex-column flex-sm-row p-5 mb-5 align-items-center">
            <!--begin::Icon-->
            {!! getIcon('information-3', 'fs-2hx fs-md-1 text-light me-4') !!}
            <!--end::Icon-->

            <!--begin::Wrapper-->
            <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                <!--begin::Title-->
                <h5 class="mb-0 mt-1 text-light">
                    @if (session('message'))
                        {{ session('message') }}
                    @else
                        Submission Failed
                    @endif
                </h5>
                <!--end::Title-->
            </div>
            <!--end::Wrapper-->
        </div>
    @endif
</div>
<!--end::Success Alert-->

<!--begin::Toast-->
<div class="position-fixed bottom-0 end-0 p-3 z-index-3">
    <div id="kt_app_toast" class="toast align-items-center text-bg-danger text-light border-0" role="alert"
        aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>
<!--end::Toast-->
