<?php

// require_once '../../../configuration.php';

abstract class Model {

    protected static $table = '';
    public static $columns = '';
    public static $where = '';
    public static $orderby;


    private static $initialized = false;

    private static function initialize()
    {
        if (self::$initialized)
            return;

        self::$initialized = true;
    }

    public static function table($table){
        global $_conf;

        static::$table = $_conf['app_database'] . '.[' . $table . ']';
    }
    public static function tableNAV($table){
        global $_conf;

        static::$table = $_conf['nav_database'] . '.[' . $table . ']';
    }
    public static function select($columns){
        self::$columns = $columns;
        return new static();
    }

    public static function where($where){
        self::$where = $where;
        return new static();
    }

    public static function orderBy($column, $orientation){
        self::$orderby = " ORDER BY ". $column ." ". $orientation;
        return new static();
    }

    public static function get($count = 0){
        global $db;

        if(static::$table == ''){
            self::table(static::$table);
        }


        $raw = "SELECT " . self::$columns . " FROM " . static::$table . " WHERE " . self::$where . " " . self::$orderby;

        echo "\n";

     

        $query = $db->prepare($raw);
        $query->execute();
        
        if($count == 1){
            $f = $query->fetch();
        } else {
            $f = $query->fetchAll();
        }


        return $f;
    }

    public static function update($sql, $columns = []){
        global $db;

        $raw = "UPDATE " . static::$table . " SET $sql WHERE " . self::$where ;
        $query = $db->prepare($raw);
        $query->execute($columns);

    }

    public static function first($object = 0){
        if($object != 0 ) {
            return self::get(1);
        } else {
            return (object) self::get(1);
        }
    }

    public function __destruct(){
        self::$orderby = '';
    }

}

class DB {
    public static function select($sql, $columns = [], $ret = 0){

        global $db;

        $query = $db->prepare($sql);
        $query->execute($columns);


        if($ret == 0){
            return (object) $query->fetchAll()[0];
        }

        return (object) $query->fetchAll();



    }


}
