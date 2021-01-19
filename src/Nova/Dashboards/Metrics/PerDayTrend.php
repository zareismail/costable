<?php

namespace Zareismail\Costable\Nova\Dashboards\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend; 

class PerDayTrend extends Trend
{ 
    use InteractsWithFilters;
    
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->sumByMonths($request, $this->query($request), 'amount', 'payment_date')
                    ->suffix(config('nova.currency').PHP_EOL)
                    ->withoutSuffixInflection();
    } 

    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return data_get($this->meta, 'costable.name').': '.__('Trend');
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
}
