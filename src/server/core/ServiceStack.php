<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 04/03/2019
 * Time: 09:22
 */

namespace CloudsDotEarth\App\Core;

use CloudsDotEarth\App\Core\Interfaces\CoreAwareInterface;

/**
 * Class ServiceStack
 * @package CloudsDotEarth\App\Core
 */
class ServiceStack extends Stack implements CoreAwareInterface
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
     */
    public function __construct()
    {
        parent::__construct("services");
    }

    /**
     * @param Core $core
     */
    public function setCore(Core &$core): void
    {
        foreach ($this->data as $v) {
            $v->setCore($core);
        }
    }

    public function start(): void {
        foreach ($this->data as $v) {
            $v->start();
        }
    }
}