<?php

namespace Zareismail\Costable\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Nova; 
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\{ID, Text, Textarea, Currency, DateTime, BelongsTo}; 
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Nemrutco\NovaGlobalFilter\NovaGlobalFilter;
use Zareismail\Costable\Models\CostableFee; 
use Zareismail\NovaContracts\Nova\User;
use Zareismail\Costable\Helper;
use Zareismail\Fields\MorphTo;  

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
    public static $with = [];

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
                ->showCreateRelationButton()
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
                ->autouploading()
                ->hideFromIndex(),
    	];
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->with([
            'costable' => function($morphTo) use ($request) {
                $morphTo->morphWith(Helper::morphs())->withTrashed();
            },
            'fee' => function($query) {
                $query->withTrashed();
            },
            'auth' => function($query) {
                $query->withTrashed();
            }
        ]);
    }

    /**
     * Authenticate the query for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function authenticateQuery(NovaRequest $request, $query)
    {
        return $query->where(function($query) use ($request) {
            $query->when(static::shouldAuthenticate($request, $query), function($query) {
                $query->authenticate()->orWhereHasMorph('costable', Helper::morphs(), function($query, $type) { 
                    forward_static_call(
                        [Nova::resourceForModel($type), 'buildIndexQuery'], app(NovaRequest::class), $query
                    );
                });
            });
        });
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
            NovaGlobalFilter::make([ 
                Filters\FromDate::make(),
                Filters\ToDate::make(),
            ]),
            
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
            Filters\FromDate::make(),
            Filters\ToDate::make(), 
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
            $searchCallback = function($morphTo, $type) use ($search) {
                $morphTo->where(function($query) use ($type, $search) { 
                    forward_static_call(
                        [Nova::resourceForModel($type), 'buildIndexQuery'], 
                        app(NovaRequest::class), 
                        $query, 
                        $search
                    ); 
                });
            };

            $query->orWhereHasMorph('costable', Helper::morphs(), $searchCallback); 
        });
    }
}