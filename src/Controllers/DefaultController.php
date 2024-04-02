<?php
namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
//use App\Facades\Twig;
use App\Facades\App;

class DefaultController {

    function index(RequestInterface $request, ResponseInterface $response, Twig $twig) {
//        dd(App::getContainer());
        return $twig->render($response, 'home.html.twig');
    }

    function documentation(RequestInterface $request, ResponseInterface $response, Twig $twig) {
        $openapi = \OpenApi\Generator::scan([CONTROLLERS . 'Api']);

        $response->withHeader('Content-Type', 'application/json');
//        header('Content-Type: application/x-yaml');
        echo $openapi->toJson();
        exit;
//        return $twig->render($response, 'documentation.html.twig');
    }

}