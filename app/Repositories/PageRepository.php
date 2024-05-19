<?php

namespace App\Repositories;

use App\Enums\PageStatus;
use App\Models\Page;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class PageRepository extends BaseRepository
{
	public function __construct(
		private Page $page,
	) {
		parent::__construct($page);
	}

	/**
	 * Get all pages.
	 */
	public function getAll(): Collection
	{
		return $this->page::all();
	}

	/**
	 * Get all active pages.
	 */
	public function getAllActive(): Collection
	{
		return $this->page
			->where('status', PageStatus::ACTIVE)
			->get();
	}

	/**
	 * Get the specified page.
	 */
	public function getById(int $pageId): ?Page
	{
		return $this->page::find($pageId);
	}

	/**
	 * Get the page by column name and value.
	 */
	public function getFirstWhere(string $columnName, mixed $value): ?Page
	{
		return $this->page::where($columnName, $value)->first();
	}

	/**
	 * Delete a specific page.
	 */
	public function delete(int $pageId): bool
	{
		$page = $this->getById($pageId);

		$this->checkModelHasParentRelations($page);

		try {
			return $page->delete($pageId);
		} catch (QueryException $e) {
			throw new \Exception($e->getMessage());

			return false;
		}
	}

	/**
	 * Create a new page.
	 */
	public function create(array $attributes): Page
	{
		$page = $this->page::create($attributes);

		return $page;
	}

	/**
	 * Update an existing page.
	 */
	public function update(int $pageId, array $newAttributes): bool
	{
		$page = $this->page::findOrFail($pageId)
			->fill($newAttributes);

		return $page->save();
	}
}
