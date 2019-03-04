<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 12:05
 */

namespace CloudsDotEarth\App\Controllers;

use CloudsDotEarth\App\Core\Controller;

class Registration extends Controller {
    public function __construct()
    {
        $this->registerMethod("/", function ($request, $regexMatches) {
            echo "march";
        });
    }
}