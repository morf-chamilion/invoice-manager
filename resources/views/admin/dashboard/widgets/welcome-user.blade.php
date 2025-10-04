<div class="card card-flush h-md-100 mb-5 mb-xl-10">
    <div class="card-header pt-5">
        <div class="card-title d-flex flex-column">
            <span class="fs-2 fw-bold me-2">Welcome {{ str()->of(auth()->user()->name)->title() }}</span>
            <span class="fw-bold fs-5 text-gray-700 mt-3">
                {{ now()->format('h:i A') }} {{ now()->format('jS F Y ') }}
            </span>
            <div class="d-flex align-items-center mt-3">
                @foreach (auth()->user()->getRoleNames() as $role)
                    <span class="fw-bold fs-7 text-success">
                        {{ $role }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
