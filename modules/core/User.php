<?php


class User extends Model
{

    public static $table = 'users';


    public static function kvoteSatnice($employee_no, $year, $satnice = 'satnice')
    {
        $kvote = VS::getKvote($employee_no, $year, $satnice);

        return $kvote;
    }
}