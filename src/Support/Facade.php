<?php
namespace App\Support;

use App\App\App;

class Facade {

    public static function __callStatic(string $method, array $args)
    {
        $accessor = static::instanceAccessor();
        $instance = App::getContainer()->get($accessor);

        return call_user_func_array([$instance, $method], $args);
    }

}