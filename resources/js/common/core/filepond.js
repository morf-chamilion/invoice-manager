import * as FilePond from "filepond";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import FilePondPluginFilePoster from "filepond-plugin-file-poster";
import FilePondPluginFileValidateSize from "filepond-plugin-file-validate-size";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";

/**
 * Filepond JS Class.
 *
 * Handles form file upload dropzone.
 */
class AppFilePond {
    constructor() {
        this.boot();
    }

    /**
     * Boot manager.
     */
    boot() {
        FilePond.registerPlugin(FilePondPluginImagePreview);
        FilePond.registerPlugin(FilePondPluginFilePoster);
        FilePond.registerPlugin(FilePondPluginFileValidateSize);
        FilePond.registerPlugin(FilePondPluginFileValidateType);
    }

    /**
     * Initialize dropzone element.
     */
    static create(
        id,
        files,
        fileMaxSize = 15,
        fileMaxCount = 1,
        mimeTypes = []
    ) {
        const inputElement = document.getElementById(id);
        const imageMimeTypes = [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/gif",
            "image/bmp",
            "image/webp",
            "image/tiff",
            "image/svg+xml",
            "image/x-icon",
        ];

        FilePond.create(inputElement, {
            server: {
                process: window.GLOBAL_STATE.COMMON_MEDIA_STORE,
                revert: null,
                restore: null,
                load: null,
                fetch: null,
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            },
            files: files?.map((file) => ({
                source: file.original_url,
                options: {
                    type: "limbo",
                    metadata: {
                        poster: file.original_url,
                        uuid: file.uuid,
                    },
                    file: {
                        name: file.file_name,
                        size: file.size,
                        type: file.mime_type,
                    },
                },
            })),
            filePosterMaxHeight: 400,
            maxFileSize: `${fileMaxSize}MB`,
            maxFiles: fileMaxCount,
            acceptedFileTypes: mimeTypes.length ? mimeTypes : imageMimeTypes,
            labelIdle: this.templateRenderer(fileMaxSize, fileMaxCount),
            credits: false,
        });
    }

    /**
     * Render Filepond template.
     */
    static templateRenderer(fileMaxSize, fileMaxCount) {
        const labelInstruction = `<i class="fas fa-cloud-upload-alt fs-1x"></i> <span class="fw-3 fs-1x">Drag & Drop your files or <span class="filepond--label-action"> Browse</span></span>`;
        const labelFileSize = `<div>Max File Size: ${fileMaxSize}MB</div>`;
        const labelFileCount =
            fileMaxCount > 1
                ? `<div>Max File Count: ${fileMaxCount}</div>`
                : "";

        return (
            `<div style="width:100%;height:100%;">` +
            labelInstruction +
            labelFileSize +
            labelFileCount +
            `</div>`
        );
    }
}

// Initialize
const dropzone = new AppFilePond();

window.AppFilePond = AppFilePond;
