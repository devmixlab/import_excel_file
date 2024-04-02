<?php
namespace App\Database\SqlBuilder;

class Param {

    const INT_TYPE = "int";
    const STRING_TYPE = "str";

    public function __construct(
        protected string $name,
        protected mixed $value,
        protected int $type
    ) {
//        dd($type);
    }

    public function name() : string {
        return $this->name;
    }

    public function value() : mixed {
        return $this->value;
    }

    public function type() : int {
        return $this->type;
    }

    public function toArray() : array {
        return [
            "name" => $this->name,
            "value" => $this->value,
            "type" => $this->type,
        ];
    }

//    public function __get($propertyName) {
//        if(in_array($propertyName, ['name','value','type']))
//            return $this->{$propertyName};
//
//        return null;
//    }

}