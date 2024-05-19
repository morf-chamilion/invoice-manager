import KTThemeMode from "../../../../resources/theme/src/js/layout/theme-mode.js";

/**
 * TinyMCE Editor Class.
 *
 * Lifecycle handler for the editor behaviour.
 */
class AppTinyMceEditor {
    constructor() {
        // Globals
        this.editor = document.querySelectorAll(".tinymce-editor");

        // Init
        this.boot();
    }

    /**
     * Boot manager.
     */
    boot() {
        if (!tinymce) {
            console.log("Warning: TinyMCE library not found.");
            return;
        }

        const themeMode = KTThemeMode.getMode();

        const options = {
            selector: ".tinymce-editor",
            height: "480",
            toolbar1:
                "formatselect | fontsizeselect bold italic strikethrough | forecolor backcolor | link image | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | code | removeformat",
            extended_valid_elements: "span[style]",
            plugins: "code lists advlist",
            branding: false,
            skin: themeMode === "dark" ? "oxide-dark" : "oxide",
            content_css: themeMode === "dark" ? "dark" : "default",
        };

        tinymce.init(options);
    }
}

// Initialize
const tinyMceEditor = new AppTinyMceEditor();
