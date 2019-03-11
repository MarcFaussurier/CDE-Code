<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 17:17
 */

namespace CloudsDotEarth\Bundles\Core;

use LightnCandy\LightnCandy;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;

class Core {
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
     * Core constructor.
     */
    public function __construct()
    {
        (
            new Dotenv()
        )   ->load(realpath(__DIR__ . "/../../configs/.env"));

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
        var_dump(LightnCandy::compile(file_get_contents(__DIR__ . "/../../src/server/views/pages/home.hbs")));
        $this->controllerStack = new ControllerStack();
        $this->serviceStack = new ServiceStack();
        $this->serviceStack->setCore($this);
        $this->setDb();

        new ViewCompiler([__DIR__ . "/../../src/client/views"], __DIR__ . "/../../generated/views");
    }

    public function setDb(): void {

            $db = new \Swoole\Coroutine\MySQL();
            try {
                $db->connect($this->envConfig["database"]);
            } catch (\Throwable $ex) {
                var_dump($ex);
            }


           // $db->setDefer(false);
          $start = (float) array_sum(explode(' ',microtime()));
            $stmt = $db->prepare('SELECT * FROM `users`');
            $ret = $stmt->execute([]);
            $end = (float) array_sum(explode(' ',microtime()));
            print "Processing time: ". sprintf("%.4f", ($end-$start))." seconds.";
            var_dump($ret);
            $db->close();
    }
}