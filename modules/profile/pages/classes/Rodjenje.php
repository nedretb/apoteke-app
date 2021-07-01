<?php

use Carbon\Carbon as Carbon;

class Rodjenje extends MainModel{
    protected static $new_table = 'users__podaci_o_rodjenju';

    public static function create(MainRequest $request){
        try{
            $request->datum_rodjena = Carbon::parse($request->datum_rodjena)->format('Y-m-d');

            $create = self::insert($request->get());
        }catch (\Exception $e){
            var_dump($e);
        }
    }
    public function getData($employee_no){
        try{
            return self::where('employee_no = '.$employee_no)->first();
        }catch (\Exception $e){}
    }
}