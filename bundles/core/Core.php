<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 17:17
 */

namespace CloudsDotEarth\Bundles\Core;

use CloudsDotEarth\App\Models\Grade;
use CloudsDotEarth\App\Models\Group;
use CloudsDotEarth\App\Models\User;
use Ellipse\Dispatcher;
use LightnCandy\LightnCandy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;

class Core implements RequestHandlerInterface {
    /**
     * @var ControllerStack
     */
    public $controllerStack;
    /**
     * @var mixed[]
     */
    public $envConfig = [];
    /**
     * @var array[]
     */
    public $parsedYamlConfig;

    /**
     * @var ServiceStack
     */
    public $serviceStack;

    /**
     * @var \Swoole\Coroutine\MySQL
     */
    public $db;

    /**
     * @var \Swoole\Coroutine\MySQL
     */
    public static $staticDb;

    /**
     * @var \Twig\Environment
     */
    public static $twig;
    /**
     * @var MiddlewareInterface[]
     */
    public $middleware;

    public $services = [];
    /**
     * Core constructor.
     */
    public function __construct($services = "")
    {
        $this->services = $services;
        (new Dotenv()
        )->     load(realpath(__DIR__ . "/../../configs/.env"));
        $this->envConfig["database"] = [];
        foreach($_ENV as $k => $v) {
            $upperKey = strtoupper($k);
            if (strtoupper(substr($upperKey,0,3)) === "DB_") {
                $keyToUse = "";
                if ($upperKey === "DB_NAME") {
                    $keyToUse = "database";
                } else {
                    $keyToUse = substr($upperKey,3,strlen($upperKey) - 3);
                }
                $this->envConfig["database"][strtolower($keyToUse)] = $v === "true" ? true : $v === "false" ? false : $v;
            } else {
                $this->envConfig[strtolower($k)] = $v;
            }
        }
        $yamlPath = __DIR__ . "/../../configs/" . $this->envConfig["env_mode"] . "/config.yaml";
        if (!file_exists($yamlPath)) {
            throw new \Exception("Unable to open yaml file : " . $yamlPath . " (please check .env file)");
        }
        $this->parsedYamlConfig = Yaml::parseFile(__DIR__ . "/../../configs/dev/config.yaml");
        $this->serviceStack = new ServiceStack();
        $this->serviceStack->setCore($this);
        $this->controllerStack = new ControllerStack();
        $this->middleware = require __DIR__ . "/../../src/server/middleware.php";
        $this->serviceStack->start();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        // REQUEST STATE WILL CONTAIN GENERATED TWIG ARGUMENTS AS ARRAY
        $GLOBALS["_REQUEST_STATE"] = [null];
        $dispatcher = new Dispatcher($this->controllerStack, $this->middleware);
        $result =  $dispatcher->handle($request);
        return $result;
    }

    /**
     * Will simply include all php files in a dir
     * @param string $path
     */
    public static function includeDirectory(string $path) {
        $files = glob($path . "/*.php");
        foreach ($files as $k => $v) {
           // echo "including .. " . $v . PHP_EOL;
            require_once $v;
        }
    }

    public function setDb(): void {

            $this->db = new \Swoole\Coroutine\MySQL();
            try {
                $this->db->connect($this->envConfig["database"]);
            } catch (\Throwable $ex) {
                echo "Unable to connect to db :(";
                var_dump($ex);
            }
            self::$staticDb = $this->db;
    }
}