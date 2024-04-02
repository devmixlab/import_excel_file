<?php
namespace App\App;

class ConsoleApp extends App{

    function __construct(array $params = []) {
        $params = array_merge([ConsoleApp::PARAM_ENV_NAMES => ".env.console"], $params);
        parent::__construct($params);

        $container_config = $this->config["container"]["console"];
        $this->setContainer($container_config);
//        $c = $this->app->getContainer();
    }

}