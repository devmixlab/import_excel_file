<?php

namespace App\Database;

use PDO;
use App\Facades\App;
use App\Database\SqlBuilder\Column;

class DB implements DBInterface{

    function __construct(protected PDO $pdo) {}

    public function inst() : self {
        return $this;
    }

//    public function getId() {
//        return spl_object_id($this);
//    }

    public function fetch(string $sql, array $parms = [], $mode = PDO::FETCH_ASSOC) {
        $stmt = $this->pdo->prepare($sql);
        $this->makeExecute($stmt, $parms);

        return $stmt->fetch($mode);
    }

    public function fetchAll(string $sql, array $parms = [], $mode = PDO::FETCH_ASSOC) {
        $stmt = $this->pdo->prepare($sql);
        $this->makeExecute($stmt, $parms);

        return $stmt->fetchAll($mode);
    }

    public function execute(string $sql, array $parms = []) {
        $stmt = $this->pdo->prepare($sql);
        return $this->makeExecute($stmt, $parms);
    }

    protected function prepareParms ($stmt, array $parms) : array {
        $execute_parms = [];

        foreach ($parms as $parm) {
            [
                "name" => $name,
                "value" => $value,
                "type" => $type,
            ] = $parm->toArray();

            $execute_parms[$name] = $value;
            $stmt->bindParam($name, $value, $type);
        }

        return $execute_parms;
    }

    protected function makeExecute($stmt, array $parms = []) {
        if(empty($parms))
            return $stmt->execute();

        $execute_parms = $this->prepareParms($stmt, $parms);
//        dd($execute_parms);
        return $stmt->execute($execute_parms);
    }

}