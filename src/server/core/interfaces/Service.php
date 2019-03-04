<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 03/03/2019
 * Time: 11:33
 */

namespace CloudsDotEarth\App\Core\Interfaces;


interface Service
{
    public function register(): void;

    public function start(): void;
}