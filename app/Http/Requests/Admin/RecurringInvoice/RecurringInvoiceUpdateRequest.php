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

class RecurringInvoiceUpdateRequest extends BaseRequest
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
            'send_automatically' => [
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
