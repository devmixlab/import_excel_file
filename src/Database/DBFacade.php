<?php

namespace App\Database;

use OpenSpout\Reader\XLSX\Reader;
use Spatie\SimpleExcel\SimpleExcelReader;
use App\Database\DB;
use App\Support\Facade;

use Cache\Adapter\Filesystem\FilesystemCachePool;

class DBFacade extends Facade{

    static protected function instanceAccessor() {
        return DBInterface::class;
    }

}