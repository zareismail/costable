<?php

namespace Zareismail\Costable\Nova\Metrics;

use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Http\Requests\NovaRequest; 
use Zareismail\Costable\Models\CostableCost;

class CostsPerDay extends Trend
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
        return $this->sumByMonths($request, $this->applyFilters($request, CostableCost::authenticate()), 'amount')
                    ->suffix(config('nova.currency').PHP_EOL)
                    ->withoutSuffixInflection()
                    ->showLatestValue();
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            10000 => __('All'), 
        ];
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
        return 'costs-per-day';
    }
}
