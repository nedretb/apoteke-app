<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once '../../configuration.php';
//include_once $root . '/modules/admin_hourly_rate/functions.php';



if (DEBUG) {

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

}

function unique_multidim_array($array, $key)
{
    $temp_array = array();
    $i = 0;
    $key_array = array();

    foreach ($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}

if (isset($_POST['request'])) {

    if($_POST['request']=='generate-satnice'){

        $params = ['year' => $_POST['year'], 'month' => $_POST['month']];
        $curl = curl_init();
        curl_setopt_array(
            $curl, array(
            CURLOPT_URL => $host."/modules/work_booklet/pages/update_hourlyrate_day.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params
        ));
        $output = curl_exec($curl);

        global $db;


        $vrstaInsert = [
            "1010" => "Redovan rad",
            "2011" => "Redovan rad noću",
            "2010" => "Rad praznikom",
            "2012" => "Noćni rad praznikom",
            "2013" => "Rad nedjeljom",
            "2015" => "Rad vikendom",
            "2014" => "Noćni rad vikendom"
        ];

        $org 		= $_POST['org'];
        $month 		= $_POST['month'];
        $year 		= $_POST['year'];
        $verifiedal = $_POST['verifiedal'];

        $month_bosnian = monthBosnian($month);

        $dana_u_mjesecu = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $kolona_max = $dana_u_mjesecu+2;



        // Podaci
        if ($org == 'Svi radnici'){
            $orgJedIme = "Svi radnici";
            $query = $db->prepare("			
		  SELECT s1.user_id, s1.fname, s1.lname, s1.termination_date, s3.id
		  FROM [c0_intranet2_apoteke].[dbo].[users] s1
		  JOIN [c0_intranet2_apoteke].[dbo].[users] s2 ON s1.user_id = s2.user_id
		  JOIN [c0_intranet2_apoteke].[dbo].[hourlyrate_year] s3 ON s3.user_id = s1.user_id and year = '$year' order by s1.employee_no");

            $query->execute();
            $fetch_users = $query->fetchAll();
        }
        else{
            $orgJedIme = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[systematization] WHERE id=".$org)->fetch()['s_title'];
            $query = $db->prepare("			
		  SELECT s1.user_id, s1.fname, s1.lname, s1.termination_date, s3.id
		  FROM [c0_intranet2_apoteke].[dbo].[users] s1
		  JOIN [c0_intranet2_apoteke].[dbo].[users] s2 ON s1.user_id = s2.user_id and s2.[egop_ustrojstvena_jedinica] = ?
		  JOIN [c0_intranet2_apoteke].[dbo].[hourlyrate_year] s3 ON s3.user_id = s1.user_id and year = '$year'  order by s1.employee_no");

            $query->execute(array($org));
            $fetch_users = $query->fetchAll();
        }

        $data_array = array();
        $user_stats_array = array();
        $userEmpNo = [];
        $yearId = [];

        $user_row_id = 1;
        foreach($fetch_users as $key => $value){
            if ($value['termination_date'] != null){ continue;}
            $data_array[$user_row_id][0] = $value['fname'] . " " . $value['lname'];

            array_push($yearId, $value['id']);

            $get_days = $db->prepare("SELECT id, status, hour, hour_pre, status_pre, day, weekday, employee_no FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE
			user_id = '$value[user_id]' and year_id = '$value[id]' and month_id = '$month'");

            $get_days->execute();
            $days_user = $get_days->fetchAll();


            $count_hourz = 0;
            foreach($days_user as $k => $v){
                if($k == 'employee_no'){
                    array_push($userEmpNo, $v['employee_no']);
                }



                $get_day_data = getHourlyrateData($v['status'], $v['hour'], $v['hour_pre'], $v['weekday']);

                if(empty($user_stats_array[$user_row_id][_shortcode($v['status'])])){
                    $user_stats_array[$user_row_id][_shortcode($v['status'])] = 0;
                }

                $user_stats_array[$user_row_id][_shortcode($v['status'])] += $get_day_data[1];

                $data_array[$user_row_id][$v['day']] = $get_day_data[0];

                $count_hourz += $get_day_data[1];
            }

            $data_array[$user_row_id][$kolona_max-1] = $count_hourz;

            $user_row_id++;
        }


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];

        $styleArrayBorderTop = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
        ];

        $styleArrayBorderBottom = [
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
        ];

        $styleArrayCornerLeftTop = [
            'font' => [
                'bold' => true,
            ],
            'alingment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'color' => ['argb' => 'FF32CD32']
            ]
        ];

        $styleArrayCornerLeftBottom = [
            'font' => [
                'bold' => true,
            ],
            'alingment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'color' => ['argb' => 'FF32CD32']
            ]
        ];

        $styleArrayLeft = [
            'font' => [
                'bold' => true,
            ],
            'alingment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'color' => ['argb' => 'FF32CD32']
            ]
        ];

        $styleArrayRight = [
            'alingment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ]
            ],
        ];


        $sheet->setCellValue('A2', $orgJedIme);
        $sheet->setCellValue('A3', strtoupper($month_bosnian).' '. $year);

        $sheet->getStyle("A2")->getFont()->setBold(true);

        $sheet->mergeCells('F2:L2' );
        $sheet->mergeCells('F3:L3' );
        $sheet->mergeCells('F4:L4' );
        $sheet->mergeCells('F5:L5' );
        $sheet->mergeCells('F6:L6' );
        $sheet->mergeCells('F7:L7' );
        $sheet->mergeCells('F8:L8' );
        $sheet->mergeCells('F9:L9' );

        $sheet->mergeCells('N2:T2' );
        $sheet->mergeCells('N3:T3' );
        $sheet->mergeCells('N4:T4' );
        $sheet->mergeCells('N5:T5' );
        $sheet->mergeCells('N6:T6' );
        $sheet->mergeCells('N7:T7' );
        $sheet->mergeCells('N8:T8' );
        $sheet->mergeCells('N9:T9' );

        $sheet->mergeCells('V2:AB2' );
        $sheet->mergeCells('V3:AB3' );
        $sheet->mergeCells('V4:AB4' );
        $sheet->mergeCells('V5:AB5' );
        $sheet->mergeCells('V6:AB6' );
        $sheet->mergeCells('V7:AB7' );
        $sheet->mergeCells('V8:AB8' );
        $sheet->mergeCells('V9:AB9' );

        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(5);
        $sheet->getColumnDimension('F')->setWidth(5);
        $sheet->getColumnDimension('G')->setWidth(5);
        $sheet->getColumnDimension('H')->setWidth(5);
        $sheet->getColumnDimension('I')->setWidth(5);
        $sheet->getColumnDimension('J')->setWidth(5);
        $sheet->getColumnDimension('K')->setWidth(5);
        $sheet->getColumnDimension('L')->setWidth(5);

        $sheet->getColumnDimension('M')->setWidth(5);
        $sheet->getColumnDimension('N')->setWidth(5);
        $sheet->getColumnDimension('O')->setWidth(5);
        $sheet->getColumnDimension('P')->setWidth(5);
        $sheet->getColumnDimension('Q')->setWidth(5);
        $sheet->getColumnDimension('R')->setWidth(5);
        $sheet->getColumnDimension('S')->setWidth(5);
        $sheet->getColumnDimension('T')->setWidth(5);

        $sheet->getColumnDimension('U')->setWidth(5);
        $sheet->getColumnDimension('V')->setWidth(5);
        $sheet->getColumnDimension('W')->setWidth(5);
        $sheet->getColumnDimension('X')->setWidth(5);
        $sheet->getColumnDimension('Y')->setWidth(5);
        $sheet->getColumnDimension('Z')->setWidth(5);
        $sheet->getColumnDimension('AA')->setWidth(5);
        $sheet->getColumnDimension('AB')->setWidth(5);

        $sheet->setCellValue('E2', '1030');
        $sheet->setCellValue('F2', 'Bolovanje do 42 dana');

        $sheet->setCellValue('E3', '1036');
        $sheet->setCellValue('F3', 'Bolovanje povreda van rada');

        $sheet->setCellValue('E4', '1037');
        $sheet->setCellValue('F4', 'Bolovanje povreda van rada preko 42 dana');

        $sheet->setCellValue('E5', '1031');
        $sheet->setCellValue('F5', 'Bolovanje preko 42 dana');

        $sheet->setCellValue('E6', '1033');
        $sheet->setCellValue('F6', 'Bolovanje povreda na radu');

        $sheet->setCellValue('E7', '1034');
        $sheet->setCellValue('F7', 'Bolovanje povreda na radu preko 42 dana');

        $sheet->setCellValue('E8', '1026');
        $sheet->setCellValue('F8', 'Državni/Vjerski praynik');

        $sheet->setCellValue('E9', '1022');
        $sheet->setCellValue('F9', 'Godišnji odmor');

        $sheet->setCellValue('M2', '1023');
        $sheet->setCellValue('N2', 'Stari godišnji odmor');

        $sheet->setCellValue('M3', '1024');
        $sheet->setCellValue('N3', 'Službeni put');

        $sheet->setCellValue('M4', '1040');
        $sheet->setCellValue('N4', 'Porodiljsko odsustvo');

        $sheet->setCellValue('M5', '2020');
        $sheet->setCellValue('N5', 'Prekovremeni rad');

        $sheet->setCellValue('M6', '2022');
        $sheet->setCellValue('N6', 'Prekovremeni rad vikendom');

        $sheet->setCellValue('M7', '1020');
        $sheet->setCellValue('N7', 'Plaćeno odsustvo');

        $sheet->setCellValue('M8', '1042');
        $sheet->setCellValue('N8', 'Trudničko bolovanje do 42 dana');

        $sheet->setCellValue('M9', '1043');
        $sheet->setCellValue('N9', 'Trudničko bolovanje preko 42 dana');

        $sheet->setCellValue('U2', '2010');
        $sheet->setCellValue('V2', 'Rad praznikom');

        $sheet->setCellValue('U3', '2012');
        $sheet->setCellValue('V3', 'Noćni rad praznikom');

        $sheet->setCellValue('U4', '1010');
        $sheet->setCellValue('V4', 'Redovan rad');

        $sheet->setCellValue('U5', '2011');
        $sheet->setCellValue('V5', 'Redovan noćni rad');

        $sheet->setCellValue('U6', '1021');
        $sheet->setCellValue('V6', 'Neplaćeno odsustvo');

        $sheet->getStyle('E2')->applyFromArray($styleArrayCornerLeftTop);
        $sheet->getStyle('E3:E8')->applyFromArray($styleArrayLeft);
        $sheet->getStyle('E9')->applyFromArray($styleArrayCornerLeftBottom);

        $sheet->getStyle('M2')->applyFromArray($styleArrayCornerLeftTop);
        $sheet->getStyle('M3:M8')->applyFromArray($styleArrayLeft);
        $sheet->getStyle('M9')->applyFromArray($styleArrayCornerLeftBottom);

        $sheet->getStyle('U2')->applyFromArray($styleArrayCornerLeftTop);
        $sheet->getStyle('U3:U8')->applyFromArray($styleArrayLeft);
        $sheet->getStyle('U9')->applyFromArray($styleArrayCornerLeftBottom);


        $sheet->getStyle('F2:L2')->applyFromArray($styleArrayBorderTop);
        $sheet->getStyle('N2:T2')->applyFromArray($styleArrayBorderTop);
        $sheet->getStyle('V2:AB2')->applyFromArray($styleArrayBorderTop);
        $sheet->getStyle('F9:L9')->applyFromArray($styleArrayBorderBottom);
        $sheet->getStyle('V9:AB9')->applyFromArray($styleArrayBorderBottom);
        $sheet->getStyle('N9:T9')->applyFromArray($styleArrayBorderBottom);
        $sheet->getStyle('AB2:AB9')->applyFromArray($styleArrayRight);


        $styleArrayLabel = [
            'alingment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ]
            ],
        ];
        $sheet->setCellValue('A13', 'Šifra');
        $sheet->setCellValue('B13', 'Ime i prezime');
        $sheet->setCellValue('C13', 'Vrsta');
        $sheet->setCellValue('D13', 'Opis');
        $sheet->getStyle('A13:D13')->applyFromArray($styleArrayLabel);


        $styleArrayNameEmpNo = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ]
            ],
        ];

        $styleArrayLabel = [
            'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];
        $styleArrayLabel2 = [
            'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];

        $styleArrayWeekendLabel = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFA9A9A9']
            ],'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];

        $styleArrayWeekendLabel2 = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFA9A9A9']
            ],'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];
        $styleArrayWeekend = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFF5F5F5']
            ],
            'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];
        $styleArrayWeekendLastRow = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFF5F5F5']
            ],
            'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];
        $styleArrayWorkDays = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];
        $styleArrayWorkDaysLastRow = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];

        $styleArrayNoBorders = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ]
            ]
        ];
        $styleArrayNoBordersButRight = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ]
            ]
        ];
        $styleArrayNoBordersButBottom = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ]
            ]
        ];
        $styleArrayNoBordersButBottomAndRight = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'top' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
                'bottom' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ],
                'left' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
                'right' =>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ]
            ]
        ];

        $numberOfDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $dayOfWeek = date('l', strtotime('2021-'.$month.'-01'));
        $column = 'E';
        for($i = 1; $i<=$numberOfDaysInMonth; $i++){
            $dayOfWeek = date('l', strtotime('2021-'.$month.'-'.$i));
            $dayOfWeekInsert = '';
            switch ($dayOfWeek){
                case 'Friday':
                case 'Monday':
                    $dayOfWeekInsert = 'P';
                    break;
                case 'Tuesday':
                    $dayOfWeekInsert = 'U';
                    break;
                case 'Saturday':
                case 'Wednesday':
                    $dayOfWeekInsert = 'S';
                    break;
                case 'Thursday':
                    $dayOfWeekInsert = 'Č';
                    break;
                case 'Sunday':
                    $dayOfWeekInsert = 'N';
                    break;
            }

            $sheet->setCellValue($column.'12', $dayOfWeekInsert);
            if($dayOfWeek == 'Sunday' or $dayOfWeek == 'Saturday'){
                $sheet->getStyle($column.'12')->applyFromArray($styleArrayWeekendLabel);
                $sheet->getStyle($column.'13')->applyFromArray($styleArrayWeekendLabel2);
                $sheet->getStyle($column.'13')->getFont()->setBold(true);
            }
            else{
                $sheet->getStyle($column.'12')->applyFromArray($styleArrayLabel);
                $sheet->getStyle($column.'13')->applyFromArray($styleArrayLabel2);
                $sheet->getStyle($column.'13')->getFont()->setBold(true);
            }

            $sheet->setCellValue($column.'13', $i);
            if($column != 'AB'){
                $sheet->getColumnDimension($column)->setWidth(5);
            }
            $column++;
        }

        $columnPrevoz = $column;
        $sheet->mergeCells($columnPrevoz.'12:'.$columnPrevoz.'13');
        $sheet->setCellValue($columnPrevoz.'12', 'Prevoz (da/ne)');
        $sheet->getStyle($columnPrevoz.'12:'.$columnPrevoz.'13')->applyFromArray($styleArrayNameEmpNo);
        $sheet->getStyle($columnPrevoz.'12:'.$columnPrevoz.'13')->getFont()->setBold(true);

        $columnKupn = $columnPrevoz ;
        $columnKupn++;
        $sheet->mergeCells($columnKupn.'12:'.$columnKupn.'13');
        $sheet->setCellValue($columnKupn.'12', 'Kupon/Novac');
        $sheet->getStyle($columnKupn.'12:'.$columnKupn.'13')->applyFromArray($styleArrayNameEmpNo);
        $sheet->getStyle($columnKupn.'12:'.$columnKupn.'13')->getFont()->setBold(true);

        $columnSati= $columnKupn;
        $columnSati++;
        $sheet->mergeCells($columnSati.'12:'.$columnSati.'13');
        $sheet->setCellValue($columnSati.'12', 'Ukupno sati');
        $sheet->getStyle($columnSati.'12:'.$columnSati.'13')->applyFromArray($styleArrayNameEmpNo);
        $sheet->getStyle($columnSati.'12:'.$columnSati.'13')->getFont()->setBold(true);

        $count = 0;
        $row = 14;

        foreach ($data_array as $data){
            $sumHourRe = $db->query("SELECT sum(hour)  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] where employee_no=".$userEmpNo[$count]." and month_id=".$month." and year_id=".$yearId[$count])->fetch();
            $sumHourPre = $db->query("SELECT sum(hour_pre)  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] where employee_no=".$userEmpNo[$count]." and month_id=".$month." and year_id=".$yearId[$count])->fetch();
            $sumHour = $sumHourRe[0] + $sumHourPre[0];

            $prevozData = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users__poreska_olaksica_i_prevoz] where employee_no=".$userEmpNo[$count])->fetch();
            $sheet->setCellValue($columnSati.$row, $sumHour);
            $sheet->setCellValue($columnPrevoz.$row, $prevozData['prevoz']);
            $sheet->setCellValue($columnKupn.$row, $prevozData['nacin_placanja']);

            $sheet->getStyle($columnPrevoz.$row.':'.$columnPrevoz.($row+5))->applyFromArray($styleArrayNoBorders);
            $sheet->getStyle($columnSati.$row.':'.$columnSati.($row+5))->applyFromArray($styleArrayNoBordersButRight);
            $sheet->getStyle($columnKupn.$row.':'.$columnKupn.($row+5))->applyFromArray($styleArrayNoBorders);


            $sheet->getStyle($columnPrevoz.($row+6))->applyFromArray($styleArrayNoBordersButBottom);
            $sheet->getStyle($columnSati.($row+6))->applyFromArray($styleArrayNoBordersButBottomAndRight);
            $sheet->getStyle($columnKupn.($row+6))->applyFromArray($styleArrayNoBordersButBottom);

            $sheet->mergeCells('A'.$row.':A'.($row+6));
            $sheet->setCellValue('A'.$row, $userEmpNo[$count]);
            $sheet->mergeCells('B'.$row.':B'.($row+6));
            $sheet->setCellValue('B'.$row, $data[0]);
            $sheet->getStyle('A'.$row.':A'.($row+6))->applyFromArray($styleArrayNameEmpNo);
            $sheet->getStyle('B'.$row.':B'.($row+6))->applyFromArray($styleArrayNameEmpNo);
            $status = $db->query("SELECT status, hour, apoteke_status, status_pre, hour_pre, apoteke_status_pre FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] where employee_no=".$userEmpNo[$count]." and month_id=".$month." order by day")->fetchAll();


            $column = 'E';
            for($i = 0; $i<=$numberOfDaysInMonth-1; $i++){
                $dayOfWeek = date('l', strtotime('2021-'.$month.'-'.($i+1)));

                if(!in_array($status[$i]['apoteke_status_pre'], ['1010', '2011', '2010', '2012', '2013', '2015', '2014'])){
                    $sheet->setCellValue($column.$row, $status[$i]['apoteke_status_pre']);
                }else {
                    $increaseRowPre = 0;
                    switch ($status[$i]['apoteke_status_pre']) {
                        case '2011':
                            $increaseRowPre = 1;
                            break;
                        case '2010':
                            $increaseRowPre = 2;
                            break;
                        case '2012':
                            $increaseRowPre = 3;
                            break;
                        case '2013':
                            $increaseRowPre = 4;
                            break;
                        case '2015':
                            $increaseRowPre = 5;
                            break;
                        case '2014':
                            $increaseRowPre = 6;
                            break;
                    }

                    if (($dayOfWeek == 'Sunday' or $dayOfWeek == 'Saturday') and $increaseRowPre == 0) {
                        $sheet->setCellValue($column . ($row + $increaseRowPre), '');
                    } else {
                        $sheet->setCellValue($column . ($row + $increaseRowPre), $status[$i]['hour_pre']);
                    }
                }

                if(!in_array($status[$i]['apoteke_status'], ['1010', '2011', '2010', '2012', '2013', '2015', '2014'])){
                    $sheet->setCellValue($column.$row, $status[$i]['apoteke_status']);
                }
                else{

                    $increaseRow = 0;
                    switch ($status[$i]['apoteke_status']){
                        case '2011':
                            $increaseRow = 1;
                            break;
                        case '2010':
                            $increaseRow = 2;
                            break;
                        case '2012':
                            $increaseRow = 3;
                            break;
                        case '2013':
                            $increaseRow = 4;
                            break;
                        case '2015':
                            $increaseRow = 5;
                            break;
                        case '2014':
                            $increaseRow = 6;
                            break;
                    }

                    if(($dayOfWeek == 'Sunday' or $dayOfWeek == 'Saturday') and $increaseRow == 0){
                            $sheet->setCellValue($column.($row + $increaseRow), '');
                    }
                    else{
                        $sheet->setCellValue($column.($row + $increaseRow), $status[$i]['hour']);
                    }

                }
                for ($j = 0; $j < 7; $j++){
                    if($dayOfWeek == 'Sunday' or $dayOfWeek == 'Saturday'){
                        ($j == 6) ? $sheet->getStyle($column.($row + $j))->applyFromArray($styleArrayWeekendLastRow) : $sheet->getStyle($column.($row + $j))->applyFromArray($styleArrayWeekend);;
//                        if($j == 6){
//                            $sheet->getStyle($column.($row + $j))->applyFromArray($styleArrayWeekendLastRow);
//                        }
//                        else{
//                            $sheet->getStyle($column.($row + $j))->applyFromArray($styleArrayWeekend);
//                        }
                    }
                    else{
                        ($j == 6) ? $sheet->getStyle($column.($row + $j))->applyFromArray($styleArrayWorkDaysLastRow) : $sheet->getStyle($column.($row + $j))->applyFromArray($styleArrayWorkDays);
//                        if($j == 6){
//                            $sheet->getStyle($column.($row + $j))->applyFromArray($styleArrayWorkDaysLastRow);
//                        }
//                        else{
//                            $sheet->getStyle($column.($row + $j))->applyFromArray($styleArrayWorkDays);
//                        }
                    }
                }
                $column++;
            }

            foreach ($vrstaInsert as $key => $value){
                $sheet->setCellValue('C'.$row, $key);
                $sheet->setCellValue('D'.$row, $value);
                $sheet->getStyle('C'.$row)->applyFromArray($styleArrayNameEmpNo);
                $sheet->getStyle('D'.$row)->applyFromArray($styleArrayNameEmpNo);
                $sheet->getStyle('C'.$row.':D'.$row)->getFont()->setBold(true);
                $row++;
            }

            $numberOfDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $dayOfWeek = date('l', strtotime('2021-'.$month.'-01'));

            $count++;
        }

        $writer = new Xlsx($spreadsheet);
        $clean_org = cleanSuskavac(strtolower (str_replace(" ", "-", $org)));
        $fname = date("d-m-Y") . '-'.$clean_org.'.xlsx';
        $filename = "../../uploads/". $fname;
        $writer->save($filename);

        echo $fname;
    }

    if($_POST['request']=='generate-satnice-old'){
        global $db;


        $org 		= $_POST['org'];
        $month 		= $_POST['month'];
        $year 		= $_POST['year'];
        $verifiedal = $_POST['verifiedal'];

        $month_bosnian = monthBosnian($month);

        $dana_u_mjesecu = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $kolona_max = $dana_u_mjesecu+2;

        // Podaci
        if ($org == 'Svi radnici'){
            $query = $db->prepare("			
		  SELECT s1.user_id, s1.fname, s1.lname, s1.termination_date, s3.id
		  FROM [c0_intranet2_apoteke].[dbo].[users] s1
		  JOIN [c0_intranet2_apoteke].[dbo].[users] s2 ON s1.user_id = s2.user_id
		  JOIN [c0_intranet2_apoteke].[dbo].[hourlyrate_year] s3 ON s3.user_id = s1.user_id and year = '$year'
		");

            $query->execute();
            $fetch_users = $query->fetchAll();
        }
        else{
            $query = $db->prepare("			
		  SELECT s1.user_id, s1.fname, s1.lname, s1.termination_date, s3.id
		  FROM [c0_intranet2_apoteke].[dbo].[users] s1
		  JOIN [c0_intranet2_apoteke].[dbo].[users] s2 ON s1.user_id = s2.user_id and s2.[egop_ustrojstvena_jedinica] = ?
		  JOIN [c0_intranet2_apoteke].[dbo].[hourlyrate_year] s3 ON s3.user_id = s1.user_id and year = '$year'
		");

            $query->execute(array($org));
            $fetch_users = $query->fetchAll();
        }


        $data_array = array();
        $user_stats_array = array();

        $user_row_id = 1;
        foreach($fetch_users as $key => $value){
            if ($value['termination_date'] != null){ continue;}
            $data_array[$user_row_id][0] = $value['fname'] . " " . $value['lname'];

            $get_days = $db->prepare("SELECT id, status, hour, hour_pre, day, weekday FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE
			user_id = '$value[user_id]' and year_id = '$value[id]' and month_id = '$month'
			");
            $get_days->execute();
            $days_user = $get_days->fetchAll();

            $count_hourz = 0;
            foreach($days_user as $k => $v){

                $get_day_data = getHourlyrateData($v['status'], $v['hour'], $v['hour_pre'], $v['weekday']);

                if(empty($user_stats_array[$user_row_id][_shortcode($v['status'])])){
                    $user_stats_array[$user_row_id][_shortcode($v['status'])] = 0;
                }

                $user_stats_array[$user_row_id][_shortcode($v['status'])] += $get_day_data[1];

                $data_array[$user_row_id][$v['day']] = $get_day_data[0];

                $count_hourz += $get_day_data[1];
            }

            $data_array[$user_row_id][$kolona_max-1] = $count_hourz;

            $user_row_id++;
        }


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];


        $sheet->setCellValue('A1', 'Ministartstvo komunikacija i prometa');
        $sheet->setCellValue('A2', 'ORGANIZACIONI DIO: ' . $org);
        $sheet->setCellValue('A4', 'Datum: '. date("d.m.Y.") . ' godine');
        $sheet->setCellValue('M4', 'PREGLED PRISUSTVA RADNIKA NA POSLU ZA MJESEC '.strtoupper($month_bosnian).' '. $year);

        $sheet->getStyle("A4")->getFont()->setBold(true);
        $sheet->getStyle("M4")->getFont()->setBold(true);



        $kolone = createColumnsArray("ZZ");

        $i 		= 0;
        $num 	= 6;
        $rows 	= 0;
        $row_calendar_iteration = 0;
        $column_calendar_days = 1;
        $worker_calendar_day = 0;



        $dey = 0;

        // generisanje tabele satnica
        while(true):
            // trenutna celija i slovo kolone
            $letter = $kolone[$i];
            $celija = $letter.$num;



            // ako je prvi red, redni broj ime prezime, dani u mjesecu...
            if($rows == 0){
                $sheet->getRowDimension($num)->setRowHeight(35);
                $sheet->getStyle($celija)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                // ako je prva kolona onda redni broj tekst
                if($i == 0){
                    $data = "Rb";
                    $sheet->getColumnDimension($letter)->setWidth(7);
                } else if($i == 1){
                    // ako je druga kolona onda ime prezime radnika
                    $sheet->getColumnDimension($letter)->setWidth(33);
                    $data = "Ime i prezime radnika";
                } else {
                    // dani u mjesecu 1 - 30/31
                    if($column_calendar_days <= $dana_u_mjesecu){ // popunjavanje kalendara

                        $dey++;
                        $data = $column_calendar_days;
                    } else if($column_calendar_days == $dana_u_mjesecu+1){
                        $data = "Ukupno sati";
                        $sheet->getColumnDimension($letter)->setAutoSize(true);
                    } else {
                        $data = "";
                    }


                }
            } else if($rows == 1){

                $sheet->getRowDimension($num)->setRowHeight(25);
                $sheet->getStyle($celija)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                if($i == 0){
                    $data = "";
                } else if($i == 1){
                    $data = "početak r.v.";
                } else if($i == $kolona_max){
                    $data = "";
                } else {
                    $data = "8:00";
                    $dey++;
                }

            } else if($rows == 2){
                if($i == 0){
                    $data = "";
                } else if($i == 1){
                    $data = "kraj r.v.";
                } else if($i == $kolona_max){
                    $data = "";
                } else {
                    $data = "16:30";
                    $dey++;
                }
                $sheet->getRowDimension($num)->setRowHeight(25);
                $sheet->getStyle($celija)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);


            } else {
                // svi ostali redovi
                $sheet->getRowDimension($num)->setRowHeight(25);
                $sheet->getStyle($celija)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                // ako je prva kolona tj redni brojevi
                if($i == 0 and $letter == 'A'){
                    $data = $row_calendar_iteration;
                } else {

                    // popunjavanje broja sati, go itd.

                    if($i > 1){
                        $dey++;
                    }

                    $data 		= @$data_array[$row_calendar_iteration][$worker_calendar_day];

                    /*if(isWeekend("$year-$month-$worker_calendar_day") == true and $worker_calendar_day != 0){
                        $sheet->getStyle($celija)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('E8E8E8');
                    }*/

                    //$data = '$var['.$row_calendar_iteration.']['.$worker_calendar_day.']';

                    if($i == $kolona_max){
                        $worker_calendar_day = 0;
                    } else {
                        $worker_calendar_day++;
                    }
                }

            }

            if($data == "0"){
                $data = "";
            }

            $sheet->getStyle($celija)->applyFromArray($styleArray);
            $sheet->getStyle($celija)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue($celija, $data);

            if(isWeekend("$year-$month-$dey") == true and $letter != 'A' and $letter != 'B' and ($rows == 1 or $rows == 2) ){
                $sheet->setCellValue($celija, "");
            }

            if(isWeekend("$year-$month-$dey") == true and $letter != 'A' and $letter != 'B'){
                $sheet->getStyle($celija)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('E8E8E8');
            }

            if(isWeekend("$year-$month-$dey") == false && $letter != 'M' && $letter != 'A' && $letter != 'B'){
                $sheet->getColumnDimension($letter)->setAutoSize(true);
            } else if(isWeekend("$year-$month-$dey") == true){
                $sheet->getColumnDimension($letter)->setWidth(5);
            }

            // ako popunimo jedan red prebacivamo na sljedeci
            if($i == $kolona_max){
                $num++;
                $rows++;
                if($rows > 2):
                    $row_calendar_iteration++;
                endif;
                $i = 0;
                $dey = 0;
            } else {
                $i++;
                if($i > 2){
                    $column_calendar_days++;
                }
            }


            if($rows == $user_row_id+2){
                break;
            }

        endwhile;



        // generisanje tabele legenda + rukovodilac

        $get_statuses = $db->prepare("SELECT shortcode FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] GROUP BY shortcode");
        $get_statuses->execute();

        $statuses = $get_statuses->fetchAll();

        $count_statuses = count($statuses);

        // popunjavanje vikend boje
        $num = $num+1;

        $sheet->getStyle('A'.$num)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('E8E8E8');
        $sheet->setCellValue('B'.$num, 'Vikend');


        $sheet->getStyle('A'.$num)->applyFromArray($styleArray);
        $sheet->getStyle('B'.$num)->applyFromArray($styleArray);


        $j = 0;
        $num_rows = $num + 1;
        $rows_status_id = 0;
        while(true):
            // trenutna celija i slovo kolone
            $letter = $kolone[$j];
            $celija = $letter.$num_rows;




            $sg = $statuses[$rows_status_id]['shortcode'];
            if($j == 0){
                // ako je prva kolona, onda slova
                $sheet->setCellValue($celija, $sg);
                $sheet->getStyle($celija)->getFont()->setBold(true);
            } else if($j == 1){
                // ako je druga kolona onda objašnjenje legende
                $sheet->setCellValue($celija, explainStatus($sg));
            }

            $sheet->getStyle($celija)->applyFromArray($styleArray);

            if($j == 1){
                $num_rows++;
                $rows_status_id++;
                $j = 0;
            } else {
                $j++;
            }


            if($num_rows == $num + $count_statuses){
                break;
            }

        endwhile;

        $sheet->getColumnDimension('B')->setWidth(33);

        $sheet->setCellValue('A'.($num_rows+1), "PUNI FOND SATI U MJESECU ".strtoupper($month_bosnian)." IZNOSI " . countWorkingDays($year, $month,  array(0, 6))*8);
        $sheet->getStyle('A'.($num_rows+1))->getFont()->setBold(true);

        $get_verified = $db->prepare(" 	  SELECT TOP 1 s2.fname, s2.lname, s2.position FROM [c0_intranet2_apoteke].[dbo].[users] s1 
										  JOIN [c0_intranet2_apoteke].[dbo].[users] s2 ON s1.user_id = s2.user_id 
										  WHERE s1.[B_1_description] = ? ");
        $get_verified->execute(array($org));
        $verified	= $get_verified->fetch();

        $sheet->setCellValue('B'.($num_rows+2), "Verifikovao: ");
        //if ($verifiedal == 1){
        $sheet->setCellValue('B'.($num_rows+3), $verified['fname'] . " " . $verified['lname']);
        $sheet->setCellValue('B'.($num_rows+4), $verified['position']);
        //}

        // generisanje finalne statistike, tabela desno

        // generisanje table headersa
        $redovi = $num;
        $let_x = 17;
        $y = 0;

        $sheet->setCellValue($kolone[16].$redovi, "Rb");
        $sheet->getStyle($kolone[16].$redovi)->getFont()->setBold(true);
        $sheet->getStyle($kolone[16].$redovi)->applyFromArray($styleArray);

        for($x = 0; $x < $count_statuses; $x++){
            $letter_head = $kolone[$let_x];
            $celija_head = $letter_head.$redovi;

            $sheet->setCellValue($celija_head, $statuses[$y]['shortcode']);
            $sheet->getStyle($celija_head)->getFont()->setBold(true);
            $sheet->getStyle($celija_head)->applyFromArray($styleArray);

            $y++;
            $let_x++;

        }



        $k = 16;
        $num_rows_stats = $num + 1;
        $rows_status_id_stats = 1; // broj reda $var[ova][...]
        $real_redovi = 0;


        while(true):
            // trenutna celija i slovo kolone
            $letter = $kolone[$k];
            $celija = $letter.$num_rows_stats;

            if($k == 16){
                $sheet->setCellValue($celija, $rows_status_id_stats);
                $sheet->getStyle($celija)->getFont()->setBold(true);
                $sheet->getStyle($celija)->applyFromArray($styleArray);
                $k++;
            } else {


                $current_stat = @$user_stats_array[$rows_status_id_stats][$statuses[$real_redovi]['shortcode']];

                $sheet->setCellValue($celija, $current_stat);
                $sheet->getStyle($celija)->applyFromArray($styleArray);


                if($k == $count_statuses+16){
                    $num_rows_stats++;
                    $rows_status_id_stats++;
                    $k = 16;
                    $real_redovi = 0;
                } else {
                    $k++;
                    $real_redovi++;
                }

            }

            if($num_rows_stats == $num + $user_row_id){
                break;
            }
        endwhile;


        $writer = new Xlsx($spreadsheet);
        $clean_org = cleanSuskavac(strtolower (str_replace(" ", "-", $org)));
        $fname = date("d-m-Y") . '-'.$clean_org.'.xlsx';
        $filename = "../../uploads/". $fname;
        $writer->save($filename);

        echo $fname;
    }


    if ($_POST['request'] == 'request-add') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $ds = explode('/', $_POST['from']);
        $de = explode('/', $_POST['to']);

        $from = $ds[2] . '-' . $ds[1] . '-' . $ds[0];
        $to = $de[2] . '-' . $de[1] . '-' . $de[0];

        $data = "INSERT INTO  " . $portal_requests . "  (
      user_id,parent_id,date_created,h_from,h_to,status,type,is_archive) VALUES (?,?,?,?,?,?,?,?)";

        $res = $db->prepare($data);
        $res->execute(
            array($_user['user_id'], $_user['parent'], date('Y-m-d', strtotime("now")), date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to)), '0', 'GO', '0'));
        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'travel-request-add') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $ds = explode('/', $_POST['from']);
        $de = explode('/', $_POST['to']);

        $from = $ds[2] . '-' . $ds[1] . '-' . $ds[0];
        $to = $de[2] . '-' . $de[1] . '-' . $de[0];

        $data = "INSERT INTO  " . $portal_travel_requests . "  (
      user_id,parent_id,date_created,h_from,h_to,status,type,is_archive,country,travel_route,comment,total_cost) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

        $res = $db->prepare($data);
        $res->execute(
            array($_user['user_id'], $_user['parent'], date('Y-m-d', strtotime("now")),
                date('Y-m-d', strtotime($from)),
                date('Y-m-d', strtotime($to)),
                '0',
                'SLUŽBENI PUT',
                '0',
                $_POST['country'], $_POST['travel_route'], $_POST['comment'], $_POST['total_cost']));
        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
        }

    }


    if ($_POST['request'] == 'year-add') {

        $check = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE year='" . $_POST['year'] . "'")->rowCount();
        if ($check < 0) {

            echo '<div class="alert alert-danger text-center">' . __('Godinu koju ste odabrali već postoji!') . '</div><br/>';

        } else {

            $_user = _user(_decrypt($_SESSION['SESSION_USER']));
            $query = $db->query("SELECT [user_id] FROM  " . $portal_users . " ");
            $get2 = $db->query("SELECT COUNT(*) FROM  " . $portal_users . " ");
            $query2 = $db->query("SELECT [year] FROM  " . $portal_hourlyrate_year . " ");

            $total = $get2->rowCount();


            foreach ($query as $item) {

                $absence_year_id = $item['user_id'];


                $data = "INSERT INTO  " . $portal_hourlyrate_year . " (
     user_id,year) VALUES (?,?)";

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $absence_year_id,
                        $_POST['year']
                    )
                );
            }
            if ($res->rowCount() == 1) {
                echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';

            }


        }
    }


    if ($_POST['request'] == 'month-add') {

        $check = $db->query("SELECT * FROM  " . $portal_hourlyrate_month . "  WHERE month='" . $_POST['month'] . "' AND year_id='" . $_POST['year'] . "'")->rowCount();

        if ($check < 0) {

            echo '<div class="alert alert-danger text-center">' . __('Mjesec koji ste odabrali već postoji!') . '</div><br/>';

        } else {


            $_user = _user(_decrypt($_SESSION['SESSION_USER']));

            $query_month = $db->query("SELECT [user_id] FROM  " . $portal_users . " ");
            $get_month = $db->query("SELECT COUNT(*) FROM  " . $portal_users . " ");
            $yearcurr = $db->query("SELECT [year] FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $_POST['year'] . "'");
            $total = $get_month->rowCount();
            foreach ($yearcurr as $value2) {
                $absence_year = $value2['year'];
            }

            foreach ($query_month as $item) {

                $month = $db->query("SELECT [id] FROM  " . $portal_hourlyrate_year . "  where [user_id]='" . $item['user_id'] . "' and year='" . $absence_year . "'");

                $absence_id_month = $item['user_id'];
                foreach ($month as $value) {
                    $absence_month = $value['id'];
                }

                $_user = _user(_decrypt($_SESSION['SESSION_USER']));

                $data = "INSERT INTO  " . $portal_hourlyrate_month . " (id,
      user_id,year_id,month) VALUES (?,?,?,?)";

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $_POST['month'],
                        $absence_id_month,
                        $absence_month,
                        $_POST['month']
                    )
                );


                $query_calendar = $db->query("SELECT [day],[weekday] FROM  " . $portal_calendar . "  where [month]='" . $_POST['month'] . "'
   and  [year]='" . $absence_year . "'");

                foreach ($query_calendar as $cal) {
                    $day = $cal ['day'];
                    $weekday = $cal ['weekday'];
                    $data = "INSERT INTO  " . $portal_hourlyrate_day . "  (
      user_id,year_id,month_id,day,hour,weekday) VALUES (?,?,?,?,?,?)";

                    $res = $db->prepare($data);

                    {
                        $res->execute(
                            array(
                                $absence_id_month,
                                $absence_month,
                                $_POST['month'],
                                $day,
                                '8',
                                '5',
                                $weekday,
                            )
                        );
                    }

                }
            }
            if ($res->rowCount() == 1) {
                echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
            }

        }

    }


    if ($_POST['request'] == 'parent-day-add') {

        $FromDay = $_POST['FromDay'];
        $ToDay = $_POST['ToDay'];
        $getMonth = $_POST['get_month'];
        $getYear = $_POST['get_year'];

        $query = $db->query("SELECT [day] FROM  " . $portal_hourlyrate_day . "   where  month_id='$getMonth'");

        foreach ($query as $item) {

            if ($item['day'] >= $FromDay && $item['day'] <= $ToDay) {

                $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
      status = ?
      where day=?
      and month_id=?
     and year_id=?";


                $res = $db->prepare($data);
                $res->execute(
                    array(

                        $_POST['hour'],
                        $_POST['status'],
                        $item['day'],
                        $getMonth,
                        $getYear
                    )
                );

            }
        }
        echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';

    }


    if ($_POST['request'] == 'daysel') {
        $YearSel = $_POST['YearSel'];
        $MonthSel = $_POST['MonthSel'];
    }

    if ($_POST['request'] == 'user-day-check') {

        $FromDay = $_POST['FromDay'];
        $ToDay = $_POST['ToDay'];
        $getMonth = $_POST['get_month'];
        $getYear = $_POST['get_year'];
        $religious_holiday = $db->query("SELECT count(KindofDay) as Kind_ofDay FROM  " . $portal_hourlyrate_day . "  WHERE KindofDay='CHOLIDAY'
    AND year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND day between '" . $_POST['FromDay'] . "'  and '" . $_POST['ToDay'] . "' ");
        foreach ($religious_holiday as $valuere) {
            $reho = $valuere['Kind_ofDay'];
            if ($reho > 0) {
                echo '<a href="' . $url . '/modules/admin_hourly_rate/ajax.php"data-widget="edit" data-id="user_day:month-' . $_POST['get_month'] . '" data-text="' . __('Ima praznik') . '" class="text-danger pull-right"><i class="ion-ios-checkmark-outline"></i></a>';
            }
        }
    }


    if ($_POST['request'] == 'user-day-edit') {

        $FromDay = $_POST['FromDay'];
        $ToDay = $_POST['ToDay'];

        $status2 = $_POST['status'];
        if (($_POST['FromDay'] > $_POST['ToDay']))
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Datum Od mora biti manji od datuma Do') . '</div>'; else {
            $getMonth = $_POST['get_month'];
            $getYear = $_POST['get_year'];

            //denis
            $get_count = $db->query("SELECT count(KindOfDay) as countHol FROM  " . $portal_hourlyrate_day . "  WHERE KindOfDay='CHOLIDAY' and month_id=" . $getMonth . " and year_id=" . $getYear . " and Day>=" . $FromDay . " and Day<=" . $ToDay . "");
            $countHoliday = $get_count->fetch();
            $countHol = $countHoliday ['countHol'];


            $emp = $db->query("SELECT employee_no FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  ");
            foreach ($emp as $valueemp) {
                $empid = $valueemp['employee_no'];
            }
            $go = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");

            $holiday_go = $db->query("SELECT COUNT(KindofDay) as Kind_ofDay FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND KindofDay='CHOLIDAY'  AND day between '" . $FromDay . "' and '" . $ToDay . "'");

            foreach ($holiday_go as $holidaygo) {
                $totalhogo = $holidaygo['Kind_ofDay'];
            }

            $holiday_go2 = $db->query("SELECT COUNT(KindofDay) as Kind_ofDay FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND KindofDay='HOLIDAY'  AND day between '" . $FromDay . "' and '" . $ToDay . "'");

            $religious_holiday = $db->query("SELECT COUNT(KindofDay) as Kind_ofDay FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND KindofDay='CHOLIDAY'  AND day between '" . $FromDay . "' and '" . $ToDay . "'");
            foreach ($religious_holiday as $religiousholiday) {
                $totalreho = $religiousholiday['Kind_ofDay'];
            }

            foreach ($holiday_go2 as $holidaygo) {
                $totalhogo2 = $holidaygo['Kind_ofDay'];
            }


            $currgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND weekday<>'6' AND weekday<>'7' AND (status='18' or status='19' or status='20') AND (date_NAV is null)");
            $askedgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND weekday<>'6' AND weekday<>'7' AND day between '" . $FromDay . "' and '" . $ToDay . "'");
            $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19') AND (date_NAV is null)");
            $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19' or status='18') AND (date_NAV is null)");
            $blooddonor = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
            $death = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='34') AND (date_NAV is null)");
            $pcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $plo = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $currplo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND ((status='27') or (status='28') or (status='29') or (status='30') or (status='31')   
  or (status='33')) AND (date_NAV is null)");
            $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='21') AND (date_NAV is null)");
            $upcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='22') AND (date_NAV is null)");
            $checkva = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' and month_id='" . $getMonth . "'and employee_no='" . $empid . "'
   and status='19'  ");
            $curruP_1 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='27') AND (date_NAV is null)");
            $curruP_2 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='28') AND (date_NAV is null)");
            $curruP_3 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='29') AND (date_NAV is null)");
            $curruP_4 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='30') AND (date_NAV is null)");
            $curruP_5 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='31') AND (date_NAV is null)");
            $curruP_6 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
            $curruP_7 = $db->query("SELECT count(day) as count_day FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  and weekday<>'6' AND weekday<>'7' and [day] between '" . $FromDay . "' and '" . $ToDay . "'");
            $P_1 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $P_2 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $P_3 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $P_4 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $P_5 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $P_6 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $P_1a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='27'");
            $P_2a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='28'");
            $P_3a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='29'");
            $P_4a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='30'");
            $P_5a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='31'");
            $P_6a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='32'");
            foreach ($go as $valuego) {
                $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
                $brdanaPG = $valuego['Br_danaPG'];
                $ostaloPG = $valuego['Br_dana_ostaloPG'];
                $iskoristeno = $valuego['Br_dana_iskoristeno'];
                $ostalo = $valuego['Br_dana_ostalo'];
                $brdana = $valuego['Br_dana'];
                $totalkrv = $valuego['Blood_days'];
                $totaldeath = $valuego['S_1_used'];
                $iskoristenokrv = $valuego['P_6_used'];
                $propaloGO = $valuego['G_2 not valid'];
            }

            foreach ($blooddonor as $blood_donor) {
                $iskorenokrv = $blood_donor['sum_hour'];
                $krvukupno = ($iskorenokrv / 8) + $iskoristenokrv;
                $totalkrvloost = $totalkrv - $krvukupno;
            }


            foreach ($askedgo as $valueasked) {
                $askeddays = $valueasked['sum_hour'];
                $totalasked = $askeddays / 8;
            }
            foreach ($currgo as $valuecurrgo) {
                $iskoristenocurr = $valuecurrgo['sum_hour'];;
                $iskoristenototal = ($iskoristenocurr / 8) + $iskoristeno;
                $totalgoost = $brdana - $iskoristenototal;
            }
            foreach ($currgoPG as $valuecurrgoPG) {
                $iskoristenocurrPG = $valuecurrgoPG['sum_hour'];;
                $iskoristenototalPG = ($iskoristenocurrPG / 8) + $iskoristenoPG;
                $totalgoostPG = $brdanaPG - $iskoristenototalPG;
                $ukupnogoiskoristeno = $iskoristenototalPG + $iskoristenototal;
                $ukupnogoost = $totalgoost + $totalgoostPG;
            }
            foreach ($pcm as $valuepcm) {
                $totalpcm = $valuepcm['Candelmas_paid_total'];
                $iskoristenopcm = $valuepcm['Candelmas_paid_used'];
                $brdanapcm = $valuepcm['Candelmas_paid'];
            }
            foreach ($upcm as $valueupcm) {
                $totalupcm = $valueupcm['Candelmas_unpaid_total'];
                $iskoristenoupcm = $valueupcm['Candelmas_unpaid_used'];
                $brdanaupcm = $valueupcm['Candelmas_unpaid'];
            }
            foreach ($P_1 as $valueP_1) {
                $iskoristenoP_1 = $valueP_1['P_1_used'];
            }
            foreach ($P_1a as $valueP_1a) {
                $totalP_1 = $valueP_1a['allowed_days'];
            }
            foreach ($P_2 as $valueP_2) {
                $iskoristenoP_2 = $valueP_2['P_2_used'];
            }
            foreach ($P_2a as $valueP_2a) {
                $totalP_2 = $valueP_2a['allowed_days'];
            }
            foreach ($P_3 as $valueP_3) {
                $iskoristenoP_3 = $valueP_3['P_3_used'];
            }
            foreach ($P_3a as $valueP_3a) {
                $totalP_3 = $valueP_3a['allowed_days'];
            }
            foreach ($P_4 as $valueP_4) {
                $iskoristenoP_4 = $valueP_4['P_4_used'];
            }
            foreach ($P_4a as $valueP_4a) {
                $totalP_4 = $valueP_4a['allowed_days'];
            }
            foreach ($P_5 as $valueP_5) {
                $iskoristenoP_5 = $valueP_5['P_5_used'];
            }
            foreach ($P_5a as $valueP_5a) {
                $totalP_5 = $valueP_5a['allowed_days'];
            }
            foreach ($P_6 as $valueP_6) {
                $iskoristenoP_6 = $valueP_6['P_6_used'];
            }
            foreach ($P_6a as $valueP_6a) {
                $totalP_6 = $valueP_6a['allowed_days'];
            }
            foreach ($currpcm as $valuecurrpcm) {
                $iskoristenocurrpcm = $valuecurrpcm['sum_hour'];;
                $iskoristenototalpcm = ($iskoristenocurrpcm / 8) + $iskoristenopcm;
                $totalpcmost = $brdanapcm - $iskoristenototalpcm;
            }
            foreach ($currupcm as $valuecurrupcm) {
                $iskoristenocurrupcm = $valuecurrupcm['sum_hour'];;
                $iskoristenototalupcm = ($iskoristenocurrupcm / 8) + $iskoristenoupcm;
                $totalupcmost = $brdanaupcm - $iskoristenototalupcm;
            }
            foreach ($checkva as $checkvalueva) {
                $sum = $checkvalueva['sum_hour'];
            }
            foreach ($curruP_1 as $valuecurrP_1) {
                $iskoristenocurrP_1 = $valuecurrP_1['sum_hour'];;
                $iskoristenototalP_1 = ($iskoristenocurrP_1 / 8) + $iskoristenoP_1;
                $totalP_1ost = $totalP_1 - $iskoristenototalP_1;
            }
            foreach ($curruP_2 as $valuecurrP_2) {
                $iskoristenocurrP_2 = $valuecurrP_2['sum_hour'];;
                $iskoristenototalP_2 = ($iskoristenocurrP_2 / 8) + $iskoristenoP_2;
                $totalP_2ost = $totalP_2 - $iskoristenototalP_2;
            }
            foreach ($curruP_3 as $valuecurrP_3) {
                $iskoristenocurrP_3 = $valuecurrP_2['sum_hour'];;
                $iskoristenototalP_3 = ($iskoristenocurrP_3 / 8) + $iskoristenoP_3;
                $totalP_3ost = $totalP_3 - $iskoristenototalP_3;
            }
            foreach ($curruP_4 as $valuecurrP_4) {
                $iskoristenocurrP_4 = $valuecurrP_4['sum_hour'];;
                $iskoristenototalP_4 = ($iskoristenocurrP_4 / 8) + $iskoristenoP_4;
                $totalP_4ost = $totalP_4 - $iskoristenototalP_4;
            }
            foreach ($curruP_5 as $valuecurrP_5) {
                $iskoristenocurrP_5 = $valuecurrP_5['sum_hour'];;
                $iskoristenototalP_5 = ($iskoristenocurrP_5 / 8) + $iskoristenoP_5;
                $totalP_5ost = $totalP_5 - $iskoristenototalP_5;
            }
            foreach ($curruP_6 as $valuecurrP_6) {
                $iskoristenocurrP_6 = $valuecurrP_6['sum_hour'];;
                $iskoristenototalP_6 = ($iskoristenocurrP_6 / 8) + $iskoristenoP_6;
                $totalP_6ost = $totalP_6 - $iskoristenototalP_6;
            }

            foreach ($curruP_7 as $valuecurrP_7) {
                $iskoristenocurrP_7 = $valuecurrP_7['count_day'];
            }
            foreach ($plo as $valueplo) {
                $iskoristenoplo = $valueplo['P_1_used'] + $valueplo['P_2_used'] + $valueplo['P_3_used'] + $valueplo['P_4_used'] + $valueplo['P_5_used'] + $valueplo['P_7_used'];
                $totalplo = $valueplo['Br_dana_PLO'];
            }

            foreach ($currplo as $valuecurrplo) {
                $iskoristenocurrplo = $valuecurrplo['sum_hour'];
                $iskoristenototalplo = ($iskoristenocurrplo / 8) + $iskoristenoplo;
                $totalploost = $totalplo - $iskoristenototalplo;
            }

            if ($countHol > 0 and $_POST['try'] == '1') {
                echo _message('holiday_change');
            } else {
                if ($totalasked > $totalgoost and $_POST['status'] == '18') {
                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO tekuće godine !') . '</div>';
                } else {
                    if ($totalasked > $totalgoostPG and $_POST['status'] == '19') {
                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO prethodne godine !') . '</div>';
                    } else {
                        if ((($totalasked > 5) and ($_POST['status'] == '19')) or ((($sum / 8) + 1 > 5) and $_POST['status'] == '19')) {
                            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 5 dana GO iz prošle godine!') . '</div>';
                        } else {
                            if ((($totalasked > 5) and ($_POST['status'] == '19')) or ((($sum / 8) + 1 > 5) and $_POST['status'] == '19') or ($propaloGO == 1)) {
                                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Nemate pravo na godišnji iz predhodne godine!') . '</div>';
                            } else {
                                if (($totalasked > $totalpcmost) and ($_POST['status'] == '21')) {
                                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana plaćenog vjerskog odsustva!') . '</div>';
                                } else {
                                    if (($totalasked > $totalupcmost) and ($_POST['status'] == '22')) {
                                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana neplaćenog vjerskog odsustva!') . '</div>';
                                    } else {
                                        if ((($totalasked > $totalP_1ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '27')) {
                                            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                        } else {
                                            if ((($totalasked > $totalP_2ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '28')) {
                                                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                            } else {
                                                if ((($totalasked > $totalP_3ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '29')) {
                                                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                                } else {
                                                    if ((($totalasked > $totalP_4ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '30')) {
                                                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                                    } else {
                                                        if ((($totalasked > $totalP_5ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '31')) {
                                                            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                                        } else {
                                                            if (($totalasked > 5) and ($_POST['status'] == '30' or $_POST['status'] == '34')) {
                                                                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti interval veći od 5 dana za smrt člana uže porodice!') . '</div>';
                                                            } else {
                                                                if ((($totalasked > 1) or ($totalasked > $totalkrvloost)) and ($_POST['status'] == '32')) {
                                                                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti interval veći od 1 dan za darivanje krvi!') . '</div>';
                                                                } else {

                                                                    $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
      status = ?
      where day BETWEEN ? and ?
    and month_id=?
    and year_id=?
    ";

                                                                    $res = $db->prepare($data);
                                                                    $res->execute(
                                                                        array(
                                                                            $_POST['hour'],
                                                                            $_POST['status'],
                                                                            $FromDay,
                                                                            $ToDay,
                                                                            $getMonth,
                                                                            $getYear


                                                                        )
                                                                    );
                                                                    if ($res->rowCount() > 0) {
                                                                        echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';
                                                                    } else {
                                                                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
                                                                    }
                                                                }

                                                            }
                                                        }
                                                    }
                                                }

                                            }
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    if ($_POST['request'] == 'day-edit') {

        $Day = $_POST['day'];
        $this_id = $_POST['request_id'];
        $status2 = $_POST['status'];

        $try = $_POST['try'];
        $get_old_status = $db->query("SELECT status, KindOfDay FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "'  ");
        $old_status = $get_old_status->fetch();


        $emp = $db->query("SELECT employee_no,year_id,month_id FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "'  ");
        $check = $db->query("SELECT year_id,month_id,employee_no FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "' ");
        foreach ($check as $checkvalue) {
            $filter_year = $checkvalue['year_id'];
            $filter_month = $checkvalue['month_id'];
            $filter_emp = $checkvalue['employee_no'];
        }
        $checkva = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $filter_year . "' and month_id='" . $filter_month . "'and employee_no='" . $filter_emp . "'
   and status='19'  ");

        foreach ($emp as $valueemp) {
            $empid = $valueemp['employee_no'];
            $getYear = $valueemp['year_id'];
            $getMonth = $valueemp['month_id'];
        }

        $askedgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND weekday<>'6' AND weekday<>'7' AND (status='32')");
        $go = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND weekday<>'6' AND weekday<>'7' AND (status='18' or status='19' or status='20') AND (date_NAV is null)");
        $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19') AND (date_NAV is null)");
        $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19' or status='18') AND (date_NAV is null)");
        $blooddonor = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
        $death = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='34') AND (date_NAV is null)");
        $pcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='21') AND (date_NAV is null)");
        $upcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='22') AND (date_NAV is null)");
        $checkva = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' and month_id='" . $getMonth . "'and employee_no='" . $empid . "'
   and status='19'  ");

        $currplo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND ((status='27') or (status='28') or (status='29') or (status='30') or (status='31')   
  or (status='33')) AND (date_NAV is null)");

        $plo = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");

        $P_1 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_2 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_3 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_4 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_5 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_6 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_1a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='27'");
        $P_2a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='28'");
        $P_3a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='29'");
        $P_4a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='30'");
        $P_5a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='31'");
        $P_6a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='32'");
        $curruP_1 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='27') AND (date_NAV is null)");
        $curruP_2 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='28') AND (date_NAV is null)");
        $curruP_3 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='29') AND (date_NAV is null)");
        $curruP_4 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='30') AND (date_NAV is null)");
        $curruP_5 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='31') AND (date_NAV is null)");
        $curruP_6 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='30') AND (date_NAV is null)");
        $curruP_7 = $db->query("SELECT count(day) as count_day FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
     and weekday<>'6' AND weekday<>'7' AND (status='30')");
        $curruP_8 = $db->query("SELECT count(day) as count_day FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
     and weekday<>'6' AND weekday<>'7' AND (status='19')");

        foreach ($askedgo as $valueasked) {
            $askeddays = $valueasked['sum_hour'];
            $totalasked = $askeddays / 8;
        }

        foreach ($go as $valuego) {
            $totalgo = $valuego['Ukupno'];
            $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
            $iskoristeno = $valuego['Br_dana_iskoristeno'];
            $brdana = $valuego['Br_dana'];
            $brdanaPG = $valuego['Br_danaPG'];
            $ostaloPG = $valuego['Br_dana_ostaloPG'];
            $iskoristenokrv = $valuego['P_6_used'];
            $totalkrv = $valuego['Blood_days'];
            $propaloGO = $valuego['G_2 not valid'];
        }


        foreach ($blooddonor as $blood_donor) {
            $iskorenokrv = $blood_donor['sum_hour'];
            $krvukupno = ($iskorenokrv / 8) + $iskoristenokrv;
            $totalkrvloost = $totalkrv - $krvukupno;
        }


        foreach ($currgo as $valuecurrgo) {
            $iskoristenocurr = $valuecurrgo['sum_hour'];;
            $iskoristenototal = ($iskoristenocurr / 8) + $iskoristeno;
            $totalgoost = $brdana - $iskoristenototal;
        }


        foreach ($currgoPG as $valuecurrgoPG) {
            $iskoristenocurrPG = $valuecurrgoPG['sum_hour'];;
            $iskoristenototalPG = ($iskoristenocurrPG / 8) + $iskoristenoPG;
            $totalgoostPG = $brdanaPG - $iskoristenototalPG;
            $ukupnogoiskoristeno = $iskoristenototalPG + $iskoristenototal;
            $ukupnogoost = $totalgoost + $totalgoostPG;
        }
        foreach ($pcm as $valuepcm) {
            $totalpcm = $valuepcm['Candelmas_paid_total'];
            $iskoristenopcm = $valuepcm['Candelmas_paid_used'];
            $brdanapcm = $valuepcm['Candelmas_paid'];
        }
        foreach ($upcm as $valueupcm) {
            $totalupcm = $valueupcm['Candelmas_unpaid_total'];
            $iskoristenoupcm = $valueupcm['Candelmas_unpaid_used'];
            $brdanaupcm = $valueupcm['Candelmas_unpaid'];
        }
        foreach ($currpcm as $valuecurrpcm) {
            $iskoristenocurrpcm = $valuecurrpcm['sum_hour'];;
            $iskoristenototalpcm = ($iskoristenocurrpcm / 8) + $iskoristenopcm;
            $totalpcmost = $brdanapcm - $iskoristenototalpcm;
        }
        foreach ($currupcm as $valuecurrupcm) {
            $iskoristenocurrupcm = $valuecurrupcm['sum_hour'];;
            $iskoristenototalupcm = ($iskoristenocurrupcm / 8) + $iskoristenoupcm;
            $totalupcmost = $brdanaupcm - $iskoristenototalupcm;
        }

        foreach ($curruP_7 as $valuecurrP_7) {
            $iskoristenocurrP_7 = $valuecurrP_7['count_day'];

        }
        foreach ($curruP_8 as $valuecurrP_8) {
            $iskoristenocurrP_8 = $valuecurrP_8['count_day'];

        }
        foreach ($P_1 as $valueP_1) {
            $iskoristenoP_1 = $valueP_1['P_1_used'];
        }
        foreach ($P_2 as $valueP_2) {
            $iskoristenoP_2 = $valueP_2['P_2_used'];
        }
        foreach ($P_1a as $valueP_1a) {
            $totalP_1 = $valueP_1a['allowed_days'];
        }
        foreach ($P_2a as $valueP_2a) {
            $totalP_2 = $valueP_2a['allowed_days'];
        }
        foreach ($P_3 as $valueP_3) {
            $iskoristenoP_3 = $valueP_3['P_3_used'];
        }
        foreach ($P_3a as $valueP_3a) {
            $totalP_3 = $valueP_3a['allowed_days'];
        }
        foreach ($P_4 as $valueP_4) {
            $iskoristenoP_4 = $valueP_4['P_4_used'];
        }
        foreach ($P_4a as $valueP_4a) {
            $totalP_4 = $valueP_4a['allowed_days'];
        }
        foreach ($P_5 as $valueP_5) {
            $iskoristenoP_5 = $valueP_5['P_5_used'];
        }
        foreach ($P_5a as $valueP_5a) {
            $totalP_5 = $valueP_5a['allowed_days'];
        }
        foreach ($P_6 as $valueP_6) {
            $iskoristenoP_6 = $valueP_6['P_6_used'];
        }
        foreach ($P_6a as $valueP_6a) {
            $totalP_6 = $valueP_6a['allowed_days'];
        }

        foreach ($curruP_1 as $valuecurrP_1) {
            $iskoristenocurrP_1 = $valuecurrP_1['sum_hour'];;
            $iskoristenototalP_1 = ($iskoristenocurrP_1 / 8) + $iskoristenoP_1;
            $totalP_1ost = $totalP_1 - $iskoristenototalP_1;
        }

        foreach ($curruP_2 as $valuecurrP_2) {
            $iskoristenocurrP_2 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_2 = ($iskoristenocurrP_2 / 8) + $iskoristenoP_2;
            $totalP_2ost = $totalP_2 - $iskoristenototalP_2;
        }
        foreach ($curruP_3 as $valuecurrP_3) {
            $iskoristenocurrP_3 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_3 = ($iskoristenocurrP_3 / 8) + $iskoristenoP_3;
            $totalP_3ost = $totalP_3 - $iskoristenototalP_3;
        }
        foreach ($curruP_4 as $valuecurrP_4) {
            $iskoristenocurrP_4 = $valuecurrP_4['sum_hour'];;
            $iskoristenototalP_4 = ($iskoristenocurrP_4 / 8) + $iskoristenoP_4;
            $totalP_4ost = $totalP_4 - $iskoristenototalP_4;
        }
        foreach ($curruP_5 as $valuecurrP_5) {
            $iskoristenocurrP_5 = $valuecurrP_5['sum_hour'];;
            $iskoristenototalP_5 = ($iskoristenocurrP_5 / 8) + $iskoristenoP_5;
            $totalP_5ost = $totalP_5 - $iskoristenototalP_5;
        }
        foreach ($curruP_6 as $valuecurrP_6) {
            $iskoristenocurrP_6 = $valuecurrP_6['sum_hour'];
            $iskoristenototalP_6 = ($iskoristenocurrP_6 / 8) + $iskoristenoP_6;
            $totalP_6ost = $totalP_6 - $iskoristenototalP_6;
        }
        foreach ($plo as $valueplo) {
            $iskoristenoplo = $valueplo['P_1_used'] + $valueplo['P_2_used'] + $valueplo['P_3_used'] + $valueplo['P_4_used'] + $valueplo['P_5_used'] + $valueplo['P_7_used'];
            $totalplo = $valueplo['Br_dana_PLO'];
        }
        foreach ($currplo as $valuecurrplo) {
            $iskoristenocurrplo = $valuecurrplo['sum_hour'];
            $iskoristenototalplo = ($iskoristenocurrplo / 8) + $iskoristenoplo;
            $totalploost = $totalplo - $iskoristenototalplo;
        }

        if (($old_status['status'] != '5' or $old_status['KindOfDay'] == 'CHOLIDAY') and $_POST['try'] == '1') {
            echo _message('unusual_change');
        } else {
            if (($totalgoost - 1 < 0) and $_POST['status'] == '18') {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO tekuće godine !') . '</div>';
            } else {
                if (($totalgoostPG - 1 < 0) and $_POST['status'] == '19') {
                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO prethodne godine !') . '</div>';
                } else {
                    if (($totalpcmost - 1 < 0) and ($_POST['status'] == '21')) {
                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana plaćenog vjerskog odsustva!') . '</div>';
                    } else {
                        if (($totalupcmost - 1 < 0) and ($_POST['status'] == '22')) {
                            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana neplaćenog vjerskog odsustva!') . '</div>';
                        } else {
                            if ((($totalP_1ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '27')) {
                                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                            } else {
                                if ((($totalP_2ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '28')) {
                                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                } else {
                                    if ((($totalP_3ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '29')) {
                                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                    } else {
                                        if ((($totalP_4ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '30')) {
                                            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 5 dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                        } else {
                                            if ((($totalP_5ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '31')) {
                                                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                            } else {
                                                if ((($totalkrvloost - 1 < 0) or ($totalP_6ost - 1 < 0)) and ($_POST['status'] == '32')) {
                                                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti interval veći od 1 dan za darivanje krvi!') . '</div>';
                                                } else {
                                                    if (($totalpcmost - 1 < 0) and ($_POST['status'] == '21')) {
                                                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana plaćenog odsustva !') . '</div>';
                                                    } else {
                                                        if (($totalupcmost - 1 < 0) and ($_POST['status'] == '22')) {
                                                            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana neplaćenog odsustva !') . '</div>';
                                                        } else {
                                                            foreach ($checkva as $checkvalueva) {
                                                                $sum = $checkvalueva['sum_hour'];
                                                            }


                                                            if ((($iskoristenocurrP_8 - 1 >= 4) and ($_POST['status'] == '19')) or ((($sum / 8) + 1 > 5) and $_POST['status'] == '19')) {
                                                                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 5 dana GO iz prošle godine!') . '</div>';
                                                            } else {

                                                                if ((($iskoristenocurrP_8 - 1 >= 4) and ($_POST['status'] == '19')) or ((($sum / 8) + 1 > 5) and $_POST['status'] == '19') or ($propaloGO == 1)) {
                                                                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Nemate pravo na godišnji iz predhodne godine!') . '</div>';
                                                                } else {
                                                                    $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      day = ?,
      hour = ?,
      status = ?
      WHERE id = ?
     ";

                                                                    $res = $db->prepare($data);
                                                                    $res->execute(
                                                                        array(
                                                                            $_POST['day'],
                                                                            $_POST['hour'],
                                                                            $_POST['status'],
                                                                            $this_id
                                                                        )
                                                                    );
                                                                    if ($res->rowCount() == 1) {
                                                                        echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';
                                                                    } else {
                                                                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    if ($_POST['request'] == 'remove-requests_remove') {
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM requests WHERE request_id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if ($delete) {
            echo 1;
        }
    }


    if ($_POST['request'] == 'remove-year_remove') {

        $this_id = explode('-', $_POST['request_id']);
        $this_id = $this_id[1];

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $query = $db->query("SELECT [user_id] FROM  " . $portal_users . " ");
        $get2 = $db->query("SELECT COUNT(*) FROM  " . $portal_users . " ");
        $query2 = $db->query("SELECT [year] FROM  " . $portal_hourlyrate_year . "  where id=$this_id");

        $total = $get2->rowCount();


        foreach ($query as $item) {

            //echo $absence_id;
            $absence_year_id = $item['user_id'];


            $data = "DELETE FROM  " . $portal_hourlyrate_year . "  WHERE id = ?";
            $delete = $db->prepare($data);
            $delete->execute(array($absence_year_id));
            if ($delete) {
                echo 1;


            }
        }
    }


    if ($_POST['request'] == 'month-remove-month') {
        $this_id = $_POST['request_id'];

        $yearcurr = $db->query("SELECT [month] FROM  " . $portal_hourlyrate_month . "  WHERE id= $this_id");

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $query_month = $db->query("SELECT [user_id] FROM  " . $portal_users . " ");
        $get_month = $db->query("SELECT COUNT(*) FROM  " . $portal_users . " ");
        $yearcurr = $db->query("SELECT [year] FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $_POST['year'] . "'");
        $total = $get_month->rowCount();
        foreach ($yearcurr as $value2) {
            $absence_year = $value2['year'];
        }

        foreach ($query_month as $item) {

            $month = $db->query("SELECT [id] FROM  " . $portal_hourlyrate_year . "  where [user_id]='" . $item['user_id'] . "' and year='" . $absence_year . "'");
            $absence_id_month = $item['user_id'];
            foreach ($month as $value) {
                $absence_month = $value['id'];
            }

            $_user = _user(_decrypt($_SESSION['SESSION_USER']));

            $data = "DELETE FROM  " . $portal_hourlyrate_month . "  where 
   (id=?
      and user_id=?
    and year_id=?
    and ,month =?)";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['month'],
                    $absence_id_month,
                    $absence_month,
                    $_POST['month']
                )
            );


            $query_calendar = $db->query("SELECT [day],[weekday] FROM  " . $portal_calendar . "  where [month]='" . $_POST['month'] . "'
   and  [year]='" . $absence_year . "'");
            foreach ($query_calendar as $cal) {
                $day = $cal ['day'];
                $weekday = $cal ['weekday'];
                $data = "DELETE FROM  " . $portal_hourlyrate_day . "  (where
      user_id=?
    and year_id=?
    and month_id=?
    and day=?
    and hour=? 
    and status=?)";

                $res = $db->prepare($data);

                {
                    $res->execute(
                        array(
                            $absence_id_month,
                            $absence_month,
                            $_POST['month'],
                            $day,
                            '8',
                            '5',

                        )
                    );
                }

            }
        }
        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno obrisane!') . '</div>';
        }

    }

    if ($_POST['request'] == 'remove-day_remove') {
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM hourlyrate_day WHERE id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if ($delete) {
            echo 1;
        }
    }


    if ($_POST['request'] == 'remove-requests_archive') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_requests . "  SET
        is_archive = ?
        WHERE request_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'remove-tasks_archive') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_tasks . "  SET
        is_archive = ?
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'accept-tasks') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_tasks . "  SET
        is_accepted = ?
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'completed-tasks') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_tasks . "  SET
        is_finished = ?,
        date_finished = ?
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                date('Y-m-d'),
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'task-comment') {

        $data3 = "INSERT INTO  " . $portal_comments . "  (
      type,user_id,comment,date_created,comment_on) VALUES (?,?,?,?,?)";

        $res3 = $db->prepare($data3);
        $res3->execute(
            array(
                'task',
                $_POST['user_id'],
                $_POST['comment'],
                date('Y-m-d H:i:s'),
                $_POST['comment_on']
            )
        );
        if ($res3->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
        }

    }


    if ($_POST['request'] == 'proc-tasks') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks_item] SET
        status = ?,
        date_completed = ?
        WHERE taskitem_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['status'],
                date('y-m-d H:i:s', strtotime("now")),
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'count-tasks') {

        $this_id = $_POST['request_id'];
        $total_0 = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[tasks_item] WHERE task_id='$this_id'")->rowCount();
        $total_1 = $db->query("SELECT Count(*) FROM [c0_intranet2_apoteke].[dbo].[tasks_item] WHERE task_id='$this_id' AND status='1'")->rowCount();

        if ($total_1 == $total_0) {
            echo 'yes';
        } else {
            echo 'no';
        }

    }


    if ($_POST['request'] == 'comments') {

        $user_id = $_POST['user'];
        $parent_id = $_POST['parent'];

        $comments = $db->query("SELECT * FROM  " . $portal_comments . "  WHERE comment_on='" . $_POST['request_id'] . "' AND type='task'");
        $comments_no = $db->query("SELECT * FROM  " . $portal_comments . "  WHERE comment_on='" . $_POST['request_id'] . "' AND type='task'");

        if ($comments_no->rowCount() < 0) {

            $user = _user($user_id);
            $parent = _user($parent_id);

            foreach ($comments as $item) {
                echo '<div class="comment">';
                if ($item['user_id'] == $user_id) {
                    echo '<div class="row">';
                    echo '<div class="col-xs-9"><div class="text-u">';
                    echo $item['comment'];
                    echo '</div><small class="text-muted">' . date('d.m.Y H:i', strtotime($item['date_created'])) . '</small></div>';
                    echo '<div class="col-xs-3 text-center">';
                    if ($user['image'] != 'none') {
                        echo '<img src="' . $_timthumb . $_uploadUrl . '/' . $user['image'] . '&w=200&h=200" class="img-circle" style="width:70%;">';
                    } else {
                        echo '<img src="' . $_themeUrl . '/images/noimage-user.png" class="img-circle" style="width:70%;">';
                    }
                    echo '<br/><small>' . $user['fname'] . ' ' . $user['lname'] . '</small>';
                    echo '</div>';
                    echo '</div>';
                } else if ($item['user_id'] == $parent_id) {
                    echo '<div class="row">';
                    echo '<div class="col-xs-3 text-center">';
                    if ($parent['image'] != 'none') {
                        echo '<img src="' . $_timthumb . $_uploadUrl . '/' . $parent['image'] . '&w=200&h=200" class="img-circle" style="width:70%;">';
                    } else {
                        echo '<img src="' . $_themeUrl . '/images/noimage-user.png" class="img-circle" style="width:70%;">';
                    }
                    echo '<br/><small>' . $parent['fname'] . ' ' . $parent['lname'] . '</small>';
                    echo '</div>';
                    echo '<div class="col-xs-9"><div class="text-p">';
                    echo $item['comment'];
                    echo '</div><small class="pull-right text-muted">' . date('d.m.Y H:i', strtotime($item['date_created'])) . '</small></div>';
                    echo '</div>';
                }
                echo '</div>';
            }
        }

    }


    if ($_POST['request'] == 'profile-edit') {

        if ($_POST['f4'] != '') {
            $pass = md5($_POST['f4']);
        } else {
            $pass = $_POST['oldpass'];
        }

        $check = $db->query("SELECT * FROM  " . $portal_users . "  WHERE username='" . $_POST['username'] . "'");
        if ($check->rowCount() > 0) {
            if ($_POST['username'] == $_POST['oldusername']) {
                $username = $_POST['username'];
            } else {
                $username = false;
            }
        } else {
            $username = $_POST['username'];
        }

        if (isset($_FILES['media_file'])) {
            if (is_uploaded_file($_FILES['media_file']['tmp_name'])) {
                $p_photo = preg_replace('/[^\w\._]+/', '_', $_FILES['media_file']['name']);
                $p_photo = _checkFile($_uploadRoot . '/', $p_photo);
                $file = $_uploadRoot . '/' . $p_photo;
                if (copy($_FILES['media_file']['tmp_name'], $file)) {
                    unlink($_uploadRoot . '/' . $_POST['oldimage']);
                }
            } else {
                $p_photo = $_POST['oldimage'];
            }
        } else {
            $p_photo = $_POST['oldimage'];
        }

        if ($username != false) {

            $this_id = $_POST['request_id'];
            $data = "UPDATE  " . $portal_users . "  SET
        username = ?,
        password = ?,
        email = ?,
        image = ?,
        fname = ?,
        lname = ?,
        address = ?,
        zip = ?,
        city = ?,
        country = ?,
        phone = ?,
        lang = ?
        WHERE user_id = ?";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['username'],
                    $pass,
                    $_POST['email'],
                    $p_photo,
                    $_POST['fname'],
                    $_POST['lname'],
                    $_POST['address'],
                    $_POST['zip'],
                    $_POST['city'],
                    $_POST['country'],
                    $_POST['phone'],
                    $_POST['lang'],
                    $this_id
                )
            );
            if ($res->rowCount() == 1) {
                echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-success text-center\">' . __('Informacije su uspješno spašene!') . '</div>"}';
            }

        } else {

            echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">' . __('Korisničko ime je zauzeto. Molimo pokušajte sa nekim drugim.') . '</div>"}';

        }

    }

    if ($_POST['request'] == 'task-review') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_tasks . "  SET
        is_user_reviewed = ?,
        user_rating = ?
        
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $_POST['rating'],
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }

    if ($_POST['request'] == 'get-organization') {
        echo json_encode(_optionName('', '', '', $_POST['org_jed'], '', ''));
    }

    if ($_POST['request'] == 'get-regions') {
        $B_1 = $_POST['B_1'];
        echo _optionRegion($B_1, '');

    }

    if ($_POST['request'] == 'get-streams') {
        $region = $_POST['region'];

        if ($region == '') {
            $opt = '<option value="">Odaberi...</option>';
            $opt .= '<option value="">Nema unesenih opcija</option>';
            echo html_entity_decode($opt);

        } else {
            echo _optionStream($region, '', $data);
        }

    }

    if ($_POST['request'] == 'get-teams') {
        $stream = $_POST['stream'];
        if ($stream == '') {
            $opt = '<option value="">Odaberi...</option>';
            $opt .= '<option value="">Nema unesenih opcija</option>';
            echo html_entity_decode($opt);

        } else {
            echo _optionTeam($stream, '');
        }

    }

    if ($_POST['request'] == 'get-users') {
        if (isset($_POST['team']))
            $team = $_POST['team'];
        else
            $team = '';
        if (isset($_POST['stream']))
            $stream = $_POST['stream'];
        else
            $stream = '';
        if (isset($_POST['region']))
            $region = $_POST['region'];
        else
            $region = '';
        if (isset($_POST['b1']))
            $b1 = $_POST['b1'];
        else
            $b1 = '';

        $idm = $_POST['month'];
        $idy = $_POST['year'];
        $month['id'] = $idm;
        $year['id'] = $idy;
        $filtertdate = $year['id'] . "-" . $month['id'] . "-1 00:00:00.000";
        //var_dump($_POST['username']);
        if ($_POST['username'] != '') {
            $opt = "<option>" . $_POST['username'] . "</option>";
            //var_dump($opt);
            echo html_entity_decode($opt) . _optionName($team, $stream, $region, $b1, '', $filtertdate);
        } else {

            echo _optionName($team, $stream, $region, $b1, '', $filtertdate);
        }


    }

    if ($_POST['request'] == 'get-tariff') {
        $region = $_POST['region'];
        echo _optionCountryWage($region);

    }


    if ($_POST['request'] == 'task-review-item') {

        parse_str($_POST["data"], $_POST);

        $this_id = $_POST['request_id'];
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks_item] SET
        is_rated = ?,
        user_rating = ?
        WHERE taskitem_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $_POST['user_rating'],
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo $this_id;
        }

    }


}


?>
