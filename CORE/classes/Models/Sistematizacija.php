<?php


class Sistematizacija extends MainModel {
    protected static $new_table = 'systematization';
    protected static $sys = [];
    protected static $mySys = [];

    public static function putIntoArr($id, $title = false, $no = false){
        if($title){
            array_push(self::$sys, [
                'id' => $id,
                'title' => $title,
                'no' => $no
            ]);
        }else{
            array_push(self::$sys, $id);
        }
    }
    public static function getSysChild($children, $onlyId = false){
        foreach ($children as $sys){
            if($onlyId){
                self::putIntoArr($sys['id']);
            }else{
                self::putIntoArr($sys['id'], $sys['s_title'], $sys['s_no']);
            }
            $children = self::where('s_parent = '.$sys['id'])->get();
            if(count($children)) self::getSysChild($children);
        }
    }

    public static function getSys($_user = null, $onlyId = false){
        if ($_user == null){
            $mineSys = self::where('id = 1')->get();
        }
        else{
            $mineSys = self::where('id = '.$_user['egop_ustrojstvena_jedinica'])->get();
        }

        foreach ($mineSys as $sys){
            if($onlyId){
                self::putIntoArr($sys['id']);
            }else{
                self::putIntoArr($sys['id'], $sys['s_title'], $sys['s_no']);
            }
            $children = self::where('s_parent = '.$sys['id'])->get();
            if(count($children)) self::getSysChild($children, $onlyId);
        }
        return self::$sys;
    }

    public static function getOnlyChild($children){
        foreach ($children as $sys){
            array_push(self::$mySys, $sys['id']);
            $children = self::where('s_parent = '.$sys['id'])->get();
            if(count($children)) self::getOnlyChild($children);
        }
    }
    public static function getIDs($id){
        $orgJed = self::where('id = '.$id)->get();

        foreach ($orgJed as $sys){
            array_push(self::$mySys, $sys['id']);
            $children = self::where('s_parent = '.$sys['id'])->get();
            if(count($children)) self::getOnlyChild($children);
        }
        return self::$mySys;
    }
}