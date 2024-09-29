<div class="card card-flush h-md-100 mb-5 mb-xl-10">
    <div class="card-header pt-5">
        <div class="card-title d-flex flex-column">
            <span class="fs-2x fw-bold me-2 lh-1 ls-n2">
                @if (auth()->user()->vendor)
                    {{ auth()->user()->vendor->count() }}
                @else
                    {{ $users->count() }}
                @endif
            </span>
            <span class="fw-bold fs-6 py-1 mt-2">
                Active Users
            </span>
        </div>
    </div>
</div>
