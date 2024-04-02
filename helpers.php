<?php
//use Slim\Views\Twig;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Facades\Twig;
use App\App;


function view(string $template, array $props = []) {
    $response = new Response();
    return Twig::render($response, $template, $props);
}

function response() {
    $c = App::getContainer();
//    $response = $c->get(Response::class);
    dd(111);
    return $c->get(Response::class);
}

function request() {
    $c = App::getContainer();
    return $c->get(Request::class);
}

function dd($value) {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    exit;
}

function statMemory() : array {
    $bytes = memory_get_usage();

    return [
        "bytes" => $bytes,
        "megabytes" => $bytes/1024/1024,
    ];
}

function colorLog($str, $type = 'i'){
    switch ($type) {
        case 'e': //error
            echo "\033[31m$str \033[0m\n";
            break;
        case 's': //success
            echo "\033[32m$str \033[0m\n";
            break;
        case 'w': //warning
            echo "\033[33m$str \033[0m\n";
            break;
        case 'i': //info
            echo "\033[36m$str \033[0m\n";
            break;
        default:
            echo "$str\n";
            break;
    }
}