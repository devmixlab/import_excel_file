<?php
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


$route->get('/', [\App\Controllers\DefaultController::class, "index"]);

$route->get('/post', [\App\Controllers\DefaultController::class, "index"]);
$route->get('/post/{index:\d{5}}', [\App\Controllers\DefaultController::class, "index"]);
$route->get('/post/create', [\App\Controllers\DefaultController::class, "index"]);
$route->get('/documentation', [\App\Controllers\DefaultController::class, "documentation"]);