<!--begin::Wrapper-->
<div class="d-flex flex-wrap justify-content-between gap-5 mb-5">
    <!--begin::Search-->
    <div class="d-flex align-items-center position-relative">
        {!! getIcon('magnifier', 'ki-duotone fs-1 position-absolute ms-6') !!}
        <input type="text" data-kt-docs-table-filter="search" class="form-control form-control-solid w-250px ps-15"
            placeholder="Search" />
    </div>
    <!--end::Search-->

    <!--begin::Toolbar-->
    <div class="d-flex justify-content-md-end gap-5" data-kt-docs-table-toolbar="base">

        <button type="button" class="btn btn-light-primary" data-kt-menu-trigger="click"
            data-kt-menu-placement="bottom-end">
            <i class="ki-duotone ki-exit-down fs-2"><span class="path1"></span><span class="path2"></span></i>
            Export
        </button>

        <div id="kt_datatable_export_menu"
            class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4"
            data-kt-menu="true">
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <a href="#" class="menu-link px-3" data-kt-export="copy">
                    Copy to clipboard
                </a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <a href="#" class="menu-link px-3" data-kt-export="excel">
                    Export as Excel
                </a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <a href="#" class="menu-link px-3" data-kt-export="csv">
                    Export as CSV
                </a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <a href="#" class="menu-link px-3" data-kt-export="pdf">
                    Export as PDF
                </a>
            </div>
            <!--end::Menu item-->
        </div>

        <!--begin::default export buttons-->
        <div id="kt_datatable_buttons" class="d-none"></div>
        <!--end::default export buttons-->

        <!--begin::Add customer-->
        <a href="{{ $create }}" class="btn btn-primary" data-bs-toggle="tooltip">
            <i class="ki-duotone ki-plus fs-2"></i>
            {{ $pageData['createTitle'] }}
        </a>
        <!--end::Add customer-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Group actions-->
    <div class="d-flex justify-content-end align-items-center d-none" data-kt-docs-table-toolbar="selected">
        <div class="fw-bold me-5">
            <span class="me-2" data-kt-docs-table-select="selected_count"></span> Selected
        </div>

        <button type="button" class="btn btn-danger" data-bs-toggle="tooltip">
            Selection Action
        </button>
    </div>
    <!--end::Group actions-->
</div>
<!--end::Wrapper-->

<!--begin::Datatable-->
<table id="kt_datatable" class="table table-bordered align-middle table-row-dashed dataTable fs-6 gy-2">
    <thead>
        <tr class="fw-semibold fs-6 text-muted">
            @foreach ($columnNames as $columnName)
                <th class="{{ $columnName }}">{{ str_replace(['_', ' Id'], ' ', str($columnName)->title) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold"></tbody>
</table>
<!--end::Datatable-->
