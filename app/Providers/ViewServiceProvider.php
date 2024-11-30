<?php

namespace App\Providers;

use Illuminate\Support\Facades\{
    Log,
    View,
};
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        View::composer('screens.*', function (\Illuminate\View\View $view) {
            if (! empty($view->getData())) {
                Log::info($view->getData());
            }
        });
    }
}
