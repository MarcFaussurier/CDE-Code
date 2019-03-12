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
            $class = ( "\\" . get_class($this));
            self::$tableName = $class::$tableName;
            $stmt = Core::$staticDb->prepare("SELECT * FROM `".self::$tableName."` WHERE row_id = ?;");
            $rowResult = $stmt->execute([$this->id]);
            $metaData = $this->getModelMetaData();

            if (count($rowResult) !== 1) {
                throw new \Exception("Unable to fetch model id " . $this->id . " from table "
                    . self::$tableName . " model have to exists and to be unique");
            }

            foreach ($rowResult[0] as $k => $v) {
                var_dump($k);
                var_dump($v);
                switch ($a = explode("(", $metaData[$k]["mysql_type"])[0]) {
                    case "int":
                        $this->$k = intval($v);
                        break;
                    case "varchar":
                        $this->$k = strval($v);
                        break;
                    case "text":
                        $this->$k = strval($v);
                        break;
                    case "datetime":
                        $this->$k = \DateTime::createFromFormat("Y-m-d H:i:s", $v);
                        break;
                    case "timestamp":
                        $this->$k = (new \DateTime())->setTimestamp(intval($v));
                        break;
                    default:
                        var_dump($a);
                        throw new \Exception("Unknow MySQL type");
                        break;
                }
            }

            var_dump($this);
        }
    }



    public function getModelMetaData() : array {
        $finalOutput = [];
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
            $output = explode("\n" , $txt);
            $col = $mysqlType = $var = "";
            foreach ($output as $k => $v) {
                if (substr_count($v, "@") === 1) {
                    $endOfLine = explode(" ", explode("@", $v)[1]);
                    switch ($endOfLine[0]) {
                        case "var":
                            $var = $endOfLine[1];
                            break;
                        case "mysql_type":
                            $mysqlType = $endOfLine[1];
                            break;
                        case "col":
                            $col = $endOfLine[1];
                            break;
                        default:
                            throw new \Exception("Unsuported PHPDoc attribute given in generated Model : " . self::$tableName);
                            break;
                    }
                }
            }
            $finalOutput[$col] = ["mysql_type" => $mysqlType, "php_type" => $var];
        }
        return $finalOutput;
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