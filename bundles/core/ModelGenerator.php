<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 11/03/2019
 * Time: 20:09
 */

namespace CloudsDotEarth\Bundles\core;


class ModelGenerator
{

    public $outputDirectory;
    /**
     * ModelGenerator constructor.
     * @param Core $core
     * @param string $outputDirectories
     * @throws \Exception
     */
    public function __construct(Core &$core, string $outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
        $tables = Utils::getDatabaseTables($core);
        foreach ($tables as $k => $v) {
            // we don't use table that start with a # as they are for jointures
            if (substr($v, 0, 1) !== "#") {
                $cols = Utils::getColsInTable($core, $v);
                $this->writePropertiesClass($v, $cols);
            }
        }
    }

    /**
     * @param string $MySQLType
     * @return string
     * @throws \Exception
     */
    public static function MySQLTypeToPHP(string $MySQLType) {
        switch($a = explode("(", $MySQLType)[0]) {
            case "int":
                return "int";
            case "varchar":
                return "string";
            case "text":
                return "string";
            case "datetime":
                return \DateTime::class;
            case "timestamp":
                return \DateTime::class;
            default:
                var_dump($a);
                throw new \Exception("Unknow MySQL type");
                break;
        }
    }

    /**
     * @param string $table
     * @param array $cols
     * @throws \Exception
     */
    public function writePropertiesClass(string $table, array $cols) : void {
        $className = ucfirst($table)."Properties";
        $fileContent = "<?php 
class $className extends \\".Model::class."
{";
        $fileContent .= "
    public \$tableName = '" . trim($table) . "';";

        foreach ($cols as $k => $v) {
            $name = $v["Field"];
            $phpType = self::MySQLTypeToPHP($v["Type"]);
            $null = $v["Null"] === "YES";
            $defaultValue = $v["Default"];
            $fileContent.=  "
    /**
    * @col $name
    * @mysql_type " . $v["Type"] . "
    * @var $phpType
    */
    public $$name";
            $toAdd = $defaultValue !== "NULL" ? $defaultValue : $null ? "null" : "";
            $fileContent .= ($toAdd !== "" ? " = " . $toAdd : "") . ";" . PHP_EOL;
        }
        $fileContent .= "}";
        Utils::filePutsContent
        (
            $this->outputDirectory . "/$className.php",
            $fileContent
        );
    }
}