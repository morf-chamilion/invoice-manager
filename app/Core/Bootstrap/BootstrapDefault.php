<?php

namespace App\Core\Bootstrap;

class BootstrapDefault
{
    public function init(): void
    {
        // 1) Light sidebar layout
        $this->initLightSidebarLayout();

        // 2) Dark sidebar layout
        //$this->initDarkSidebarLayout();

        // 3) Dark header layout
        // $this->initDarkHeaderLayout();

        // 4) Light header layout
        // $this->initLightHeaderLayout();

        $this->initAssets();
    }

    public function initAssets(): void
    {
        # Include global vendors
        addVendors(['datatables', 'fullcalendar', 'tinymce', 'formrepeater', 'filepond', 'draggable']);

        # Include global javascript files
        addJavascriptFile('assets/js/admin/core/menu-aside.js');

        addJavascriptFile('assets/js/common/core/checkbox.js');
        addJavascriptFile('assets/js/common/core/form-repeater.js');
        addJavascriptFile('assets/js/common/core/select2.js');
        addJavascriptFile('assets/js/common/core/filepond.js');
        addJavascriptFile('assets/js/common/core/tinymce-editor.js');
        addJavascriptFile('assets/js/common/core/datetime.js');
        addJavascriptFile('assets/js/common/core/datatable.js');
        addCssFile('assets/css/admin/master.css');
    }

    public function initDarkSidebarLayout(): void
    {
        addHtmlAttribute('body', 'data-kt-app-layout', 'dark-sidebar');
        addHtmlAttribute('body', 'data-kt-app-header-fixed', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-enabled', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-fixed', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-hoverable', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-push-header', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-push-toolbar', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-push-footer', 'true');
        addHtmlAttribute('body', 'data-kt-app-toolbar-enabled', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-minimize', 'off');

        addHtmlClass('body', 'app-default');
    }

    public function initLightSidebarLayout(): void
    {
        addHtmlAttribute('body', 'data-kt-app-layout', 'light-sidebar');
        addHtmlAttribute('body', 'data-kt-app-header-fixed', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-enabled', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-fixed', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-hoverable', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-push-header', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-push-toolbar', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-push-footer', 'true');
        addHtmlAttribute('body', 'data-kt-app-toolbar-enabled', 'true');

        addHtmlClass('body', 'app-default');
    }

    public function initDarkHeaderLayout(): void
    {
        addHtmlAttribute('body', 'data-kt-app-layout', 'dark-header');
        addHtmlAttribute('body', 'data-kt-app-header-fixed', 'true');
        addHtmlAttribute('body', 'data-kt-app-toolbar-enabled', 'true');

        addHtmlClass('body', 'app-default');
    }

    public function initLightHeaderLayout(): void
    {
        addHtmlAttribute('body', 'data-kt-app-layout', 'light-header');
        addHtmlAttribute('body', 'data-kt-app-header-fixed', 'true');
        addHtmlAttribute('body', 'data-kt-app-toolbar-enabled', 'true');

        addHtmlClass('body', 'app-default');
    }
}
