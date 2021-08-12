<?php
use Carbon\Carbon as Carbon;

class PorodicnoStanje extends MainModel{
    protected static $new_table = 'users__podaci_o_porodicnom_stanju';
    protected static $_porodicno_stanje = [
        'Oženjen / Udata' => 'Oženjen / Udata',
        'Neoženjen / Neudata' => 'Neoženjen / Neudata',
        'Razveden / Razvedena' => 'Razveden / Razvedena',
        'Udovac / Udovica' => 'Udovac / Udovica',
        'Vanbračna zajednica' => 'Vanbračna zajednica'
    ];

    public static function stanje(){return self::$_porodicno_stanje; }

    public static function create(MainRequest $request){
        $request->supruznik_datum_rodjenja = Carbon::parse($request->supruznik_datum_rodjenja)->format('Y-m-d');
        try{
            $create = self::insert($request->get());
        }catch (\Exception $e){
            var_dump($e);
        }
    }
    public function getData($employee_no){
        return Profile::select("employee_no")->where('employee_no = '.$employee_no)
            ->with('users__podaci_o_porodicnom_stanju', 'employee_no', 'employee_no')
            ->get();
    }
}