<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 13:36
 */

namespace CloudsDotEarth\Bundles\Core;

use PharIo\Manifest\RequiresElement;

class Model {

    /**
     * @var int
     */
    public $row_id;
    public static $tableName;

    /**
     * Model constructor.
     * @param int $id
     */
    public function __construct(int $id = -1)
    {
        $this->id = $id;
        if ($this->id !== -1) {
            echo "TABLE NAME : " . PHP_EOL;

            $class = ( "\\" . get_class($this));
            self::$tableName = $class::$tableName;
            $stmt = Core::$staticDb->prepare("SELECT * FROM `".self::$tableName."` WHERE row_id = ?;");
            $rowResult = $stmt->execute([$this->id]);
            $this->getModelMetaData();
        }
    }



    public function getModelMetaData() : void {
        $output = [];
        $source = file_get_contents( __DIR__ . "/../../generated/models/".ucfirst(self::$tableName)."Properties.php" );

        $tokens = token_get_all( $source );
        $comment = array(
            T_COMMENT,      // All comments since PHP5
            T_DOC_COMMENT   // PHPDoc comments
        );
        foreach( $tokens as $token ) {
            if( !in_array($token[0], $comment) )
                continue;
            // Do something with the comment
            $txt = $token[1];
            var_dump($txt);

            //$output = explode("\n" . $txt);
        }
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