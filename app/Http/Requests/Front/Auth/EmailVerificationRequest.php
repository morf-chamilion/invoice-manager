<?php

namespace App\Http\Requests\Front\Auth;

use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Validator;

class EmailVerificationRequest extends FormRequest
{
    public ?Customer $customer;

    /**
     * Get the customer service instance.
     */
    private function customerService(): CustomerService
    {
        return App::make(CustomerService::class);
    }

    /**
     * Determine if the customer is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->customer = $this->customerService()->getCustomer($this->route('id'));

        if (!$this->customer) {
            return false;
        }

        if (!hash_equals((string) $this->customer->getKey(), (string) $this->route('id'))) {
            return false;
        }

        if (!hash_equals(sha1($this->customer->getEmailForVerification()), (string) $this->route('hash'))) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): Validator
    {
        return $validator;
    }
}
