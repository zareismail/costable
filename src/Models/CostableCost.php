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
    	'target_date' => 'datetime',
    	'created_at'  => 'datetime',
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function($model) {
        	if(is_null($model->due_amount)) {
	        	$model->relationLoaded('costable') || $model->load('costable');
	        	$model->relationLoaded('fee') || $model->load('fee'); 

	        	$model->forceFill([
	        		'due_amount' => $model->costable->dueAmount($model->fee)
	        	]);

	        	$model->save(); 
        	}
        });
    }

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
