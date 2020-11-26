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

	/**
	 * Get the default amount for the CostableCost.
	 * 
	 * @param \Zareismail\Costable\Models\CostableFee $fee
	 * @return float
	 */
	public function dueAmount(CostableFee $fee): float;
} 