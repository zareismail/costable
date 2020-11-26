<?php

namespace Zareismail\Costable\Contracts;

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

interface Costable
{
	/**
	 * Query the related details.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasOneOrMany
	 */
	public function costs(): HasOneOrMany;
} 