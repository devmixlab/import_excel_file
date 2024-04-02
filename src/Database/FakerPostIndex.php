<?php
namespace App\Database;

use Faker\Factory;
use Faker\Generator;
use App\Database\SqlBuilder\Param;
use App\Models\Postindex;
use PDO;

class FakerPostIndex{

    /**
     * @var Generator faker.
     */
    protected Generator $faker;

    /**
     * @var Postindex model.
     */
    protected Postindex $model;

    /**
     * @var string model`s index column name.
     */
    protected string $model_index_column;

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->model = new Postindex();
        $this->model_index_column = $this->model->index();
    }

    /**
     * Performs fake data generation
     *
     * @param    int		            $count	How much rows to generate
     * @param    array<string, string>	$data   Set value of every row of column
     * @param    array<string>          $only List of columns to apply
     * @param    array<string>          $except List of columns to exclude from applying
     *
     * @return   array<int,array>       Generated data
     */
    public function __invoke(int $count = 10, array $data = [], array $only = [], array $except = []) : array
    {
        $columns_names = $this->model->columnsNames(only: $only, except: $except);

        $indexes = '';
        $values = [];
        for ($i = 0; $i < $count; $i++) {

            if(!empty($data["post_code_of_post_office"])){
                $index = $data["post_code_of_post_office"];
            }else{
//                $index = $faker->unique()->numberBetween(10000, 99999);
                for($ii = 0; $ii < 100; $ii++) {
                    $index = $this->faker->numberBetween(10000, 99999);
                    if(str_contains($indexes, $index))
                        continue;

                    $builder = Postindex::initSelect("COUNT(*)")
                        ->append("WHERE `{$this->model_index_column}` = :{$this->model_index_column}", [
                            new Param(":{$this->model_index_column}", $index, PDO::PARAM_INT)
                        ]);
                    $is_exists = $builder->fetch(PDO::FETCH_COLUMN);
                    if(!$is_exists)
                        break;
                }
            }

            $indexes .= $index . "|";

            $values[] = $this->getColumnValues($index, $columns_names);
        }

        return $values;
    }

    /**
     * Generates row of values(columns)
     *
     * @param    int		            $index	Generated index
     * @param    array<string>          $columns_names  $only List of columns to apply
     *
     * @return   array<string,mixed>   Generated data
     */
    protected function getColumnValues(int $index, array $columns_names) : array {
        $values = [];
        foreach($columns_names as $v) {
            $value = $this->getFakeColumnValue($v, $index);
            if(!is_null($value))
                $values[$v] = $value;
        }

        return $values;
    }

    /**
     * Generates column value
     *
     * @param    string		            $name	Column name
     * @param    int		            $index	Generated index
     *
     * @return   string|int|null   Generated data
     */
    protected function getFakeColumnValue(string $name, int $index) : string|int|null {
        switch($name){
            case "region":
            case "district_old":
            case "district_new":
            case "settlement":
            case "region_en":
            case "district_new_en":
            case "settlement_en":
            case "post_office":
            case "post_office_en":
                return $this->faker->regexify('[A-Za-z0-9]{20}');
            case "post_code_of_post_office":
            case "postal_code":
                return $index;
            case "from_api":
                return  (int)round(mt_rand(0,1));
            default:
                return null;
        }
    }

}