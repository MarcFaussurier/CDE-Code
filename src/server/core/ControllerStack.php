<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 03/03/2019
 * Time: 10:37
 */

namespace CloudsDotEarth\App\Core;


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
    }
}