<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 12:05
 */

namespace CloudsDotEarth\App\Models;

class User extends \UsersProperties {
    public $relations = [
        // one to many
        // many to many


        // one to one
        // many to one

        'grade'     => ['one_to_one', Grade::class],
        'groups'    => ['many_to_many', Group::class, 'users_groups']
    ];
}
