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
        $static = [
            'css'  => 'text/css',
            'js'   => 'text/javascript',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'jpg'  => 'image/jpg',
            'jpeg' => 'image/jpg',
            'mp4'  => 'video/mp4',
            'map' => 'application/json'
        ];

        $this->service->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response &$response) use ($static) {
            $this->initDbIfNotSet();
            if ($this->getStaticFile($request, $response, $static)) {
                return;
            }
            $psr_request = $this->convertToPsrRequest($request);
            $psr_response = $this->core->handle($psr_request);
            $this->replyUsingResponse($response, $psr_response);
        });
    }

    /**
     * @param swoole_http_request $request
     * @paramdocument_rootdocument_rootdc swoole_http_response $response
     * @param array $static
     * @return bool
     */
    public function getStaticFile(
        \Swoole\Http\Request $request,
        \Swoole\Http\Response &$response,
        array $static
    ) : bool {
        $staticFile = __DIR__ . "/../../../www". $request->server['request_uri'];
        var_dump($staticFile);
        if (! file_exists($staticFile)) {
            var_dump("NT EXIST");
            return false;
        }
        var_dump("EXISTRS");
        $type = pathinfo($staticFile, PATHINFO_EXTENSION);
        if (! isset($static[$type])) {
            return false;
        }
        $response->header('Content-Type', $static[$type]);
        $response->sendfile($staticFile);
        return true;
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
