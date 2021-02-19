<?php

namespace Zareismail\Costable\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest; 
use Zareismail\Costable\Nova\Filters\{FromDate, ToDate};

trait GlobalFilterable  
{ 
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function applyFilters(NovaRequest $request, $model)
    {
        $filters = json_decode($request->filters, true);

        return $model::when(isset($filters[FromDate::class]), function($query) use ($filters) {
            $query->whereDate('payment_date', '>=', $filters[FromDate::class]);
        })->when(isset($filters[ToDate::class]), function($query) use ($filters) {
            $query->whereDate('payment_date', '<=', $filters[ToDate::class]);
        });
    } 
}
