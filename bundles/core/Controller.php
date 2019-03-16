<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 13:36
 */

namespace CloudsDotEarth\Bundles\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Controller
 * @package CloudsDotEarth\Bundles\Core
 */
class Controller {
    /**
     * Format is function name, uri regex, service regex
     * @var array[]
     */
    public $methods = [];

    /**
     * Will return false if n controller method match,
     * Else if a match occurred, it will return a ServerRequestInterface
     * @param ServerRequestInterface $request
     * @return bool|ResponseInterface
     */
    public function handle(ServerRequestInterface $request) {
        foreach($this->methods as $v) {
            if ((preg_match($v[1], $request->getUri()) !== false) &&
                (preg_match($v[2], $request->getMethod()) !== false)) {
                return $this->$v[0]($request);
            }
        }
        return false;
    }

    /**
     * Will set $methods array using reflexion // tokens parsing
     * @throws \ReflectionException
     */
    public function setMetaData() {
        $childClass = get_class($this);
        $child = new $childClass();
        $class_info = new \ReflectionClass($child);
        $source = file_get_contents($class_info->getFileName());
        $tokens = token_get_all( $source );
        $output = [];
        $lastComment = "";
        foreach ($tokens as $token) {
            // dont proceed useless tokens
            if (in_array($token[0], [
                T_COMMENT,      // All comments since PHP5
                T_DOC_COMMENT,   // PHPDoc comments
                T_STRING
            ])) {
                if (($lastComment !== "") && ($token[0] === T_STRING)) {
                    $lastFunction = $token[1];
                    if ((strpos($lastComment, "@uri ") !== false) &&
                        (strpos($lastComment, "@services ") !== false)) {
                        $exploded = explode("@uri ", $lastComment)[1];
                        $uri = explode("\n",$exploded)[0];
                        $exploded = explode("@services ", $lastComment)[1];
                        $services = explode("\n",$exploded)[0];
                        $output[] = [$lastFunction, $uri, $services];
                    }
                    $lastComment = "";
                } else {
                    if ($token[0] === T_DOC_COMMENT) {
                        $lastComment = $token[1];
                    }
                }
            }
        }
    }
}