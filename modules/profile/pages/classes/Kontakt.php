<?php


class Kontakt extends MainModel{
    protected static $new_table = 'users__kontakt_informacije';
    protected static $_range = [];

    public static function range($from, $to){
        for($i=$from; $i<=$to; $i++) self::$_range[$i] = $i;
        return self::$_range;
    }

    public static function create(MainRequest $request){
        try{
            $create = self::insert($request->get());
        }catch (\Exception $e){}
    }
    public function getData($employee_no){
        return Profile::select("employee_no")->where('employee_no = '.$employee_no)
            ->with('users__kontakt_informacije', 'employee_no', 'employee_no')
            ->get();
    }
}