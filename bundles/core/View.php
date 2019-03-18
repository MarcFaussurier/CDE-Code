<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 13:36
 */

namespace CloudsDotEarth\Bundles\Core;

class View {
    public $result = "";
    public function __construct(string $name, array $params)
    {
        $this->result = Core::$twig->render($name, $GLOBALS["_REQUEST_STATE"] = $params);
    }

    public function __toString() : string
    {
       return $this->result;
    }
}