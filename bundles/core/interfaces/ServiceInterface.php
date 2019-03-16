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

    public function convertToPsrRequest(\stdClass $request): ServerRequestInterface;

    public function convertToPsrResponse(ResponseInterface $response) : \stdClass;

    public function start(): void;
}