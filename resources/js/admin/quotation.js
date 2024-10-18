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
            number = $("#number").val();
            customer = $("#customer").val();
            datatable.draw();
        });

        $("#datatable_index").on("reset", function (event) {
            dateStart = "";
            dateEnd = "";
            status = "";
            number = "";
            customer = "";
            setTimeout(() => {
                $(this).find("select").trigger("change");
            }, 200);
            datatable.draw();
        });
    }

    if ($('.draggable-zone').length) {
        QuotationDraggable.init();
    }

    if ($('#quotationData').length) {
        QuotationRepeater.init();
    }

    if (document.getElementById("quotation_download")) {
        $("#quotation_download").on("click", function () {
            window.open($(this).data("url"), "_blank");
        });
    }

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

    if ($("button#quotation_notification").length) {
        $("button#quotation_notification").on("click", function (event) {
            event.preventDefault();
            const lastSentTimestamp = $(this).data('timestamp');

            Swal.fire({
                title: "Send quotation to client?",
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

    if ($("button#invoice_generate").length) {
        $("button#invoice_generate").on("click", function (event) {
            event.preventDefault();

            Swal.fire({
                title: "Convert to an invoice?",
                text: "This quotation will be locked and will be converted into an invoice.",
                icon: "info",
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: "Convert To Invoice",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: `${window.location.href}/invoice-generate`,
                        beforeSend: function (xhr, options) {
                            Swal.fire({
                                text: "Converting..",
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
                                ).then(() => {
                                    window.location.href = response['body']['redirect_url'];
                                });
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

$('input[name="item_type"]').on('change', function (event) {
    let value = parseInt($('input[name="item_type"]:checked').val());
    $('.itinerary-type').addClass('d-none');

    $('#customItem').removeClass('d-none');
});

$('#addQuotationItemBtn').on('click', function (event) {
    event.preventDefault();

    let itemType = parseInt($('input[name="item_type"]:checked').val());
    let itemTypeName = $('input[name="item_type"]:checked').parent().find('.form-check-label').text();
    let itemName = null;
    let itemID = null;

    itemID = $('#title').val();
    itemName = $('#title').val();
    itemTypeName = 'Custom';
    itemType = 1;

    let description = $('#description').val();
    let quantity = $('#quantity').val();
    let unitPrice = parseFloat($('#unit_price').val())
        .toFixed(2)
        .toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

    if (itemType && itemTypeName && itemID && itemName && unitPrice && !isNaN(unitPrice)) {
        let currentQuotationData = ($('tr.draggable').length) ? $('#quotationData').repeaterVal()['quotation_items'] : [];

        let updatedCurrentQuotationData = [];

        for (const [key, value] of Object.entries(currentQuotationData)) {
            updatedCurrentQuotationData.push({
                'type_id': value['type_id'],
                'type': value['type'],
                'item_id': value['item_id'],
                'title': value['title'],
                'description': value['description'],
                'quantity': value['quantity'],
                'unit_price': value['unit_price'],
                'amount': value['amount'],
            });
        }

        let rowTotal = parseFloat(parseFloat(quantity) * parseFloat(unitPrice))
            .toFixed(2)
            .toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });

        updatedCurrentQuotationData.push({
            'type_id': itemType,
            'type': itemTypeName.trim(),
            'item_id': itemID,
            'title': itemName.trim(),
            'description': description,
            'quantity': quantity,
            'unit_price': unitPrice,
            'amount': rowTotal,
        });

        quotationItemsRepeater.setList(updatedCurrentQuotationData);

        let totalPrice = parseFloat($('#totalPriceInput').val());
        if (isNaN(totalPrice)) {
            totalPrice = 0;
        }

        let total = parseFloat(parseFloat(totalPrice) + parseFloat(rowTotal))
            .toFixed(2)
            .toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });

        $('#totalPrice').text(total);
        $('#totalPriceInput').val(total);

        $('#quotationItemForm input[type=text], #quotationItemForm input[type=number]').val('');
        $('#quotationItemForm select').val('');
        $('#quotationItemForm select').trigger('change');
    } else {
        Swal.fire('Error', 'Please fill out all required fields.', 'error');
    }
});

let swappable;
let quotationItemsRepeater;

let QuotationDraggable = function () {
    return {
        //main function to initiate the module
        init: function () {
            let containers = document.querySelectorAll('.draggable-zone');

            if (containers.length === 0) {
                return false;
            }

            swappable = new Sortable.default(containers, {
                draggable: '.draggable',
                handle: '.draggable-handle',
                mirror: {
                    appendTo: 'body',
                    constrainDimensions: true
                }
            });
        }
    };
}();

// Quotation Items Repeater
let QuotationRepeater = function () {

    // Private functions
    let quotationRepeater = function () {
        quotationItemsRepeater = $('#quotationData').repeater({
            initEmpty: true,

            show: function () {
                $(this).slideDown();
            },

            hide: function (deleteElement) {
                Swal.fire({
                    title: 'Are you sure you want to remove this item?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes! Remove',
                    denyButtonText: 'Cancel',
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-light-primary",
                    },
                }).then((result) => {
                    if (result.isConfirmed) {

                        let totalPrice = parseFloat($('#totalPriceInput').val());
                        let amount = parseFloat($(this).find('#amount').val());

                        totalPrice = (!isNaN(totalPrice)) ? totalPrice : 0;
                        amount = (!isNaN(amount)) ? amount : 0;

                        let total = parseFloat(totalPrice - amount)
                            .toFixed(2)
                            .toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            });

                        $('#totalPrice').text(total);
                        $('#totalPriceInput').val(total);

                        $(this).slideUp(deleteElement);
                    }
                });
            },

            ready: function (setIndexes) {
                swappable.on('sortable:sorted', () => {
                    setTimeout(() => {
                        setIndexes();
                    }, 1000);
                });
            },
        });
    }

    return {
        // public functions
        init: function () {
            quotationRepeater();
            quotationItemsRepeater.setList(items);
        }
    };
}();