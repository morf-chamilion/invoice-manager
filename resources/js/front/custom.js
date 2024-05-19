jQuery(document).ready(function ($) {
    const IS_MOBILE_DEVICE = window.matchMedia(
        "only screen and (max-width: 767px)"
    ).matches;
    const IS_TAB_DEVICE = window.matchMedia(
        "only screen and (min-width: 768px) and (max-width: 1024px)"
    ).matches;
    const IS_DESKTOP_DEVICE = !IS_MOBILE_DEVICE && !IS_TAB_DEVICE;

    if (IS_MOBILE_DEVICE || IS_TAB_DEVICE) {
        new Mmenu(
            "#primaryNav",
            {
                offCanvas: {
                    position: "right-front",
                },
                navbars: [
                    {
                        position: "top",
                        content: ["<img src='" + SITE_LOGO + "' />"],
                    },
                ],
            },
            {
                offCanvas: {
                    page: {
                        selector: "#page",
                    },
                },
            }
        );
    }

    // Init addons
    if ($(".select-2").length) {
        $(".select-2").select2();
    }

    if ($("[data-fancybox]").length) {
        Fancybox.bind("[data-fancybox]");
    }

    if ($("#logout").length) {
        $("#logout button").on("click", function (event) {
            event.preventDefault();

            DangerAlert.fire({
                title: "Are you sure you want to logout?",
                icon: "warning",
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: "Logout",
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#logout").submit();
                }
            });
        });
    }
});

/**
 * Prevent user interaction.
 */
const PageBlocker = (function () {
    return {
        block: () => {
            jQuery("body").addClass("maya-blocker");
        },

        unblock: () => {
            jQuery("body").removeClass("maya-blocker");
        },
    };
})();

/**
 * Theme danger alert handler.
 */
const DangerAlert = Swal.mixin({
    customClass: {
        confirmButton: "btn btn-danger mx-1",
        cancelButton: "btn btn-secondary mx-1",
    },
    buttonsStyling: false,
});
