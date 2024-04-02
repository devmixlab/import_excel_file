<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap.php';

use App\FileImporter\Importer;
use App\App\ConsoleApp;

$app = new ConsoleApp();

$importer = new Importer(FILES, "postindex_mini.xlsx");
//$importer = new Importer(FILES, "postindex.xlsx");
//$importer = new Importer(FILES, "new.xlsx");

colorLog("Started importing ...");
$importer->run();

colorLog("Successfully imported!", "s");
exit;