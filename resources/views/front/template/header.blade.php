<header class="main-header bg-dark">
    <div class="container">
        <div class="logo-wrapper">
            <a href="{{ route(FrontPageRoutePath::HOME) }}">
                @if ($siteLogo = settings(SettingModule::GENERAL)->getFirstMedia('site_logo'))
                    <img src="{{ $siteLogo?->getFullUrl() }}"
                        alt="{{ settings(SettingModule::GENERAL)->get('site_name') }}" />
                @endif
                <script>
                    const SITE_LOGO = '{{ settings(SettingModule::GENERAL)->getFirstMedia('site_logo')?->getFullUrl() }}';
                </script>
            </a>
        </div>
        <div class="menu-wrapper">
            <div class="page-menu">
                @if (false)
                    <nav class="navbar navbar-expand-md p-0" id="menu">
                        <div id="primaryNav">
                            <ul>
                                <li>
                                    <a href="{{ route(FrontPageRoutePath::HOME) }}">
                                        {{ __('Home') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                @endif
                <div class="menu-icon">
                    <a href="#primaryNav">
                        <i class="fa-solid fa-bars"></i>
                    </a>
                </div>
            </div>
            <div class="account-wrapper">
                <button type="button" class="btn btn-link btn-color-light" data-kt-menu-trigger="hover"
                    data-kt-menu-placement="bottom-start" data-kt-menu-offset="0px, 10px">
                    {{ CustomerServiceProvider::getAuthUser() ? CustomerServiceProvider::getAuthUser()->name : __('Account') }}
                    <span class="svg-icon fs-3 rotate-180 ms-3 me-0">
                        <i class="fas fa-chevron-down"></i>
                    </span>
                </button>

                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-auto min-w-200 mw-300px py-3"
                    data-kt-menu="true">

                    @if (CustomerServiceProvider::getAuthUser())
                        <div class="menu-item px-3">
                            <a href="{{ route(FrontCustomerRoutePath::EDIT) }}"
                                class="btn btn-light-secondary text-dark btn-sm px-4 w-100">
                                {{ __('Profile Settings') }}
                            </a>
                        </div>

                        <div class="separator my-3"></div>

                        <div class="menu-item px-3">
                            <form action="{{ route(FrontAuthRoutePath::LOGOUT) }}" method="POST" id="logout">
                                @csrf

                                <button class="btn btn-light-danger btn-sm px-4 w-100">
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="menu-item px-3 pb-2">
                            <a href="{{ route(FrontAuthRoutePath::LOGIN) }}"
                                class="btn btn-light-secondary text-dark btn-sm px-4 w-100">
                                {{ __('Login') }}
                            </a>
                        </div>

                        <div class="menu-item px-3">
                            <a href="{{ route(FrontAuthRoutePath::REGISTER) }}"
                                class="btn btn-light-secondary text-dark btn-sm px-4 w-100">
                                {{ __('Create New Account') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>
