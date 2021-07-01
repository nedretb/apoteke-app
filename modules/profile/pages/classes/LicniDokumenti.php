<?php


class LicniDokumenti extends MainModel{
    protected static $new_table = 'users__licni_dokumenti';
    protected static $_drzavljanstvo = [
        'Bosna i Hercegovina' => 'Bosna i Hercegovina',
        'Hrvatska' => 'Hrvatska',
        'Srbija' => 'Srbija',
    ];
    protected static $_kategorija = [
        'A1' => 'A1',
        'A' => 'A',
        'B1' => 'B',
        'C' => 'C',
    ];

    public static function drzavljanstvo(){return self::$_drzavljanstvo; }
    public static function kategorije(){return self::$_kategorija; }

    public static function create(MainRequest $request){

        try{
            $create = self::insert($request->get());
        }catch (\Exception $e){
            var_dump($e);
        }
    }
    public function getData($employee_no){
        return Profile::select("employee_no")->where('employee_no = '.$employee_no)
            ->with('users__licni_dokumenti', 'employee_no', 'employee_no')
            ->get();
    }
}
