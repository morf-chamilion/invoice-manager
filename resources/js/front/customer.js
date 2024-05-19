KTUtil.onDOMContentLoaded(function () {
    let datatable;

    if ($("#customer_invoice_index").length) {
        datatable = $("#customer_invoice_index").DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            pageLength: 10,
            columnDefs: [
                {
                    targets: ["id"],
                    orderable: false,
                    width: 240,
                },
                {
                    targets: ["price"],
                    className: "text-md-start",
                    width: 200,
                },
                {
                    targets: ["status"],
                    className: "text-md-center",
                    width: 180,
                },
                {
                    targets: ["actions"],
                    className: "text-md-end",
                    orderable: false,
                    width: 240,
                },
            ],
        });
    }

    const defaultFilter = document.querySelector(
        "#customer_invoice_index_filter"
    );

    defaultFilter?.classList.add("d-none");

    const filterSearch = document.querySelector(
        '[data-kt-docs-table-filter="search"]'
    );

    filterSearch.addEventListener("keyup", function (e) {
        datatable.search(e.target.value).draw();
    });
});
