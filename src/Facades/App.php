<?php

namespace App\Facades;

use App\App\AppInterface;
use App\Support\Facade;

class App extends Facade{

    static protected function instanceAccessor() {
        return AppInterface::class;
    }

}