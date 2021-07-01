<?php


class Solidarnost extends MainModel{
    protected static $new_table = 'users__fond_solidarnosti_i_sindikat';

    public static function create(MainRequest $request){
        try{
            $create = self::insert($request->get());
        }catch (\Exception $e){
            var_dump($e);
        }
    }
    public function getData($employee_no){
        return Profile::select("employee_no")->where('employee_no = '.$employee_no)
            ->with('users__fond_solidarnosti_i_sindikat', 'employee_no', 'employee_no')
            ->get();
    }
}