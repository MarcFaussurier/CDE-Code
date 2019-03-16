<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 17:25
 */

namespace CloudsDotEarth\Bundles\Core\Controllers;

use CloudsDotEarth\Bundles\Core\Controller;
use Jasny\HttpMessage\OutputBufferStream;
use Jasny\HttpMessage\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TotoTYest extends Controller {
    /**
     * Home controller
     *
     * @uri \/*
     * @services .
     * @priority 10
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function someTest(ServerRequestInterface $request, ResponseInterface &$response) : bool {
        $status = 200;
        $headers = ['X-Foo' => 'Bar'];
        $body = 'someTest!';
        $protocol = '1.1';
        $response = new \GuzzleHttp\Psr7\Response($status, $headers, $body, $protocol);
        return false;
    }
}