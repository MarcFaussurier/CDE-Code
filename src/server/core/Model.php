<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 13:36
 */

namespace CloudsDotEarth\App\Core;

use PharIo\Manifest\RequiresElement;

class Model {
    public static $tableName;
    public static $primaryKey;
    public static $relations = [
      //  'grade' => ['one_to_many', 'ModelName']
    ];

    /**
     * Model constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
    }

    /**
     * @param int $id
     * @return Model
     */
    public static function getFromId(int $id) : Model {

    }

    /**
     * @param int[] $id
     * @return Model[]
     */
    public static function getFromIdArray(array $id): array {

    }

    /**
     *
     */
    public function save(): void {

    }

    /**
     *
     */
    public function delete(): void {

    }
}