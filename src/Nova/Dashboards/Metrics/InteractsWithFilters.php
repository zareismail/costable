<?php

namespace Zareismail\Costable\Nova\Dashboards\Metrics;
 
use Laravel\Nova\Http\Requests\NovaRequest; 
use Laravel\Nova\Nova; 
use Zareismail\Costable\Nova\Cost;
use Zareismail\Costable\Helper; 

trait InteractsWithFilters 
{   
    /**
     * Return`s metric query for the given request.
     * 
     * @return \Laravel\Nova\ResourceCollection
     */
    public function query(NovaRequest $request)
    {  
        return Cost::authenticateQuery($request, Cost::newModel())
                ->where('fee_id', data_get($this->meta, 'costable.id'))
                ->when(request('viaResource'), function($query) use ($request) {
                    $costable = Nova::resourceForKey(request('viaResource'));
                    $costableId = intval(request('viaResourceId'));

                    $query->where('costable_type', $costable::newModel()->getMorphClass())
                          ->when($costableId, function($query) use ($costableId) {
                                $query->where('costable_id', $costableId);
                            });
                });
    }

    /**
     * Return morphs classes from the Nova's costable resources.
     * 
     * @return \Laravel\Nova\ResourceCollection
     */
    public function getMorphs($request)
    {
        return Helper::morphs();
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            30 => __('30 Days'),
            60 => __('60 Days'),
            90 => __('90 Days'),
            180 => __('180 Days'),
            270 => __('270 Days'),
            365 => __('365 Days'), 
            730 => __('Two Year'),  
        ];
    }
 
    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    { 
        $viaResource = data_get($this->meta, 'viaResource');
        $viaResourceId = data_get($this->meta, 'viaResourceId');

        return  'costs-reports-'.
                parent::uriKey(). 
                data_get($this->meta, 'costable.id').
                ($viaResource ? '?'.http_build_query(compact('viaResource', 'viaResourceId')) : '');
    }
}
