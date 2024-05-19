<?php

namespace App\Core\Bootstrap;

class BootstrapFrontCustomer
{
    public function init(): void
    {
        addVendors(['datatables', 'filepond']);

        addHtmlClass('body', 'app-default');

        addCssFile('assets/css/admin/master.css');
    }
}
