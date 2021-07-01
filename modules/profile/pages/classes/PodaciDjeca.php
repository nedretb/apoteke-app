<?php
use Carbon\Carbon as Carbon;

class PodaciDjeca extends MainModel{
    protected static $new_table = 'users__podaci_o_djeci';
    protected static $_pol = [
        'M' => 'M',
        'Ž' => 'Ž'
    ];

    public static function stanje(){return self::$_pol; }

    public static function create(MainRequest $request){

        $request->datum_rodjenja = Carbon::parse($request->datum_rodjenja)->format('Y-m-d');
        try{
            $create = self::insert($request->get());
        }catch (\Exception $e){
            var_dump($e);
        }
    }
    public function getData($employee_no){
        return Profile::select("employee_no")->where('employee_no = '.$employee_no)
            ->with('users__podaci_o_djeci', 'employee_no', 'employee_no')
            ->get();
    }
}