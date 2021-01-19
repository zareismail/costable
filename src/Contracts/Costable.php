<?php

namespace Zareismail\Costable\Contracts;

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Zareismail\Costable\Models\CostableFee;

interface Costable
{
	/**
	 * Query the related details.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasOneOrMany
	 */
	public function costs(): HasOneOrMany; 
} 