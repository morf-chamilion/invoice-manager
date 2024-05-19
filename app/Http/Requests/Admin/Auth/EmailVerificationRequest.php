<?php

namespace App\Http\Requests\Admin\Auth;

use App\Messages\AuthMessage;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Validator;

class EmailVerificationRequest extends FormRequest
{
    public ?User $user;

    /**
     * Get the user service instance.
     */
    private function userService(): UserService
    {
        return App::make(UserService::class);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->user = $this->userService()->getUser($this->route('id'));

        if (!$this->user) {
            return false;
        }

        if (!hash_equals((string) $this->user->getKey(), (string) $this->route('id'))) {
            return false;
        }

        if (!hash_equals(sha1($this->user->getEmailForVerification()), (string) $this->route('hash'))) {
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
