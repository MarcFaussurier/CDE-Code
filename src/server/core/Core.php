<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 17:17
 */

namespace CloudsDotEarth\App\Core;

use Symfony\Component\Yaml\Yaml;

class Core {
    /**
     * @var ControllerStack
     */
    public $controllerStack;

    /**
     * @var array[]
     */
    public $parsedYamlConfig;

    /**
     * @var ServiceStack
     */
    public $serviceStack;

    /**
     * Core constructor.
     */
    public function __construct()
    {
        $this->parsedYamlConfig = Yaml::parseFile(__DIR__ . "/../../../config.yaml");
        $this->controllerStack = new ControllerStack();
        $this->serviceStack = new ServiceStack();
        $this->serviceStack->setCore($this);
        $this->serviceStack->start();
    }
}