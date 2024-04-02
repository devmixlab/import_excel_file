<?php
namespace App\Database;

use App\Database\SqlBuilder\Builder;

class Model {

    /**
     * @var string Table name.
     */
    protected string $table;

    /**
     * @var array<string, array{
     *      name: string,
     *      create_defination: string,
     *      pdo_type: ?int
     * }> Columns definition
     */
    protected array $columns;

    /**
     * @var string|null index column name.
     */
    protected ?string $index = null;

    /**
     * @var string Table engine.
     */
    protected string $engine;

    protected array $timestamp_columns = [
        'created_at' => [
            'name' => 'created_at',
            'create_defination' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'create_defination' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        ],
    ];

    public function index() {
        return $this->index;
    }

    public function table() {
        return $this->table;
    }

    public function engine() {
        return $this->engine;
    }

    public function columns() {
        return $this->columns;
    }

    /**
     * @param    string		$name	Name of table column in database
     *
     * @returns  int|null 	        PDO Type of variable for statement->prepareParms
     *                              if column not exists null will be returned
     */
    public function columnPDOType(string $name) : int|null {
        if(empty($this->columns[$name]) || empty($this->columns[$name]['pdo_type']))
            return null;

        return $this->columns[$name]['pdo_type'];
    }

    /**
     * @param    bool		$as_string	    Defines return type array|string
     * @param    array		$only	        returns only specified columns names
     *                                      takes precedence over except param
     * @param    array		$except	        returns only columns names wich not in except array
     *
     * @returns  array|string 	            columns list as array or string
     */
    public function columnsNames(bool $as_string = false, array $only = [], array $except = []) : array|string {
        $columns_names = array_column($this->columns, 'name');

        if(!empty($only) || !empty($except))
            $columns_names = array_filter($columns_names, function ($itm) use ($only, $except) {
                if(!empty($only)) {
                    return in_array($itm, $only);
                }else if(!empty($except)) {
                    return !in_array($itm, $except);
                }
            });

        if($as_string)
            return implode(',', $columns_names);

        return $columns_names;
    }

    /**
     * @param    bool		$as_string	        Defines return type array|string
     * @param    bool		$with_timestamp	    Include or not timestamp columns definitions
     * @returns  array|string 	                columns definitionts for create table
     */
    public function columnsCreateDefinitions(bool $as_string = false, bool $with_timestamp = true) : array|string {
        $definitions = [];
        if($with_timestamp)
            $this->columns = array_merge($this->columns, $this->timestamp_columns);
        foreach ($this->columns as $column) {
            $definitions[] = "`" . $column['name'] . "` " . $column['create_defination'];
        }

        if($as_string)
            $definitions = implode(",", $definitions);

        return $definitions;
    }

//    public function __call($name, $arguments) {
//        if(method_exists(Builder::class, $name)){
//            $builder = new Builder($this);
//            return call_user_func_array([$builder, $name], $arguments);
//        }
//    }

    static public function __callStatic($name, $arguments) {
//        if(method_exists(static::class, $name)){
//            $c = App::getContainer();
//            $model = $c->get(static::class);
////            $model = new static();
//            return call_user_func_array([$model, $name], $arguments);
//        }
        if(method_exists(Builder::class, $name)){
            $builder = new Builder(new static());
            return call_user_func_array([$builder, $name], $arguments);
        }
    }

}