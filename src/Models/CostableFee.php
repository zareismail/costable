<?php

namespace Zareismail\Costable\Models;  
  
use Zareismail\Contracts\Concerns\InteractsWithConfigs;  

class CostableFee extends Model
{
    use InteractsWithConfigs;  

    /**
     * Query the related costs.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function costs()
    { 
        return $this->hasMany(CostableCost::class, 'fee_id');
    } 
}
