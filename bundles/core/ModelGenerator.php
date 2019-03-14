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
    public function __construct(Core &$core, array $mdelDirectories, string $outputDirectories)
    {
        $tables = Utils::getDatabaseTables($core);
        foreach ($tables as $k => $v) {
            $cols = Utils::getColsInTable($core, $v);
            $this->writePropertiesClass($v, $cols);
        }
    }

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

    public function writePropertiesClass(string $table, array $cols) {
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
            "generated/models/$className.php",
            $fileContent
        );
    }
}