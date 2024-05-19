<?php

namespace App\Http\Requests\Common\Media;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\File;

class MediaStoreRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
	 */
	public function rules(): array
	{
		$safeFileTypes = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg', 'gif', 'svg'];

		return [
			'file' => [
				'required', File::types($safeFileTypes)->max(50 * 1024),
			],
		];
	}
}
