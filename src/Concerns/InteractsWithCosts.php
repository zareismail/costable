<?php

namespace Zareismail\Costable\Concerns; 

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Zareismail\Costable\Models\CostableCost;

trait InteractsWithCosts
{ 
	/**
	 * Query the related costs.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasOneOrMany
	 */
	public function costs(): HasOneOrMany
	{
		return $this->morphMany(CostableCost::class, 'costable');
	}
} 