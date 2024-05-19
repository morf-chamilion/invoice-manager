<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use App\Providers\AdminServiceProvider;
use App\Providers\CustomerServiceProvider;
use App\Repositories\BaseRepository;
use Illuminate\Support\Str;

abstract class BaseService
{
	public function __construct(
		public BaseRepository $repository,
	) {
	}

	/**
	 * Get the model class name.
	 */
	public function modelClassName(): string
	{
		return $this->repository->modelClassName();
	}

	/**
	 * Get the model human readable name.
	 */
	public function modelName(): string
	{
		return $this->repository->modelName();
	}

	/**
	 * Get the authenticated admin user.
	 */
	protected function getAdminAuthUser(): ?User
	{
		return AdminServiceProvider::getAuthUser();
	}

	/**
	 * Get the authenticated customer user.
	 */
	protected function getCustomerAuthUser(): ?Customer
	{
		return CustomerServiceProvider::getAuthUser();
	}

	/**
	 * return the filtered result as a page.
	 *
	 * @return integer 	$data[count]		Results Count
	 * @return array 	$data[data]			Results
	 */
	public function getAllWithFilter($filterQuery, $filterColumns)
	{
		$query = $this->modelClassName()::select("*");

		foreach ($filterColumns as $col) {
			if ($col && strpos($col, ".") !== false) {
				$coldata = explode(".", $col);
				$query->orWhereHas($coldata[0], function ($subquery) use ($filterQuery, $coldata) {
					$subquery->orWhere($coldata[1], 'like', '%' . $filterQuery->search['value'] . '%');
				});
			} elseif ($col) {
				$query->orWhere($col, 'like', '%' . $filterQuery->search['value'] . '%');
			}
		}

		$data['count'] = $query->count();

		$orderByColumn = $filterColumns[$filterQuery->order[0]['column']] ?? $filterColumns[count($filterColumns) - 1];
		$orderByDirection = $filterQuery->order[0]['dir'];

		$query->orderBy($orderByColumn, $orderByDirection);

		if ($filterQuery->length != -1) {
			$query->skip($filterQuery->start)->take($filterQuery->length);
		}

		$data['data'] = $query->get();

		return $data;
	}

	/**
	 * Format mail template content with data.
	 */
	public static function formatMailContent(?string $template, array $content): string
	{
		foreach ($content as $key => $parameter) {
			$template = Str::replace($key, $parameter, $template);
		}

		return $template;
	}
}
