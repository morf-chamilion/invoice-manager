<?php

namespace App\Services;

use App\Models\Page;
use App\Repositories\PageRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class PageService extends BaseService
{
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
		return $this->pageRepository->create([
			...$attributes,
			'created_by' => $this->getAdminAuthUser()->id,
		]);
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
		return $this->pageRepository->delete($pageId);
	}

	/**
	 * Update an existing page.
	 */
	public function updatePage(int $pageId, array $newAttributes): bool
	{
		return $this->pageRepository->update($pageId, [
			...$newAttributes,
			'updated_by' => $this->getAdminAuthUser()->id,
		]);
	}

	/**
	 * Get the page route name.
	 */
	public static function pageRouteName(Page $page): string
	{
		return 'page-' . $page->id;
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
