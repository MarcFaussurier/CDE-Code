<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 17:25
 */

namespace CloudsDotEarth\Bundles\Test\Controllers;

use CloudsDotEarth\Bundles\Core\Controller;

class Toto extends Controller {
    public function __construct()
    {
        $this->registerMethod("/", function ($request, $regexMatches) {
            echo "march";
        });
    }
}