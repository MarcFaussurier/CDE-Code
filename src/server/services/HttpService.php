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
use Workerman\WebServer;

class HttpService extends Service implements ServiceInterface {

    public $defaultPort = 8080;

    /**
     * @var \Workerman\WebServer
     */
    public $service;

    public function register(): void {
        $this->service = new WebServer
        ("http://".$this->defaultIP.":". $this->defaultPort, [],
            function (ServerRequestInterface $request) : ResponseInterface {
                $this->initDbIfNotSet();
            return $this->core->handle($request);  });
        $this->service->addRoot($this->defaultIP, __DIR__ . "/../../../www");
        $this->service->addRoot("127.0.0.1", __DIR__ . "/../../../www");
        $this->service->addRoot("0.0.0.0", __DIR__ . "/../../../www");
    }
}
