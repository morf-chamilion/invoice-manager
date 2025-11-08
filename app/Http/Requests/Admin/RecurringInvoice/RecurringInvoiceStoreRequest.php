<?php

namespace App\Http\Requests\Admin\RecurringInvoice;

use App\Enums\RecurringInvoiceEndType;
use App\Enums\RecurringInvoiceFrequency;
use App\Enums\RecurringInvoiceItemType;
use App\Enums\RecurringInvoiceStatus;
use App\Http\Requests\BaseRequest;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class RecurringInvoiceStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'integer',
                new Enum(RecurringInvoiceStatus::class),
            ],
            'date' => [
                'nullable',
                'date',
            ],
            'due_date' => [
                'nullable',
                'date',
            ],
            'customer_id' => [
                'required',
                Rule::exists(Customer::class, 'id'),
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'frequency' => [
                'required',
                'string',
                new Enum(RecurringInvoiceFrequency::class),
            ],
            'end_condition_type' => [
                'required',
                'string',
                new Enum(RecurringInvoiceEndType::class),
            ],
            'due_after_days' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'end_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($this->input('end_condition_type') === RecurringInvoiceEndType::DATE->value && empty($value)) {
                        $fail('The end date field is required when end condition type is date.');
                    }
                },
            ],
            'end_count' => [
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if ($this->input('end_condition_type') === RecurringInvoiceEndType::COUNT->value && empty($value)) {
                        $fail('The end count field is required when end condition type is count.');
                    }
                },
            ],
            'send_automatically' => [
                'nullable',
                'boolean',
            ],
            'next_run_date' => [
                'nullable',
                'date',
            ],
            'last_run_date' => [
                'nullable',
                'date',
            ],
            'discount_type' => [
                'nullable',
                'string',
            ],
            'discount_value' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (empty($this->input('invoice_items')) && $value != 0) {
                        $fail('The discount value must be 0 if there are no invoice items.');
                    }
                },
            ],
            'invoice_items' => [
                'nullable',
                'array',
                'min:1',
            ],
            'invoice_items.*.type_id' => [
                'required',
                'integer',
                new Enum(RecurringInvoiceItemType::class),
            ],
            'invoice_items.*.item_id' => [
                'required',
            ],
            'invoice_items.*.title' => [
                'nullable',
                'string',
            ],
            'invoice_items.*.description' => [
                'nullable',
                'string',
            ],
            'invoice_items.*.quantity' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999',
            ],
            'invoice_items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999.99',
            ],
            'invoice_items.*.amount' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999.99',
            ],
            'total_price' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999.99',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:510',
            ],
            'notification' => [
                'sometimes',
                'bool',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'start_date.required' => 'The start date field is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'frequency.required' => 'The frequency field is required.',
            'frequency.enum' => 'The selected frequency is invalid.',
            'end_condition_type.required' => 'The end condition type field is required.',
            'end_condition_type.enum' => 'The selected end condition type is invalid.',
            'due_after_days.integer' => 'The due after days must be an integer.',
            'due_after_days.min' => 'The due after days must be at least 1.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_count.integer' => 'The end count must be an integer.',
            'end_count.min' => 'The end count must be at least 1.',
            'send_automatically.boolean' => 'The send automatically field must be true or false.',
            'next_run_date.date' => 'The next run date must be a valid date.',
            'last_run_date.date' => 'The last run date must be a valid date.',
            'invoice_items.*.type.required' => 'The type field for invoice item is required.',
            'invoice_items.*.content.required' => 'This content is required for this item.',
            'invoice_items.*.quantity.required' => 'The quantity is required for this item.',
            'invoice_items.*.unit_price.required' => 'The unit price is required for this item.',
            'invoice_items.*.unit_price.max' => 'The unit price field must be at most 9999999.99.',
            'invoice_items.*.amount.required' => 'The amount is required for this item.',
            'invoice_items.*.amount.max' => 'The amount field must be at most 9999999.99.',
        ];
    }
}
