<?php

namespace App\View\Components;

use App\Core\Bootstrap\BootstrapFront;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FrontLayout extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        app(BootstrapFront::class)->init();
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('front.template.front');
    }
}
