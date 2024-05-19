<!--begin::Navbar-->
<div class="app-navbar flex-shrink-0">
    <!--begin::Theme mode-->
    <div class="app-navbar-item ms-1 ms-md-3">
        @include('admin.layout.theme-mode.main')
    </div>
    <!--end::Theme mode-->
    <!--begin::User menu-->
    <div class="app-navbar-item ms-1 ms-md-3" id="kt_header_user_menu_toggle">
        <!--begin::Menu wrapper-->
        <div class="cursor-pointer symbol symbol-30px symbol-md-40px"
            data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
            data-kt-menu-placement="bottom-end">
            <img src="{{ image('svg/avatars/blank.svg') }}" class="theme-light-show" alt="user" />
            <img src="{{ image('svg/avatars/blank-dark.svg') }}" class="theme-dark-show" alt="user" />
        </div>

        <!--begin::User account menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
            data-kt-menu="true">
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <div class="menu-content d-flex align-items-center px-3">
                    <!--begin::Username-->
                    <div class="d-flex flex-column">
                        <div class="fw-bold d-flex align-items-center fs-5 text-dark">{{ auth()->user()->name }}
                            @foreach (auth()->user()->getRoleNames() as $role)
                                <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">
                                    {{ $role }}
                                </span>
                            @endforeach
                        </div>
                        <span class="fw-semibold text-muted fs-7">{{ auth()->user()->email }}</span>
                    </div>
                    <!--end::Username-->
                </div>
            </div>

            <!--end::Menu item-->
            <!--begin::Menu separator-->
            <div class="separator my-2"></div>
            <!--end::Menu separator-->

            <!--begin::Menu item-->
            <div class="menu-item px-5 my-1">
                <a href="{{ route(AdminRoutePath::PROFILE_EDIT) }}" class="menu-link px-5">Profile Settings</a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu separator-->
            <div class="separator my-2"></div>
            <!--end::Menu separator-->

            <!--begin::Menu item-->
            <div class="menu-item px-5">
                <form action="{{ route(AdminAuthRoutePath::LOGOUT) }}" method="POST">
                    @method('post')
                    @csrf
                    <button class="btn btn-light-danger w-100 px-5">Sign Out</button>
                </form>

            </div>
            <!--end::Menu item-->
        </div>
        <!--end::User account menu-->

        <!--end::Menu wrapper-->
    </div>
    <!--end::User menu-->
    <!--begin::Header menu toggle-->
    <div class="app-navbar-item d-lg-none ms-2 me-n3" title="Show header menu">
        <div class="btn btn-icon btn-active-color-primary w-30px h-30px w-md-35px h-md-35px"
            id="kt_app_header_menu_toggle">{!! getIcon('text-align-left', 'fs-2 fs-md-1') !!}</div>
    </div>
    <!--end::Header menu toggle-->
</div>
<!--end::Navbar-->
