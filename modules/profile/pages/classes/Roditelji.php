<?php

use Carbon\Carbon as Carbon;

class Roditelji extends MainModel {
    protected static $new_table = 'users__podaci_o_roditeljima';

    public static function create(MainRequest $request){

        $request->otac_datum_rodjenja = Carbon::parse($request->otac_datum_rodjenja)->format('Y-m-d');
        $request->majka_datum_rodjenja_ = Carbon::parse($request->majka_datum_rodjenja_)->format('Y-m-d');

        try{
            $create = self::insert($request->get());
        }catch (\Exception $e){
            var_dump($e);
        }
    }
    public function getData($employee_no){
        return Profile::select("employee_no")->where('employee_no = '.$employee_no)
            ->with('users__podaci_o_roditeljima', 'employee_no', 'employee_no')
            ->get();
    }
}
