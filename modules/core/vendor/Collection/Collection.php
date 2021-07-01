<?php

abstract class Arr
{

    public static function sum($items, $status)
    {

        $sum = 0;

        foreach ($items as $key => $value) {
            if ($value['status'] == $status) $sum += $value['hour'];
        }

        return $sum;
    }

    public static function where($items, $column, $result)
    {
        $allowed_days = 0;

        foreach ($items as $key => $value) {
            if ($value[$column] == $result) {
                $allowed_days = $value['allowed_days'];
                break;
            }
        }

        return $allowed_days;
    }
}
