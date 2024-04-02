<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Cache\Adapter\Filesystem\FilesystemCachePool;

use OpenSpout\Reader\XLSX\Reader;

use App\Database\DB;

$db = new DB([
    "host" => "127.0.0.1"
]);

$filesystemAdapter = new Local('/cache');
$filesystem        = new Filesystem($filesystemAdapter);

$pool = new FilesystemCachePool($filesystem);

$pool->clear();

$fileName = "new.xlsx";

$reader = new Reader();
$reader->open(__DIR__ . '/files/' . $fileName);

