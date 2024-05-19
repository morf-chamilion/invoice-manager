<?php

namespace App\View\Components;

use App\Core\Bootstrap\BootstrapSystem;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SystemLayout extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        app(BootstrapSystem::class)->init();
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('admin.template.system');
    }
}
