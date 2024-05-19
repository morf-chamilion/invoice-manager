<?php

namespace App\Helpers;

use App\Handlers\MoneyHandler;

class MoneyHelper extends MoneyHandler
{
    public function __construct(
        private MoneyHandler $moneyHandler,
    ) {
    }
}
