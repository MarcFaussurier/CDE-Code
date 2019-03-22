<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 04/03/2019
 * Time: 09:22
 */

namespace CloudsDotEarth\Bundles\Core;

use CloudsDotEarth\Bundles\Core\Interfaces\CoreAwareInterface;

/**
 * Class ServiceStack
 * @package CloudsDotEarth\App\Core
 */
class ServiceStack extends Stack implements CoreAwareInterface
{
    /**
     * @var Service[]
     */
    public $data = [];

    /**
     * @var Core
     */
    public $core;

    /**
     * ServiceStack constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param Core $core
     */
    public function setCore(Core &$core): void
    {
        $this->core = $core;
        $this->stackType = "services";
        if ($this->core->services !== [])
            foreach ($this->core->services as $class)
                array_push($this->data, new $class);
        else {
            // get all controller files
            $classes = $this->getStackFiles();
            foreach ($classes as $k => $v) {
                $class = explode("../", $v);
                $class = $class[count($class) - 1];
                $class = explode(".php", str_replace("/", "\\", $class))[0];
                $class = explode("\\", $class);
                $firstItem = "";
                // convert foo/bar to Foo/Bar
                foreach ($class as $key => $item) {
                    // needed for class prefix
                    if ($firstItem === "") {
                        $firstItem = $item;
                    }
                    $class[$key] = ucfirst($item);
                }
                if ($class[0] === "Src") {
                    unset($class[0]);
                }
                if ($class[1] === "Server") {
                    unset($class[1]);
                }
                $class = "\\" . join("\\", $class);
                // add the appropriate class prefix
                if ($firstItem === "bundles") {
                    $class = "\\CloudsDotEarth" . $class;
                } else {
                    $class = "\\CloudsDotEarth\\App" . $class;
                }
                $class = new $class();
                // push a new instance
                array_push($this->data, $class);
            }
        }
        foreach ($this->data as $v) {
            $v->setCore($core);
        }
    }

    public function start(): void {
        foreach ($this->data as $v) {
            $v->start();
        }
        if (defined("UNIX_MODE")) {
            \Workerman\Worker::runAll();
        }
    }
}