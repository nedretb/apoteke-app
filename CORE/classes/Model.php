<?php


class DataBase {
    private static $db = null;

    public static function doSQL($sql, $values = null, $special = false){

        self::set_db();

        try{
            $query = DataBase::$db->prepare($sql);

            $done = $query->execute($values);
        }catch(PDOException $e){}

        //restart values
        MainModel::$table = '';
        MainModel::$columns = '*';
        MainModel::$where = '';
        MainModel::$orderby = '';
        MainModel::$joined_table = '';
        MainModel::$joined_table_name = '';
        MainModel::$primary_col = '';
        MainModel::$secoundary_col = '';
        MainModel::$first_l = '';
        MainModel::$second_l = '';
        MainModel::$where_with = '';
        MainModel::$group_by = '';

        if ($special) return $done;
        return $query;
    }

    public static function select($sql, $first = null){

        self::set_db();

        $query = self::doSQL($sql);
        return isset($first) ? $query->fetch(PDO::FETCH_ASSOC) : $query->fetchAll(PDO::FETCH_ASSOC);
        // return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function rowsChanged($sql, $values = null){

        self::set_db();

        $query = self::doSQL($sql, $values);

        return $query->rowCount();
    }

    static function set_db() {
        try {
            self::$db = new PDO("sqlsrv:Server=192.168.14.13\NAVDEMO;Database=c0_intranet2_apoteke;", "intranet", "DynamicsNAV16!");
            self::$db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $e) {
            echo $db_error;
        }

        if(!self::$db){
            echo "Connection could not be established.\n";
        }

        //kasnije ukinuti global TODO
        $GLOBALS['db'] = self::$db;
    }
}


class MainModel {
    protected static $instance; // self instancing
    public static $table = '';
    public static $columns = '*';
    public static $where = '';
    public static $orderby = '';
    public static $joined_table = '';
    public static $joined_table_name = '';
    public static $primary_col = '';
    public static $secoundary_col = '';
    public static $first_l = '';
    public static $second_l = '';
    public static $where_with = '';
    public static $group_by = '';
    public static $data;

    public static function newInstance() {
        return new static();
    }

    public static function setTable($table){
        global $_conf;

        self::$table = $_conf['app_database'] . '.[' . $table . ']';
    }


    public static function select($columns){
        self::$columns = $columns;
        return new static();
    }

    public static function whereArr($arr){
        foreach ($arr as $key => $val){
            if(!empty($val)){
                if (self::$where != ''){
                    if ($key == 'egop_ustrojstvena_jedinica'){
                        self::$where .= " AND ".$key." ='".$val."'";
                    }
                    else{
                        self::$where .= " AND ".$key." LIKE N'%".$val."%'";
                    }
                } else{
                    if ($key == 'egop_ustrojstvena_jedinica'){
                        self::$where = " WHERE ".$key."='".$val."'";
                    }
                    else{
                        self::$where = " WHERE ".$key." LIKE N'%".$val."%'";
                    }
                }
            }
        }
        return new static();
    }

    public static function where($where){
        if (self::$where != ''){
            self::$where .= ' AND '.$where;
        } else{
            self::$where = ' WHERE '.$where;
        }

        return new static();
    }

    public static function orWhere($or_where){
        self::$where .= ' OR '.$or_where;

        return new static();
    }

    public static function orderBy($column, $orientation = 'DESC'){
        self::$orderby = " ORDER BY ". $column ." ". $orientation;
        return new static();
    }
    public static function pluck($key, $val = null, $val2 = null){
        self::setTable(static::$new_table);
        $pluckData = array();

        $raw = "SELECT " . self::$columns . " FROM " . self::$table . self::$where . " " . self::$group_by . " " . self::$orderby;
        try{
            $data = DataBase::select($raw);
        }catch (\Exception $e){}

        if($val == null){
            foreach ($data as $d){
                $pluckData[$d[$key]] = $d[$key];
            }
        }else{
            foreach ($data as $d){
                if(isset($val2)){
                    $pluckData[$d[$key]] = $d[$val].' '.$d[$val2];
                }else $pluckData[$d[$key]] = $d[$val];
            }
        }

        return $pluckData;
    }
    public static function get($how_many = null){
        self::setTable(static::$new_table);

        $tmpTable = self::$joined_table;
        $tmpFirst = self::$primary_col;
        $tmpSec   = self::$secoundary_col;
        $table    = self::$joined_table_name;

        if ($how_many != null){
            $top = "TOP($how_many)";
        } else $top = '';

        $first = ($how_many === 0) ? true : null;

        $raw = "SELECT $top " . self::$columns . " FROM " . self::$table . self::$where . " " . self::$group_by . " " . self::$orderby;
        try{
            $data = DataBase::select($raw, $first);
        }catch (\Exception $e){}

        if(!empty($tmpTable)){
            self::$joined_table   = $tmpTable;
            self::$primary_col    = $tmpFirst;
            self::$secoundary_col = $tmpSec;
        }

        // $join = "INNER JOIN ". self::$joined_table ." ON Orders.CustomerID=Customers.CustomerID";

        if (self::$joined_table != ''){
            foreach ($data as $key => $value) {
                if($first){
                    $raw_join = "SELECT * FROM " . self::$joined_table . " WHERE " . self::$secoundary_col . "=" . $value;
                }else{
                    $raw_join = "SELECT * FROM " . self::$joined_table . " WHERE " . self::$secoundary_col . "=" . $value[self::$primary_col];
                }

                $data_joined = DataBase::select($raw_join);

                if($first){
                    $data[$table] = $data_joined;;
                }else{
                    $data[$key][$table] = $data_joined;
                }

            }
        }

        if ($how_many == 1 and $data){
            return  $data[0];
        }

        self::$data = $data;

        return  $data;
    }

    public static function first(){
        return self::get(0);
    }

    public static  function update($parameters){
        self::setTable(static::$new_table);

        $set_data = '';
        $values = [];
        foreach ($parameters as $key => $value) {
            if ($set_data == '') $set_data .="[$key] = ?";
            else $set_data .=",[$key] = ? ";
            array_push($values, $value);
        }

        if ($set_data != ''){
            $raw = "UPDATE ". self::$table . " SET " . $set_data . self::$where;

            return DataBase::rowsChanged($raw, $values);
        }
    }

    public static function delete($id = null){
        self::setTable(static::$new_table);

        if ($id){
            $new_where = " WHERE id = $id";
        } else{
            $new_where = self::$where;
        }

        $raw = "DELETE " . self::$table . $new_where;

        return DataBase::rowsChanged($raw);
    }

    public static function insert($parameters){
        self::setTable(static::$new_table);


        $cols = '';
        $values = '';
        $real_values = [];
        foreach ($parameters as $key => $value) {
            if ($cols == '') $cols .= "[$key]";
            else $cols .= ", [$key]";

            if ($values == '') $values .= "?";
            else $values .= ", ?";

            array_push($real_values, $value);
        }

        $raw = "INSERT INTO " . self::$table . "($cols) VALUES ($values)";

        return DataBase::rowsChanged($raw, $real_values);
    }

    public static function with($table_to_join, $first_col, $second_col){

        global $_conf;

        self::$joined_table = $_conf['app_database'] . '.[' . $table_to_join . ']';
        self::$joined_table_name = $table_to_join;
        self::$primary_col = $first_col;
        self::$secoundary_col = $second_col;
        // self::$first_l = $first_l;
        // self::$second_l = $second_l;

        return new static();
    }

    public static function where_with($where){
        if (self::$where_with != ''){
            self::$where_with .= ' AND '.$where;
        } else{
            self::$where_with = ' WHERE '.$where;
        }

        return new static();
    }
    public static function like($where){
        if(self::$where_with == ''){
            self::$where_with = 'WHERE LIKE %'.$where.'%';
        }
        return new static();
    }

    public static function orWhere_with($or_where){
        self::$where_with .= ' OR '.$or_where;

        return new static();
    }

    public static function groupBy($group){
        self::$group_by = 'GROUP BY '.$group;

        return new static();
    }
}