<?php

namespace Zareismail\Costable\Models;  
  
use Zareismail\Contracts\Concerns\InteractsWithConfigs; 
use Zareismail\NovaContracts\Concerns\ShareableResource; 

class CostableFee extends Model
{
    use ShareableResource, InteractsWithConfigs; 

    /**
     * Get the sharing contracts interface.
     *  
     * @return string            
     */
    public static function sharingContract(): string
    {
        return \Zareismail\Costable\Contracts\Costable::class;
    } 

    /**
     * Determine share condition.
     * 
     * @param  \Laravel\Nova\Resource $resource
     * @param  string $condition 
     * @return bool            
     */
    public function sharedAs($resource, string $condition): bool
    {
        return boolval($this->getConfig($condition.'.'.$resource::uriKey()));
    } 

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
