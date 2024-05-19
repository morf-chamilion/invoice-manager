<?php

namespace App\RoutePaths\Admin\Page;

use App\RoutePaths\Admin\AdminRoutePathInterface;
use App\Services\PageService;

class PageRoutePath implements AdminRoutePathInterface
{
	public function __construct(
		protected PageService $pageService,
	) {
	}

	public const INDEX = 'admin.page.index';

	public const CREATE = 'admin.page.create';

	public const STORE = 'admin.page.store';

	public const EDIT = 'admin.page.edit';

	public const UPDATE = 'admin.page.update';

	public const DESTROY = 'admin.page.destroy';

	/**
	 * Name of the resource.
	 */
	public function resourceName(): string
	{
		return $this->pageService->modelName();
	}

	/**
	 * Associative mapping resource actions to route names.
	 */
	public static function routeMappings(): array
	{
		return [
			'List' => self::INDEX,
			'Create' => [self::CREATE, self::STORE],
			'Edit' => [self::EDIT, self::UPDATE],
			'Delete' => self::DESTROY,
		];
	}
}
