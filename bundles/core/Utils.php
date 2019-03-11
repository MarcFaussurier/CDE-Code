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
}