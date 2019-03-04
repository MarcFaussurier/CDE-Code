<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 13:36
 */

namespace CloudsDotEarth\App\Core;

use CloudsDotEarth\App\Core\Interfaces\CoreAwareInterface;
use CloudsDotEarth\App\Core\Interfaces\ServiceInterface;

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

    public function start(): void {
        echo "WARNING: default service start function called ... " . PHP_EOL;
    }

    public function register(): void {
        echo "WARNING: default service register function called ... " . PHP_EOL;
    }
}