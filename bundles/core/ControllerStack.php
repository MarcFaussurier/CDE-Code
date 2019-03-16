<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 03/03/2019
 * Time: 10:37
 */

namespace CloudsDotEarth\Bundles\Core;


use Jasny\HttpMessage\Response;
use Jasny\HttpMessage\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ControllerStack extends Stack implements RequestHandlerInterface
{
    /**
     * @var Controller[]
     */
    public $data = [];

    public $methods = [];

    /**
     * ControllerStack constructor.
     * @throws \ReflectionException
     */
    public function __construct()
    {
       parent::__construct("controllers");
       var_dump("SETTING CONTROLLER METADATA : ");
       foreach ($this->data as $controller) {
           echo "setting metadata of " . get_class($controller) . PHP_EOL;
           $controller->setMetaData();
           $this->methods = array_merge($this->methods, $controller->methods);
           uasort($this->methods, function ($a, $b) {
               return ($a[3] > $b[3]) ? -1 : 1;
           });
       }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        $response = new Response();
        $n0ne = true;
        foreach ($this->methods as $v) {
            /**
             * @var Controller $controller
             */
            $controller = $v[4];
            var_dump($v[1]);
            if ((preg_match("/" . $v[1] . "/", $request->getUri()) !== false) &&
                (preg_match("/" . $v[2] . "/", $request->getMethod()) !== false)) {
                $method = $v[0];
                if ($controller->$method($request, $response)) {
                    break;
                }
                $n0ne = false;
            }
        }
        if ($n0ne) {
            $status = 200;
            $headers = ['X-Foo' => 'Bar'];
            $body = '404 error!';
            $protocol = '1.1';
            $response = new \GuzzleHttp\Psr7\Response($status, $headers, $body, $protocol);
        }
        return $response;
    }
}