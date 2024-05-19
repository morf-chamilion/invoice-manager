/**
 * Datatable JS Class.
 *
 * Handles data table rendering lifecycle.
 */
class DataTable {
    constructor() {
        this.boot();
    }

    /**
     * Boot manager.
     */
    boot() {
        this.init();
    }

    /**
     * Listen and handle input check behaviour.
     */
    init() {
        window.GLOBAL_STATE.ADMIN_DATATABLE_DEFAULTS = {
            language: {
                info: "Showing _START_ to _END_ of _TOTAL_ records",
                infoEmpty: "Showing no records",
                lengthMenu: "_MENU_",
                processing:
                    '<span class="spinner-border w-15px h-15px text-muted align-middle me-2"></span> <span class="text-gray-600">Loading...</span>',
                paginate: {
                    first: '<i class="first"></i>',
                    last: '<i class="last"></i>',
                    next: '<i class="next"></i>',
                    previous: '<i class="previous"></i>',
                },
            },
        };
    }

    /**
     * Handle resource deletion base on user interactions.
     *
     * @param {jQuery} datatable Datable element
     */
    handleRowActionDelete(datatable) {
        // Select all delete buttons
        const deleteButtons = document.querySelectorAll(
            '[data-kt-docs-table-filter="delete_row"]'
        );

        deleteButtons.forEach((d) => {
            // Delete button on click
            d.addEventListener("click", function (e) {
                e.preventDefault();

                const parent = e.target.closest("tr");
                const resourceId = parent.querySelectorAll("td")[0].innerText;
                const resourceName = parent.querySelectorAll("td")[1].innerText;

                Swal.fire({
                    text:
                        "Are you sure you want to delete " + resourceName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-light-primary",
                    },
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: `${window.location.href}/${resourceId}`,
                            beforeSend: function (xhr, options) {
                                Swal.fire({
                                    text: "Deleting..",
                                    icon: "warning",
                                    showConfirmButton: false,
                                });

                                setTimeout(function () {
                                    $.ajax(
                                        $.extend(options, {
                                            beforeSend: $.noop,
                                        })
                                    );
                                }, 1000);

                                return false;
                            },
                            success: function (response) {
                                if (response["status"]) {
                                    Swal.fire(
                                        "Success",
                                        response["message"],
                                        "success"
                                    );
                                } else {
                                    Swal.fire(
                                        "Error",
                                        response["message"],
                                        "error"
                                    );
                                }
                            },
                            error: function ({ responseJSON }) {
                                Swal.fire(
                                    "Failed to Delete",
                                    responseJSON["message"],
                                    "error"
                                );
                            },
                            complete: function () {
                                datatable.draw();
                            },
                        });
                    }
                });
            });
        });
    }

    /**
     * Initialize Toggle Toolber.
     */
    initToggleToolbar() {
        // Toggle selected action toolbar
        // Select all checkboxes
        const container = document.querySelector("#kt_datatable");
        const checkboxes = container.querySelectorAll('[type="checkbox"]');

        // Select elements
        const deleteSelected = document.querySelector(
            '[data-kt-docs-table-select="delete_selected"]'
        );

        // Toggle delete selected toolbar
        checkboxes.forEach((c) => {
            // Checkbox on click event
            c.addEventListener("click", function () {
                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });
    }

    /**
     * Toggle Toolbars.
     */
    toggleToolbars() {
        const container = document.querySelector("#kt_datatable");
        const toolbarBase = document.querySelector(
            '[data-kt-docs-table-toolbar="base"]'
        );

        const toolbarSelected = document.querySelector(
            '[data-kt-docs-table-toolbar="selected"]'
        );

        const selectedCount = document.querySelector(
            '[data-kt-docs-table-select="selected_count"]'
        );

        // Select refreshed checkbox DOM elements
        const allCheckboxes = container.querySelectorAll(
            'tbody [type="checkbox"]'
        );

        // Detect checkboxes state & count
        let checkedState = false;
        let count = 0;

        // Count checked boxes
        allCheckboxes.forEach((c) => {
            if (c.checked) {
                checkedState = true;
                count++;
            }
        });

        if (checkedState) {
            selectedCount.innerHTML = count;
            toolbarBase.classList.add("d-none");
            toolbarSelected.classList.remove("d-none");
        } else {
            toolbarBase.classList.remove("d-none");
            toolbarSelected.classList.add("d-none");
        }
    }

    /**
     * Handle Search.
     *
     * @docs reference: https://datatables.net/reference/api/search()
     *
     * @param {jQuery} datatable Datable element
     */
    handleSearchDatatable(datatable) {
        const defaultFilter = document.querySelector("#kt_datatable_filter");
        defaultFilter?.classList.add("d-none");

        const filterSearch = document.querySelector(
            '[data-kt-docs-table-filter="search"]'
        );

        filterSearch.addEventListener("keyup", function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    /**
     * Reset filter.
     *
     * @param {jQuery} datatable Datable element
     */
    handleResetForm(datatable) {
        // Select reset button
        const resetButton = document.querySelector(
            '[data-kt-docs-table-filter="reset"]'
        );

        // Reset datatable
        resetButton?.addEventListener("click", function () {
            // Reset datatable --- official docs reference: https://datatables.net/reference/api/search()
            datatable.search("").draw();
        });
    }

    /**
     * Export buttons.
     *
     * @param {jQuery} datatable Datable element
     */
    exportButtons(datatable) {
        new $.fn.dataTable.Buttons(datatable, {
            buttons: [
                {
                    extend: "copyHtml5",
                    exportOptions: {
                        columns: ":not(.export-hidden)",
                    },
                },
                {
                    extend: "excelHtml5",
                    exportOptions: {
                        columns: ":not(.export-hidden)",
                    },
                },
                {
                    extend: "csvHtml5",
                    exportOptions: {
                        columns: ":not(.export-hidden)",
                    },
                },
                {
                    extend: "pdfHtml5",
                    exportOptions: {
                        columns: ":not(.export-hidden)",
                    },
                },
            ],
        })
            .container()
            .appendTo($("#kt_datatable_buttons"));

        // Hook dropdown menu click event to datatable export buttons
        const exportButtons = document.querySelectorAll(
            "#kt_datatable_export_menu [data-kt-export]"
        );
        exportButtons.forEach((exportButton) => {
            exportButton.addEventListener("click", (e) => {
                e.preventDefault();

                // Get clicked export value
                const exportValue = e.target.getAttribute("data-kt-export");
                const target = document.querySelector(
                    ".dt-buttons .buttons-" + exportValue
                );

                // Trigger click event on hidden datatable export buttons
                target.click();
            });
        });
    }
}

// Initialize
const appDatatable = new DataTable();

window.AppDataTable = appDatatable;
