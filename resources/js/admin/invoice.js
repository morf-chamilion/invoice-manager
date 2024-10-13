"use strict";

// Set datatable defaults
if (window.GLOBAL_STATE?.ADMIN_DATATABLE_DEFAULTS) {
    $.extend(
        true,
        $.fn.dataTable.defaults,
        window.GLOBAL_STATE.ADMIN_DATATABLE_DEFAULTS
    );
}

// Filter input states.
let dateStart = "",
    dateEnd = "",
    status = "",
    paymentStatus = "",
    number = "",
    customer = "",
    company = "";

let datatable;

// Class definition
var KTDatatablesServerSide = (function () {
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
            order: [[0, "desc"]], // Show latest first
            columnDefs: [
                {
                    targets: ["id"],
                    width: 50,
                },
                {
                    targets: ["total_price"],
                    className: "text-end pe-5",
                },
                {
                    targets: ["status"],
                    className: "text-md-center",
                    width: 100,
                },
                {
                    targets: ["payment_status"],
                    className: "text-md-center",
                    width: 130,
                },
                {
                    targets: ["image"],
                    orderable: false,
                    className: "text-md-center",
                    width: 100,
                },
                {
                    targets: ["actions"],
                    width: 150,
                    orderable: false,
                    className: "text-md-end export-hidden",
                    render: (items) => {
                        let template = `<div class="d-flex gap-3 justify-content-end">`;

                        if (items.overdue) {
                            template += `<button id="overdue" type="button" class="btn btn-sm btn-icon btn-danger" title="Overdue Notification" data-timestamp="${items.data.overdueSentAt}"><i class="fa-solid fa-bell"></i></button>`;
                        }

                        if (items.show) {
                            template += `<a href="${items.show}" class="btn btn-sm btn-icon btn-light-dark" title="Show"><i class="fa-solid fa-eye"></i></a>`;
                        }

                        if (items.edit) {
                            template += `<a href="${items.edit}" class="btn btn-sm btn-icon btn-light-primary" title="Edit"><i class="fa-solid fa-pen"></i></a>`;
                        }

                        if (items.destroy) {
                            template += `<a href="${items.destroy}" class="btn btn-sm btn-icon btn-light-danger" title="Delete" data-kt-docs-table-filter="delete_row"><i class="fa-solid fa-trash-can"></i></a>`;
                        }

                        template += `</div>`;

                        return template;
                    },
                },
            ],
            ajax: {
                url: `${window.location.href}/list`,
                type: "POST",
                data: function (data) {
                    data._token = $('meta[name="csrf-token"]').attr("content");
                    data.date_start = dateStart;
                    data.date_end = dateEnd;
                    data.status = status;
                    data.payment_status = paymentStatus;
                    data.number = number;
                    data.customer = customer;
                    data.company = company;
                },
            },
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

// Class definition
var InvoiceHandler = (function () {
    let swappable;
    let itemsRepeater;

    const InvoiceItemType = {
        heading: 0,
        description: 1,
        subtitle: 2,
    };

    let itemDraggable = function () {
        var containers = document.querySelectorAll(".draggable-zone");

        if (containers.length === 0) {
            return false;
        }

        swappable = new Sortable.default(containers, {
            draggable: ".draggable",
            handle: ".draggable-handle",
            mirror: {
                appendTo: "body",
                constrainDimensions: true,
            },
        });
    };

    /**
     * Handles invoice item state.
     *
     * @param {jQuery} row repeater instance.
     * @param {repeaterType} repeaterType repeater type.
     */
    function invoiceItemTypeHandler(row, repeaterType) {
        if (repeaterType === "heading") {
            row.find("#content").parent().attr("colspan", "4");

            row.find("#type").val(InvoiceItemType.heading);

            row.find("#description, #quantity, #unit_price, #amount")
                .parent()
                .addClass("d-none");
        } else if (repeaterType === "description") {
            row.find("#content").parent().attr("colspan", "1");

            row.find("#type").val(InvoiceItemType.description);

            row.find("#description, #quantity, #unit_price, #amount")
                .parent()
                .removeClass("d-none");
        } else if (repeaterType === "subtitle") {
            row.find("#content").parent().attr("colspan", "4");

            row.find("#type").val(InvoiceItemType.subtitle);

            row.find("#description, #quantity, #unit_price, #amount")
                .parent()
                .addClass("d-none");
        }
    }

    /**
     * Invoice row action handler.
     */
    let repeater = function () {
        itemsRepeater = $("#invoice").repeater({
            initEmpty: false,

            show: function () {
                $(this).slideDown();
            },

            hide: function (deleteElement) {
                Swal.fire({
                    title: "Are you sure you want to remove this row?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Remove",
                    denyButtonText: "Cancel",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).slideUp(deleteElement);

                        // awaiting for DOM element removal.
                        setTimeout(() => {
                            invoiceStateHandler();
                        }, 600);
                    }
                });
            },

            ready: function (setIndexes) {
                swappable.on("sortable:sorted", () => {
                    setTimeout(() => {
                        setIndexes();
                    }, 1000);
                });

                $("[data-repeater-create-custom]").on("click", function () {
                    let $this = $(this);
                    let repeaterType = $this.data("repeater-create-custom");
                    let newRow = $('[data-repeater-list="invoice_items"]')
                        .children()
                        .first()
                        .clone();

                    invoiceItemTypeHandler(newRow, repeaterType);

                    // flash state
                    newRow.find('input[type="text"], textarea').val("");
                    newRow.find('input[type="number"]').val("0");
                    newRow.find("ul.validation-message").html("");

                    $('[data-repeater-list="invoice_items"]').append(newRow);

                    setIndexes();
                    invoiceStateHandler();
                });
            },
        });
    };

    let invoiceItemRenderer = function () {
        $("table#invoice tbody tr").each(function () {
            const repeaterType = $(this).find("input#type").val();

            if (repeaterType == InvoiceItemType.description) {
                invoiceItemTypeHandler($(this), "description");
            } else if (repeaterType == InvoiceItemType.heading) {
                invoiceItemTypeHandler($(this), "heading");
            } else if (repeaterType == InvoiceItemType.subtitle) {
                invoiceItemTypeHandler($(this), "subtitle");
            }
        });
    };

    /**
     * Updates the prices based on the specified field sets.
     */
    let invoiceStateHandler = function () {
        const invoiceTable = document.getElementById("invoice");
        const quantityInputs = invoiceTable.querySelectorAll("#quantity");
        const unitPriceInputs = invoiceTable.querySelectorAll("#unit_price");
        const amountInputs = invoiceTable.querySelectorAll("#amount");
        const totalPriceDisplay = document.getElementById("total_price_show");
        const totalPriceInput = document.getElementById("total_price");

        /**
         * Handle state update calculations.
         */
        function handleTableRow() {
            quantityInputs.forEach((quantityInput, index) => {
                const unitPrice = parseFloat(unitPriceInputs[index].value || 0);
                const quantity = parseFloat(quantityInput.value || 0);
                const amount = quantity * unitPrice;

                amountInputs[index].value = amount.toFixed(2);
            });

            let totalPrice = 0.0;
            amountInputs.forEach((amountInput) => {
                totalPrice += parseFloat(amountInput.value || 0);
            });

            totalPriceInput.value = totalPrice.toFixed(2);
            totalPriceDisplay.textContent = totalPrice.toLocaleString(
                undefined,
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                }
            );
        }

        // Initial state
        handleTableRow();

        // Life Cycle Updates
        quantityInputs.forEach((quantityInput) => {
            quantityInput.addEventListener("input", handleTableRow);
        });

        unitPriceInputs.forEach((unitPriceInput) => {
            unitPriceInput.addEventListener("input", handleTableRow);
        });
    };

    // Public methods
    return {
        init: function () {
            itemDraggable();
            repeater();
            invoiceStateHandler();
            invoiceItemRenderer();
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    // Configure jQuery to include the CSRF token with every AJAX request
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    if (document.getElementById("kt_datatable")) {
        KTDatatablesServerSide.init();

        $("#date_range").on("apply.daterangepicker", function (ev, picker) {
            dateStart = picker.startDate.format("YYYY-MM-DD");
            dateEnd = picker.endDate.format("YYYY-MM-DD");
            $(this).val(dateStart + " - " + dateEnd);
        });

        $("#datatable_index").on("submit", function (event) {
            event.preventDefault();
            status = $("#status").val();
            paymentStatus = $("#payment_status").val();
            number = $("#number").val();
            customer = $("#customer").val();
            company = $("#company").val();
            datatable.draw();
        });

        $("#datatable_index").on("reset", function (event) {
            dateStart = "";
            dateEnd = "";
            status = "";
            paymentStatus = "";
            number = "";
            customer = "";
            company = "";
            setTimeout(() => {
                $(this).find("select").trigger("change");
            }, 200);
            datatable.draw();
        });
    }

    if (document.getElementById("invoice")) {
        InvoiceHandler.init();
    }

    if (document.getElementById("payment_method")) {
        $("#payment_date, #payment_link").hide();

        // Map enum values to their corresponding strings
        const paymentMethods = {
            card: 0,
            cash: 1,
            bankTransfer: 2,
        };

        /**
         * Handle payment method state ineactions.
         */
        function handlePaymentMethodInputs() {
            const selectedPaymentMethod = parseInt($("#payment_method").val());

            if (selectedPaymentMethod == paymentMethods.card) {
                $('label[for="payment_date"]').hide();
                $('input[name="payment_date"]').prop("disabled", true).hide();

                $('label[for="payment_link"]').show();
                $('input[name="payment_link"]').show();
                $('#payment_link_btn').show();

                $('label[for="payment_reference"]').hide();
                $('textarea[name="payment_reference"]')
                    .prop("disabled", true)
                    .hide();

                $('label[for="payment_reference_receipt"]').hide();
                $('#payment_reference_receipt')
                    .prop("disabled", true)
                    .hide();
            }

            if (selectedPaymentMethod == paymentMethods.cash) {
                $('label[for="payment_date"]').show();
                $('input[name="payment_date"]').prop("disabled", false).show();

                $('label[for="payment_link"]').hide();
                $('input[name="payment_link"]').hide();
                $('#payment_link_btn').hide();

                $('label[for="payment_reference"]').show();
                $('textarea[name="payment_reference"]')
                    .prop("disabled", false)
                    .show();

                $('label[for="payment_reference_receipt"]').hide();
                $('#payment_reference_receipt')
                    .prop("disabled", true)
                    .hide();
            }

            if (selectedPaymentMethod == paymentMethods.bankTransfer) {
                $('label[for="payment_date"]').show();
                $('input[name="payment_date"]').prop("disabled", false).show();

                $('label[for="payment_link"]').hide();
                $('input[name="payment_link"]').hide();
                $('#payment_link_btn').hide();

                $('label[for="payment_reference_receipt"]').show();
                $('#payment_reference_receipt')
                    .prop("disabled", false)
                    .show();

                $('label[for="payment_reference"]').show();
                $('textarea[name="payment_reference"]')
                    .prop("disabled", false)
                    .show();
            }
        }

        // on load.
        handlePaymentMethodInputs();

        // on update.
        $('select[name="payment_method"]').on("change", () =>
            handlePaymentMethodInputs()
        );
    }

    if (document.getElementById("invoice_download")) {
        $("#invoice_download").on("click", function () {
            window.open($(this).data("url"), "_blank");
        });
    }

    setTimeout(() => {
        if ($("button#overdue").length) {
            $("button#overdue").on("click", function (event) {
                event.preventDefault();

                const parent = event.target.closest("tr");
                const resourceId = parent.querySelectorAll("td")[0].innerText;
                const lastSentTimestamp = $(this).data('timestamp');

                Swal.fire({
                    title: "Send invoice overdue mail?",
                    icon: "info",
                    text: lastSentTimestamp && 'Last Sent: ' + lastSentTimestamp,
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: "Send Mail",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: `${window.location.href}/${resourceId}/overdue`,
                            beforeSend: function (xhr, options) {
                                Swal.fire({
                                    text: "Sending..",
                                    icon: "info",
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
                                    "Failed to process the request",
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
        }
    }, 2000);

    if ($('#customer_create button[type="submit"]').length) {
        $('#customer_create button[type="submit"]').on('click', function (e) {
            e.preventDefault();

            $.ajax({
                url: `${window.location.href}/customers`,
                method: 'POST',
                data: {
                    name: $('#customer_name').val(),
                    email: $('#customer_email').val(),
                    phone: $('#customer_phone').val(),
                    address: $('#customer_address').val(),
                },
                success: function (response) {
                    if (response.status) {
                        let customer = response.body;

                        $('#customer_id').empty();

                        $('#customer_id').select2({
                            ajax: {
                                url: `${window.location.href}/customers`,
                                method: 'GET',
                                dataType: 'json',
                                processResults: function ({ body }) {
                                    return {
                                        results: body.map(function (customer) {
                                            return {
                                                id: customer.id,
                                                text: customer.name
                                            };
                                        })
                                    };
                                }
                            }
                        });

                        $('#customer_id').select2('trigger', 'select', {
                            data: { id: customer.id, text: customer.name }
                        });

                        $('#customer_create').modal('hide');
                        $('#customer_create').find('input, select, textarea').val('');
                    }
                },
                error: function (response) {
                    let errorMessage = 'An unknown error has occurred';
                    if (response.responseJSON && response.responseJSON.errors) {
                        const inputPrefix = 'customer';
                        let errors = response.responseJSON.errors;
                        errorMessage = '';

                        $(`#customer_create`).find('input, select, textarea')
                            .removeClass('is-invalid');

                        $.each(errors, function (field, messages) {
                            errorMessage += messages.join(' ') + '\n';
                            $(`[name="${inputPrefix}_${field}"]`)
                                .addClass('is-invalid')
                                .next('.invalid-feedback')
                                .remove()
                                .after(`<div class="invalid-feedback">${messages.join(' ')}</div>`);
                        });
                    }

                    Swal.fire(
                        "Error!",
                        response?.responseJSON?.message ?? 'An unknown error has occured',
                        "error"
                    );
                }
            });
        });
    }

    if ($("button#invoice_notification").length) {
        $("button#invoice_notification").on("click", function (event) {
            event.preventDefault();
            const lastSentTimestamp = $(this).data('timestamp');

            Swal.fire({
                title: "Send invoice to client?",
                text: lastSentTimestamp && 'Last Sent: ' + lastSentTimestamp,
                icon: "info",
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: "Send Mail",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: `${window.location.href}/notification`,
                        beforeSend: function (xhr, options) {
                            Swal.fire({
                                text: "Sending..",
                                icon: "info",
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
                                "Failed to process the request",
                                responseJSON["message"],
                                "error"
                            );
                        },
                    });
                }
            });
        });
    }
});
