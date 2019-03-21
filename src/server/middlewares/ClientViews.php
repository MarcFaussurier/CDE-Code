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
use Zend\Diactoros\Stream;
use Zend\Diactoros\StreamFactory;

class ClientViews implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // TODO: Implement process() method._REQUEST_STATE
        $_SERVER["REQUEST_METHOD"] = "TESTONE";
        $request->withMethod($_SERVER["REQUEST_METHOD"]);
        /**
         * @var ResponseInterface $result
         */
        $result = $handler->handle($request);
        $bodyStr=   $result->GetBody()->getContents();
        $matches = [];
        if (preg_match("/var JAVASCRIT_VIEW_HOOK;/", $bodyStr, $matches) > 0) {
            //ar_dump($bodyStr);
            $newBody = str_replace
            ("var JAVASCRIT_VIEW_HOOK;",
                'window["templates"] = ["test"]; window["previousStates"] = [];', $bodyStr);
            $status = $result->getStatusCode();
            $headers = $result->getHeaders();
            $body = $newBody;
            $protocol = $result->getProtocolVersion();
            $result = new \GuzzleHttp\Psr7\Response($status, $headers, $body, $protocol);
        }
        return $result;
    }
}