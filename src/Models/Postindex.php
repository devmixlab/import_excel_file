<?php
namespace App\Models;

use App\Database\Model;
use PDO;

class Postindex extends Model {

    protected string $table = "postindex";

    protected string $engine = "InnoDB";

    protected ?string $index = "post_code_of_post_office";

    protected array $hidden_columns_on_insert = ['import_key'];

    protected array $columns = [
        'post_code_of_post_office' => [
            'name' => 'post_code_of_post_office',
            'create_defination' => "MEDIUMINT NOT NULL PRIMARY KEY",
            'pdo_type' => PDO::PARAM_INT,
        ],
        'region' => [
            'name' => 'region',
            'create_defination' => "VARCHAR(255) NOT NULL",
            'pdo_type' => PDO::PARAM_STR,
        ],
        'district_old' => [
            'name' => 'district_old',
            'create_defination' => "VARCHAR(255) NOT NULL",
            'pdo_type' => PDO::PARAM_STR,
        ],
        'district_new' => [
            'name' => 'district_new',
            'create_defination' => "VARCHAR(255) NOT NULL",
            'pdo_type' => PDO::PARAM_STR,
        ],
        'settlement' => [
            'name' => 'settlement',
            'create_defination' => "VARCHAR(255) NOT NULL",
            'pdo_type' => PDO::PARAM_STR,
        ],
        'postal_code' => [
            'name' => 'postal_code',
            'create_defination' => "MEDIUMINT",
            'pdo_type' => PDO::PARAM_INT,
        ],
        'region_en' => [
            'name' => 'region_en',
            'create_defination' => "VARCHAR(255) NOT NULL",
            'pdo_type' => PDO::PARAM_STR,
        ],
        'district_new_en' => [
            'name' => 'district_new_en',
            'create_defination' => "VARCHAR(255) NOT NULL",
            'pdo_type' => PDO::PARAM_STR,
        ],
        'settlement_en' => [
            'name' => 'settlement_en',
            'create_defination' => "VARCHAR(255) NOT NULL",
            'pdo_type' => PDO::PARAM_STR,
        ],
        'post_office' => [
            'name' => 'post_office',
            'create_defination' => "VARCHAR(255) NOT NULL",
            'pdo_type' => PDO::PARAM_STR,
        ],
        'post_office_en' => [
            'name' => 'post_office_en',
            'create_defination' => "VARCHAR(255) NOT NULL",
            'pdo_type' => PDO::PARAM_STR,
        ],
        'from_api' => [
            'name' => 'from_api',
            'create_defination' => "TINYINT DEFAULT(0)",
            'pdo_type' => PDO::PARAM_INT,
        ],
        'import_key' => [
            'name' => 'import_key',
            'create_defination' => "VARCHAR(255) DEFAULT NULL",
            'pdo_type' => PDO::PARAM_STR,
        ],
    ];

    /**
     * @var array<string, int> Columns names map to index of column in excel file
     */
    protected array $excel_keys_map = [
        'post_code_of_post_office' => 11,'region' => 1,'district_old' => 2,
        'district_new' => 3,'settlement' => 4,'postal_code' => 5,
        'region_en' => 6,'district_new_en' => 7,'settlement_en' => 8,
        'post_office' => 9,'post_office_en' => 10
    ];

    /**
     * Maps column names to index of column in excel file to create
     * array with column names as keys and column excel values as keys values
     *
     * @param array<int, string|int>         Row data from excel file
     *
     * @returns array<string, string|int>    Array with key as column name and value as value
     *                                       from excel column
     */
    public function mapValuesToKeys(array $data) : array {
        $mapped = [];
        foreach ($this->excel_keys_map as $k => $v)
            $mapped[$k] = $data[$v];

        return $mapped;
    }

    /**
     * @returns int    row column`s index from excel file wich is an index column in table
     */
    public function indexExcelNumericKey() : int {
        return $this->excel_keys_map[$this->index];
    }

}