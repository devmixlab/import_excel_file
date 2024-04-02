<?php

namespace App\Facades;

use App\Database\DBInterface;
use App\Support\Facade;

class DB extends Facade{

    static protected function instanceAccessor() {
        return DBInterface::class;
    }

}