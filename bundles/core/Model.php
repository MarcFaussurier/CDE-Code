<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 13:36
 */

namespace CloudsDotEarth\Bundles\Core;

/**
 * Class Model
 * @package CloudsDotEarth\Bundles\Core
 */
class Model {

    /**
     * @var int
     */
    public $row_id;

    /**
     * Current table name
     * @var string
     */
    public static $tableName;

    /**
     *  Used for switching between insert / update modes
     */
    public const DEFAULT_ID = -1;

    /**
     * mysql seems having issues with low timestamp,
     * consider using datetime for older dates
     */
    public const MINIMAL_TIMESTAMP = 37630741;

    /**
     * Will be filled with all model/table metadata
     * @var array
     */
    public static $tableMetaData = [];

    /**
     * Model constructor.
     * @param int $id
     * @throws \Exception
     */
    public function __construct(int $id = self::DEFAULT_ID)
    {
        $this->row_id = $id;
        if ($this->row_id !== -1) {
            $class = ( "\\" . get_class($this));
            self::$tableName = $class::$tableName;
            $stmt = Core::$staticDb->prepare("SELECT * FROM `".self::$tableName."` WHERE row_id = ?;");
            $rowResult = $stmt->execute([$this->row_id]);
            if (empty(self::$tableMetaData)) {
                self::$tableMetaData = $this->getModelMetaData();
            }
            if (count($rowResult) !== 1) {
                throw new \Exception("Unable to fetch model id " . $this->row_id . " from table "
                    . self::$tableName . " model have to exists and to be unique");
            }

            foreach ($rowResult[0] as $k => $v) {
                $this->$k = self::mysqlToPhpVal($k, $v);

            }
        }
    }

    /**
     * Will return model/table metadatas (types for conversions)
     * @return array
     * @throws \Exception
     */
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
     * Convert a mysql value to the appropriate model value using given columnName
     * @param string $columnName
     * @param mixed $mysqlVaue
     * @return bool|\DateTime|int|string
     * @throws \Exception
     */
    public static function mysqlToPhpVal(string $columnName, $mysqlVaue) {
        switch ($a = explode("(", self::$tableMetaData[$columnName]["mysql_type"])[0]) {
            case "int":
                return intval($mysqlVaue);
            case "varchar":
                return strval($mysqlVaue);
            case "text":
                return strval($mysqlVaue);
            case "datetime":
                return \DateTime::createFromFormat("Y-m-d H:i:s", $mysqlVaue);
            case "timestamp":
                return (new \DateTime())->setTimestamp(intval($mysqlVaue));
            default:
                throw new \Exception("Unknow MySQL type");
                break;
        }
    }

    /**
     * Reciprocal function of Model::mysqlToPhpVal
     * @param string $key
     * @return int|string
     * @throws \Exception
     */
    public function phpToMysqlVal(string $key) {
        $value = $this->$key;
        switch ($a = explode("(", self::$tableMetaData[$key]["mysql_type"])[0]) {
            case "int":
                return intval($this->$key);
            case "varchar":
                return strval($this->$key);
            case "text":
                return strval($this->$key);
            case "datetime":
                /**
                 * @var \DateTime $value
                 */
                return ($value)->format("Y-m-d H:i:s");
            case "timestamp":
                /**
                 * @var \DateTime $value
                 */
                return
                    date
                    (
                        'Y-m-d H:i:s',
                        (
                            $a = intval(is_null($value) ?
                            self::MINIMAL_TIMESTAMP
                            :
                            ($value)->getTimestamp())
                        ) < self::MINIMAL_TIMESTAMP ? self::MINIMAL_TIMESTAMP	 : $a);
            default:
                throw new \Exception("Unknow MySQL type");
                break;
        }
    }

    /**
     *
     */
    public function save(): bool {
        $query = "";
        $params = [];
        // insert if no id were given
        if ($this->row_id === self::DEFAULT_ID) {
            $query = "INSERT INTO `".self::$tableName."` VALUES(null";
            foreach (self::$tableMetaData as $key => $value) {
                if ($key !== "row_id") {
                    $query .= ",?";
                    array_push($params, $this->phpToMysqlVal($key));
                }
            }
            $query .= ");";
        }
        // else we perform an update
        else {
            $query = "UPDATE `".self::$tableName."` SET ";
            $countOfCols = count(self::$tableMetaData);
            $cnt = 0;
            foreach (self::$tableMetaData as $key => $value) {
                $cnt++;
                if ($key !== "row_id") {
                    $query .= "$key = ?" . (($cnt < $countOfCols) ? "," : "");
                    array_push($params, $this->phpToMysqlVal($key));
                }
            }
            $query .= " WHERE row_id = ?;";
            array_push($params, $this->row_id);
        }
        $stmt = Core::$staticDb->prepare($query);
        return $stmt->execute($params);
    }

    /**
     *
     */
    public function delete(): void {

    }
}