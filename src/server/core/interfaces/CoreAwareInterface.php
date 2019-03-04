<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 04/03/2019
 * Time: 10:19
 */

namespace CloudsDotEarth\App\Core\Interfaces;

use CloudsDotEarth\App\Core\Core;

interface CoreAwareInterface {
    public function setCore(Core &$core);
}