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
            foreach(Utils::recursiveGlob($dir, "hbs") as $v) {
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
            Utils::filePutsContent
            (
                str_replace(".hbs", ".php", $outputDirectory . $v[1]),
                "<?php " . LightnCandy::compile($v[0])
            );
        }
    }
}