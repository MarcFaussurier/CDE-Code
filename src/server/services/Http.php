<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 12:02
 */


namespace CloudsDotEarth\App\Services;

use CloudsDotEarth\App\Core\Interfaces\Service as IService;
use CloudsDotEarth\App\Core\Service;

class Http extends Service implements IService {

    public const supportedTypes = [
        "GET", "POST", "UPDATE", "DELETE"
    ];

    public $defaultPort = 8080;

    public function register(): void {
        $this->service = new \Swoole\Http\Server($this->defaultIP, $this->defaultPort);

        $this->service->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) use (&$kernel) {
            $response->end("hello world");
            //  $response->end();
        });

        $this->service->set([
            'document_root' => realpath(__DIR__ . "/../public"),
            'enable_static_handler' => true
        ]);
    }

    public function start(): void {
        $this->service->start();
    }
}
