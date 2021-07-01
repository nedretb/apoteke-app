<?php
_pagePermission(5, false);
?>

<!-- START - Main section -->
<section class="full tasks-page">

    <?php

    $limit = 20;
    $where = "WHERE user_id='" . $_user['user_id'] . "'";
    $path = '?m=' . $_mod . '&p=' . $_page;
    $path .= '&pg=';

    if (isset($_GET['u'])) {
        $usr = $_GET['u'];
        if ($usr != '') {
            $where = "WHERE user_id='" . $usr . "'";
            $path .= '&u=' . $usr;
        } else {
            $usr = '';
            $where .= "";
        }
    } else
        $usr = $_user['user_id'];;
    ?>

    <?php
    $rok_cilj = '';
    $procjena = '';
    $evaluacija = '';
    $year_procjena = '';
    $query_deadline = $db->query("SELECT * FROM  " . $portal_objective_deadline . "  WHERE YEAR = " . date("Y"));
    foreach ($query_deadline as $item) {
        $year_procjena = $item['year'];
        $period_start = $item['period_start'];
        $period_end = $item['period_end'];

        if ($item['phase'] == 1)
            $rok_cilj = $item['objective_deadline'];
        if ($item['phase'] == 2)
            $procjena = $item['objective_deadline'];
        if ($item['phase'] == 3)
            $evaluacija = $item['objective_deadline'];
    }

    ?>

    <br/>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-2">
                <div class="box" style="background:none;">

                    <div class="content" id="c1a" style="display: block;padding:0px;">


                        <?php
                        $get_image = $db->query("SELECT image_no FROM  " . $portal_users . "  WHERE user_id='90'");
                        $image_id = $db->query("SELECT user_id FROM  " . $portal_users . "  WHERE user_id='90'"); ?>
                        <?php if ($_user['image_no'] != 'none') { ?>

                            <img src="<?php echo $_uploadUrl; ?>/<?php echo _user($usr)['image']; ?>" class=""
                                 style="width:65%;">
                        <?php } else { ?>
                            <img src="<?php echo $_themeUrl; ?>/images/noimage-user.png" class="img-circle"
                                 style="width:65%;">
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-2">
                <big><b><?php echo _user($usr)['fname'] . ' ' . _user($usr)['lname']; ?></b></big><br/>
                <?php echo _role(_user($usr)['role']);
                ?>
            </div>


            <?php
            if ($_user['role'] == '1' || $_user['role'] == '2') {
                ?>
                <div class="col-sm-2">
                    <h4><?php echo __('Opcije'); ?></h4>
                    <select id="opcije_mbo" name="opcije_mbo" class="rcorners1" class="form-control"
                            style="height:37px;outline:none;">
                        <?php if (isset($_GET['u'])) {
                            $opcija = 0;
                        } else {
                            $opcija = 1;
                        }
                        echo _optionMBOProfilHR($opcija); ?>
                    </select><br/>
                </div>
            <?php } else {
                ?>
                <div class="col-sm-3">
                    <h3 style="margin-left:200px;"><?php echo __('MBO profil'); ?></h3>
                    <select id="profil" name="profil" class="rcorners1" class="form-control"
                            style="height:37px;outline:none;display:none;">
                        <?php echo _optionMBOProfil(0); ?>
                    </select><br/>
                </div>
            <?php } ?>


            <div id="name_search" style="display:<?php if (isset($_GET['u'])) {
                echo '';
            } else {
                echo 'none';
            } ?>;width:225px;padding-top: 34px;" class="col-sm-2">
                <form action="" method="get" class="">
                    <input type="hidden" name="m" value="<?php echo $_mod; ?>">
                    <input type="hidden" name="p" value="<?php echo $_page; ?>">
                    <?php if (isset($_GET['t'])) { ?>
                        <input type="hidden" name="t" value="<?php echo $_GET['t']; ?>">
                    <?php } ?>


                    <select name="u" id="select_input" class="pull-right rcorners1" class=""
                            style="width:400px;height:37px;padding-top:0px;padding-bottom:0px;"
                            onchange="this.form.submit();">
                        <option value=""><?php echo __('Odaberi'); ?></option>
                        <?php
                        $_user_role = $_user['role'];
                        if ($_user_role == 1) {
                            $get_departments = $db->query("SELECT DISTINCT sector FROM  " . $portal_users . "  WHERE (hr = " . $_user['employee_no'] . " or hr2 = " . $_user['employee_no'] . " or hr3 = " . $_user['employee_no'] . ") and termination_date is null order by sector");
                            $get_users2 = $db->query("SELECT count(*) FROM  " . $portal_users . "  WHERE (hr = " . $_user['employee_no'] . " or hr2 = " . $_user['employee_no'] . " or hr3 = " . $_user['employee_no'] . ") and termination_date is null");
                            if ($get_users2->rowCount() < 0) {
                                foreach ($get_departments as $sector) {
                                    echo '<optgroup label="' . $sector['sector'] . '">';
                                    $get_users = $db->query("SELECT * FROM  " . $portal_users . "  WHERE (hr = " . $_user['employee_no'] . " or hr2 = " . $_user['employee_no'] . " or hr3 = " . $_user['employee_no'] . ") and termination_date is null AND sector= N'" . $sector['sector'] . "'");
                                    foreach ($get_users as $user) {
                                        if ($usr == $user['user_id']) {
                                            $sel = 'selected="selected"';
                                        } else {
                                            $sel = '';
                                        }
                                        echo '<option value="' . $user['user_id'] . '" ' . $sel . '>' . $user['fname'] . ' ' . $user['lname'] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                            }
                        }

                        if ($_user_role == 2) {
                            $get_departments = $db->query("SELECT DISTINCT sector FROM  " . $portal_users . "  WHERE parent = " . $_user['employee_no']);
                            $get_users2 = $db->query("SELECT count(*) FROM  " . $portal_users . "  WHERE parent = " . $_user['employee_no']);
                            if ($get_users2->rowCount() < 0) {
                                foreach ($get_departments as $sector) {
                                    echo '<optgroup label="' . $sector['sector'] . '">';
                                    $get_users = $db->query("SELECT * FROM  " . $portal_users . "  WHERE parent = " . $_user['employee_no'] . " AND sector= N'" . $sector['sector'] . "'");
                                    foreach ($get_users as $user) {
                                        if ($usr == $user['user_id']) {
                                            $sel = 'selected="selected"';
                                        } else {
                                            $sel = '';
                                        }
                                        echo '<option value="' . $user['user_id'] . '" ' . $sel . '>' . $user['fname'] . ' ' . $user['lname'] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                            }
                        }
                        ?>
                    </select>

                </form>
            </div>

            <div id="name_search1" style="display:none;width:225px;padding-top: 34px;" class="col-sm-2">
                <form action="" method="get" class="">
                    <input type="hidden" name="m" value="<?php echo $_mod; ?>">
                    <input type="hidden" name="p" value="<?php echo $_page; ?>">
                    <?php if (isset($_GET['t'])) { ?>
                        <input type="hidden" name="t" value="<?php echo $_GET['t']; ?>">
                    <?php } ?>


                    <select name="u" id="select_input1" class="pull-right rcorners1" class=""
                            style="width:400px;height:37px;padding-top:0px;padding-bottom:0px;"
                            onchange="this.form.submit();">
                        <option value=""><?php echo __('Odaberi'); ?></option>
                        <?php
                        $_user_role = $_user['role'];
                        if ($_user_role == 1) {
                            $get_b1 = $db->query("SELECT DISTINCT B_1_description FROM  " . $portal_users . "  WHERE (hr = " . $_user['employee_no'] . " or hr2 = " . $_user['employee_no'] . " or hr3 = " . $_user['employee_no'] . ") and termination_date is null order by B_1_description");

                            $get_users2 = $db->query("SELECT count(*) FROM  " . $portal_users . "  WHERE (hr = " . $_user['employee_no'] . " or hr2 = " . $_user['employee_no'] . " or hr3 = " . $_user['employee_no'] . ") and termination_date is null");
                            if ($get_users2->rowCount() < 0) {
                                foreach ($get_b1 as $b1) {
                                    echo '<optgroup label="' . $b1['B_1_description'] . '">';
                                    $get_departments = $db->query("SELECT DISTINCT sector FROM  " . $portal_users . "  WHERE (hr = " . $_user['employee_no'] . " or hr2 = " . $_user['employee_no'] . " or hr3 = " . $_user['employee_no'] . ") and termination_date is null and B_1_description = N'" . $b1['B_1_description'] . "'");
                                    foreach ($get_departments as $sector) {
                                        echo '<optgroup label="' . $sector['sector'] . '">';
                                        $get_users = $db->query("SELECT * FROM  " . $portal_users . "  WHERE (hr = " . $_user['employee_no'] . " or hr2 = " . $_user['employee_no'] . " or hr3 = " . $_user['employee_no'] . ") and termination_date is null AND sector= N'" . $sector['sector'] . "'");
                                        foreach ($get_users as $user) {
                                            if ($usr == $user['user_id']) {
                                                $sel = 'selected="selected"';
                                            } else {
                                                $sel = '';
                                            }
                                            echo '<option value="' . $user['user_id'] . '" ' . $sel . '>' . $user['fname'] . ' ' . $user['lname'] . '</option>';
                                        }
                                        echo '</optgroup>';
                                        echo '</optgroup>';
                                    }
                                }
                            }
                        }

                        if ($_user_role == 2) {
                            $get_b1 = $db->query("SELECT DISTINCT B_1_description FROM  " . $portal_users . "  WHERE parent = " . $_user['employee_no'] . " and termination_date is null order by B_1_description");

                            $get_users2 = $db->query("SELECT count(*) FROM  " . $portal_users . "  WHERE parent = " . $_user['employee_no'] . " and termination_date is null");
                            if ($get_users2->rowCount() < 0) {
                                foreach ($get_b1 as $b1) {
                                    echo '<optgroup label="' . $b1['B_1_description'] . '">';
                                    $get_departments = $db->query("SELECT DISTINCT sector FROM  " . $portal_users . "  WHERE parent = " . $_user['employee_no'] . " and termination_date is null and B_1_description = N'" . $b1['B_1_description'] . "'");
                                    foreach ($get_departments as $sector) {
                                        echo '<optgroup label="' . $sector['sector'] . '">';
                                        $get_users = $db->query("SELECT * FROM  " . $portal_users . "  WHERE parent = " . $_user['employee_no'] . " and termination_date is null AND sector= N'" . $sector['sector'] . "'");
                                        foreach ($get_users as $user) {
                                            if ($usr == $user['user_id']) {
                                                $sel = 'selected="selected"';
                                            } else {
                                                $sel = '';
                                            }
                                            echo '<option value="' . $user['user_id'] . '" ' . $sel . '>' . $user['fname'] . ' ' . $user['lname'] . '</option>';
                                        }
                                        echo '</optgroup>';
                                        echo '</optgroup>';
                                    }
                                }
                            }
                        }
                        ?>
                    </select>

                </form>
            </div>


            <div class="col-sm-2" style="float:right;">
                <div class="btn btn-filter active-task" style="margin-bottom: 15px;width: 130px;"><a style="color:black"
                                                                                                     class="active-white"
                                                                                                     href="?m=default&p=mbo_pocetna">
                        Nazad na početnu</a></div>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-6">
                <table class="alt col-sm-12">
                    <tr>
                        <th style="height:20px;">Podaci o poziciji</th>
                        <th style="height:20px;"></th>
                    </tr>

                    <tr>
                        <td><b><?php echo __('Pozicija'); ?></b></td>
                        <td><?php echo _user($usr)['position']; ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Datum zaposlenja'); ?></b></td>
                        <td><?php echo date('d/m/Y', strtotime(_user($usr)['employment_date'])); ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Na radnom mjestu od'); ?></b></td>
                        <td><?php echo date('d/m/Y', strtotime(_user($usr)['employment_date'])); ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('ID OJ'); ?></b></td>
                        <td><?php echo _user($usr)['department_code']; ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Naziv organizacione jedinice'); ?></b></td>
                        <td><?php echo _user($usr)['sector']; ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Neposredni rukovodilac'); ?></b></td>
                        <td><?php echo _employee(_user($usr)['parent'])['fname'] . " " . _employee(_user($usr)['parent'])['lname']; ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Naredni nadređeni'); ?></b></td>
                        <td><?php echo _employee(_user($usr)['parentMBO2'])['fname'] . " " . _employee(_user($usr)['parentMBO2'])['lname']; ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Datum polugodišnjeg razgovora'); ?></b></td>
                        <td><?php echo date('d/m/Y', strtotime($procjena)); ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Datum godišnjeg razgovora'); ?></b></td>
                        <td><?php echo date('d/m/Y', strtotime($evaluacija)); ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Period procjene (godina)'); ?></b></td>
                        <td><?php echo $year_procjena; ?></td>
                    </tr>

                </table>
            </div>


            <div class="col-sm-6">
                <table class="alt col-sm-12">
                    <tr>
                        <th style="height:20px;">Lični podaci</th>
                        <th style="height:20px;"></th>
                    </tr>

                    <tr>
                        <td><b><?php echo __('JMB'); ?></b></td>
                        <td><?php echo _user($usr)['employee_no']; ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('ID obrasca'); ?></td>
                        <td><?php echo '543098430'; ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Ime'); ?></b></td>
                        <td><?php echo _user($usr)['fname']; ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Prezime'); ?></b></td>
                        <td><?php echo _user($usr)['lname']; ?></td>
                    </tr>

                    <tr>
                        <td><b><?php echo __('Spol'); ?></b></td>
                        <td><?php echo _getSexById(_user($usr)['gender']); ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('E-mail'); ?></b></td>
                        <td><?php echo _user($usr)['email']; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>

                </table>
            </div>
        </div>

    </div>
    </div>
    <br/>


    <?php


    $query = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_experience . "  " . $where);
    $query1 = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_projects . "  " . $where);
    $query2 = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_language_skills . "  " . $where);
    $query3 = $db->query("SELECT TOP " . $limit . "* FROM [c0_intranet2_apoteke].[dbo].[Certifikati] " . $where);
    $query4 = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_misc . "  " . $where . " AND year = " . date("Y"));
    $query5 = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_education . " " . $where);
    /*  $result = $query5->fetchAll();
    print_r($result); */

    foreach ($query4 as $item) {
        $ambicije = $item['ambicije'];
        $mobilnost = $item['mobilnost'];
        $lokacija = $item['lokacija'];
        $vjestina = $item['vjestina'];
        $nivo = $item['nivo'];
        $rizik_gubitka = $item['rizik_gubitka'];
        $uticaj_gubitka = $item['uticaj_gubitka'];
        $razlog_odlaska = $item['razlog_odlaska'];
        $karijera = $item['karijera'];
        $novi_zaposlenik = $item['novi_zaposlenik'];
        $pozicija = $item['pozicija'];
        $spremnost = $item['spremnost'];
        $prezime_ime = $item['prezime_ime'];
        $datum = $item['datum'];

    }


    ?>

    <?php
    if ($_user['role'] == '1' || $_user['role'] == '2' and isset($_GET['u'])) {
        ?>
        <div class="col-sm-6">
            <span style="height:20px; background: #FFC000;color: black;padding-left:20px;display:block;"><b>Talent  pool</b></span><br/>
            <div style="margin-bottom:5px;" class="">
                <label><?php echo __('Rizik gubitka'); ?></label>
                <select style="float:right" id="rizik_gubitka" name="<?php echo 'rizik_gubitka-' . $item['user_id']; ?>"
                        class="" class="form-control">
                    <?php echo _OptionRizikGubitka($rizik_gubitka); ?>
                </select>
            </div>
            <div style="margin-bottom:5px;" class="">
                <label><?php echo __('Uticaj gubitka'); ?></label>
                <select style="float:right" id="uticaj_gubitka"
                        name="<?php echo 'uticaj_gubitka-' . $item['user_id']; ?>" class="" class="form-control">
                    <?php echo _OptionUticajGubitka($uticaj_gubitka); ?>
                </select>
            </div>
            <div style="margin-bottom:5px;" class="">
                <label><?php echo __('Mogući razlog odlaska'); ?></label>
                <select style="float:right" id="razlog_odlaska"
                        name="<?php echo 'razlog_odlaska-' . $item['user_id']; ?>" class="" class="form-control">
                    <?php echo _OptionRazlogOdlaska($razlog_odlaska); ?>
                </select>
            </div>
            <div style="margin-bottom:5px;" class="">
                <label><?php echo __('Karijera'); ?></label>
                <select style="float:right" id="karijera" name="<?php echo 'karijera-' . $item['user_id']; ?>" class=""
                        class="form-control">
                    <?php echo _OptionKarijera($karijera); ?>
                </select>
            </div>
            <div style="margin-bottom:5px;" class="">
                <label><?php echo __('Novi zaposlenik'); ?></label>
                <select style="float:right" id="novi_zaposlenik"
                        name="<?php echo 'novi_zaposlenik-' . $item['user_id']; ?>" class="" class="form-control">
                    <?php echo _OptionNoviZaposlenik($novi_zaposlenik); ?>
                </select>
            </div>
        </div>

        <div class="col-sm-6">
            <span style="height:20px; background: #FFC000;color: black;padding-left:20px;display:block;"><b>Nominiranje nasljednika</b></span><br/>

            <div style="margin-bottom:5px;" class="">
                <label><?php echo __('Pozicija'); ?></label>
                <input type="text" style="float:right" id="pozicija"
                       name="<?php echo 'pozicija-' . $item['user_id']; ?>" value="<?php echo $pozicija; ?>"
                       spellcheck="false"></input>
            </div>


            <div style="margin-bottom:5px;" class="">
                <label><?php echo __('Spremnost za preuzimanje pozicije'); ?></label>
                <select style="float:right" id="spremnost" name="<?php echo 'spremnost-' . $item['user_id']; ?>"
                        class="" class="form-control">
                    <?php echo _OptionSpremnost($spremnost); ?>
                </select>
            </div>

            <div style="margin-bottom:5px;" class="">
                <label><?php echo __('Prezime i ime'); ?></label>
                <input type="text" style="float:right" id="prezime_ime"
                       name="<?php echo 'prezime_ime-' . $item['user_id']; ?>" value="<?php echo $prezime_ime; ?>"
                       spellcheck="false"></input><br/>
            </div>

            <div style="margin-bottom:5px;" class="">
                <label><?php echo __('Datum'); ?></label>
                <input style="width: 115px;height: 30px;display: initial;float:right;border-bottom-style: groove;;border-style: groove;"
                       type="text" id="datum" name="<?php echo 'datum-' . $item['user_id']; ?>" class="form-control"
                       id="date_to" placeholder="dd/mm/yyyy" value="<?php if ($datum != '') {
                    echo date('d/m/Y', strtotime($datum));
                } else {
                    echo '';
                } ?>">
            </div>
            <br/><br/><br/>
        </div>
    <?php } ?>

    <div class="btn btn-filter active-task" style="margin-bottom:15px;width: 185px;margin-left:15px;"><a
                style="color:black" class="active-white" href="#karijera_pozicija"> Karijera i ostale vještine</a><a
                style="margin-left:5px;" href="#karijera_pozicija"><i class="fa fa-arrow-down" style="color:white;"
                                                                      aria-hidden="true"></i></a></div>


    <div class="col-sm-12">
        <table class="alt col-sm-12">
            <tr>
                <th style="height:20px;width: 600px;">Prethodno radno iskustvo prije Sberbank BH d.d.</th>
                <th style="height:20px;width: 300px;"></th>
                <th style="height:20px;width: 300px;"></th>
                <th style="height:20px;width: 300px;"></th>
                <th style="height:20px;width: 300px;"></th>
                <th style="height:20px;width: 300px;"></th>
                <th style="height:20px;width: 20px;"></th>
            </tr>
            <tr>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Navi pozicije</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Naziv OJ</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Datum od</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Datum do</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Poslodavac</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Napomena</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 20px;"></th>
            </tr>
            <?php
            foreach ($query as $item) {
                ?>
                <tr>
                    <td><?php echo $item['position']; ?></td>
                    <td><?php echo $item['OJ']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($item['date_from'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($item['date_to'])); ?></td>
                    <td><?php echo $item['poslodavac']; ?></td>
                    <td><?php echo $item['napomena']; ?></td>
                    <td><a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>" class="table-btn1"
                           data-widget="remove" data-id="experience_remove:<?php echo $item['id']; ?>"
                           data-text="<?php echo __('Dali ste sigurni da želite poništiti radno iskustvo?'); ?>"
                           title="Obriši" style="display:<?php if (isset($_GET['u'])) {
                            echo 'none';
                        } else {
                            echo '';
                        } ?>"><i class="ion-android-close"></i></a></td>

                </tr>

            <?php } ?>


        </table>
        <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_experience_add.php' ?>" data-widget="ajax"
           data-id="opt2" data-width="1500" class="btn btn-sm btn-red"
           style="margin-bottom:5px;float: left;background: cadetblue;display:<?php if (isset($_GET['u'])) {
               echo 'none';
           } else {
               echo '';
           } ?>">Novi unos <i class="ion-ios-plus-empty"></i></a>
        <br/> <br/>
    </div>

    <div class="col-sm-12">
        <table class="alt col-sm-12">
            <tr>
                <th style="height:20px;width: 600px;">Projekti</th>
                <th style="height:20px;width: 300px;"></th>
                <th style="height:20px;width: 300px;"></th>
                <th style="height:20px;width: 300px;"></th>
                <th style="height:20px;width: 300px;"></th>
                <th style="height:20px;width: 300px;"></th>
                <th style="height:20px;width: 20px;"></th>
            </tr>
            <tr>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Naziv projekta</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Područje</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Datum od</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Datum do</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Poslodavac</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Uloga u projektu</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 20px;"></th>
            </tr>

            <?php
            foreach ($query1 as $item) {
                ?>
                <tr>
                    <td><?php echo $item['project_name']; ?></td>
                    <td><?php echo $item['area']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($item['date_from'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($item['date_to'])); ?></td>
                    <td><?php echo $item['poslodavac']; ?></td>
                    <td><?php echo $item['uloga']; ?></td>
                    <td><a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>" class="table-btn1"
                           data-widget="remove" data-id="project_remove:<?php echo $item['id']; ?>"
                           data-text="<?php echo __('Dali ste sigurni da želite poništiti projekt?'); ?>" title="Obriši"
                           style="display:<?php if (isset($_GET['u'])) {
                               echo 'none';
                           } else {
                               echo '';
                           } ?>"><i class="ion-android-close"></i></a></td>

                </tr>

            <?php } ?>

        </table>
        <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_project_add.php' ?>" data-widget="ajax"
           data-id="opt2" data-width="1500" class="btn btn-sm btn-red"
           style="margin-bottom:5px;float: left;background: cadetblue;display:<?php if (isset($_GET['u'])) {
               echo 'none';
           } else {
               echo '';
           } ?>">Novi unos <i class="ion-ios-plus-empty"></i></a>
        <br/> <br/>
    </div>

    <div class="col-sm-12">
        <table class="alt col-sm-12">
            <tr>
                <th style="height:20px; background: #8DB4E2;color: #ffffff;width: 600px;">Obrazovanje</th>
                <th style="height:20px; background: #8DB4E2;color: #ffffff;width: 300px;"></th>
                <th style="height:20px; background: #8DB4E2;color: #ffffff;width: 300px;"></th>
                <th style="height:20px; background: #8DB4E2;color: #ffffff;width: 300px;"></th>
                <th style="height:20px; background: #8DB4E2;color: #ffffff;width: 300px;"></th>
                <th style="height:20px; background: #8DB4E2;color: #ffffff;width: 300px;"></th>
            </tr>
            <tr>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Naziv završene obrazovne
                    institucije
                </th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Stepen stručne spreme</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Odsjek/smjer</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Datum od</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Datum do</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Zvanje</th>
            </tr>

            <?php
            foreach ($query5 as $item) {
                ?>

                <tr>
                    <td nowrap="nowrap"><?php echo $item['institucija']; ?></td>
                    <td nowrap="nowrap"><?php echo $item['stepen']; ?></td>
                    <td nowrap="nowrap"><?php echo $item['odsjek']; ?></td>
                    <td nowrap="nowrap"><?php if ($item['date_from'] != '') {
                            echo date('d/m/Y', strtotime($item['date_from']));
                        } else {
                            echo '';
                        } ?></td>
                    <td nowrap="nowrap"><?php if ($item['date_to'] != '') {
                            echo date('d/m/Y', strtotime($item['date_to']));
                        } else {
                            echo '';
                        } ?></td>
                    <td nowrap="nowrap"><?php echo $item['zvanje']; ?></td>
                </tr>

            <?php } ?>

        </table>
        <br/> <br/>
    </div>

    <div class="col-sm-12">
        <table class="alt col-sm-12">
            <tr>
                <th style="height:20px;width: 600px;">Poznavanje stranih jezika</th>
                <th style="height:20px;width: 500px;"></th>
                <th style="height:20px;width: 500px;"></th>
                <th style="height:20px;width: 500px;"></th>
                <th style="height:20px;width: 20px;"></th>
            </tr>
            <tr>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Jezik</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Razumijevanje</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Govor</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Pisanje</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 20px;"></th>
            </tr>

            <?php
            foreach ($query2 as $item) {
                ?>
                <tr>
                    <td><?php echo _getLanguageById($item['language']); ?></td>
                    <td><?php echo _getSkillById($item['understanding']); ?></td>
                    <td><?php echo _getSkillById($item['speech']); ?></td>
                    <td><?php echo _getSkillById($item['writing']); ?></td>
                    <td><a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>" class="table-btn1"
                           data-widget="remove" data-id="language_remove:<?php echo $item['id']; ?>"
                           data-text="<?php echo __('Dali ste sigurni da želite poništiti jezik?'); ?>" title="Obriši"
                           style="display:<?php if (isset($_GET['u'])) {
                               echo 'none';
                           } else {
                               echo '';
                           } ?>"><i class="ion-android-close"></i></a></td>
                </tr>

            <?php } ?>

        </table>
        <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_language_add.php' ?>" data-widget="ajax"
           data-id="opt2" data-width="1500" class="btn btn-sm btn-red"
           style="margin-bottom:5px;float: left;background: cadetblue;display:<?php if (isset($_GET['u'])) {
               echo 'none';
           } else {
               echo '';
           } ?>">Novi unos <i class="ion-ios-plus-empty"></i></a>
        <br/> <br/>
    </div>

    <div class="col-sm-12">
        <table class="alt col-sm-12">
            <tr>
                <th style="height:20px;width: 600px;">Certifikati i licence</th>
                <th style="height:20px;width: 500px;"></th>
                <th style="height:20px;width: 500px;"></th>
                <th style="height:20px;width: 500px;"></th>
                <th style="height:20px;width: 20px;"></th>
            </tr>
            <tr>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Naziv završene edukacije /
                    treninga
                </th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Naziv institucije</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Vrsta edukacije</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Datum završetka</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 20px;"></th>
            </tr>

            <?php
            foreach ($query3 as $item) {
                ?>
                <tr>
                    <td><?php echo $item['certifikat']; ?></td>
                    <td><?php echo $item['institucija']; ?></td>
                    <td><?php echo _getEducationById($item['vrsta']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($item['zavrsetak'])); ?></td>
                    <td><a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>" class="table-btn1"
                           data-widget="remove" data-id="certifikat_remove:<?php echo $item['id']; ?>"
                           data-text="<?php echo __('Dali ste sigurni da želite poništiti certifikat?'); ?>"
                           title="Obriši" style="display:<?php if (isset($_GET['u'])) {
                            echo 'none';
                        } else {
                            echo '';
                        } ?>"><i class="ion-android-close"></i></a></td>
                </tr>

            <?php } ?>

        </table>

        <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_certifikat_add.php' ?>" data-widget="ajax"
           data-id="opt2" data-width="1500" class="btn btn-sm btn-red"
           style="margin-bottom:5px;float: left;background: cadetblue;display:<?php if (isset($_GET['u'])) {
               echo 'none';
           } else {
               echo '';
           } ?>">Novi unos <i class="ion-ios-plus-empty"></i></a>
        <br/> <br/>
    </div>


    <div id="karijera_pozicija" class="col-sm-6">
        <span style="height:20px;padding-left:20px;display:block;"><b>Karijera</b></span><br/>
        <div style="margin-bottom:80px;" class="">
            <label><?php echo __('Ambicije u karijeri (poslovi, radna mjesta, odjeli):'); ?></label>
            <textarea id="ambicije" <?php if (isset($_GET['u'])) {
                echo 'readonly';
            } ?> style="float:right;height:100px;" rows="3" cols="70"
                      name="<?php echo 'ambicije-' . $item['user_id']; ?>"
                      spellcheck="false"><?php echo $ambicije; ?></textarea>
        </div>
        <div style="margin-bottom:5px;" class="">
            <label><?php echo __('Mobilnost (DA / NE)'); ?></label>
            <select <?php if (isset($_GET['u'])) {
                echo 'disabled';
            } ?> style="float:right;" id="mobilnost" name="<?php echo 'mobilnost-' . $item['user_id']; ?>" class=""
                 class="form-control">
                <?php echo _OptionVerification($mobilnost); ?>
            </select>
        </div>
        <div style="margin-bottom:5px;" class="">
            <label><?php echo __('Željena lokacija'); ?></label>
            <input type="text" <?php if (isset($_GET['u'])) {
                echo 'disabled';
            } ?> style="float:right;" id="lokacija" name="<?php echo 'lokacija-' . $item['user_id']; ?>"
                   spellcheck="false" value="<?php echo $lokacija; ?>"></input><br/>
        </div>
    </div>

    <div class="col-sm-6">
        <span style="height:20px;padding-left:20px;display:block;"><b>Ostale vještine</b></span><br/>
        <div style="margin-bottom:5px;" class="">
            <label><?php echo __('Vještina'); ?></label>
            <input type="text" <?php if (isset($_GET['u'])) {
                echo 'disabled';
            } ?> style="float:right;" id="vjestina" name="<?php echo 'vjestina-' . $item['user_id']; ?>"
                   value="<?php echo $vjestina; ?>" spellcheck="false"></input><br/>
        </div>
        <div style="margin-bottom:5px;" class="">
            <label><?php echo __('Nivo'); ?></label>
            <input type="text" <?php if (isset($_GET['u'])) {
                echo 'disabled';
            } ?> style="float:right;" id="nivo" name="<?php echo 'nivo-' . $item['user_id']; ?>"
                   value="<?php echo $nivo; ?>" spellcheck="false"></input><br/>
        </div>
        <br/><br/>
        <div class="btn btn-filter active-task" style="margin-bottom:15px;width: 185px;margin-left:15px;"><a
                    style="color:black" class="active-white" href="#c1a"> Nazad na početak</a><a
                    style="margin-left:5px;" href="#c1a"><i class="fa fa-arrow-up" style="color:white;"
                                                            aria-hidden="true"></i></a></div>
        <br/><br/><br/>
    </div>

    </div>


</section>
<!-- END - Main section -->

<?php

include $_themeRoot . '/footer.php';

?>
<script>

    $('#opcije_mbo').on('change', function () {
        if (this.value == 0) {
            $('#name_search').css('display', '');
            $('#name_search1').css('display', 'none');
        } else if (this.value == 2) {
            $('#name_search1').css('display', '');
            $('#name_search').css('display', 'none');
        } else if (this.value == 1) {
            $('#name_search').css('display', 'none');
            <?php echo 'location.href = "' . $url . '/?m=default&p=mbo";' ?>
        }
    })


    $('#datum').datepicker({
        todayBtn: "linked",
        format: 'dd/mm/yyyy',
        language: 'bs'
        //startDate: startDate,
        //endDate: new Date('2017/12/31')
    });


    $('#ambicije').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-ambicije",
                ambicije: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })

    $('#mobilnost').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-mobilnost",
                mobilnost: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);
                window.location.reload();
            });
    })

    $('#lokacija').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-lokacija",
                lokacija: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })
    $('#vjestina').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-vjestina",
                vjestina: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })
    $('#nivo').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-nivo",
                nivo: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })

    $('#rizik_gubitka').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-rizik_gubitka",
                rizik_gubitka: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })

    $('#uticaj_gubitka').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-uticaj_gubitka",
                uticaj_gubitka: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })

    $('#razlog_odlaska').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-razlog_odlaska",
                razlog_odlaska: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })

    $('#karijera').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-karijera",
                karijera: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })

    $('#novi_zaposlenik').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-novi_zaposlenik",
                novi_zaposlenik: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })

    $('#pozicija').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-pozicija",
                pozicija: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })

    $('#spremnost').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-spremnost",
                spremnost: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })

    $('#prezime_ime').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-prezime_ime",
                prezime_ime: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })

    $('#datum').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-datum",
                datum: this.value,
                user_id: this.name
            },
            function (returnedData) {
                //$('#poslovnice').html(returnedData);

            });
    })

    function removeHash() {
        var scrollV, scrollH, loc = window.location;
        if ("pushState" in history)
            history.pushState("", document.title, loc.pathname + loc.search);
        else {
            // Prevent scrolling by storing the page's current scroll offset
            scrollV = document.body.scrollTop;
            scrollH = document.body.scrollLeft;

            loc.hash = "";

            // Restore the scroll offset, should be flicker free
            document.body.scrollTop = scrollV;
            document.body.scrollLeft = scrollH;
        }
    }

    $(document).ready(function () {

        $("#select_input").select2();
        $("#select_input1").select2();
        removeHash();

    });

</script>


</body>
</html>
