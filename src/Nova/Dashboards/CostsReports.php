<?php

namespace Zareismail\Costable\Nova\Dashboards;
 
use Illuminate\Support\Str;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\{Select, Number};
use Laravel\Nova\Dashboard;
use Laravel\Nova\Nova; 
use Zareismail\Fields\Contracts\Cascade;
use Zareismail\Costable\Nova\Metrics\Costs; 
use Zareismail\Costable\Models\CostableFee; 
use Zareismail\Costable\Helper; 

class CostsReports extends Dashboard
{
    use ConditionallyLoadsAttributes;

    /**
     * Get the displayable name of the dashboard.
     *
     * @return string
     */
    public static function label()
    {
        return __('Costs Reports');
    }

    public function filters(NovaRequest $request)
    { 
        return $this->filter([    
            Select::make(__('Report Of'), 'costable') 
                ->options(Helper::costableResources($request)->mapWithKeys(function($resource) {
                    return [
                        $resource::uriKey() => $resource::label()
                    ];
                }))
                ->displayUsingLabels()
                ->nullable()
                ->withMeta([
                    'placeholder' => __('All')
                ]), 

            $this->mergeWhen($request->filled('costable'), function() use ($request) {
                $resource = $this->findResourceForKey($request->get('costable'));

                return (array) $this->getFieldsForResource($request, $resource); 
            }), 
        ]);
    }

    public function findResourceForKey($key)
    {
        return Helper::costableResources(request())->first(function($resource) use ($key) {
            return $resource::uriKey() == $key;
        });
    }

    public function getFieldsForResource($request, $resource)
    {
        $fields = []; 
        $viaResourceId = null;

        if($parent = $this->findParentForResource($resource)) {
            $fields = array_merge($fields, $this->getFieldsForResource(
                $request, $parent
            ));  
        }
        
        if(! is_null($parent) && ! $request->filled($this->resourceFilterKey($parent))) {
            return $fields;
        } elseif(! is_null($parent)) {
            $viaResourceId = intval($request->get($this->resourceFilterKey($parent)));
        }

        $selection = tap($this->getResourceSelection($request, $resource, $viaResourceId), function($field) {
            $costable = $this->findResourceForKey(request('costable'));

            if($field->attribute == $this->resourceFilterKey($costable)) {
                $field->nullable()->withMeta([
                    'placeholder' => __('All') 
                ]); 
            }
        });  

        array_push($fields, $selection); 

        return $fields;
    }

    /**
     * Get the parent resource of the given resource.
     * 
     * @param  string $resource 
     * @return string           
     */
    public function findParentForResource($resource)
    {
        if($resource::newModel() instanceof Cascade) {
            return Nova::resourceForModel($resource::newModel()->parent()->getModel());
        }  
    }

    /**
     * Get Resoruce item selction.
     * 
     * @param  \Laravel\Nova\Http\Requests\NovaRequest $request       
     * @param  string $resource      
     * @param  string $viaResourceId 
     * @return \LaravelNova\Fields\Field                
     */
    public function getResourceSelection($request, $resource, $viaResourceId)
    {
        return Select::make($resource::label(), $this->resourceFilterKey($resource)) 
                ->options($resource::newModel()->when($viaResourceId, function($query) use ($viaResourceId) {
                    return $query->whereHas('parent', function($query) use ($viaResourceId) {
                        $query->whereKey($viaResourceId);
                    });
                })->get()->keyBy('id')->mapInto($resource)->map->title())
                ->displayUsingLabels();
    }

    public function resourceFilterKey($resource)
    {
        return $resource::uriKey();
    }

    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {  
        if(! $this->isValidRequest()) {
            return [];
        }

        return CostableFee::get()->flatMap(function($costable) {
            $viaResource = $this->findResourceForKey(request('costable'));
            $viaResourceId = $viaResource ? request($this->resourceFilterKey($viaResource)) : null;

            return [
                Metrics\PerDayTrend::make()->withMeta([
                    'costable' => $costable,
                    'viaResource' => request('costable'),
                    'viaResourceId' => $viaResourceId,
                ])->width('1/2'),

                Metrics\PerDayValue::make()->withMeta([
                    'costable' => $costable,
                    'viaResource' => request('costable'),
                    'viaResourceId' => $viaResourceId,
                ])->width('1/2'), 
            ];
        })->all(); 
    }

    public function isValidRequest()
    {
        if(request()->route('dashboard') === static::uriKey()) {
            return true;
        }

        if(Str::startsWith(request()->route('metric'), ['costs-reports'])) {
            return true;
        }

        return false;
    } 

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'costs-reports';
    }
}
