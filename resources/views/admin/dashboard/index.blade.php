<x-default-layout>

    <div class="row g-xl-10 mb-5 mb-xl-10">
        <div class="col-lg-3 mb-8">
            @include('admin/dashboard/widgets/welcome')
        </div>

        <div class="col-lg-2 mb-8">
            @include('admin/dashboard/widgets/users', ['users' => $users])
        </div>

        <div class="col-lg-4 mb-8">
            @include('admin/dashboard/widgets/time')
        </div>

        <div class="col-lg-3 mb-8">
            @include('admin/dashboard/widgets/application')
        </div>
    </div>

</x-default-layout>
