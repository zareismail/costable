<?php

namespace Zareismail\Costable; 

use Laravel\Nova\Nova;

class Helper
{      
    /**
     * Return Nova's contractable resources.
     * 
     * @return \Laravel\Nova\ResourceCollection
     */
    public static function costableResources($request)
    {
        return Nova::authorizedResources($request)->filter(function($resource) { 
            return collect(class_implements($resource::newModel()))->contains(Contracts\Costable::class); 
        })->values();
    }  

    /**
     * Return Nova's contractable resources.
     * 
     * @return \Laravel\Nova\ResourceCollection
     */
    public static function morphs()
    {
        return static::costableResources(app('request'))->map(function($resource) { 
            return $resource::$model; 
        })->all();
    }  
}
