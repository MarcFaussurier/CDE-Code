<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 12:05
 */

namespace CloudsDotEarth\App\Models;

use CloudsDotEarth\Bundles\Core\Model;

class User extends Model {
    public static $tableName = "users";
    public static $primaryKey = "row_id";
    public static $relations = [
        'grade' => ['one_to_many', Group::class]
    ];

    public $row_id;
    public $name;
    public $email;
    public $password;
    public $create_time;
    public $first_name;
    public $last_name;

}
