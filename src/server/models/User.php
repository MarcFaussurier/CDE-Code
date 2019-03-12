<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 12:05
 */

namespace CloudsDotEarth\App\Models;

use CloudsDotEarth\Bundles\Core\Model;

class User extends \UsersProperties {
    public static $relations = [
        'grade' => ['one_to_many', Group::class]
    ];
}
