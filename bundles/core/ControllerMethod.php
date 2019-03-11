<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 17:17
 */

namespace CloudsDotEarth\Bundles\Core;

class ControllerMethod {
    /**
     * @var callable
     */
    public $callback;
    /**
     * @var string
     */
    public $requestType = "GET";
    /**
     * @var string
     */
    public $urlPattern;

    /**
     * ControllerMethod constructor.
     * @param callable $callback
     * @param string $urlPattern
     * @param string $requestType
     */
    public function __construct(string $urlPattern, callable $callback, string $requestType = "GET")
    {
        $this->callback = $callback;
        $this->requestType = $requestType;
        $this->urlPattern = $urlPattern;
    }

    public function getMatchResults(string $url): array
    {
        $matches = [];
        preg_match($this->urlPattern, $url, $matches);
        return $matches;
    }

    /**
     * Will run the inner callback using an url and a swoole request
     * @param string $url
     * @param \Swoole\Http\Request $request
     */
    public function run(string $url, \Swoole\Http\Request $request) {
        $matchResuts = $this->getMatchResults($url);
        if (count($matchResuts) > 0) {
            $func = $this->callback;
            $func($request, $matchResuts);
        } else {
            // we simply ignore as it is not a match
        }
    }
}