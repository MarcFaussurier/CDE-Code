<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 13:36
 */

namespace CloudsDotEarth\Bundles\Core;

use CloudsDotEarth\Bundles\Core\Interfaces\CoreAwareInterface;
use CloudsDotEarth\Bundles\Core\Interfaces\ServiceInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Service implements CoreAwareInterface, ServiceInterface {
    /**
     * @var string[]
     */
    public $supportedTypes;

    /**
     * @var string
     */
    public $defaultIP = "0.0.0.0";

    /**
     * @var int
     */
    public $defaultPort = 8080;

    /**
     * @var \Swoole\Server
     */
    public $service;

    /**
     * @var Core
     */
    public $core;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        // todo : replace $this->defaultPort by the one in the yaml config file if any and set needed options
        var_dump($this->getServiceName());
    }

    public function setCore(Core &$core)
    {
        $this->core  = &$core;
        $this->configure();
    }

    private function configure() {
        foreach($this->core->parsedYamlConfig["services"] as $v) {
            var_dump($v);
            if ($v["name"] === $this->getServiceName()) {
                if (isset($v["ip"]))
                    $this->defaultIP = $v["ip"];
                if (isset($v["port"]))
                    $this->defaultPort = $v["port"];
            }
        }
        $this->register();
        foreach($this->core->parsedYamlConfig["services"] as $v) {
            if ($v["name"] === $this->getServiceName() && isset($v["swoole_options"]))
                    $this->service->set($v["swoole_options"]);
        }
    }

    private function getServiceName(): string {
        return lcfirst(($a = explode("\\", get_class($this)))[count($a) - 1]);
    }

    public function convertToPsrRequest(\stdClass $request): ServerRequestInterface {
        echo "WARNING: default service convertToPsrRequest function called ... " . PHP_EOL;
    }

    public function convertToPsrResponse(ResponseInterface $response) : \stdClass{
        echo "WARNING: default service convertToPsrResponse function called ... " . PHP_EOL;
    }

    public function start(): void {
        echo "WARNING: default service start function called ... " . PHP_EOL;
    }

    public function register(): void {
        echo "WARNING: default service register function called ... " . PHP_EOL;
    }

    /***
     * @var bool
     */
    private $wasDbInit = false;

    /**
     * @throws \Exception
     */
    public function initDbIfNotSet(): void {
        if (!$this->wasDbInit) {
            $this->controllerStack = new ControllerStack();

            $this->core->setDb();

            new ViewCompiler
            (
                [__DIR__ . "/../../src/client/views"],
                __DIR__ . "/../../generated/views"
            );

            $mg = new \CloudsDotEarth\Bundles\Core\ModelGenerator
            ($this->core,
                __DIR__ . "/../../generated/models"
            );

            foreach (glob(__DIR__ . "/../../generated/models/*.php") as $k => $v) {
                require_once $v;
            }
            Core::includeDirectory(__DIR__ . "/../../src/server/models");

            $mg->secondStep();
            $this->wasDbInit = true;
        }
    }
}