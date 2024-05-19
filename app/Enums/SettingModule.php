<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum SettingModule
{
    case GENERAL;
    case MAIL;
    case INVOICE;

    /** Pages */
    case HOME;

    /**
     * Get storage optimized name.
     */
    public function getName(): string
    {
        return Str::of($this->name)->lower();
    }
}
