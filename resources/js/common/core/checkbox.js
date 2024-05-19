/**
 * Checkbox JS Class.
 *
 * Handles form select behaviour based on user interactions.
 */
class AppCheckbox {
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
        $.when($.ready).then(function () {
            const elementSelector = ".checkbox-visual-toggle";
            const displayElementAttribute = "data-visual-element";

            /**
             * Handle visibility based on user interaction.
             *
             * @param {HTMLElement} element Checkbox
             */
            const handleVisibility = function (element) {
                const visualElement = $(element).attr(displayElementAttribute);

                if ($(element).is(":checked")) {
                    $(visualElement).show();
                    $(visualElement)
                        .find("input, textarea, select")
                        .prop("disabled", false);
                } else {
                    $(visualElement).hide();
                    $(visualElement)
                        .find("input, textarea, select")
                        .prop("disabled", true);
                }
            };

            // First render.
            handleVisibility(elementSelector);

            // Checkbox state change.
            $(elementSelector).on("change", function () {
                handleVisibility(this);
            });
        });
    }
}

// Initialize
const select2 = new AppCheckbox();
