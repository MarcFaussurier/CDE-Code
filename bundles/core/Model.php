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
    public $tableName;

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
    public $tableMetaData = [];

    /**
     * @var array
     */
    public $relations = [];

    /**
     * Model constructor.
     * @param int $id
     * @throws \\Exception
     */
    public function __construct(int $id = self::DEFAULT_ID)
    {
        $this->row_id = $id;
        $this->tableMetaData = $this->getModelMetaData();
        if ($this->row_id !== -1) {
            $stmt = Core::$staticDb->prepare("SELECT * FROM ".Utils::graveify($this->tableName)." WHERE row_id = ?;");
            $rowResult = $stmt->execute([$this->row_id]);
            if (count($rowResult) !== 1) {
                throw new \Exception("Unable to fetch model id " . $this->row_id . " from table "
                    . $this->tableName . " model have to exists and to be unique");
            }
            $this->tableMetaData = $this->getModelMetaData();
            foreach ($rowResult[0] as $k => $v) {
                 $this->$k = $this->mysqlToPhpVal($k, $v);
            }

            foreach ($this->relations as $col => $v) {
                // todo : save of one-to-many and many-to-one
                // relation can be one-to-many
                // or many-to-many
                if (in_array($v[0], ['many_to_many', 'one_to_many'])) {
                    $targetTable = $v[2];
                    $query = "SELECT * FROM " . Utils::graveify($v[2]) . " WHERE " . Utils::graveify(self::pluralToSingular($this->tableName) . "_id") . " = ?;";
                    $stmt = Core::$staticDb->prepare($query);
                    $rows = $stmt->execute([$this->row_id]);
                    $this->$col = [];
                    foreach ($rows as $id => $cols) {
                        $className = self::tableNameToClass($col);
                        $colName = self::pluralToSingular($col) . "_id";
                        $foundId = $cols[$colName];
                        array_push($this->$col, new $className($foundId));
                    }
                    // $this->$col
                    // create new groups
                }
            }
        }
    }

    /**
     *
     */
    public function save(): bool {
        $query = "";
        $params = [];
        $success = true;
        // insert if no id were given
        if ($this->row_id === self::DEFAULT_ID) {
            $query = "INSERT INTO `".$this->tableName."` VALUES(null";
            foreach ($this->tableMetaData as $key => $value) {
                if ($key !== "row_id") {
                    $query .= ",?";
                    array_push($params, $this->phpToMysqlVal($key));
                }
            }
            $query .= ");";
        }
        // else we perform an update
        else {
            $query = "UPDATE `".$this->tableName."` SET ";
            $countOfCols = count($this->tableMetaData);
            $cnt = 0;
            foreach ($this->tableMetaData as $key => $value) {
                $cnt++;
                if (($key !== "row_id") && ($key !== "")) {
                    $query .= "$key = ?" . (($cnt < ($countOfCols - 1)) ? "," : "");
                    array_push($params, $this->phpToMysqlVal($key));
                }
            }
            $query .= " WHERE row_id = ?;";
            array_push($params, $this->row_id);
        }
        $stmt = Core::$staticDb->prepare($query);
        if (!$stmt->execute($params)) {
            $success = false;
        }
        foreach ($this->relations as $col => $v) {
            if(in_array($v[0], ["one_to_many", "many_to_many"])) {
               $query = "DELETE FROM " . Utils::graveify($v[2]) . " WHERE " . Utils::graveify(self::pluralToSingular($this->tableName) . "_id") . " = ?;";
               $stmt = Core::$staticDb->prepare($query);
                if (!$stmt->execute([$this->row_id])) {
                    $success = false;
                }
               foreach ($this->$col as $instance) {
                   $query = "INSERT INTO " . Utils::graveify($v[2]) . " VALUES (".join(",",str_split(str_repeat("?", 3))) . ");";
                   $stmt = Core::$staticDb->prepare($query);
                   if (!$stmt->execute([null, $this->row_id, $instance->row_id])) {
                       $success = false;
                   }
                   if (!$instance->save()) {
                       $success = false;
                   }
               }
             }
        }
        foreach ($this->relations as $col => $v) {
            if (in_array($v[0], ["one_to_one", "many_to_one"])) {
                $this->$col->save();
            }
        }
        return $success;
    }

    /**
     * Will delete the current model
     * @return bool
     */
    public function delete(): bool {
        $success = true;
        // if the model was created, no need to delete it
        if ($this->row_id !== self::DEFAULT_ID) {
            $query = "DELETE FROM ".Utils::graveify(self::$tableName)." WHERE row_id = ?;";
            $stmt = Core::$staticDb->prepare($query);
            if (!$stmt->execute([$this->row_id])) {
                $success = false;
            }
            foreach ($this->relations as $col => $v) {
                // todo : save of one-to-many and many-to-one
                // relation can be one-to-many
                // or many-to-many
                if (in_array($v[0], ["one_to_many", "many_to_many"])) {
                    $query = "DELETE FROM " . Utils::graveify($v[2]) . " WHERE " . Utils::graveify(self::pluralToSingular($this->tableName) . "_id") . " = ?;";
                    $stmt = Core::$staticDb->prepare($query);
                    if (!$stmt->execute([$this->row_id])) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }

    /**
     * @param string $tableName
     * @return mixed
     */
    public static function tableNameToClass(string $tableName) {
        $propertiesClass = ucfirst($tableName) . "Properties";
        foreach(get_declared_classes() as $class){
            if(get_parent_class($class) === $propertiesClass)
                return $class;
        }
        throw new \Exception("Unable to find appropriate model for table name : " . $tableName);
    }

    /**
     * @param string $tableName
     * @return mixed
     */
    public static function singularModelToClass(string $name) {
        $toFind = "\\Models\\" . ucfirst($name);
        foreach(get_declared_classes() as $class){
            if(strpos($class, $toFind) !== false)
                return $class;
        }
        throw new \Exception("Unable to find appropriate model for singular model name : " . $name);
    }

    /**
     * @param string $plural
     * @return mixed
     */
    public static function pluralToSingular(string $plural) {
        $propertiesClass = ucfirst($plural) . "Properties";
        foreach(get_declared_classes() as $class){
            var_dump($class);
            if(get_parent_class($class) === $propertiesClass)
                return strtolower(($a = explode("\\", $class))[count($a) - 1]);
        }
        throw new \Exception("Unable to find appropriate model for table name : " . $plural);
    }

    /**
     * Will return model/table metadatas (types for conversions)
     * @return array
     * @throws \\Exception
     */
    public function getModelMetaData() : array {
        $finalOutput = [];
        $source = file_get_contents( __DIR__ . "/../../generated/models/".ucfirst($this->tableName)."Properties.php" );
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
     * @throws \\Exception
     */
    public function mysqlToPhpVal(string $columnName, $mysqlVaue) {
        if (isset($this->relations[$columnName])) {
            switch($this->relations[$columnName][0]) {
                case "one_to_one":
                    $class = (self::singularModelToClass($columnName));
                    return new $class(intval($mysqlVaue));
                    break;
                case "one_to_many":
                    // we should do nothing as the case will be handled later
                    break;
                case "many_to_one":
                    $class = (self::singularModelToClass($columnName));
                    return new $class(intval($mysqlVaue));
                    break;
                case "many_to_many":
                    // we do nothing as the case will be executed later
                    break;
                default:
                    throw new \Exception("Unknow relation ship : " . $this->relations[$columnName][0] . " in model " .  self::getModelClass());
                    break;
            }
        } else {
            //   var_dump("NO RElATION FOR " . $k);
            switch ($a = explode("(", $this->tableMetaData[$columnName]["mysql_type"])[0]) {
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
    }

    /**
     * Reciprocal function of Model::mysqlToPhpVal
     * @param string $key
     * @return int|string
     * @throws \\Exception
     */
    public function phpToMysqlVal(string $key) {
        $value = $this->$key;
        if (isset($this->relations[$key])) {
            switch($this->relations[$key][0]) {
                case 'one_to_one':
                    return $this->$key->row_id;
                    break;
                case 'one_to_many':
                    break;
                case 'many_to_one':
                    return $this->$key->row_id;
                    break;
                case 'many_to_many':
                    break;
                default:
                    throw new \Exception("Unsupported relation ship in table " . $this->tableName . " for " . $key);
                    break;
            }
            return 1;
        } else {
            switch ($a = explode("(", $this->tableMetaData[$key]["mysql_type"])[0]) {
                case "":
                    break;
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
                    throw new \Exception("Unknow MySQL type :".$a);
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public static function getModelClass(): string {
        return "\\" . get_called_class();
    }


    /**
     * @param string $condition
     * @param array $args
     * @return Model[]
     */
    public function select(string $condition, array $args) {
        $query = "SELECT * FROM " . Utils::graveify($this->tableName) . " WHERE ".$condition." ;";
        $stmt = Core::$staticDb->prepare($query);
        $result = $stmt->execute($args);
        $output = [];
        $className = self::getModelClass();
        foreach ($result as $k => $v) {
            array_push($output, new $className($v["row_id"]));
        }
        return $output;
    }
}