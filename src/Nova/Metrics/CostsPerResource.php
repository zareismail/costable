<?php

namespace Zareismail\Costable\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Nova;
use Zareismail\Costable\Nova\Cost;

class CostsPerResource extends Partition
{
    use GlobalFilterable;

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    { 
        $query = Cost::authenticateQuery($request, Cost::newModel());

        return $this->sum($request, $this->applyFilters($request, $query), 'amount', 'costable_type')
                    ->label(function($value) {
                        if($resource = Nova::resourceForModel($value)) {
                            return $resource::label();
                        }

                        return class_basename($value);
                    });
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'costs-per-resource';
    }
}
