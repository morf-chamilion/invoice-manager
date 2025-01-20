<?php

namespace App\Rules;

use App\Enums\PaymentStatus;
use App\Helpers\MoneyHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AmountLessThanModelField implements ValidationRule
{
    protected string $model;
    protected string $field;
    protected string $identifier;
    protected string $identifierValue;

    public function __construct(
        string $model,
        string $field,
        string $identifier,
        mixed $identifierValue
    ) {
        $this->model = $model;
        $this->field = $field;
        $this->identifier = $identifier;
        $this->identifierValue = $identifierValue;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $model = app($this->model)
            ->where($this->identifier, $this->identifierValue)
            ->first();

        if (!$model) {
            $fail("The specified {$this->model} was not found.");
            return;
        }

        $formattedValue = MoneyHelper::format($value);

        if (!is_numeric((int) $formattedValue)) {
            $fail("The :attribute must be a valid numeric value.");
            return;
        }

        $existingPaymentsSum = $model->payments->where('status', PaymentStatus::PAID)->sum('amount');
        $newTotal = MoneyHelper::format((int) $existingPaymentsSum + (int) $formattedValue);

        if ($newTotal > $model->{$this->field}) {
            $modelName = class_basename($this->model);
            $fail("The total payments, including this payment, cannot exceed the {$modelName} total.");
        }
    }
}
