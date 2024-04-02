<?php
namespace App\App;

use Slim\App as SlimApp;
use DI\Container;

class App implements AppInterface {

    /**
     * @var \Slim\App instance of slim app.
     */
    static protected SlimApp $app;

    /**
     * @var string env file to use, can be setted with constructor params.
     */
    protected string $env_file_name = ".env";

    /**
     * @var string name of parameter passed in with construct $parameters,
     *              contains name of env file to use.
     */
    const PARAM_ENV_NAMES = "param_env_names";

    /**
     * @param array $params          Parameters
     *
     * @returns  self
     */
    function __construct(protected array $params = []) {
        $this->processParams();
        $this->loadEnv();
        $this->config = require(ROOT . 'config.php');
        require ROOT . 'helpers.php';
    }

    /**
     * Processes parameters passed into constructor
     *
     * @returns  void
     */
    protected function processParams() : void {
        if(!empty($this->params[static::PARAM_ENV_NAMES]) && is_string($this->params[static::PARAM_ENV_NAMES]))
            $this->env_file_name = $this->params[static::PARAM_ENV_NAMES];
    }

    /**
     * Loads env file
     *
     * @returns  self
     */
    protected function loadEnv() : self {
        $dotenv = \Dotenv\Dotenv::createImmutable(ROOT, $this->env_file_name);
        $dotenv->load();

        return $this;
    }

    /**
     * Sets application container
     *
     * @param array $container_config container config
     *
     * @returns  self
     */
    protected function setContainer(array $container_config = []) : self {
        self::$app = \DI\Bridge\Slim\Bridge::create(
            new \DI\Container($container_config)
        );

        $c = self::$app->getContainer();
        $c->set(AppInterface::class, function() {
//            return self::$app;
            return $this;
        });

        return $this;
    }

    /**
     * Retrievs application container
     *
     * @returns  Container
     */
    static public function getContainer () : Container {
        return self::$app->getContainer();
    }

//    static public function testt () {
//        return '111';
//    }

    /**
     * @returns SlimApp instance of slim application
     */
    static public function getApp () : SlimApp {
        return self::$app;
    }

//    protected function regCacheEngine() : self {
//        $filesystemAdapter = new Local(CACHE . '/');
//        $this->cache = new Filesystem($filesystemAdapter);
//
//        return $this;
//    }

    public function __call($name, $arguments) {
        if(method_exists(self::$app, $name))
            return call_user_func_array([self::$app, $name], $arguments);
    }

}