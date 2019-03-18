<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 12:03
 */

namespace CloudsDotEarth\App\Controllers;

use CloudsDotEarth\Bundles\Core\Controller;
use CloudsDotEarth\Bundles\Core\View;
use Jasny\HttpMessage\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Home extends Controller {
    /**
     * Home controller
     *
     * @uri \/*
     * @services .
     * @priority 0
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function home(ServerRequestInterface $request , ResponseInterface &$response) : bool {
        $status = 200;
        $headers = ['X-Foo' => 'Bar'];
        $body = 'home!';
        $protocol = '1.1';
        $body = new View("pages/home.twig", ["name" => "Fabien"]);
        $response = new \GuzzleHttp\Psr7\Response($status, $headers, $body, $protocol);
        return false;
    }
}