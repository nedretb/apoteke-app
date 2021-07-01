<?php


class Skolovanje extends MainModel{
    protected static $new_table = 'users__skolovanje';

    public static function create(MainRequest $request){
        try{
            $create = self::insert($request->get());
        }catch (\Exception $e){
            var_dump($e);
        }
    }
    public function getData($employee_no){
        return Profile::select("employee_no")->where('employee_no = '.$employee_no)
            ->with('users__skolovanje', 'employee_no', 'employee_no')
            ->get();
    }
}
