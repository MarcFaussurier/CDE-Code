<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 12:02
 */


namespace CloudsDotEarth\App\Services;

use CloudsDotEarth\App\Models\Grade;
use CloudsDotEarth\App\Models\Group;
use CloudsDotEarth\App\Models\User;
use CloudsDotEarth\Bundles\Core\ControllerStack;
use CloudsDotEarth\Bundles\Core\Core;
use CloudsDotEarth\Bundles\Core\Interfaces\ServiceInterface;
use CloudsDotEarth\Bundles\Core\RequestHandler;
use CloudsDotEarth\Bundles\Core\Service;
use CloudsDotEarth\Bundles\core\ViewCompiler;
use Ellipse\Dispatcher;
use Jasny\HttpMessage\ServerRequest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Http extends Service implements ServiceInterface {

    public const supportedTypes = [
        "GET", "POST", "UPDATE", "DELETE"
    ];

    public $defaultPort = 8080;

    public function register(): void {
        $this->service = new \Swoole\Http\Server($this->defaultIP, $this->defaultPort);

        $this->service->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response &$response) {
            $this->initDbIfNotSet();
            $psr_request = $this->convertToPsrRequest($request);
            $psr_response = $this->core->handle($psr_request);
            $this->replyUsingResponse($response, $psr_response);
        });
    }

    /**
     * @param \Swoole\Http\Request $request
     * @return RequestInterface
     */
    public function convertToPsrRequest($request): ServerRequestInterface {
      //  return new R
        $_SERVER = $GLOBALS["_SERVER"] = is_null($request->server) ? [] : $request->server;
        $_REQUEST = $GLOBALS["_REQUEST"] = is_null($request->request) ? [] : $request->request;
        $_COOKIE = $GLOBALS["_COOKIE"] = is_null($request->cookie) ? [] : $request->cookie;
        $_GET = $GLOBALS["_GET"] = is_null($request->get) ? [] : $request->get;
        $_FILES = $GLOBALS["_FILES"] = is_null($request->files) ? [] : $request->files;
        $_POST = $GLOBALS["_POST"] = is_null($request->post) ? [] : $request->post;
        $request = (new ServerRequest())->withGlobalEnvironment(true);

        return $request;
    }

    /**
     * @param \Swoole\Http\Response $swooleResponse
     * @param ResponseInterface $psrResponse
     * @return void
     */
    public function replyUsingResponse(&$swooleResponse, ResponseInterface $psrResponse) : void {
        foreach ($psrResponse->getHeaders() as $key => $header) {
            $swooleResponse->header($key, join(",", $header));
        }
        $swooleResponse->end($psrResponse->getBody()->getContents());
        var_dump($psrResponse->getBody()->getContents());
    }


    public function start(): void {
        var_dump("SERVER STARTED");
        $this->service->start();
    }
}
