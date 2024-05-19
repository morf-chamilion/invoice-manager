<?php

namespace App\Core\Bootstrap;

class BootstrapSystem
{
    public function init(): void
    {
        addHtmlClass('body', 'app-blank');
    }
}
