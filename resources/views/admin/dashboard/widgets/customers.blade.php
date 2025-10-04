<div class="card card-flush h-md-100 mb-5 mb-xl-10">
    <div class="card-header pt-5">
        <div class="card-title d-flex flex-column">
            <span class="fw-bold fs-6 py-1">
                {{ __('Total Customers') }}
            </span>
            <span class="fs-2x fw-bold me-2 lh-1 ls-n2 mt-2">
                {{ number_format($total) }}
            </span>
            <div class="d-flex align-items-center mt-3">
                <span class="fw-bold fs-7 text-gray-500">
                    {{ __('Active Customers') }}
                </span>
            </div>
        </div>
    </div>
</div>
