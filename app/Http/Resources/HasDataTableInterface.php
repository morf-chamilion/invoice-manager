<?php

namespace App\Http\Resources;

interface HasDataTableInterface
{
	/** Transform & reformat the record collection */
	public function transformRecords($data);
}
