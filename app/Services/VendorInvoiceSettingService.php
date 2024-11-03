<?php

namespace App\Services;

use App\Repositories\VendorRepository;

class VendorInvoiceSettingService extends BaseService
{
	public function __construct(
		private VendorRepository $invoiceRepository,
		private SettingService $settingService,
	) {
		parent::__construct($invoiceRepository);
	}

	/**
	 * Update an existing invoice.
	 */
	public function updateVendorInvoiceSetting(int $invoiceId, array $newAttributes): bool
	{
		if ($this->getAdminAuthUser()) {
			$newAttributes['updated_by'] = $this->getAdminAuthUser()->id;
		}

		$updated = $this->invoiceRepository->update($invoiceId, $newAttributes);

		return $updated;
	}
}
