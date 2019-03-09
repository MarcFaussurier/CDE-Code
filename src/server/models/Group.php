<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 12:05
 */

namespace CloudsDotEarth\App\Models;

use CloudsDotEarth\App\Core\Model;

class Group extends Model {
    public static $tableName = "users";
    public static $primaryKey = "row_id";
    public static $relations = [
        'grade' => ['one_to_many', 'ModelName']
    ];
}
