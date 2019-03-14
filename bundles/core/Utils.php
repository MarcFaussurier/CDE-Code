<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 11/03/2019
 * Time: 10:24
 */

namespace CloudsDotEarth\Bundles\Core;

/**
 * Class Utils
 * @package CloudsDotEarth\Bundles\Core
 */
class Utils {

    /**
     * Returns all table in the current database
     * @param Core $core
     * @return string[]
     */
    public static function getDatabaseTables(Core &$core) : array {
        $output = [];
        $tables = $core->db->query("show tables");
        foreach ($tables as $k => $v) {
            array_push($output, $v["Tables_in_" . $core->envConfig["database"]["database"]]);
        }

        return $output;
    }

    /**
     * @param Core $core
     * @param string $tableName
     * @return array
     */
    public static function getColsInTable(Core &$core, string $tableName) : array {
        return
            $core->db->query("SHOW COLUMNS FROM `". $tableName . "`");
    }

    /**
     * @param string $path
     * @param string $content
     * @param bool $createPath
     */
    public static function filePutsContent(string $path, string $content, bool $createPath = true) {
        if ($createPath) {
            $h = explode("/", $path);
            unset($h[count($h) - 1]);
            $dirPath = join("/", $h);
            if (!is_dir($dirPath)) {
                mkdir(join("/", $h), 0755, true);
            }
        }
        file_put_contents( $path, $content);

    }

    /**
     * @param string $dir
     * @param string $ext
     * @return array
     */
    public static function recursiveGlob(string $dir, string $ext) : array  {
        $extLen = strlen($ext);
        if (!is_dir($dir)) {
            return [];
        } else {
            $output = [];
            $files = glob($dir . "/*");
            foreach ($files as $v) {
                $v = realpath($v);
                // if not OS relative
                if (!in_array(($a = explode("/", $v))[count($a) - 1], [".", ".."])) {
                    if (is_dir($v)) {
                        $output = array_merge(self::recursiveGlob($v, $ext), $output);
                    } else {
                        // if ext match
                        if (substr($v, strlen($v) - $extLen - 1, $extLen + 1) === "." . $ext) {
                            array_push($output, $v);
                        }
                    }
                }
            }
            return $output;
        }
    }

    /**
     * Will simply return the string in `
     * @param string $tableName
     * @return string
     */
    public static function graveify(string $tableName) {
        return "`" . $tableName . "`";
    }
}