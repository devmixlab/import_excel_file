<?php

use App\Controllers\Api\PostindexController;


$index_param = "index:\d{3,6}";

$route->get('/postindex', [PostindexController::class, "index"])
    ->setName('postindex.index');

$route->get("/postindex/{{$index_param}}", [PostindexController::class, "show"])
    ->setName('postindex.show');

$route->post('/postindex', [PostindexController::class, "store"])
    ->setName('postindex.store');

$route->delete("/postindex/{{$index_param}}", [PostindexController::class, "destroy"])
    ->setName('postindex.destroy');