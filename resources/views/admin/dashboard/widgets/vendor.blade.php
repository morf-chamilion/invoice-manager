<div class="card card-flush h-md-100 mb-5 mb-xl-10">
    <div class="card-header pt-5">
        <div class="card-title d-flex flex-column">
            @if (auth()->user()->vendor?->id)
                <span class="fs-1 fw-bold me-2">{{ str()->of(auth()->user()->vendor->name)->title() }}</span>
                <span class="badge badge-light-info fw-bold fs-8 px-2 py-1 mt-3">
                    Currency: {{ auth()->user()->vendor->currency }}
                </span>
            @endif
        </div>
    </div>
</div>
