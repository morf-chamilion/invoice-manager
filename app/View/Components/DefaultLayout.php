<?php

namespace App\View\Components;

use App\Core\Bootstrap\BootstrapDefault;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class DefaultLayout extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public ?Model $model = null,
    ) {
        app(BootstrapDefault::class)->init();
    }

    /**
     * Get the view that represent the component.
     */
    public function render(): View
    {
        return view('admin.template.default');
    }
}
