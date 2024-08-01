<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true"
        data-kt-scroll-activate="true" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
        data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">

        <!--begin::Menu-->
        <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu" data-kt-menu="true"
            data-kt-menu-expand="false">

            @canany([AdminRoutePath::DASHBOARD])
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    @include('admin.layout.sidebar.menu-item-group', [
                        'content' => 'Dashboards',
                        'icon' => 'element-11',
                    ])

                    <div class="menu-sub menu-sub-accordion">
                        @can(AdminRoutePath::DASHBOARD)
                            @include('admin.layout.sidebar.menu-item', [
                                'content' => 'Home',
                                'route' => route(AdminRoutePath::DASHBOARD),
                            ])
                        @endcan
                    </div>
                </div>
            @endcan

            @canany([InvoiceRoutePath::INDEX, InvoiceRoutePath::CREATE])
                @include('admin.layout.sidebar.menu-heading', ['content' => 'Invoices'])
                @php $invoiceService = app()->make(App\Services\InvoiceService::class); @endphp

                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    @include('admin.layout.sidebar.menu-item-group', [
                        'content' => 'Invoices',
                        'icon' => 'book',
                        'count' => $invoiceService->dueInvoiceCount(),
                    ])

                    <div class="menu-sub menu-sub-accordion">
                        @can(InvoiceRoutePath::INDEX)
                            @include('admin.layout.sidebar.menu-item', [
                                'content' => 'View All Invoices',
                                'route' => route(InvoiceRoutePath::INDEX),
                            ])
                        @endcan

                        @can(InvoiceRoutePath::CREATE)
                            @include('admin.layout.sidebar.menu-item', [
                                'content' => 'Create New Invoice',
                                'route' => route(InvoiceRoutePath::CREATE),
                            ])
                        @endcan
                    </div>
                </div>
            @endcanany

            @canany([VendorRoutePath::INDEX, VendorRoutePath::CREATE])
                @include('admin.layout.sidebar.menu-heading', ['content' => 'Organizations'])

                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    @include('admin.layout.sidebar.menu-item-group', [
                        'content' => 'Vendors',
                        'icon' => 'shop',
                    ])

                    <div class="menu-sub menu-sub-accordion">
                        @can(VendorRoutePath::INDEX)
                            @include('admin.layout.sidebar.menu-item', [
                                'content' => 'View All Vendors',
                                'route' => route(VendorRoutePath::INDEX),
                            ])
                        @endcan

                        @can(VendorRoutePath::CREATE)
                            @include('admin.layout.sidebar.menu-item', [
                                'content' => 'Create New Vendors',
                                'route' => route(VendorRoutePath::CREATE),
                            ])
                        @endcan
                    </div>
                </div>
            @endcanany

            @canany([CustomerRoutePath::INDEX, CustomerRoutePath::CREATE])
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    @include('admin.layout.sidebar.menu-item-group', [
                        'content' => 'Customers',
                        'icon' => 'people',
                    ])

                    <div class="menu-sub menu-sub-accordion">
                        @can(CustomerRoutePath::INDEX)
                            @include('admin.layout.sidebar.menu-item', [
                                'content' => 'View All Customers',
                                'route' => route(CustomerRoutePath::INDEX),
                            ])
                        @endcan

                        @can(CustomerRoutePath::CREATE)
                            @include('admin.layout.sidebar.menu-item', [
                                'content' => 'Create New Customer',
                                'route' => route(CustomerRoutePath::CREATE),
                            ])
                        @endcan
                    </div>
                </div>
            @endcanany

            @canany([UserRoutePath::INDEX, UserRoutePath::CREATE])
                @include('admin.layout.sidebar.menu-heading', ['content' => 'Admin Users'])

                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    @include('admin.layout.sidebar.menu-item-group', [
                        'content' => 'Users',
                        'icon' => 'user-square',
                    ])

                    <div class="menu-sub menu-sub-accordion">
                        @can(UserRoutePath::INDEX)
                            @include('admin.layout.sidebar.menu-item', [
                                'content' => 'View All Users',
                                'route' => route(UserRoutePath::INDEX),
                            ])
                        @endcan

                        @can(UserRoutePath::CREATE)
                            @include('admin.layout.sidebar.menu-item', [
                                'content' => 'Create New User',
                                'route' => route(UserRoutePath::CREATE),
                            ])
                        @endcan
                    </div>
                </div>
            @endcanany

            @canany([UserRoleRoutePath::INDEX, UserRoleRoutePath::CREATE])
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    @include('admin.layout.sidebar.menu-item-group', [
                        'content' => 'User Roles',
                        'icon' => 'shield',
                    ])

                    <div class="menu-sub menu-sub-accordion">
                        @can(UserRoleRoutePath::INDEX)
                            @include('admin.layout.sidebar.menu-item', [
                                'content' => 'View All Roles',
                                'route' => route(UserRoleRoutePath::INDEX),
                            ])
                        @endcan

                        @can(UserRoleRoutePath::CREATE)
                            @include('admin.layout.sidebar.menu-item', [
                                'content' => 'Create New Role',
                                'route' => route(UserRoleRoutePath::CREATE),
                            ])
                        @endcan
                    </div>
                </div>
            @endcanany

            @if (false)
                @canany([PageSettingRoutePath::HOME])
                    @include('admin.layout.sidebar.menu-heading', ['content' => 'Pages'])

                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                        @include('admin.layout.sidebar.menu-item-group', [
                            'content' => 'Pages',
                            'icon' => 'some-files',
                        ])

                        <div class="menu-sub menu-sub-accordion">
                            @can(PageSettingRoutePath::HOME)
                                @include('admin.layout.sidebar.menu-item', [
                                    'content' => 'Home',
                                    'route' => route(PageSettingRoutePath::HOME),
                                ])
                            @endcan
                        </div>
                    </div>
                @endcanany

                @canany([PageRoutePath::INDEX, PageRoutePath::CREATE])
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                        @include('admin.layout.sidebar.menu-item-group', [
                            'content' => 'Content Pages',
                            'icon' => 'some-files',
                        ])

                        <div class="menu-sub menu-sub-accordion">
                            @can(PageRoutePath::INDEX)
                                @include('admin.layout.sidebar.menu-item', [
                                    'content' => 'View All Content Pages',
                                    'route' => route(PageRoutePath::INDEX),
                                ])
                            @endcan

                            @can(PageRoutePath::CREATE)
                                @include('admin.layout.sidebar.menu-item', [
                                    'content' => 'Create New Content Page',
                                    'route' => route(PageRoutePath::CREATE),
                                ])
                            @endcan
                        </div>
                    </div>
                @endcanany
            @endif

            @canany([SettingRoutePath::GENERAL, SettingRoutePath::MAIL, SettingRoutePath::INVOICE,
                VendorRoutePath::INVOICE_SETTING_EDIT])
                @include('admin.layout.sidebar.menu-heading', ['content' => 'Settings'])

                @can(SettingRoutePath::GENERAL)
                    @include('admin.layout.sidebar.menu-item', [
                        'content' => 'General Settings',
                        'route' => route(SettingRoutePath::GENERAL),
                        'icon' => 'color-swatch',
                    ])
                @endcan

                @can(SettingRoutePath::MAIL)
                    @include('admin.layout.sidebar.menu-item', [
                        'content' => 'Mail Settings',
                        'route' => route(SettingRoutePath::MAIL),
                        'icon' => 'sms',
                    ])
                @endcan

                @can(VendorRoutePath::INVOICE_SETTING_EDIT)
                    @if (auth()->user()->vendor)
                        @include('admin.layout.sidebar.menu-item', [
                            'content' => 'Invoice Settings',
                            'route' => route(VendorRoutePath::INVOICE_SETTING_EDIT, auth()->user()->vendor),
                            'icon' => 'update-file',
                        ])
                    @endif
                @endcan
            @endcanany

        </div>
        <!--end::Menu-->
    </div>
</div>
