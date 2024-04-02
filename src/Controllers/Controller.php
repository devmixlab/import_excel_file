<?php
namespace App\Controllers;

use App\Database\SqlBuilder\Column;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

//use Psr\Http\Message\ResponseInterface as Response;
//use Psr\Http\Message\ServerRequestInterface as Request;

use Respect\Validation\Exceptions\NestedValidationException as NestedValidationExceptionAlias;
//use Respect\Validation\Rules\Locale\Factory;
use Respect\Validation\Factory;
use Slim\Views\Twig;
//use App\Facades\Twig;
//use App\App;
use App\Facades\App;
use App\Facades\SqlBuilder;
use App\Facades\DB;
use Respect\Validation\Validator as v;
use App\FileImporter\Importer;
use App\Models\Postindex;

/**
 * This is REST api endpoints for interaction with post indexes.
 *
 * @author  Mike van Riel <devmixlab@gmail.com>
 */
class Controller {

//    protected int $default_per_page = 5;

    protected function jsonResponse(Response $response, array $data, int $status = 200) : Response {
        $payload = json_encode($data);

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    protected function uriQuery(Request $request) : array {
        $uri = $request->getUri();
        $uri_query = $uri->getQuery();
        parse_str($uri_query, $uri_query);

        return $uri_query;
    }

}