<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 03/03/2019
 * Time: 11:33
 */

namespace CloudsDotEarth\Bundles\Core\Interfaces;

interface ServiceInterface
{
    public function register(): void;

    public function start(): void;
}