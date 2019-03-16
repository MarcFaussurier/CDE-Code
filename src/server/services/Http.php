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

        $this->service->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
            $this->initDbIfNotSet();
            $psr7Request = $this->convertToPsrRequest($request);
            $requestHandler = new RequestHandler();
            $requestHandler->setCore($this->core);
            $path = __DIR__ . "/../middlewares.php";
            var_dump($path);
            $f = require_once $path;
            var_dump($f);
            $dispatcher = new Dispatcher($requestHandler, $f);
            $psr7response = $dispatcher->handle($psr7Request);

            /*
            $user = new User(1);
            $user->username = "toto";
            $user->grade = new Grade(1);
            $user->groups[0] = new Group();
            $user->groups[0]->name = "YEAHHHH";
            $group = new  Group();
            $group->name = "testone";
            $user->groups[] = $group;
            $user->save();
            var_dump($user); */


            var_dump($request);

            $response->end("hello world");
        });
    }

    /**
     * @param \Swoole\Http\Request $request
     * @return RequestInterface
     */
    public function convertToPsrRequest($request): ServerRequestInterface {
      //  return new R
        var_dump($request);
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
     * @param ResponseInterface $response
     * @return \Swoole\Http\Response
     */
    public function convertToPsrResponse(ResponseInterface $response) : \StdClass {

    }


    public function start(): void {
        var_dump("SERVER STARTED");
        $this->service->start();
    }
}
