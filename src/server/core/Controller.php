<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 13:36
 */

namespace CloudsDotEarth\App\Core;

class Controller {

    public const requestTypeKeySeparator = "#_°°9";

    /**
     * @var ControllerMethod[]
     */
    public $methods = [];


    /**
     * @param string $urlPattern
     * @param callable $callback
     * @param mixed $requestType
     */
    public function registerMethod(string $urlPattern, callable $callback, string $requestType = "GET") {
        array_push($this->methods, new ControllerMethod($urlPattern, $callback, $requestType));
    }
}