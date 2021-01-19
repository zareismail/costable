<?php

namespace Zareismail\Costable;
 
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider; 
use Laravel\Nova\Nova as LaravelNova; 

class CostableServiceProvider extends ServiceProvider 
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Models\CostableFee::class => Policies\Fee::class,
        Models\CostableCost::class => Policies\Cost::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');  
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        LaravelNova::serving([$this, 'servingNova']);
        $this->registerPolicies();
    }  

    /**
     * Register any Nova services.
     *
     * @return void
     */
    public function servingNova()
    {
        LaravelNova::resources([
            Nova\Fee::class,
            Nova\Cost::class,
        ]);
    } 

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    } 
}
