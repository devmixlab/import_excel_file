<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap.php';

use App\Database\FakerPostIndex;
use App\Models\Postindex;
use App\App\ConsoleApp;

$app = new ConsoleApp();

colorLog("Migration started ...");

Postindex::appendDropTable(true)
    ->appendCreateTable()
    ->execute();

$options = getopt("f:");
if(in_array('-f', $argv) || array_key_exists('f', $options)){
    $fake_count = empty($options['f']) || !is_numeric($options['f']) ? 10 : (int)$options['f'];

    $faker = new FakerPostIndex();
    $data = $faker($fake_count);

    $builder = Postindex::initInsert();

    foreach($data as $columns)
        $builder->appendData($columns, false, function($sql, $keys){
            return $sql . "(" . $keys . "), ";
        });

    $builder->format(function($sql) {
        return rtrim($sql, ", ");
    })->execute();
}

colorLog("Migration successfully completed!", "s");
exit;