<?php

namespace Husseinsayed\Paytabs\Facades;

use Illuminate\Support\Facades\Facade;

class Paytabs extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'paytabs';
    }

}
