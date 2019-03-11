<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 12:03
 */
namespace CloudsDotEarth\App\Controllers;

use CloudsDotEarth\Bundles\Core\Controller;

class SearchBar extends Controller {
    public function __construct()
    {
        $this->registerMethod("/", function ($request, $regexMatches) {
            echo "march";
        });
    }
}
