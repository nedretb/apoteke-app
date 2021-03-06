<?php

class Form{
    public static function setOptions($options){
        $optionsStr = '';
        foreach ($options as $key => $val){
            $optionsStr .= $key.'="'.$val.'" ';
        }
        return $optionsStr;
    }
    public static function select($name, $values, $value, $options){
        $optionsStr = self::setOptions($options);
        $data = '<select name="'.$name.'" '.$optionsStr.'>';
        foreach ($values as $key => $val){
            $data .= '<option value="'.$key.'" '.(($key == $value) ? 'selected' : '').'>'.$val.'</option>';
        }
        return ($data .= '</select>');
    }
    public static function text($name, $value, $options){
        $optionsStr = self::setOptions($options);
        return '<input type="text" value="'.$value.'" name="'.$name.'" '.$optionsStr.'>';
    }
    public static function number($name, $value, $options){
        $optionsStr = self::setOptions($options);
        return '<input type="number" value="'.$value.'" name="'.$name.'" '.$optionsStr.'>';
    }
    public static function email($name, $value, $options){
        $optionsStr = self::setOptions($options);
        return '<input type="email" value="'.$value.'" name="'.$name.'" '.$optionsStr.'>';
    }
}