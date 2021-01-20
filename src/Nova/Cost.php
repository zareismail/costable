<?php

namespace Zareismail\Costable\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Nova; 
use Laravel\Nova\Fields\{ID, Text, Textarea, Currency, DateTime, BelongsTo}; 
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
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'tracking_code', 'notes'
    ];

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

            Currency::make(__('Amount'), 'amount')
                ->required()
                ->rules('required'),

            Text::make(__('Payment Tracking Code'), 'tracking_code'),

            Textarea::make(__('Additional Tips'), 'notes'),

            Medialibrary::make(__('Payment Invoices'), 'inovice') 
                ->autouploading(),
    	];
    }

    /**
     * Get the cards available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            Metrics\CostsPerType::make(), 
            Metrics\CostsPerResource::make(),
            Metrics\CostsPerDay::make(),
        ];
    }

    /**
     * Get the filters available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            Filters\MinAmount::make(),
            Filters\MaxAmount::make(), 
            Filters\Fee::make(), 
            Filters\Resource::make(), 
        ];
    }

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    { 
        $titles = [];

        if($fee = Nova::resourceForModel($this->fee)) {
            $titles[] = (new $fee($this->fee))->title();
        } 

        if($costable = Nova::resourceForModel($this->costable)) {
            $titles[] = (new $costable($this->costable))->title();
        } 

        return implode(': ', $titles);
    }

    /**
     * Apply the search query to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applySearch($query, $search)
    {
        return parent::applySearch($query, $search)->orWhere(function($query) use ($search) {
            $query->orWhereHasMorph('costable', Helper::morphs(), function($morphTo, $type) use ($search) {
                $morphTo->where(function($query) use ($type, $search) {
                    $resource = Nova::resourceForModel($type);

                    foreach ($resource::searchableColumns() as $column) {
                        $query->orWhere($query->qualifyColumn($column), 'like', '%'.$search.'%');
                    }  
                });
            }); 
        });
    }
}