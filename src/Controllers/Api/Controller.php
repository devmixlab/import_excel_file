<?php
namespace App\Controllers\Api;

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
use App\Controllers\Controller as WebController;
use App\Services\Pagination;

/**
 * This is REST api endpoints for interaction with post indexes.
 *
 * @author  Mike van Riel <devmixlab@gmail.com>
 */
class Controller extends WebController {

    protected int $default_per_page = 5;

    protected bool $relative_links = false;


    public function indexResponseData(Request $request, array $query_params, array $data, int $total, string $route_name) {
        [
            "page" => $page,
            "per_page" => $per_page,
            "offset" => $offset,
            "settlement" => $settlement,
            "relative_links" => $relative_links,
        ] = $query_params;

        if(!empty($relative_links))
            $this->relative_links = true;

        $routeParser = \Slim\Routing\RouteContext::fromRequest($request)->getRouteParser();

        $results_on_page = count($data);
        $total_pages = ceil($total/$per_page);

        $page_params = [
            "per_page" => $per_page,
            "settlement" => $settlement,
        ];

//        dd($total_pages);

        $pagination = new Pagination($page, $total, $per_page);
        $links = $pagination->links();
        if(!empty($links))
            $links = array_map(function($itm) use ($route_name, $request, $routeParser, $page_params) {
                if(!is_null($itm['page'])){
                    $itm['url'] = $this->getLink($route_name, $request, $routeParser, array_merge($page_params, [
                        "page" => $itm['page'],
                    ]));
                } else {
                    $itm['url'] = null;
                }

                return $itm;
            }, $links);

//        dd($links);
//        dd($pagination->links());


        $current_page = $this->getLink($route_name, $request, $routeParser, array_merge($page_params, [
            "page" => $pagination->page()
        ]));

        $firts_page = $results_on_page < $total ?
            $this->getLink($route_name, $request, $routeParser, $page_params): null;

        $previous_page = $pagination->isPreviousPage() ?
            $this->getLink($route_name, $request, $routeParser, array_merge($page_params, [
                "page" => $pagination->previousPage()
            ])) : null;

        $next_page = $pagination->isNextPage() ?
            $this->getLink($route_name, $request, $routeParser, array_merge($page_params, [
                "page" => $pagination->nextPage()
            ])) : null;

        $pagination_last_link = $pagination->lastLink(true);
//        dd($pagination_last_link);
        $last_page = !is_null($pagination_last_link) ?
            $this->getLink($route_name, $request, $routeParser, array_merge($page_params, [
                "page" => $pagination_last_link
            ])) : null;

        return [
            "pages" => $links,
            "current_page" => $current_page,
            "first_page" => $firts_page,
            "last_page" => $last_page,
            "previous_page" => $previous_page,
            "next_page" => $next_page,
            "results_on_page" => count($data),
            "total" => $total,
            "total_pages" => $total_pages,
            "data" => $data
        ];
    }

    protected function getLink (string $route_name, Request $request, $routeParser, $params = []) : string {
        if($this->relative_links)
            return $routeParser->urlFor(
                $route_name,
                [],
                $params
            );

        return $routeParser->fullUrlFor(
            $request->getUri(),
            $route_name,
            [],
            $params
        );
    }

    protected function indexQueryParams(Request $request) {
        $query_arr = $this->uriQuery($request);

        $relative_links = !empty($query_arr['relative_links']) && is_numeric($query_arr['relative_links']) ?
            (bool)$query_arr['relative_links'] : false;

        $page = !empty($query_arr['page']) && is_numeric($query_arr['page']) ?
            (int)$query_arr['page'] : 1;

        $per_page = !empty($query_arr['per_page']) && is_numeric($query_arr['per_page']) ?
            (int)$query_arr['per_page'] : $this->default_per_page;

        $settlement = $query_arr['settlement'] ?? null;

        $offset = ($page * $per_page) - $per_page;

        return [
            "page" => $page,
            "per_page" => $per_page,
            "offset" => $offset,
            "settlement" => $settlement,
            "relative_links" => $relative_links,
        ];
    }

}