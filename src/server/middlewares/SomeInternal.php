<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 16/03/2019
 * Time: 09:57
 */

namespace CloudsDotEarth\App\middlewares;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SomeInternal implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // TODO: Implement process() method.
        $_SERVER["REQUEST_METHOD"] = "TESTONE";
        $request->withMethod($_SERVER["REQUEST_METHOD"]);
        return $handler->handle($request);
    }
}