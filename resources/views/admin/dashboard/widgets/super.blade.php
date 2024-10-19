<div class="card card-flush h-md-100 mb-5 mb-xl-10">
    <div class="card-header pt-5">
        <div class="card-title d-flex flex-column">
            <span class="fs-1 fw-bold me-2">{{ $vendors->count() }}</span>
            @isset($vendors)
                <span class="fw-bold fs-6 py-1 mt-2">
                    All Vendors
                </span>
            @endisset
        </div>
    </div>
</div>
