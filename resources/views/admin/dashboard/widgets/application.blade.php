<div class="card card-flush h-md-100 mb-5 mb-xl-10">
    <div class="card-header pt-5">
        <div class="card-title d-flex flex-column">
            <span class="fs-1 fw-bold me-2">
                Laravel <span class="fs-4 fw-bolder text-muted">v{{ app()->version() }}</span>
            </span>
            <span class="badge badge-light-dark fw-bold fs-8 px-2 py-1 mt-2">
                {{ app()->environment() }}
            </span>
        </div>
    </div>
</div>
