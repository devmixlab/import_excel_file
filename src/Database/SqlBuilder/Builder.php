<?php
namespace App\Database\SqlBuilder;

use App\Database\Model;
use App\Facades\DB;

class Builder {

    /**
     * @var string SQL string
     */
    protected string $sql = "";

    /**
     * @var string list of PDO keys separated by comma
     */
    protected string $pdo_keys = "";

    /**
     * @var array<Param> list of Params
     */
    protected array $params = [];

    /**
     * @var int counter to make PDO keys unique when generating them from array of data
     */
    protected int $index = 1;

    /**
     * @param \App\Database\Model $model
     */
    public function __construct(protected Model $model) {}

    public function model() : Model {
        return $this->model;
    }

    /**
     * Formats SQL
     *
     * @param callable(string, string): string $format
     *
     * @returns  self
     */
    public function format(callable $format) : self {
        $this->sql = $format($this->sql, $this->pdo_keys);

        return $this;
    }

    public function initDelete() : self {
        $this->sql = "DELETE FROM `{$this->model->table()}`";

        return $this;
    }

    public function initSelect(array|string $columns = "*") : self {
        $columns = is_string($columns) ? $columns : implode(', ', $columns);
        $this->sql = "SELECT {$columns} FROM `" . $this->model->table() . "`";

//        dd($this->sql);

        return $this;
    }

    public function appendDropTable(bool $if_exist = false) : self {
        $this->sql .= "DROP TABLE" . ($if_exist ? " IF EXISTS" : "") . " `" . $this->model->table() . "`;";

        return $this;
    }

    public function appendCreateTable() : self {
        $this->sql .= "
            CREATE TABLE `" . $this->model->table() . "` (
                " . $this->model->columnsCreateDefinitions(true) . "
            ) ENGINE=" . $this->model->engine() . ";
        ";

        return $this;
    }

    public function initInsert(array $only = [], array $except = [], array $with_hidden = []) : self {
        $hidden_columns_on_insert = $this->model->hiddenColumnsOnInsert();
        $hidden = array_diff($hidden_columns_on_insert, $with_hidden);

        $except = array_unique(array_merge($except, $hidden));

        $sql = "
            INSERT INTO `" . $this->model->table() . "` (" . $this->model->columnsNames(true, $only, $except) . ") VALUES
        ";

//        dd($sql);

        $this->sql = trim($sql);

        return $this;
    }

    /**
     * Appends Data
     *
     * @param array<string, array> $data Row of data
     * @param bool $append_pdo_keys Append PDO keys into string of PDO keys
     * @param null|callable(string, string): string $format Make modifications to SQL string
     *
     * @returns  self
     */
    public function appendData(array $data, bool $append_pdo_keys = true, ?callable $format = null) : self {
//        $pdo_values = '(';

        $pdo_keys = "";
        foreach($data as $name => $value) {
            $key = ':' . $name . '_' . $this->index;
            $pdo_keys .= $key . ",";
            $this->params[] = new Param($key, $value, $this->model->columnPDOType($name));

            $this->index++;
        }

        $pdo_keys = trim(rtrim($pdo_keys, ","));
        if(!empty($append_pdo_keys)){
            $this->pdo_keys .= (!empty($this->pdo_keys) ? ',' : '') . $pdo_keys;
        }


        if(!empty($format))
            $this->sql = $format($this->sql, $pdo_keys);

        return $this;
    }

    /**
     * Gets pdo keys
     *
     * @param null|callable(string, string): string $format Make modifications to SQL string
     *
     * @returns  self
     */
    public function pdoKeys(?callable $format = null) {
        $pdo_keys = rtrim($this->pdo_keys, ',');

        return !empty($format) ? $format($pdo_keys) : $pdo_keys;
    }

    /**
     * Appends sql and params
     *
     * @param string $sql Sql to append
     * @param array<Param> $params Params to append
     *
     * @returns  self
     */
    public function append(string $sql, array $params = []) : self  {
        $this->sql .= " " . trim($sql);
        if(!empty($params))
            $this->params = array_merge($this->params, $params);

        return $this;
    }

    /**
     * Appends sql on duplicate key update
     *
     * @param array<string> $only List of columns to apply
     * @param array<string> $except List of columns to exclude from applying
     *
     * @returns  self
     */
    public function appendOnDuplicateKeyUpdate(
        array $only = [], array $except = []
    ) : self {
        $names = $this->model->columnsNames(false, $only, $except);

//        dd($names);

        $sql_on_duplicate = '';
        foreach ($names as $name){
            $sql_on_duplicate .= "{$name} = VALUES({$name}), ";
        }
        $sql_on_duplicate = rtrim($sql_on_duplicate, ", ");

//        dd($sql_on_duplicate);

        $this->sql .= " ON DUPLICATE KEY UPDATE " . $sql_on_duplicate;

        return $this;
    }

    public function sql(?callable $format = null) : string {
        $sql = $this->sql;

        if(!empty($format))
            return $format($this->sql, $this->pdo_keys);

        return $sql;
    }

    public function params() : array {
        return $this->params;
    }

    public function isParamsEmpty() : bool {
        return empty($this->params);
    }

    public function __call($name, $arguments) {
        $db = DB::inst();
//        if(!empty($this->params))
//            dd($this->params);
//        dd(555);
        if(method_exists($db, $name)){
            $arguments = array_merge([$this->sql, $this->params], $arguments);
            return DB::{$name}(...$arguments);
        }
    }

}