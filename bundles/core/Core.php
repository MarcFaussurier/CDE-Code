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
     * @var \Swoole\Coroutine\MySQL
     */
    public static $staticDb;

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
        $this->controllerStack = new ControllerStack();
        $this->serviceStack = new ServiceStack();
        $this->serviceStack->setCore($this);
        $this->setDb();

        new ViewCompiler
        (
            [__DIR__ . "/../../src/client/views"],
            __DIR__ . "/../../generated/views"
        );

        $mg = new \CloudsDotEarth\Bundles\Core\ModelGenerator
        ($this,
            __DIR__ . "/../../generated/models"
        );

        foreach (glob(__DIR__ . "/../../generated/models/*.php") as $k => $v) {
            require_once $v;
        }
        self::includeDirectory(__DIR__ . "/../../src/server/models");

        $mg->secondStep();

        // update sample
       $user = new User(1);
       // $user->username = "toto";
        $user->grade = new Grade(1);
        $user->groups[0] = new Group();
        $user->groups[0]->name = "YEAHHHH";
        $group = new  Group();
        $group->name = "testone";
        $user->groups[] = $group;

        $user->save();

         var_dump($user);

     //   $results = (new User())->select("row_id = ?", [1]);
     //   var_dump($results);

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