<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 03/03/2019
 * Time: 11:33
 */

namespace CloudsDotEarth\Bundles\Core\Interfaces;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ServiceInterface
{
    public function register(): void;

    /**
     * @param \stdClass $request
     * @return ServerRequestInterface
     */
    public function convertToPsrRequest(\stdClass $request): ServerRequestInterface;

    /**
     * @param \stdClass
     * @param ResponseInterface $psrResponse
     * @return void
     */
    public function replyUsingResponse(&$swooleResponse, ResponseInterface $psrResponse) : void;

    public function start(): void;
}