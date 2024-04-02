<?php
namespace App\App;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
//use App\Middleware\WebGroupMiddleware;
//use App\Middleware\TwigGroupMiddleware;


class WebApp extends App{

    function __construct(array $params = []) {
        parent::__construct($params);

        // Register App with Container
//        $container_config = $this->config["container"]["web"];
//        $this->app = \DI\Bridge\Slim\Bridge::create(
//            new \DI\Container($container_config)
//        );

        $container_config = $this->config["container"]["web"];
        $this->setContainer($container_config);

        $c = self::$app->getContainer();

        // Add Request to container
        self::$app->add(function (Request $request, RequestHandler $handler) use ($c) {
            $c->set(Request::class, function() use ($request) {
                return $request;
            });

            $response = $handler->handle($request);
            return $response;
        });

        // Register custom rules Respect/Validation
        \Respect\Validation\Factory::setDefaultInstance(
            (new \Respect\Validation\Factory())
                ->withRuleNamespace('App\\Validation\\Rules')
                ->withExceptionNamespace('App\\Validation\\Exceptions')
        );

        self::$app->addBodyParsingMiddleware();

        $this->registerTemplateEngine()
            ->registerRoutes();
    }

    public function test() {
        return 222;
    }

    protected function registerRoutes() : self {
        self::$app->group('', function (RouteCollectorProxy $route) {
            require ROUTES . 'web.php';
        });
//            ->add(new TwigGroupMiddleware());

        self::$app->group('/api', function (RouteCollectorProxy $route) {
            require ROUTES . 'api.php';
        });
//            ->add(new WebGroupMiddleware());

        return $this;
    }

    protected function registerTemplateEngine() : self {
        $twig = \Slim\Views\Twig::create(VIEWS, ['cache' => false]);
        self::$app->add(\Slim\Views\TwigMiddleware::create(self::$app, $twig));

        return $this;
    }

}