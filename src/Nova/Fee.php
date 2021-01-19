<?php

namespace Zareismail\Costable\Nova; 

use Illuminate\Http\Request; 
use Laravel\Nova\Fields\{ID, Text, Number, HasMany};  

class Fee extends Resource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Costable\Models\CostableFee::class;

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

            Text::make(__('Cost Type'), 'name'), 

            HasMany::make(__('Costs'), 'costs', Cost::class),
    	];
    }  
}