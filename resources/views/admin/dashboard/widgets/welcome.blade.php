<div class="card card-flush h-md-100 mb-5 mb-xl-10">
    <div class="card-header pt-5">
        <div class="card-title d-flex flex-column">
            <span class="fs-1 fw-bold me-2">Welcome {{ str()->of(auth()->user()->name)->title() }}</span>
            @foreach (auth()->user()->getRoleNames() as $role)
                <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 mt-2">
                    {{ $role }}
                </span>
            @endforeach
        </div>
    </div>
</div>
