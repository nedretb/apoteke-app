<?php
global $db, $portal_users;

use Carbon\Carbon;

//require 'count_go3.php';
if ($_GET['edit'] != 0) {
    try {
        $check_active = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=1 and [Employee No_]=" . $_GET['edit']);
        $q = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=1 and [Employee No_]=" . $_GET['edit'])->fetch();
        $work_experience = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[work_booklet] where Employer <> 'MKT' and Active=0 and [Employee No_]='" . $_GET['edit'] . "'");
        $experience_days = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[total_experience] where [Employee No_]='" . $_GET['edit'] . "'")->fetch();
        $coefficients = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[coefficient_history] where active=0 and [employee_no]='" . $_GET['edit'] . "'");
        $work_experience_curr = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[work_booklet] where Employer='MKT' and Active=0 and [Employee No_]='" . $_GET['edit'] . "'");

        if ($check_active->rowCount() < 0) {
            $active = 1;
        } else {
            $active = 0;
            $inactive_name = $db->query("select [First Name], [Last Name] from [c0_intranet2_apoteke].[dbo].[work_booklet] where [Employee No_]=" . $_GET['edit'])->fetch();
            //var_dump($inactive_name);
        }


        $staz_sad = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=1 and [Employee No_]='" . $_GET['edit'] . "'");
        //var_dump($staz_sad->fetch());
        $staz_prije = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=0 and [Employee No_]='" . $_GET['edit'] . "'");
        //var_dump(count($staz_prije));
        $total_days_prev = 0;

        $years_b = 0;
        $months_b = 0;
        $days_b = 0;

        foreach ($staz_prije as $s) {
            $days_c = $s['previous_exp_d'];
            $months_c = $s['previous_exp_m'];
            $years_c = $s['previous_exp_y'];

            $days_b += $days_c;
            if ($days_b >= 30) {
                $days_b = $days_b - 30;
                $months_b += 1;
            }
            $months_b += $months_c;
            if ($months_b >= 12) {
                $months_b -= 12;
                $years_b += 1;
            }
            $years_b += $years_c;
        }


        $total_years = $q['previous_exp_y'] + $years_b;
        $total_months = $q['previous_exp_m'] + $months_b;
        $total_days = $q['previous_exp_d'] + $days_b;
//        var_dump($total_years, $total_months, $total_days);

        $total_days_m = floor($total_days / 30);
        $total_days = $total_days - $total_days_m * 30;
        $total_months = $total_months + $total_days_m;

        $total_months_y = floor($total_months / 12);
        $total_months = $total_months - $total_months_y * 12;
        $total_years = $total_years + $total_months_y;

        //var_dump($total_years, $total_months, $total_days);
        //var_dump($total_days_prev);
        $years = floor($total_days_prev / 365);
        $months = floor(($total_days_prev - $years * 365) / 30);
        $days = $total_days_prev - $years * 365 - 30 * $months;

        if ($total_days_prev == 0) {
            $days = 0;
        }
//        $total_days = $total_days_prev + $q['previous_exp_y'] * 365 + $q['previous_exp_m'] * 30 + $q['previous_exp_d'];
//        $years_total = floor($total_days / 365);
//        $months_total = floor(($total_days - $years_total * 365) / 30);
//        $days_total = $total_days - $years_total * 365 - 30 * $months_total;
        $current_experience = Carbon::parse($q['Starting Date'])->diff($q['Ending Date']);

    } catch (Exception $e) {
        var_dump($e);
        die();
    }

    //Prethodni staz u MKT
    $prethodni_mktq = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Employer='MKT' and Active=0 and [Employee No_]='" . $_GET['edit'] . "'");

    $years_prethodni_mkt_ispis = 0;
    $months_prethodni_mkt_ispis = 0;
    $days_prethodni_mkt_ispis = 0;
    foreach ($prethodni_mktq as $s) {
        $days_prethodni_mkt = $s['previous_exp_d'];
        $months_prethodni_mkt = $s['previous_exp_m'];
        $years_prethodni_mkt = $s['previous_exp_y'];

        $days_prethodni_mkt_ispis += $days_prethodni_mkt;
        if ($days_prethodni_mkt_ispis >= 30) {
            $days_prethodni_mkt_ispis = $days_prethodni_mkt_ispis - 30;
            $months_prethodni_mkt_ispis += 1;
        }
        $months_prethodni_mkt_ispis += $months_prethodni_mkt;
        if ($months_prethodni_mkt_ispis >= 12) {
            $months_prethodni_mkt_ispis -= 12;
            $years_prethodni_mkt_ispis += 1;
        }
        $years_prethodni_mkt_ispis += $years_prethodni_mkt;
    }

    //Prethodni staz da nije MKT
    $prethodniq = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Employer<>'MKT' and Active=0 and [Employee No_]='" . $_GET['edit'] . "'");

    $years_prethodni_ispis = 0;
    $months_prethodni_ispis = 0;
    $days_prethodni_ispis = 0;
    foreach ($prethodniq as $s) {
        $days_prethodni = $s['previous_exp_d'];
        $months_prethodni = $s['previous_exp_m'];
        $years_prethodni = $s['previous_exp_y'];

        $days_prethodni_ispis += $days_prethodni;
        if ($days_prethodni_ispis >= 30) {
            $days_prethodni_ispis = $days_prethodni_ispis - 30;
            $months_prethodni_ispis += 1;
        }
        $months_prethodni_ispis += $months_prethodni;
        if ($months_prethodni_ispis >= 12) {
            $months_prethodni_ispis -= 12;
            $years_prethodni_ispis += 1;
        }
        $years_prethodni_ispis += $years_prethodni;
    }

    //Prethodni staz da nije MKT
    $prethodni_total_mktq = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Employer='MKT' and [Employee No_]='" . $_GET['edit'] . "'");

    $years_total_mkt_ispis = 0;
    $months_total_mkt_ispis = 0;
    $days_total_mkt_ispis = 0;
    foreach ($prethodni_total_mktq as $s) {
        $days_total_mkt = $s['previous_exp_d'];
        $months_total_mkt = $s['previous_exp_m'];
        $years_total_mkt = $s['previous_exp_y'];

        $days_total_mkt_ispis += $days_total_mkt;
        if ($days_total_mkt_ispis >= 30) {
            $days_total_mkt_ispis = $days_total_mkt_ispis - 30;
            $months_total_mkt_ispis += 1;
        }
        $months_total_mkt_ispis += $months_total_mkt;
        if ($months_total_mkt_ispis >= 12) {
            $months_total_mkt_ispis -= 12;
            $years_total_mkt_ispis += 1;
        }
        $years_total_mkt_ispis += $years_total_mkt;
    }
}else{ $active =1;}

$emp_nos = $db->query("select employee_no from [c0_intranet2_apoteke].[dbo].[users] where employee_no not in (SELECT [Employee No_] FROM [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=1)");
$emp_name = $db->query("select fname, lname from [c0_intranet2_apoteke].[dbo].[users] where employee_no not in (SELECT [Employee No_] FROM [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=1)");

$invalid_category = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_kategorije_invalidnosti_go] where Active=1")->fetchAll();
?>


<div id="tabs">
    <div class="simple-header">
        <div class="sh-left">
            <p class="sh-click" val="tabs-1">
                Trenutni poslodavac
            </p>
            <?php
            if ($_GET['edit'] != 0) {
                ?>

                <p class="sh-click" val="tabs-2">
                    Prethodni poslodavac
                </p>
                <p class="sh-click" val="tabs-3">
                    Radni staz
                </p>
                <?php
            }
            ?>
        </div>
        <div class="sh-right">
            <div class="inside-link">
                <a href="">
                    <p><i class="fas fa-chevron-left"></i> Nazad </p>
                </a>
            </div>
        </div>
    </div>

    <div class="hidden-tabs ht-active" id="tabs-1">
        <br>
        <div id="respons" class="alert alert-warning">
            Molimo vas unesite tačan raspon datuma
        </div>
        <div id="respons2" class="alert alert-warning">
            Korisnik sa unesenom šifrom postoji
        </div>
        <?php if ($active == 1){
        ?>
        <form method="post" action="<?php if ($_GET['edit'] != 0) {
            echo "?m=work_booklet&p=edit_current_exp";
        } else {
            echo "?m=work_booklet&p=save_current_exp";
        } ?>">
            <input hidden id="edit" value="<?php echo $_GET['edit']; ?>">
            <input hidden id="data_id" value="<?php if ($_GET['edit'] != 0) {
                echo $q['id'];
            } ?>">

            <h3>Trenutni poslodavac</h3>
            <div class="row">
                <div class="col-md-4">
                    <label>Ime i prezime državnog službenika</label>
                    <?php if ($_GET['edit'] != 0) { ?>
                        <input required name="employee_name" class="form-control" id="emp_name" type="text"
                               value="<?php if ($_GET['edit'] != 0) {
                                   echo $q['First Name'] . " " . $q['Last Name'];
                               } ?>">
                    <?php } else {
                        echo '<select id="emp_name" name="option_name"  class="form-control js-example-basic-multiple2">';
                        echo '<option selected disabled>Odaberi...</option>';
                        foreach ($emp_name as $n) {
                            echo '<option value="' . $n['fname'] . ' ' . $n['lname'] . '">' . $n['fname'] . ' ' . $n['lname'] . '</option>';
                        }
                        echo '</select>';
                    }
                    ?>
                </div>

                <div class="col-md-4">
                    <label>Šifra državnog službenika</label>
                    <?php if ($_GET['edit'] != 0) { ?>
                        <input required pattern="[0-9]{1,5}" name="employee_no" id="emp_no" class="form-control"
                               type="text" value="<?php if ($_GET['edit'] != 0) {
                            echo $_GET['edit'];
                        } ?>">
                    <?php } else {
                        echo '<select id="emp_no" class="form-control js-example-basic-multiple">';
                        echo '<option selected disabled>Odaberi...</option>';
                        foreach ($emp_nos as $e) {
                            //var_dump($e);
                            echo '<option value="' . $e['employee_no'] . '">' . $e['employee_no'] . '</option>';
                        }
                        echo '</select>';
                    } ?>
                </div>

                <div class="col-md-4">
                    <label>Poslodovac</label>
                    <input required name="employer" class="form-control" id="employer" type="text"
                           value="<?php if ($_GET['edit'] != 0) {
                               echo $q['Employer'];
                           } else {
                               echo 'MKT';
                           } ?>">
                </div>
            </div>

            <br>
            <div class="row">
                <div class="col-md-4">
                    <label>Datum početka rada</label>
                    <input autocomplete="off" required name="dateFrom" class="form-control" type="text" id="dateFrom"
                           value="<?php if ($_GET['edit'] != 0) {

                               echo date('d.m.Y', strtotime($q['Starting Date']));

                           } ?>">
                </div>

                <div class="col-md-4">
                    <label>Datum završetka rada</label>
                    <input required autocomplete="off" name="dateTo" class="form-control" type="text" id="dateTo"
                           value="<?php if ($_GET['edit'] != 0) {

                               echo date('d.m.Y', strtotime($q['Ending Date']));

                           } ?>">
                </div>

                <div class="col-md-4">
                    <label>Koeficijent</label>
                    <input required name="coefficient" id="coefficient" class="form-control" type="number" min="0"
                           max="1" step="0.01" value="<?php if ($_GET['edit'] != 0) {
                        echo number_format((float)$q['Coefficient'], 2, '.', '');
                    } ?>">
                </div>
            </div>

            <br>
            <div style="display: none;" class="row">
                <div class="col-md-4">
                    <label>Dijete sa posebnim potrebama</label>
                    <select id="dc" name="dc" class="form-control">
                        <?php
                        if ($_GET['edit'] != 0) {
                            if ($q['child_disabled'] == 'DA') {
                                echo '<option>DA</option>';
                                echo '<option>NE</option>';
                            } else {
                                echo '<option>NE</option>';
                                echo '<option>DA</option>';
                            }
                        } else {
                            echo ' <option>DA</option> <option selected>NE</option>';
                        }
                        ?>
                    </select>
                </div>

                <div id="invalid" class="col-md-4">
                    <label>Invalid</label>
                    <select id="invalid_select" name="invalid" class="form-control">
                        <?php
                        if ($_GET['edit'] != 0) {
                            if ($q['invalid'] == 'DA') {
                                echo '<option>DA</option>';
                                echo '<option>NE</option>';
                            } else {
                                echo '<option>NE</option>';
                                echo '<option>DA</option>';
                            }
                        } else {
                            echo ' <option>DA</option> <option selected>NE</option>';
                        }
                        ?>
                    </select>
                </div>

                <div id="invalidity_category" class="col-md-4">
                    <label>Kategorija</label>
                    <select id="invalid_category" name="invalidity_category" class="form-control">
                        <?php
                        if ($_GET['edit'] != 0) {
                            if ($q['invalid_category'] != 0) {
                                echo '<option>' . $q['invalid_category'] . '</option>';
                            } else {
                                echo '<option value="" disabled selected>Odaberi..</option>';
                            }

                            foreach ($invalid_category as $i) {
                                if ($q['invalid_category'] != $i['category']) {
                                    echo '<option>' . $i['category'] . '</option>';
                                }
                            }
                        } else {
                            echo '<option value="" disabled selected>Odaberi..</option>';
                            foreach ($invalid_category as $i) {
                                echo '<option>' . $i['category'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <br><br>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" name="save" class="btn btn-secondary">Ažurirajte informacije</button>
                    <?php
                    if ($_GET['edit'] != 0) {
                        if ($active == 1) {
                            ?>
                            <button type="button" name="archive" class="btn btn-secondary" id="archive">Arhiviraj
                                karton
                            </button>
                            <?php
                        }
                    }
                    ?>

                </div>
            </div>
        </form>
        <?php }?>
        <?php
        if ($_GET['edit'] != 0) {
            ?>
            <br><br>
            <h3>Prethodno iskustvo</h3>
            <br>
            <?php if ($active == 1) {
                ?>
                <div class="row">
                    <div class="col-md-3">
                        <a id="add_new" type="button" style="background-color:#006595; color:white;"
                           class="btn btn-secondary"
                           href="?m=work_booklet&p=add_work_experience&new=<?php echo $_GET['edit']; ?>">Dodaj prethodno
                            iskustvo</a>
                    </div>
                </div>
            <?php } ?>
            <br>
            <?php
            if ($_GET['edit'] != 0 and $work_experience_curr->rowCount() < 0) {
                ?>
                <table class="table table-bordered my-table table-sm">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center">#</th>
                        <th scope="col">Ime firme</th>
                        <th scope="col">Datum početka rada</th>
                        <th scope="col">Datum završetka rada</th>
                        <th scope="col">Radni staž (G)</th>
                        <th scope="col">Radni staž (M)</th>
                        <th scope="col">Radni staž (D)</th>
                        <th scope="col">Koeficijent</th>
                        <th scope="col">Akcije</th>
                        <?php if ($active == 1) {
                            //echo '<th scope="col" class="text-center">Akcije</th>';
                        }
                        ?>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($work_experience_curr as $exp) {
                        echo "<tr>";

                        if ($exp['id']) {
                            echo "<td class='text-center'>" . $exp['id'] . "</td>";
                        }

                        if ($exp['Employer']) {
                            echo "<td>" . $exp['Employer'] . "</td>";
                        }

                        if ($exp['Starting Date']) {
                            echo "<td>" . date("d.m.Y", strtotime($exp['Starting Date'])) . "</td>";
                        }

                        if ($exp['Ending Date']) {
                            echo "<td>" . date("d.m.Y", strtotime($exp['Ending Date'])) . "</td>";
                        }

                        if ($exp['previous_exp_y'] >= 0) {
                            if ($exp['previous_exp_y'] != 0) {
                                echo "<td>" . $exp['previous_exp_y'] . "</td>";
                            } else {
                                echo "<td>0</td>";
                            }

                        }

                        if ($exp['previous_exp_m'] >= 0) {
                            if (isset($exp['previous_exp_m'])) {
                                echo "<td>" . $exp['previous_exp_m'] . "</td>";
                            } else {
                                echo "<td>0</td>";
                            }
                        }

                        if ($exp['previous_exp_d'] >= 0) {
                            if (isset($exp['previous_exp_d'])) {
                                echo "<td>" . $exp['previous_exp_d'] . "</td>";
                            } else {
                                echo "<td>0</td>";
                            }
                        }

                        if ($exp['Coefficient']) {
                            echo "<td>" . round($exp['Coefficient'], 2) . "</td> ";
                        }

                        // <a id="add_new" type="button" style="background-color:#006595; color:white;"class="btn btn-secondary" href="?m=work_booklet&p=del_work_experience&edit=' . $exp['id'] . '">Izbriši</a>


                            echo '<td class="text-center"><a class="my-btn" href="?m=work_booklet&p=add_work_experience&edit=' . $exp['id'] . '">Uredite</a>
<a id="add_new" type="button" style="background-color:#006595; color:white;"class="my-btn" href="?m=work_booklet&p=del_work_experience&edit=' . $exp['id'] . '">Izbriši</a>
</td>';


                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            }
        } ?>
    </div>
    <?php
    if ($_GET['edit'] != 0){
    ?>
    <div class="hidden-tabs" id="tabs-2">
        <h3>Prethodno iskustvo</h3>

        <?php if ($active == 1) {
            ?>
            <div class="row">
                <div class="col-md-3">
                    <a id="add_new" type="button" style="background-color:#006595; color:white;"
                       class="btn btn-secondary"
                       href="?m=work_booklet&p=add_work_experience&new=<?php echo $_GET['edit']; ?>">Dodaj prethodno
                        iskustvo</a>
                </div>
            </div>
            <br>
        <?php } ?>
        <br><br>
        <?php
        }
        if ($_GET['edit'] != 0 and $work_experience->rowCount() < 0){
        ?>

        <table class="table table-bordered my-table table-sm">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Ime firme</th>
                <th scope="col">Datum početka rada</th>
                <th scope="col">Datum završetka rada</th>
                <th scope="col">Radni staž (G)</th>
                <th scope="col">Radni staž (M)</th>
                <th scope="col">Radni staž (D)</th>
                <th scope="col">Koeficijent</th>
                <th scope="col">Akcije</th>
                <?php if ($active == 1) {
                    //echo '<th scope="col">Akcije</th>';
                }
                ?>

            </tr>
            </thead>
            <tbody>
            <?php

            foreach ($work_experience as $exp) {
                echo "<tr>";

                if ($exp['id']) {
                    echo "<td>" . $exp['id'] . "</td>";
                }

                if ($exp['Employer']) {
                    echo "<td>" . $exp['Employer'] . "</td>";
                }

                if (is_null($exp['Starting Date'])) {
                    echo "<td>Nije uneseno</td>";
                } else {
                    echo "<td>" . date("d.m.Y", strtotime($exp['Starting Date'])) . "</td>";
                }

                if (is_null($exp['Ending Date'])) {
                    echo "<td>Nije uneseno</td>";
                } else {
                    echo "<td>" . date("d.m.Y", strtotime($exp['Ending Date'])) . "</td>";
                }

                if ($exp['previous_exp_y'] >= 0) {
                    //echo "<td>125</td>";
                    if ($exp['previous_exp_y'] != 0) {
                        echo "<td>" . $exp['previous_exp_y'] . "</td>";
                    } else {
                        echo "<td>0</td>";
                    }

                }

                if ($exp['previous_exp_m'] >= 0) {
                    if (isset($exp['previous_exp_m'])) {
                        echo "<td>" . $exp['previous_exp_m'] . "</td>";
                    } else {
                        echo "<td>0</td>";
                    }
                }

                if ($exp['previous_exp_d'] >= 0) {
                    if (isset($exp['previous_exp_d'])) {
                        echo "<td>" . $exp['previous_exp_d'] . "</td>";
                    } else {
                        echo "<td>0</td>";
                    }
                }

                if ($exp['Coefficient']) {
                    echo "<td>" . round($exp['Coefficient'], 2) . "</td> ";
                }

                    echo '<td>
                        <a id="add_new" type="button" style="background-color:#006595; color:white;"class="my-btn" href="?m=work_booklet&p=add_work_experience&edit=' . $exp['id'] . '">Uredite</a>
                        <a id="add_new" type="button" style="background-color:#006595; color:white;"class="my-btn" href="?m=work_booklet&p=del_work_experience&edit=' . $exp['id'] . '">Izbriši</a>
                      </td>';


                echo "</tr>";
            }
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php if ($_GET['edit'] != 0) { ?>
        <div class="hidden-tabs" id="tabs-3">
            <h6>prethodni(GMD)</h6>
            <div class="row">
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $years_prethodni_ispis; ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $months_prethodni_ispis; ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $days_prethodni_ispis; ?>">
                </div>
            </div>

            <h6>Prethodni u MKT(GMD)</h6>
            <div class="row">
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $years_prethodni_mkt_ispis; ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $months_prethodni_mkt_ispis; ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $days_prethodni_mkt_ispis; ?>">
                </div>
            </div>


            <h6>Ukupni prethodni(GMD)</h6>
            <div class="row">
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $years_b; ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $months_b; ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $days_b; ?>">
                </div>
            </div>
            <br><br>

            <h6>Trenutni MKT(GMD)</h6>
            <div class="row">
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $q['previous_exp_y'] ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $q['previous_exp_m']; ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $q['previous_exp_d']; ?>">
                </div>
            </div>

            <h6>Ukupni MKT(GMD)</h6>
            <div class="row">
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $years_total_mkt_ispis ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $months_total_mkt_ispis; ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $days_total_mkt_ispis; ?>">
                </div>
            </div>
            <br><br>


            <h6>ukupni(GMD)</h6>
            <div class="row">
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $total_years; ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $total_months; ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?php echo $total_days; ?>">
                </div>
            </div>
        </div>

    <?php } ?>
    <div class="hidden-tabs" id="tabs-4">
        <?php
        if ($_GET['edit'] != 0) {
            if ($coefficients->rowCount() < 0) {
                ?>

                <h5>Arhiva</h5>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Koeficijent</th>
                        <th scope="col">Datum od</th>
                        <th scope="col">Datum do</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($coefficients as $c) {
                        echo "<tr>";

                        if ($c['id']) {
                            echo "<td>" . $c['id'] . "</td>";
                        }

                        if ($c['coefficient']) {
                            echo "<td>" . round($c['coefficient'], 2) . "</td>";
                        }

                        if ($c['date_from']) {
                            echo "<td>" . date("d.m.Y", strtotime($c['date_from'])) . "</td>";
                        }

                        if ($c['date_to']) {
                            echo "<td>" . date("d.m.Y", strtotime($c['date_to'])) . " ";
                        }

                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>

                <?php
            } else {
                echo '<p>Trenutno nema koeficijenata</p>';
            }
        }
        ?>
    </div>
</div>


<br>


<br><br>


<?php

include $_themeRoot . '/footer.php';

?>

<!--<script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script> -->
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
<script src="<?php echo $_pluginUrl; ?>/jquery-cookie-master/src/jquery.cookie.js"></script>

<script src="modules/work_booklet/pages/js/script.js">
</script>
