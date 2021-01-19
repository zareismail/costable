<?php

namespace Zareismail\Costable\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID, Text, Textarea, Currency, DateTime, BelongsTo, HasMany}; 
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Zareismail\NovaContracts\Nova\User;
use Zareismail\Costable\Models\CostableFee; 
use Zareismail\Fields\MorphTo;  
use Zareismail\Costable\Helper;

class Cost extends Resource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Costable\Models\CostableCost::class;

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['costable'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
    	return [
    		ID::make(), 

            BelongsTo::make(__('User'), 'auth', User::class)
                ->withoutTrashed()
                ->default($request->user()->getKey())
                ->searchable()
                ->canSee(function($request) {
                    return $request->user()->can('forceDelete', static::newModel());
                }),  

            BelongsTo::make(__('Type Of Cost'), 'fee', Fee::class)
                ->withoutTrashed(),

            MorphTo::make(__('Paid For'), 'costable')
                ->types(Helper::costableResources($request)->all())
                ->withoutTrashed(),

            DateTime::make(__('Payment Date'), 'payment_date'), 

            Currency ::make(__('Amount'), 'amount'),

            Text::make(__('Tracking Code'), 'tracking_code'),

            Textarea::make(__('Additional Tips'), 'notes'),

            Medialibrary::make(__('Payment Invoices'), 'inovice') 
                ->autouploading(),
    	];
    }
}