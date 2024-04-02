<?php
namespace App;

use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use App\FileImporter\Importer;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use DI\Container;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Middleware\WebGroupMiddleware;
use App\Middleware\TwigGroupMiddleware;

use Respect\Validation\Factory;

use Slim\Routing\RouteContext;


//use App\Database\DBInterface;

class App {

    static protected $app;
    protected $cache;

    protected array $config;

    function __construct() {
        $this->config = require(ROOT . 'config.php');
//        $container = new \Slim\Container;
//        $container = new Container();
//        AppFactory::setContainer();

//        self::$app = AppFactory::create();

//        self::$app = \DI\Bridge\Slim\Bridge::create($this->webContainer());
    }

    static public function getApp () {
        return self::$app;
    }

//    protected function webContainer() {
//        return new \DI\Container($this->config["container"]["web"]);
//    }

    protected function consoleContainer() {
        return new \DI\Container([
            \App\Database\DBInterface::class => function () {
                return new \App\Database\DB([
                    "host" => "127.0.0.1"
                ]);
            },
        ]);
    }

    public function test() {
        return 222;
    }

    public function initWeb() {
        $this->loadEnv();

//        $this->regTemplateEngine();

        // Register container
        $container_config = $this->config["container"]["web"];
        $container_config["app"] = function () {
            return self::$app;
        };
        self::$app = \DI\Bridge\Slim\Bridge::create(
            new \DI\Container($container_config)
        );

        // Register custom rules Respect/Validation
        Factory::setDefaultInstance(
            (new Factory())
                ->withRuleNamespace('App\\Validation\\Rules')
                ->withExceptionNamespace('App\\Validation\\Exceptions')

        );

//        self::$app->addBodyParsingMiddleware();
//        self::$app->addRoutingMiddleware();

//        $app->addRoutingMiddleware();
//        $app->addErrorMiddleware(true, true, true);

        $beforeMiddleware = function (Request $request, RequestHandler $handler) {
//        $beforeMiddleware = function (Request $request, RequestHandler $handler) {

//            $routeParser = \Slim\Routing\RouteContext::fromRequest($request)->getRouteParser();
//            $request->router = $routeParser;

            $c = self::$app->getContainer();
            $c->set(Request::class, function() use ($request) {
//                $routeParser = \Slim\Routing\RouteContext::fromRequest($request)->getRouteParser();
//                $request->router = $routeParser;
                return $request;
            });

//            $response = 11;
//            $c->set(Response::class, function() use (&$response) {
//                return $response;
//            });

            $response = $handler->handle($request);

//            dd(11);
//            $c->set(Response::class, function() use ($response) {
//                return $response;
//            });

            return $response;

        };

        self::$app->add($beforeMiddleware);

        $this->loadEnv();
        $this->regTemplateEngine();
//        $this->regCacheEngine();

        require ROOT . 'helpers.php';

//        dd(request());

        self::$app->group('', function (RouteCollectorProxy $route) {
            require ROUTES . 'web.php';
        })->add(new TwigGroupMiddleware());

        self::$app->group('/api', function (RouteCollectorProxy $route) {
            require ROUTES . 'api.php';
        })->add(new WebGroupMiddleware());

        self::$app->run();
    }

    public function initConsole() {
        self::$app = \DI\Bridge\Slim\Bridge::create($this->consoleContainer());

        $this->loadEnv();

//        var_dump(333);
//        exit;

        require ROOT . 'helpers.php';

//        var_dump(333);
//        exit;

//        self::$app->run();
    }

    protected function loadEnv() : self {
//        var_dump(ROOT);
        $dotenv = \Dotenv\Dotenv::createImmutable(ROOT);
        $dotenv->load();

        return $this;
    }

    protected function regTemplateEngine() : self {
        $twig = Twig::create(VIEWS, ['cache' => false]);
        self::$app->add(TwigMiddleware::create(self::$app, $twig));

        return $this;
    }

    protected function regCacheEngine() : self {
        $filesystemAdapter = new Local(CACHE . '/');
        $this->cache = new Filesystem($filesystemAdapter);

        return $this;
    }

    public static function __callStatic($name, $arguments) {
//        if($name == 'getContainer')
//            return call_user_func_array([self::$app, $name], $arguments));
        if(method_exists(self::$app, $name))
            return call_user_func_array([self::$app, $name], $arguments);
    }

}