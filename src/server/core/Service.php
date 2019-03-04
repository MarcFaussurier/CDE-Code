<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 13:36
 */

namespace CloudsDotEarth\App\Core;

class Service {
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
     * Service constructor.
     */
    public function __construct()
    {
        // todo : replace $this->defaultPort by the one in the yaml config file if any and set needed options
    }

    private function getServiceName(): string {
        return "HTTP";
    }
}