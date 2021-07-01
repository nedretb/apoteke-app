<?php


class Rodbina extends MainModel{
    protected static $new_table = 'users__podaci_o_rodbinskim_odnosima';
    protected static $_srodstvo = [
        'Srodnici u banci' => 'Srodnici u banci',
        'Svekar / Svekrva' => 'Svekar / Svekrva'
    ];

    public static function srodstvo(){return self::$_srodstvo; }

    public static function create(MainRequest $request){

        try{
            $create = self::insert($request->get());
        }catch (\Exception $e){
            var_dump($e);
        }
    }
    public function getData($employee_no){
        return Profile::select("employee_no")->where('employee_no = '.$employee_no)
            ->with('users__podaci_o_rodbinskim_odnosima', 'employee_no', 'employee_no')
            ->get();
    }
}

