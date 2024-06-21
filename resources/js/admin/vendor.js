"use strict";

// Set datatable defaults
if (window.GLOBAL_STATE?.ADMIN_DATATABLE_DEFAULTS) {
    $.extend(
        true,
        $.fn.dataTable.defaults,
        window.GLOBAL_STATE.ADMIN_DATATABLE_DEFAULTS
    );
}

// Class definition
var KTDatatablesServerSide = (function () {
    var datatable;

    // Get the CSRF token from the meta tag
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Configure jQuery to include the CSRF token with every AJAX request
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": csrfToken,
        },
    });

    // Private functions
    var initDatatable = function () {
        datatable = $("#kt_datatable").DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            responsive: true,
            dom: '<"top">rt<"bottom"<"d-flex flex-stack"li><"d-flex justify-content-center"p>><"clear">',
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"],
            ],
            ajax: {
                url: window.location.href + "/list",
                type: "POST",
            },
            columnDefs: [
                {
                    targets: ["id"],
                    width: 50,
                },
                {
                    targets: ["status"],
                    className: "text-md-center",
                    width: 100,
                },
                {
                    targets: ["image"],
                    orderable: false,
                    className: "text-md-center export-hidden",
                    width: 100,
                },
                {
                    targets: ["actions"],
                    width: 100,
                    orderable: false,
                    className: "text-md-end export-hidden",
                    render: (routes) => {
                        let template = `<div class="d-flex gap-3 justify-content-end">`;

                        if (routes.edit) {
                            template += `<a href="${routes.edit}" class="btn btn-sm btn-icon btn-light-primary" title="Edit"><i class="fa-solid fa-pen"></i></a>`;
                        }

                        if (routes.destroy) {
                            template += `<a href="${routes.destroy}" class="btn btn-sm btn-icon btn-light-danger" title="Delete" data-kt-docs-table-filter="delete_row"><i class="fa-solid fa-trash-can"></i></a>`;
                        }

                        template += `</div>`;

                        return template;
                    },
                },
                {
                    targets: ["email"],
                    orderable: false,
                },
            ],
        });

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        datatable.on("draw", function () {
            window.AppDataTable.initToggleToolbar();
            window.AppDataTable.toggleToolbars();
            window.AppDataTable.handleRowActionDelete(datatable);
            KTMenu.createInstances();
        });
    };

    // Public methods
    return {
        init: function () {
            initDatatable();
            window.AppDataTable.handleSearchDatatable(datatable);
            window.AppDataTable.initToggleToolbar();
            window.AppDataTable.handleRowActionDelete(datatable);
            window.AppDataTable.exportButtons(datatable);
            window.AppDataTable.handleResetForm(datatable);
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
