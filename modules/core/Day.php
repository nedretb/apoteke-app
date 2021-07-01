<?php

class Day extends Model
{

    protected static $table = 'hourlyrate_day';


    public static function updateStatus($columns, $employee_no)
    {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));


        $cols = '';


        foreach ($columns as $k => $v) {
            $cols .= $k . ' = ' . $v . ", ";
        }


        self::update('    hour = ?,
                                      hour_pre = null,
                                      timest_edit = ?,
                                      timest_edit_corr = ?,
                                      employee_timest_edit = ?,
                                      ' . $cols . ' 
                                      review_status = ?,
                                      corr_review_status = ?,
                                      employee_comment = ?,
                                      review_comment = ?,
                                      status_rejected = NULL
                                    ',
            [
                $_POST['hour'], date('Y-m-d h:i:s'), date('Y-m-d h:i:s'), $_user['employee_no'], '0', 0,
                $_POST['komentar'], ''
            ]);

    }

    public static function updateStatusCorr($columns, $employee_no)
    {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));


        $cols = '';


        foreach ($columns as $k => $v) {
            $cols .= $k . ' = ' . $v . ", ";
        }


        self::update('    hour = ?,
                                      hour_pre = null,
                                      timest_edit_corr = ?,
                                      employee_timest_edit = ?,
                                      ' . $cols . ' 
                                      corr_review_status = ?,
                                      employee_comment = ?,
                                      review_comment = ?
                                    ',
            [
                $_POST['hour'], date('Y-m-d h:i:s'), $_user['employee_no'], 0,
                $_POST['komentar'], ''
            ]);

    }

    public static function countBolovanja($id_before, $status_first)
    {

        $count_take = 0;
        while (true) {
            $dan_before = Day::select('status, weekday')->where("id = '" . $id_before . "' ")->first();

            if ($dan_before->status != $status_first) {
                break;
            }

            $count_take++;
            $id_before = $id_before - 1;
        }

        return $count_take;
    }

}