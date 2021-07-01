<?php


class MainRequest{
    public static $_data = array(), $_newData = array();

    /*
     *  Method for setting data and returning an object of MainRequest. It's been called in index.php file
     */
    public static function set($data){
        self::$_data = $data;
        return new static();
    }

    /*
     *  Magic method, gives an option to return data such as $request->someAttr
     */
    public function __get($name){
        if(isset($this::$_data[$name])) return $this::$_data[$name];
        else throw new Exception("$name dow not exists");
    }
    public function __set($name, $value){
        $this::$_data[$name] = $value;
    }

    public static function get(){
        return self::$_data;
    }
    public static function except($params){
        // TODO :: Shall be set
        foreach ($params as $key => $val){
            $found = false;
            foreach (self::$_data as $d_key => $d_val){
                if($d_key != $d_val) $found = true;
            }
        }
    }
}