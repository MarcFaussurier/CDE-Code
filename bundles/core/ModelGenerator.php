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
        $tables = $core->db->query("show tables");
        foreach ($tables as $k => $v) {
            var_dump("SHOW COLUMNS FROM " . $v["Tables_in_" . $core->envConfig["database"]["database"]]);
            $stmt = $core->db->query("SHOW COLUMNS FROM ". $v["Tables_in_" . $core->envConfig["database"]["database"]]);
            var_dump($stmt);
        }
    }
}