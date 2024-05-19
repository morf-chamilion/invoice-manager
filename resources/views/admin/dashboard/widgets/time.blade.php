<div class="card card-flush h-md-100 mb-5 mb-xl-10">
    <div class="card-header pt-5">
        <div class="card-title d-flex flex-column">
            <span class="fs-2x fw-bold me-2 lh-1 ls-n2">
                {{ now()->format('h:i A') }} <span class="fs-2 fw-bold text-muted">{{ now()->format('jS F Y ') }}</span>
            </span>
            <span class="badge badge-light-warning fw-bold fs-8 px-2 py-1 mt-3">
                Timezone {{ config('app.timezone') }}
            </span>
        </div>
    </div>
</div>
