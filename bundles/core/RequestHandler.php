<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 16/03/2019
 * Time: 09:45
 */

namespace CloudsDotEarth\Bundles\Core;


use CloudsDotEarth\Bundles\Core\Interfaces\CoreAwareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface, CoreAwareInterface
{
    /**
     * @var Core
     */
    public $core;

    public function setCore(Core &$core)
    {
        $this->core = $core;
    }

    /**
     * Handles a request and produces a response.
     * May call other collaborating code to generate the response.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        // run all controllers
        var_dump("GT METHD :: ");
        var_dump($request->getMethod());
    }
}