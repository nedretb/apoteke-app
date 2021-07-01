<?php

function view($location, $variables = [])
{

    $location = $location . ".aurora.php";

    $s = file_get_contents('../views/' . $location);

    foreach ($variables as $key => $value) {
        $s = str_replace('{{ $' . $key . ' }}', $value, $s);
    }

    echo $s;
}

class Helper
{

    public static function monthName($month)
    {
        switch ($month) {
            case 1:
                return "Januar";
                break;
            case 2:
                return "Februar";
                break;
            case 3:
                return "Mart";
                break;
            case 4:
                return "April";
                break;
            case 5:
                return "Maj";
                break;
            case 6:
                return "Juni";
                break;
            case 7:
                return "Juli";
                break;
            case 8:
                return "August";
                break;
            case 9:
                return "Septembar";
                break;
            case 10:
                return "Oktobar";
                break;
            case 11:
                return "Novembar";
                break;
            case 12:
                return "Decembar";
                break;
        }
    }


    public static function getDayColor($status, $review_status, $weekday)
    {


        if (in_array($status, array(18, 19, 106)) and $review_status == 1) {

            // GodiÅ¡nji odmor

            $color = "blue";

        } else if (in_array($status, array(83, 84, 21, 22)) and $review_status == 1) {

            // Praznici

            $color = "orange";

        } else if (in_array($status, array(43, 44, 45, 61, 62, 65, 67, 68, 69, 107, 108)) and $review_status == 1) {

            // Bolovanje

            $color = "blue";

        } elseif ($weekday == 6 or $weekday == 7) {
            $color = "grey";

        } else {

            $color = "white";
        }


        return $color;
    }

    public static function Message($class, $text)
    {
        return view('messages/poruka', ['message_class' => $class, 'message_text' => $text]);
    }


}


?>