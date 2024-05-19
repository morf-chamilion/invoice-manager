<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class RedirectResponseException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): ?bool
    {
        return false;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(): RedirectResponse
    {
        return Redirect::back()->with(
            // Formmat for flashing error messages for frontend alerts.
            ['status' => false, 'message' => $this->getMessage()]
        );
    }
}
