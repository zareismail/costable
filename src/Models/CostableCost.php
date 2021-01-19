<?php

namespace Zareismail\Costable\Models;

use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Zareismail\NovaContracts\Models\AuthorizableModel;

class CostableCost extends AuthorizableModel implements HasMedia
{    
	use HasMediaTrait;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
    	'payment_date' => 'datetime',
    	'created_at'  => 'datetime',
    ]; 

	/**
	 * Query the related details.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function fee()
	{ 
		return $this->belongsTo(CostableFee::class, 'fee_id');
	} 

	/**
	 * Query the related costable.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function costable()
	{ 
		return $this->morphTo();
	}  

	public function registerMediaCollections(): void
	{ 
	    $this->addMediaCollection('invoice');
	}
}
