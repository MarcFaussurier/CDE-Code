<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 11/03/2019
 * Time: 10:10
 */

namespace CloudsDotEarth\Bundles\core;


use LightnCandy\LightnCandy;

class ViewCompiler
{
    /**
     * ViewCompiler constructor. Will simply compile all views for handlebars.
     * @param array $viewDirectories
     * @param string $outputDirectory
     */
    public function __construct(array $viewDirectories, string $outputDirectory)
    {
        $hbsFiles = [];
        foreach ($viewDirectories as $dir) {
            var_dump($dir);
            foreach(Utils::recursiveGlob($dir, "hbs") as $v) {

                var_dump(($a =  explode($dir, $v))[count($a) - 1]);
                array_push
                ($hbsFiles,
                    [   $v,
                        ($a =
                            explode
                            (
                                ($b = explode("../", $dir))[count($b) - 1],
                                $v
                            )
                        )
                        [count($a) - 1]
                    ]
                );
            }
        }

        foreach ($hbsFiles as $v) {
            var_dump($v);
            $i = $outputDirectory . $v[1];
            $h = explode("/", $i);
            unset($h[count($h) - 1]);
            $dirPath = join("/", $h);
            if (!is_dir($dirPath)) {
                mkdir(join("/", $h), 0755, true);
            }
            file_put_contents(str_replace(".hbs", ".php", $i), "<?php " . LightnCandy::compile($v[0]));
        }
    }
}