<?php

namespace Zareismail\Costable\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Zareismail\Costable\Models\{CostableCost, CostableFee};

class CostsPerType extends Partition
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
        $fees = CostableFee::get()->pluck('name', 'id');

        return $this->sum($request, $this->applyFilters($request, CostableCost::authenticate()), 'amount', 'fee_id')
                    ->label(function($value) use ($fees) {
                        return $fees->get($value) ?? $value;
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
        return 'costs-per-type';
    }
}
