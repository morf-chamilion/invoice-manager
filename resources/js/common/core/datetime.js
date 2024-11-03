/**
 * Date Range Picker Class.
 *
 * Component for choosing date ranges, dates and times.
 */
class AppDateTime {
    constructor() {
        // Init
        this.boot();
    }

    /**
     * Boot manager.
     */
    boot() {
        $("input.datetime").each((index, element) => {
            const options = $(element).data();
            const type = $(element).data("type") ?? false;
            const format = $(element).data("locale-format");
            const initEmpty = $(element).data("init-empty");

            if (format) {
                Object.assign(options, {
                    locale: { format },
                });
            }

            if (type) {
                Object.assign(options, {
                    singleDatePicker: type === "multiple" ? false : true,
                });
            }

            let datePicker = $(element).daterangepicker(options);

            if (initEmpty) {
                datePicker.val('');
            }

            datePicker.on('cancel.daterangepicker', function () {
                $(this).val('');
            });
        });
    }
}

// Initialize
const appDateTime = new AppDateTime();