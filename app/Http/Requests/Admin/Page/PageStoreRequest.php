<?php

namespace App\Http\Requests\Admin\Page;

use App\Enums\PageStatus;
use App\Http\Requests\BaseRequest;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Str;

class PageStoreRequest extends BaseRequest
{
	public function __construct(
		private PageService $pageService,
	) {
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
	 */
	public function rules(): array
	{
		return [
			'status' => [
				'integer', new Enum(PageStatus::class),
			],
			'slug' => [
				'required', 'string', Rule::unique(Page::class), 'max:255',
			],
			'title' => [
				'required', 'string', 'max:255',
			],
			'admin_template' => [
				'required', 'string',
			],
			'front_template' => [
				'required', 'string',
			],
		];
	}

	/**
	 * Prepare the data for validation.
	 */
	protected function prepareForValidation(): void
	{
		$this->merge([
			'slug' => Str::slug($this->slug),
			'front_template' => $this->pageService
				->getFrontTemplatePath($this->admin_template),
		]);
	}
}
