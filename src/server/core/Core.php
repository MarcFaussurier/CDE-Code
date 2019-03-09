<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 17:17
 */

namespace CloudsDotEarth\App\Core;

use Symfony\Component\Yaml\Yaml;

class Core {
    /**
     * @var ControllerStack
     */
    public $controllerStack;

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
        $this->parsedYamlConfig = Yaml::parseFile(__DIR__ . "/../../../config.yaml");
        $this->controllerStack = new ControllerStack();
        $this->serviceStack = new ServiceStack();
        $this->serviceStack->setCore($this);
        $this->setDb();
    }

    public function setDb(): void {
            $server = array(
                'host' => '127.0.0.1',
                'port' => 3306,
                'user' => 'root',
                'password' => '123456##',
                'database' => 'cde_code',
                'charset' => 'utf8',
                'timeout' => 2,  // Optional: connection timeout (non-query timeout), default is，SW_MYSQL_CONNECT_TIMEOUT（1.0）
                'prefix' => '', // table prefix
                'debug' => true // Debug mode, open will output the query statement when executing the query
            );

            $db = new \Swoole\Coroutine\MySQL();
            try {
                $db->connect($server);
            } catch (\Throwable $ex) {
                var_dump($ex);
            }
            /*
            if (!$db->connect($server)) {
                var_dump($db->error);
                var_dump($db->errno);
                throw new Exception("NOT CONNECTED");
            } else {
                var_dump($db->error);
                var_dump($db->errno);
                echo "Connected";
            }
           // $db->setDefer(false);
          $start = (float) array_sum(explode(' ',microtime()));
            $stmt = $db->prepare('SELECT * FROM `users`');
            $ret = $stmt->execute([]);
            $end = (float) array_sum(explode(' ',microtime()));
            print "Processing time: ". sprintf("%.4f", ($end-$start))." seconds.";
            var_dump($ret);
            $db->close(); */
    }
}