<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 04/03/2019
 * Time: 09:22
 */

namespace CloudsDotEarth\App\Core;

/**
 * Class ServiceStack
 * @package CloudsDotEarth\App\Core
 */
class ServiceStack extends Stack
{
    /**
     * @var Service[]
     */
    public $data = [];

    /**
     * @var Core
     */
    public $core;

    /**
     * ServiceStack constructor.
     * @param Core $core
     */
    public function __construct(Core &$core)
    {
        parent::__construct("services");
        var_dump($this->data);
    }
}