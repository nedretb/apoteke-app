<?php
use Carbon\Carbon;

class HelpClass{
    protected static $years = 0, $months = 0, $days = 0; // Calculate working days
    public function __construct(){

    }

    public static function getDMY($from, $to, $coefficient){
        if($coefficient == 1){
            $date = Carbon::parse($from)->diff(Carbon::parse($to)->addDay());

            self::$years  = $date->y;
            self::$months = $date->m;
            self::$days   = $date->d;

        }else{
            $days = Carbon::parse($from)->diffInDays(Carbon::parse($to)) * $coefficient;

            self::$years  = floor($days / 365);
            self::$months = floor((($days) - (self::$years * 365)) / 30);
            self::$days   = floor($days - (self::$years * 365) - self::$months * 30);
        }

        /*
        $p_from = Carbon::parse($from);
        $p_to   = Carbon::parse($to);

        $p_from_n = Carbon::parse($p_from->year.'-'.$p_from->month.'-01')->addMonth(); // First day of next month
        $p_to_n   = Carbon::parse($p_to->year.'-'.$p_to->month.'-01');

        $months_between = $p_from_n->diffInMonths($p_to_n);

        $p_from_c = $p_from; $p_to_c = $p_to;

        $endOfFirstMonth  = $p_from->endOfMonth();
        $startOfLastMonth = $p_to->startOfMonth();

        $days = 0;
        if(Carbon::parse($from) == Carbon::parse($from)->startOfMonth()) $months_between ++;
        else $days += (Carbon::parse($from)->diffInDays($endOfFirstMonth) + 1);

        if(Carbon::parse($to)->endOfMonth()->format('Y-m-d') == Carbon::parse($to)->format('Y-m-d')) $months_between++;
        else $days += ($startOfLastMonth->diffInDays(Carbon::parse($to)));

        if($days > 30){
            $months_between++;
            $days = $days - 30;
        }

        self::$years  = floor($months_between / 12);
        self::$months = $months_between - (self::$years * 12);
        self::$days   = $days; */

        return [
            'y' => self::$years,
            'm' => self::$months,
            'd' => self::$days
        ];
    }
}