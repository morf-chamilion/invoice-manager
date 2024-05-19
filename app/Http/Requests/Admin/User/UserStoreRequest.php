<?php

namespace App\Http\Requests\Admin\User;

use App\Enums\UserStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
	 */
	public function rules(): array
	{
		return [
			'name' => [
				'string', 'min:3', 'max:255',
			],
			'status' => [
				'integer', new Enum(UserStatus::class),
			],
			'email' => [
				'email', 'max:255', Rule::unique(User::class),
			],
			'password' => [
				Password::defaults(), 'max:255',
			],
			'role' => [
				'int', 'nullable'
			],
			'notification' =>	[
				'sometimes', 'bool',
			],
		];
	}
}
