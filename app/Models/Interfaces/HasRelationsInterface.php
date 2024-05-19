<?php

namespace App\Models\Interfaces;

interface HasRelationsInterface
{
	/** 
	 * Define model methods with Has relations. 
	 * 
	 * @return string[] An array of method names.
	 */
	public function defineHasRelationships(): array;
}
