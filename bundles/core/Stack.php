<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 04/03/2019
 * Time: 09:33
 */

namespace CloudsDotEarth\Bundles\Core;

/**
 * Class Stack
 * Stacks allow framework to load faster all its components
 * @package CloudsDotEarth\App\core
 */
class Stack
{
    /**
     * @var string
     */
    private $stackType;

    /**
     * @var \stdClass[]
     */
    public $data = [];

    /**
     * ControllerMethodStack constructor.
     * Will load all class instances in $this->data
     * Used to load services, controllers ...
     */
    public function __construct(string $stackType)
    {
        $this->stackType = $stackType;
        // get all controller files
        $classes = $this->getStackFiles();
        foreach ($classes as $k => $v) {
            // convert toto/../foo/bar.php to foo/bar
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
            $class = "\\" . join("\\", $class);
            // add the appropriate class prefix
            if ($firstItem === "bundles") {
                $class = "\\CloudsDotEarth" . $class;
            } else {
                $class = "\\CloudsDotEarth\\App" . $class;
            }
            // push a new controller instance
            array_push($this->data, new $class());
        }
    }

    /**
     * Will load all stack files and return a stack containing all key : value arguments
     * @return array
     */
    public function getStackFiles(): array {
        $bundleFiles = glob(__DIR__ . "/../../../bundles/*/". $this->stackType ."/*.php");
        $srcFiles = glob(__DIR__ . "/../" . $this->stackType ."/*.php");
        // array merge priority is the higher to the left
        return array_merge($bundleFiles, $srcFiles);
    }
}