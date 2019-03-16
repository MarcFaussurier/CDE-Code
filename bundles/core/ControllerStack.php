<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 03/03/2019
 * Time: 10:37
 */

namespace CloudsDotEarth\Bundles\Core;


class ControllerStack extends Stack
{
    /**
     * @var Controller[]
     */
    public $data = [];

    /**
     * ControllerMethodStack constructor.
     * Will load all controllers in $this->controllers
     */
    public function __construct()
    {
       parent::__construct("controllers");
       var_dump("SETTING CONTROLLER METADATA : ");
       foreach ($this->data as $controller) {
           echo "setting metadata of " . get_class($controller) . PHP_EOL;
           $controller->setMetaData();
       }
    }
}