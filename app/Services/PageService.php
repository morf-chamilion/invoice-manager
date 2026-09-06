<?php

namespace App\Services;

use App\Models\Page;
use App\Repositories\PageRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PageService extends BaseService
{
	/**
	 * Cache key holding the id/slug pairs the front page routes are built from.
	 */
	public const PAGE_ROUTE_CACHE_KEY = 'front_page_routes';

	protected array $pageTemplates;

	public function __construct(
		private PageRepository $pageRepository,
		private SettingService $settingService,
	) {
		$this->pageRepository->setModelName('content page');
		parent::__construct($pageRepository);
	}

	/**
	 * Bind page templates from container.
	 */
	public function setPageTemplates(array $pageTemplates): void
	{
		$this->pageTemplates = $pageTemplates;
	}

	/**
	 * Get page templates from container.
	 */
	public function getPageTemplates(): array
	{
		return $this->pageTemplates;
	}

	/**
	 * Get all pages.
	 */
	public function getAllPages(): Collection
	{
		return $this->pageRepository->getAll();
	}

	/**
	 * Get all active pages.
	 */
	public function getAllActivePages(): Collection
	{
		return $this->pageRepository->getAllActive();
	}

	/**
	 * Create a new page.
	 */
	public function createPage(array $attributes): Page
	{
		$page = $this->pageRepository->create([
			...$attributes,
			'created_by' => $this->getAdminAuthUser()->id,
		]);

		$this->flushPageRouteCache();

		return $page;
	}

	/**
	 * Get the specified page.
	 */
	public function getPage(int $pageId): ?Page
	{
		return $this->pageRepository->getById($pageId);
	}

	/**
	 * Get the specified page attribute.
	 */
	public function getPageWhere(string $columnName, mixed $value): ?Page
	{
		return $this->pageRepository->getFirstWhere($columnName, $value);
	}

	/**
	 * Delete a specific page.
	 */
	public function deletePage(int $pageId): int
	{
		$deleted = $this->pageRepository->delete($pageId);

		$this->flushPageRouteCache();

		return $deleted;
	}

	/**
	 * Update an existing page.
	 */
	public function updatePage(int $pageId, array $newAttributes): bool
	{
		$updated = $this->pageRepository->update($pageId, [
			...$newAttributes,
			'updated_by' => $this->getAdminAuthUser()->id,
		]);

		$this->flushPageRouteCache();

		return $updated;
	}

	/**
	 * Get the page route name.
	 */
	public static function pageRouteName(Page $page): string
	{
		return self::pageRouteNameFromId($page->id);
	}

	/**
	 * Get the page route name from an id, for callers that hold no model.
	 */
	public static function pageRouteNameFromId(int $pageId): string
	{
		return 'page-' . $pageId;
	}

	/**
	 * Get the storage page setting module.
	 */
	public static function pageSettingModule(Page $page): string
	{
		return 'page_' . $page->id;
	}

	/**
	 * Get the page by slug.
	 */
	public function getPageBySlug(string $slug): ?Page
	{
		return $this->pageRepository->getFirstWhere('slug', $slug);
	}

	/**
	 * Get the id and slug of every page, for registering front page routes.
	 *
	 * RouteServiceProvider needs this on every single request, including the
	 * ones that never reach a page, so it is cached rather than queried each
	 * time. Returns an empty list when the database is unavailable so the
	 * application still boots without one.
	 */
	public function getPageRouteDefinitions(): array
	{
		try {
			return Cache::remember(
				key: self::PAGE_ROUTE_CACHE_KEY,
				ttl: config('settings.page_routes_cache_ttl'),
				callback: fn () => $this->getAllPages()
					->map(fn (Page $page) => ['id' => $page->id, 'slug' => $page->slug])
					->all(),
			);
		} catch (QueryException $e) {
			report($e);

			return [];
		}
	}

	/**
	 * Forget the cached page route definitions.
	 *
	 * Front page routes are registered from this list at boot, so a page whose
	 * slug changed stays unreachable until the cache is dropped.
	 */
	public function flushPageRouteCache(): void
	{
		Cache::forget(self::PAGE_ROUTE_CACHE_KEY);
	}

	/**
	 * Get the table column names.
	 */
	public function getAllColumns(): array
	{
		return Schema::getColumnListing($this->pageRepository->getModel()->getTable());
	}

	/**
	 * Get the template path for the front blade view.
	 */
	function getFrontTemplatePath(string $adminTemplatePath): ?string
	{
		foreach ($this->pageTemplates as $templatePaths) {
			if ($templatePaths['admin'] === $adminTemplatePath) {
				return $templatePaths['front'];
			}
		}

		return null;
	}

	/**
	 * Get the name of a specific admin or front template path.
	 */
	function getTemplateName(string $templatePath): ?string
	{
		foreach ($this->pageTemplates as $template => $templatePaths) {
			if (\in_array($templatePath, $templatePaths, true)) {
				return $template;
			}
		}

		return null;
	}
}
