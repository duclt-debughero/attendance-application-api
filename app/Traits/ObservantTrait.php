<?php

namespace App\Traits;

use App\Observers\ModelObserver;

trait ObservantTrait
{
    public static function bootObservantTrait() {
        static::observe(new ModelObserver());
    }
}
