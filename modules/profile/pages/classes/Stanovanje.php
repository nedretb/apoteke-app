<?php


class Stanovanje extends MainModel{
    protected static $new_table = 'users__podaci_o_stanovanju';

    public static function create(MainRequest $request){
        try{
            $create = self::insert($request->get());
        }catch (\Exception $e){}
    }
    public function getData($employee_no){
        return Profile::select("employee_no")->whereArr(['employee_no' => $employee_no])
            ->with('users__podaci_o_stanovanju', 'employee_no', 'employee_no')
            ->get();
    }
}