<?php


class Profile extends MainModel{
    protected static $new_table = 'users';
    protected static $employees = [

    ];

    public static function getEmployees($employee, $addQuery = []){
        $syst = Sistematizacija::getSys($employee, true);

        return self::where('egop_ustrojstvena_jedinica IN (\''.(implode("','",$syst)).'\')')
            ->whereArr($addQuery)
            ->get();
    }
}