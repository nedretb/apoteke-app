<?php


class Porez extends MainModel{
    protected static $new_table = 'users__poreska_olaksica_i_prevoz';

    public static function create(MainRequest $request){
        try{
            $create = self::insert($request->get());
        }catch (\Exception $e){
            var_dump($e);
        }
    }
    public function getData($employee_no){
        return Profile::select("employee_no")->where('employee_no = '.$employee_no)
            ->with('users__poreska_olaksica_i_prevoz', 'employee_no', 'employee_no')
            ->get();
    }
}