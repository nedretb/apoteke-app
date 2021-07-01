<?php
_pagePermission(5, false);

header('Location: ?m=profile&p=edit-profile');


$filtertdate = date("Y") . "-" . date("m") . "-1 00:00:00.000";

if (isset($_GET['u']) and $_GET['u'] != '')
    $usr = $_GET['u'];
else
    $usr = $_user['employee_no'];

$work_experience = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where  [Employee No_]=".$usr." order by Active desc");

$get = $db->query("SELECT * FROM  " . $portal_users . "  WHERE employee_no='" . $usr . "'");
if ($get->rowCount() < 0)
    $row = $get->fetch();
$get = $db->query("SELECT * FROM  " . $nav_employee . "  WHERE No_='" . $usr . "'");
if ($get->rowCount() < 0)
    $row_personal = $get->fetch();
$get = $db->query("SELECT * FROM  ".$nav_employee_relative."  WHERE [Employee No_]='" . $usr . "'");
if ($get->rowCount() < 0)
    $rows_relatives = $get->fetchAll();

$get = $db->query("SELECT * FROM  ".$nav_employee_relative."  WHERE [Employee No_]='" . $usr . "' AND [Relative Code] = 'OTAC'");
if ($get->rowCount() < 0)
    $row_otac = $get->fetchAll();

$get = $db->query("SELECT * FROM  ".$nav_employee_relative."  WHERE [Employee No_]='" . $usr . "' AND [Relative Code] = 'MAJKA'");
if ($get->rowCount() < 0)
    $row_majka = $get->fetchAll();

$get = $db->query("SELECT * FROM  ".$nav_employee_relative."  WHERE [Employee No_]='" . $usr . "' AND [Relative Code] = N'SUPRUŽNIK'");
if ($get->rowCount() < 0)
    $row_supruznik = $get->fetchAll();

$get = $db->query("SELECT * FROM  ".$nav_employee_relative."  WHERE [Employee No_]='" . $usr . "' AND [Relative Code] = 'DIJETE'");
if ($get->rowCount() < 0)
    $row_dijete = $get->fetchAll();

$get = $db->query("SELECT * FROM  ".$nav_employee_relative."  WHERE [Employee No_]='" . $usr . "' AND [Relative Code] = 'PASTORAK/P'");
if ($get->rowCount() < 0)
    $row_pastorak = $get->fetchAll();

$get = $db->query("SELECT * FROM  ".$nav_employee_relative."  WHERE [Employee No_]='" . $usr . "' AND [Relative_s Employee No_] <> ''");
if ($get->rowCount() < 0)
    $row_srodnici_banka = $get->fetchAll();

$get = $db->query("SELECT * FROM  ".$nav_additional_education."  WHERE Active=1 and [Employee No_]='" . $usr . "'");
if ($get->rowCount() < 0)
    $rows_education = $get->fetch();

$get = $db->query("SELECT * FROM  ".$nav_employee_qualification."  WHERE [Employee No_]='" . $usr . "'");
if ($get->rowCount() < 0)
    $rows_qualification = $get->fetchAll();


$get = $db->query("SELECT * FROM  ".$nav_employee_qualification."  WHERE [Employee No_]='" . $usr . "' AND [Language Code]<>''");
if ($get->rowCount() < 0)
    $row_jezici = $get->fetchAll();

$get = $db->query("SELECT * FROM  ".$nav_employee_qualification."  WHERE [Employee No_]='" . $usr . "' AND [Qualification Code]<>''");
if ($get->rowCount() < 0)
    $row_certifikati = $get->fetchAll();

$get = $db->query("SELECT * FROM  ".$nav_employee_union."  WHERE [Employee No_]='" . $usr . "'");
if ($get->rowCount() < 0)
    $rows_unions = $get->fetchAll();


$get_parent = $db->query("SELECT * FROM  " . $portal_users . "  WHERE parent='" . $_user['employee_no'] . "'");
$row_parent = $get_parent->fetchAll();
$countParent = count($row_parent);


$get_alternative = $db->query("SELECT * FROM  ".$nav_alternative_address."  WHERE [Employee No_]='" . $usr . "' and Active = '1'");
if ($get_alternative->rowCount() < 0)
    $alternative_row = $get_alternative->fetch();

$get_personal = $db->query("SELECT [ID Card No_] FROM  ".$nav_personal_documents."  WHERE [Employee No_]='" . $usr . "' and Active = '1' and Switch = '8'");
if ($get_personal->rowCount() < 0)
    $personal_row = $get_personal->fetch();

$get_citizen = $db->query("SELECT [Citizenship] FROM  ".$nav_personal_documents."  WHERE [Employee No_]='" . $usr . "' and Active = '1' and Switch = '1'");
if ($get_citizen->rowCount() < 0)
    $citizen_row = $get_citizen->fetch();


if (isset($row_otac))
    $count_otac = count($row_otac);
else
    $count_otac = 0;

if (isset($row_majka))
    $count_majka = count($row_majka);
else
    $count_majka = 0;

if (isset($row_supruznik))
    $count_supruznik = count($row_supruznik);
else
    $count_supruznik = 0;

if (isset($row_dijete))
    $count_dijete = count($row_dijete);
else
    $count_dijete = 0;

if (isset($row_pastorak))
    $count_pastorak = count($row_pastorak);
else
    $count_pastorak = 0;

if (isset($row_srodnici_banka))
    $count_srodnici_banka = count($row_srodnici_banka);
else
    $count_srodnici_banka = 0;

if (isset($rows_education))
    $count_education = count($rows_education);
else
    $count_education = 0;

if (isset($rows_qualification))
    $count_qualification = count($rows_qualification);
else
    $count_qualification = 0;

if (isset($row_jezici))
    $count_jezici = count($row_jezici);
else
    $count_jezici = 0;

if (isset($row_certifikati))
    $count_certifikati = count($row_certifikati);
else
    $count_certifikati = 0;

if (isset($rows_unions))
    $count_unions = count($rows_unions);
else
    $count_unions = 0;

// echo $count_supruznik.'denis';
//print_r($row_otac);
// print_r($row_majka);
//print_r($row_dijete);
// print_r($row_pastorak);
// print_r($row_supruznik);

// hm 02
$x_user = _user(_decrypt($_SESSION['SESSION_USER']));

?>
<body class=" bg-rf">
<style>


    body .dialog-main {
        width: 50% !important;
        margin: 0 auto !important;
        left: 0 !important;
        display: table;
        margin-top: 10% !important;
        position: static !important;
    }

    input[type="text"] {
        line-height: 10px;
        height: 30px;
    }

    select {
        line-height: 10px;
        height: 30px;
    }

    span {
        line-height: 10px;
        height: 30px;
    }

    body {
        line-height: 1;
    }

    .box > .head > .box-head-btn a {
        display: inline-block;
        color: #666;
        font-size: 20px;
        width: 40px;
        height: 25px;
        line-height: 25px;
        text-align: center;
        border-radius: 3px;
    }

    .jconfirm .jconfirm-box div.jconfirm-content-pane .jconfirm-content {
        overflow: auto;
        color: black;
        line-height: 1.5;
    }

    .h4, .h5, .h6, h4, h5, h6 {
        margin-top: 7px;
        margin-bottom: 10px;
    }

    body .tooltip {
        position: absolute !important;
    }
</style>

<!-- START - Main section -->
<section class="full">

    <br/>
    <div style="display:none;" class="localStorageRemoval">0</div>
    <div class="container-fluid">

        <div id="res"></div>

        <form action="" method="post" id="profile-form">

            <input type="hidden" name="request" value="profile-edit"/>
            <input type="hidden" name="request_id" value="<?php echo $row['employee_no']; ?>"/>
            <!--- hm 01 -- obrisati liniju 187, nepotrebna ----->

            <!--- hm 01 end --->
            <div class="row" style="background: rgb(85,142,170);
background: linear-gradient(180deg, rgba(85,142,170,1) 0%, rgba(209,209,212,1) 9%, rgba(255,255,255,1) 57%);" >
                <div class="col-sm-8">
                    <?php if ($_user['B_1_description'] == 'KADROVSKI POSLOVI') { ?>

                        <h2 style="height: 50px;">
                            <?php echo __('Moj profil'); ?> &rarr; <?php echo __('Ažuriranje!'); ?><br/><br/>
                        </h2>

                    <?php } else { ?>

                        <h2>
                            <?php echo __('Moj Karton'); ?><br/><br/>
                        </h2>

                    <?php } ?>
                </div>

                <?php if ($_user['B_1_description'] == 'KADROVSKI POSLOVI2' or $countParent > 199990) { ?>
                    <div class="col-sm-4" style="margin-top: 15px;">
                        <label class="lable-admin1" style="width: 50px;"><?php echo __('Ime'); ?></label>
                        <select readonly id="ime_prezime" name="ime_prezime" class="" style="outline:none;width:200px;"
                                class="form-control" onchange="insertParam('u', this.value);">
                            <?php echo _optionNameEditProfile($row['employee_no'], $filtertdate) ?>
                        </select>
                        <button type="button" class="btn btn-red btn-lg"
                                style="  background:linear-gradient(#2396bf, #006595 40%, #004961 3%, #004961);margin-left: 5px;height: 40px; display:none;" onClick="SyncNAV_user();">Sync NAV
                        </button>
                    </div>
                <?php } ?>


                <div class="<?php if ($_user['B_1_description'] == 'KADROVSKI POSLOVI' or $countParent > 0) {
                    echo 'col-sm-4';
                } else {
                    echo 'col-sm-4';
                } ?>"><br/>

                    <?php if ($_user['B_1_description'] == 'KADROVSKI POSLOVI') { ?>
                        <button type="button" class="btn btn-red btn-lg" style="display:none;" onClick="SyncNAV();">Sync
                            NAV Global
                        </button>
                    <?php } ?>



                </div>


            </div>


            <div class="">


                <div class="col-sm-4" style="padding-left:0%;padding-right:2%;width: 40%;">

                    <div class="col-sm-12" style="padding-left:0%">

                        <div class="box">

                            <!-- <div class="head">
                       <div class="box-head-btn">
                                 <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse" data-id="c1a"></a>
                               </div>
                         </div>-->

                            <div class="content" id="c1a" style="display: block; width:80%; margin:auto; padding-bottom:0%;  ">

                                <div class="row">

                                    <div class=".col-6 .col-md-4">
                                        <label><?php echo __('Ime'); ?></label>
                                        <input type="text" id="fname" name="fname" value="<?php echo $row['fname']; ?>"
                                               class="form-control" disabled required>
                                    </div>

                                    <div class=".col-6 .col-md-4">
                                        <label><?php echo __('Prezime'); ?></label>
                                        <input type="text" id="lname" name="lname"
                                               value="<?php echo _optionGetLastNameNAV($usr); ?>" class="form-control"
                                               style="background:#6dacc9; color:white;" disabled>
                                    </div>

                                </div>
                                <br/>

                                <div class="row">
                                    <div class=".col-6 .col-md-4 text-center">
                                        <?php if ($row['image'] != 'none') { ?>
                                            <?php

                                            echo '<img src="' . $row['picture'] . '" class="img-circle" style="width: 170px;height: 170px;"  alt="" />';

                                            ?>
                                        <?php } else { ?>
                                            <img src="<?php echo $_themeUrl; ?>./images/avatar.png"
                                                 class="img-circle" style="width:70%;">
                                        <?php } ?>
                                    </div>

                                    <div class=".col-6 .col-md-4">
                                        <label><?php echo __('Fotografija'); ?></label>
                                        <input type="file" name="media_file" id="file-1"
                                               class="inputfile inputfile-1 big" accept="image/jpeg, image/png"
                                               data-multiple-caption="{count} files selected"/>
                                        <label for="file-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="25"
                                                 viewBox="0 0 20 17">
                                                <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/>
                                            </svg>
                                            <span ><?php echo __('Odaberi'); ?>&hellip;</span>
                                        </label>

                                        <small><?php echo __('Dozvoljeni format je JPG. Preporučene dimenzije 225x225 px (portret).'); ?></small>
                                    </div>
                                </div>
                                <hr/>
                                <?php if ($countParent > 0) { ?>
                                    <label><?php echo __('Zamjenik'); ?></label>
                                    <select name="zamjenik" class="form-control">
                                        <?php echo _optionPodredzeni($row['zamjenik']); ?>
                                    </select><br/>
                                <?php } ?>
                            </div>
                        </div>

                    </div>


                    <div class="col-sm-12" style="margin-top: -3%;padding-left: 0%;">

                        <div class="box" style="margin-bottom:3%;">
                            <div class="head" style="height: 42px; padding-top: 1px; padding-bottom: 1px;">
                                <div class="box-head-btn">
                                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse"
                                       data-id="c1e"></a>
                                </div>
                                <h5><?php echo __('Lični podaci'); ?></h5>
                            </div>

                            <div class="content" id="c1e" style="padding-bottom: 0%;">


                                <label style="font-size:12px;"><?php echo __('JMBG'); ?></label>
                                <input type="text" name="JMBG" value="<?php echo $row_personal['Employee ID']; ?>"
                                       class="form-control" disabled><br/>

                                <label style="font-size:12px;"><?php echo __('Djevojačko prezime'); ?></label>
                                <input type="text" name="maiden_lname"
                                       value="<?php echo $row_personal['Maiden Name']; ?>" class="form-control"
                                       style="background:#6dacc9; color:white;"><br/>


                            </div>
                        </div>

                    </div>

                    <div class="col-sm-12" style="padding-left:0%">

                        <div class="box" >
                            <div class="head" style="height: 42px; padding-top: 1px; padding-bottom: 1px;">
                                <div class="box-head-btn">
                                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse"
                                       data-id="c1f"></a>
                                </div>
                                <h5><?php echo __('Podaci o rođenju'); ?></h5>
                            </div>

                            <div class="content" id="c1f" style="padding-bottom: 2%;">


                                <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                                    <div class="col-sm-4" style="font-size:12px;padding-left: 0%;">
                                        <label><?php echo __('Datum rođenja'); ?></label>
                                        <input type="text" name="birth_date"
                                               value="<?php echo date('d.m.Y', strtotime($row_personal['Birth Date'])); ?>"
                                               class="form-control"><br/>
                                    </div>
                                </div>


                                <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                                    <div class="col-sm-4" style="padding-left: 0%;">
                                        <label style=""><?php echo __('Šifra opštine rođenja'); ?></label>
                                        <select id="code_sifra_rodjenje_mun" name="code_sifra_rodjenje_mun" class=""
                                                style="outline:none;width:100%;" class="form-control"
                                        onchange="fillMunName();">
                                        <?php echo _optionMunicipalityCodeNAV($row_personal['Municipality Code of Birth']); ?>
                                        </select>
                                    </div>

                                    <div class="col-sm-8" style="padding-left: 0%;">
                                        <label style=""><?php echo __('Naziv opštine rođenja'); ?></label>
                                        <input type="text" id="mun_name" name="mun_name" style="background:#6dacc9; color:white;"
                                               value="<?php echo $row_personal['Municipality Name of Birth']; ?>"
                                               class="form-control" style="width: 100%;"><br/>
                                    </div>
                                </div>

                                <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                                    <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                                        <label><?php echo __('Mjesto rođenja'); ?></label>
                                        <input type="text" maxlength="30" ;" id="mjesto_rodjenja" name="mjesto_rodjenja"
                                        value="<?php echo $row_personal['Place of birth']; ?>"
                                        class="form-control""><br/>
                                    </div>
                                </div>


                                <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">

                                    <div class="col-sm-4" style="width:35%;padding-left: 0%;">
                                        <label style=""><?php echo __('Šifra države rođenja'); ?></label>
                                        <select id="code_sifra_rodjenje_drz" name="code_sifra_rodjenje_drz" class=""
                                                style="background:#6dacc9; color:white;outline:none;width:100%;"
                                                class="form-control">
                                            <?php echo _optionCountryCodeNAVBirth($row_personal['Country_Region Code of Birth']); ?>
                                        </select>
                                    </div>

                                    <div class="col-sm-8" style="width:65%;padding-left: 0%;">

                                        <label style="font-size:12px;"><?php echo __('Grad rođenja'); ?></label>
                                        <select id="grad_rodjenja" name="grad_rodjenja" class=""
                                                style="outline:none;width:100%;background:#6dacc9;  color:white;"
                                                class="form-control">
                                            <?php echo _optionCityCodeNAVBirth($row_personal['City of Birth']) ?>
                                        </select>


                                    </div>


                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-sm-8" style="padding-left:0%;width: 60%;">
                    <div class="col-sm-12" style="font-size:12px; ">

                        <div class="box" style="margin-bottom:1%; ">
                            <div class="head" style="height: 42px; padding-top: 0.5%; padding-bottom: 0.5%;  border-radius:10px;">

                                <div class="box-head-btn"">
                                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse"
                                       data-id="c1d"></a>
                                </div>


                                <h5><?php echo __('Podaci o stanovanju'); ?></h5>

                            </div>
                            <div class="content" id="c1d" style="display: none;padding-bottom: 0%;">


                                <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                                    <div class="col-sm-4" style="font-size:12px;padding-left: 0%;">
                                        <label><?php echo __('Adresa (CIPS)'); ?></label>
                                        <input type="text" id="Address_CIPS" name="Address CIPS"
                                               value="<?php echo $alternative_row['Address CIPS']; ?>"
                                               class="form-control" style="color:black;"><br>
                                    </div>
                                </div>


                                <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">

                                    <div class="col-sm-4" style="font-size:12px;padding-left: 0%;">
                                        <label><?php echo __('Šifra opštine (CIPS)'); ?></label>
                                        <input type="text" id="zip_cips" name="zip_cips"
                                               value="<?php echo $alternative_row['Municipality Code CIPS']; ?>"
                                               class="form-control" style="color:black;"><br>
                                    </div>

                                    <div class="col-sm-4" style="font-size:12px;padding-left: 0%;">
                                        <label><?php echo __('Naziv opštine (CIPS)'); ?></label>
                                        <input type="text" id="municipality_name_cips" name="municipality_name_cips"
                                               value="<?php echo $alternative_row['Municipality Name CIPS']; ?>"
                                               class="form-control" style="color:black;"><br>
                                    </div>

                                </div>

                                <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">

                                    <div class="col-sm-4" style="font-size:12px;padding-left: 0%;">
                                        <label><?php echo __('Grad (CIPS)'); ?></label>
                                        <input type="text" id="city_cips" name="city_cips"
                                               value="<?php echo $alternative_row['City CIPS']; ?>" class="form-control"
                                               style="color:black; "><br>
                                    </div>

                                    <div class="col-sm-4" style="font-size:12px;padding-left: 0%;">
                                        <label><?php echo __('Poštanski broj (CIPS)'); ?></label>
                                        <input type="text" id="postal_num_cips" name="postal_num_cips"
                                               value="<?php echo $alternative_row['Post Code CIPS']; ?>"
                                               class="form-control" style="color:black;">
                                    </div>

                                </div>

                            </div>
                        </div>
        </form>
    </div>


    <div class="col-sm-12" style="font-size:12px;">

        <div class="box" style="margin-bottom:1%;">
            <div class="head" style="height: 42px; padding-top: 0.5%; padding-bottom: 0.5%;">
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse" data-id="c1b"></a>
                </div>

                <h5><?php echo __('Kontakt informacije'); ?></h5>

            </div>

            <div class="content" id="c1b" style="display: none;padding-bottom: 0%;">

                <div class="col-sm-12" style="padding-left: 0%;">
                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Kućni telefonski broj'); ?></label>
                        <select id="country_region" name="country_region" class="" style="outline:none;width:100%;"
                                class="form-control">
                            <?php echo _optionCountryCodeNAV($row_personal['Country_Region Code Home']) ?>
                        </select><br/><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Regionalni kod'); ?></label>
                        <select id="country_region_region" name="country_region_region" class=""
                                style="outline:none;width:100%;" class="form-control">
                            <?php echo _optionRegionCodeNAVHome($row_personal['Dial Code Home']) ?>
                        </select><br/><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Broj'); ?></label>
                        <input type="text" maxlength="8" id="Phone_No" name="Phone_No"
                               title="Obavezan broj telefona u formatu: 123 456"
                               value="<?php echo $row_personal['Phone No_']; ?>" class="form-control"
                               style="width:100%;display: inline;"
                               onkeypress='return ((event.charCode >= 48 && event.charCode <= 57) || (event.charCode ==32))'><br/>
                    </div>
                </div>

                <div class="col-sm-12" style="padding-left: 0%;">
                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Broj privatnog mobitela'); ?></label>
                        <select id="country_region_mobile" name="country_region_mobile" class=""
                                style="outline:none;width:100%;" class="form-control">
                            <?php echo _optionCountryCodeNAV($row_personal['Country_Region Code Mobile']) ?>
                        </select><br/><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Regionalni kod'); ?></label>
                        <select id="country_region_region_mobile" name="country_region_region_mobile" class=""
                                style="outline:none;width:100%;" class="form-control">
                            <?php echo _optionRegionCodeNAVMobile($row_personal['Dial Code Mobile']) ?>
                        </select><br/><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Broj'); ?></label>
                        <input type="text" maxlength="8" id="phone_mob" name="phone_mob"
                               title="Obavezan broj telefona u formatu: 123 456"
                               value="<?php echo $row_personal['Mobile Phone No_']; ?>" class="form-control"
                               style="width:100%;display: inline;"
                               onkeypress='return ((event.charCode >= 48 && event.charCode <= 57) || (event.charCode ==32))'><br/>
                    </div>
                </div>

                <div class="col-sm-12" style="padding-left: 0%;">
                    <div class="col-sm-12" style="padding-left: 0%;">
                        <label><?php echo __('Privatna email adresa'); ?></label>
                        <input type="text" maxlength="60" id="email" name="email"
                               value="<?php echo $row_personal['E-Mail']; ?>" class="form-control" style="width:31.5%;"><br/>
                    </div>
                </div>

                <div class="col-sm-12" style="padding-left: 0%;">
                    <div class="col-sm-4" style="width:33%;padding-left: 0%;">
                        <label><?php echo __('Ime i prezime kontakt osobe u hitnom slučaju'); ?></label>
                        <input type="text" maxlength="50" id="fname_lname_emergency_person"
                               name="fname_lname_emergency_person"
                               value="<?php echo $row_personal['Related Person to be informed']; ?>"
                               class="form-control" style="width: 100%;"><br/>
                    </div>

                    <div class="col-sm-5" style="padding-left: 0%;">
                        <label style=""><?php echo __('U kakvom je odnosu radnik sa kontakt osobom za hitni slučaj'); ?></label>
                        <input type="text" maxlength="30" id="relationship_emergency_person"
                               name="relationship_emergency_person" style="width: 80%;"
                               value="<?php echo $row_personal['Relationship with Related Per_']; ?>"
                               class="form-control"><br/>
                    </div>

                </div>

                <div class="col-sm-12" style="padding-left: 0%;">
                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Broj telefona kontakt osobe'); ?></label>
                        <select id="country_rel_person" name="country_rel_person" class=""
                                style="outline:none;width:100%;" class="form-control">
                            <?php echo _optionCountryCodeNAV($row_personal['Country_Region Code Emergency']) ?>
                        </select><br/><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Regionalni kod'); ?></label>
                        <select id="country_region_region_rel_person" name="country_region_region_rel_person" class=""
                                style="outline:none;width:100%;" class="form-control">
                            <?php echo _optionRegionCodeNAVHome($row_personal['Dial Code Emergency']) ?>
                        </select><br/><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Broj'); ?></label>
                        <input type="text" maxlength="8" title="Obavezan broj telefona u formatu: 123 456"
                               id="phone_emergency_person" name="phone_emergency_person"
                               value="<?php echo $row_personal['Phone No_ Emergency']; ?>" class="form-control"
                               style="width:100%;display: inline;"
                               onkeypress='return ((event.charCode >= 48 && event.charCode <= 57) || (event.charCode ==32))'><br/>
                    </div>
                </div>


            </div>
        </div>

    </div>

    <div class="col-sm-12" style="font-size:12px;">

        <div class="box" style="margin-bottom:1%;">
            <div class="head" style="height: 42px; padding-top: 0.5%; padding-bottom: 0.5%;">
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse" data-id="c1o"></a>
                </div>
                <h5><?php echo __('Podaci o roditeljima'); ?></h5>
            </div>

            <div class="content" id="c1o" style="display: none;padding-bottom: 1%;">
                <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Ime oca'); ?></label>
                        <input type="text" maxlength="30" id="ime_oca" name="ime_oca"
                               value="<?php if ($count_otac > 0) {
                                   echo $row_otac[0]['First Name'];
                               } else echo ''; ?>" class="form-control" style="">
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Prezime oca'); ?></label>
                        <input type="text" maxlength="30" id="prezime_oca" name="prezime_oca"
                               value="<?php if ($count_otac > 0) {
                                   echo $row_otac[0]['Last Name'];
                               } else echo ''; ?>" class="form-control"><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Datum rođenja oca'); ?></label>
                        <input type="text" id="rodjenje_otac" name="rodjenje_otac" value="<?php if ($count_otac > 0) {
                            if ($row_otac[0]['Birth Date'] != '1753-01-01 00:00:00.000') {
                                echo date('d.m.Y', strtotime($row_otac[0]['Birth Date']));
                            } else {
                                echo '';
                            }
                        } else {
                            echo '';
                        } ?>" class="form-control"><br/>
                    </div>
                </div>

                <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Ime  majke'); ?></label>
                        <input type="text" maxlength="30" id="ime_majke" name="ime_majke"
                               value="<?php if ($count_majka > 0) {
                                   echo $row_majka[0]['First Name'];
                               } else echo ''; ?>" class="form-control"><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Prezime majke'); ?></label>
                        <input type="text" maxlength="30" id="prezime_majke" name="prezime_majke"
                               value="<?php if ($count_majka > 0) {
                                   echo $row_majka[0]['Last Name'];
                               } else echo ''; ?>" class="form-control"><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Datum rođenja majke'); ?></label>
                        <input type="text" id="rodjenje_majka" name="rodjenje_majka"
                               value="<?php if ($count_majka > 0) {
                                   if ($row_majka[0]['Birth Date'] != '1753-01-01 00:00:00.000') {
                                       echo date('d.m.Y', strtotime($row_majka[0]['Birth Date']));
                                   } else {
                                       echo '';
                                   }
                               } else {
                                   echo '';
                               } ?>" class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" style="padding-left: 0%;">
                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Majčino djevojačko prezime'); ?></label>
                        <input type="text" maxlength="30" id="m_djevojacko" name="m_djevojacko"
                               value="<?php if ($count_majka > 0) {
                                   echo $row_personal['Mother Maiden Name'];
                               } else echo ''; ?>" class="form-control">
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-sm-12" style="font-size:12px;">

        <div class="box" style="margin-bottom:1%;">
            <div class="head" style="height: 42px; padding-top: 0.5%; padding-bottom: 0.5%;">
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse" data-id="c1n"></a>
                </div>
                <h5><?php echo __('Podaci o porodičnom stanju'); ?></h5>
            </div>

            <div class="content" id="c1n" style="display: none;padding-bottom: 0%;">

                <div class="col-sm-12" style="padding-left: 0%;">
                    <div class="col-sm-12" style="padding-left: 0%;">
                        <label style=""><?php echo __('Bračni status'); ?></label></br>
                        <select id="m_status" name="m_status" class="" style="outline:none;width:32%;"
                                class="form-control">
                            <?php echo _optionMaritalStatus($row_personal['Marital status']) ?>
                        </select><br/><br/>
                    </div>
                </div>

                <div class="col-sm-12" style="padding-left: 0%;">
                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Ime supružnika'); ?></label>
                        <input type="text" id="ime_supruznika" name="ime_supruznika"
                               value="<?php if ($count_supruznik > 0) {
                                   echo $row_supruznik[0]['First Name'];
                               } else echo ''; ?>" class="color:black;"><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Prezime supružnika'); ?></label>
                        <input type="text" id="prezime_supruznika" name="prezime_supruznika"
                               value="<?php if ($count_supruznik > 0) {
                                   echo $row_supruznik[0]['Last Name'];
                               } else echo ''; ?>" class="form-control" style="color:black;"><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Datum rođenja supružnika'); ?></label>
                        <input type="text" id="rodjenje_supruznik" name="rodjenje_supruznik"
                               value="<?php if ($count_supruznik > 0) {
                                   if ($row_supruznik[0]['Birth Date'] != '1753-01-01 00:00:00.000') {
                                       echo date('d.m.Y', strtotime($row_supruznik[0]['Birth Date']));
                                   } else {
                                       echo '';
                                   }
                               } else {
                                   echo '';
                               } ?>" class="form-control" style="color:black;"><br/>
                    </div>
                </div>

                <?php if ($count_dijete > 0) {
                    foreach ($row_dijete as $key => $dijete) { ?>

                        <div class="col-sm-12" style="padding-left: 0%;">
                            <div class="col-sm-4" style="padding-left: 0%;">
                                <label><?php echo __('Ime dijeteta'); ?></label>
                                <input type="text" id="ime_dijeteta<?php echo $key; ?>" name="ime_dijeteta"
                                       value="<?php echo $dijete['First Name']; ?>" class="form-control"
                                       style="color:black;"><br/>
                            </div>

                            <div class="col-sm-4" style="padding-left: 0%;">
                                <label><?php echo __('Prezime dijeteta'); ?></label>
                                <input type="text" id="prezime_dijeteta<?php echo $key; ?>" name="prezime_dijeteta"
                                       value="<?php echo $dijete['Last Name']; ?>" class="form-control"
                                       style="color:black;"><br/>
                            </div>

                            <div class="col-sm-4" style="padding-left: 0%;">
                                <label><?php echo __('Datum rođenja dijeteta'); ?></label>
                                <input type="text" id="rodjenje_dijete<?php echo $key; ?>" name="rodjenje_dijete"
                                       value="<?php if ($dijete['Birth Date'] != '1753-01-01 00:00:00.000') {
                                           echo date('d.m.Y', strtotime($dijete['Birth Date']));
                                       } else {
                                           echo '';
                                       } ?>" class="form-control" style="color:black;"><br/>
                            </div>
                        </div>

                    <?php }
                } ?>

                <?php if ($count_pastorak > 0) {
                    foreach ($row_pastorak as $key => $pastorak) { ?>

                        <div class="col-sm-4" style="padding-left: 0%;">
                            <label><?php echo __('Ime dijeteta'); ?></label>
                            <input type="text" id="ime_pastorka<?php echo $key; ?>" name="ime_pastorka"
                                   value="<?php echo $pastorak['First Name']; ?>" class="form-control"
                                   style="color:black;"><br/>
                        </div>

                        <div class="col-sm-4" style="padding-left: 0%;">
                            <label><?php echo __('Prezime dijeteta'); ?></label>
                            <input type="text" id="prezime_pastorka<?php echo $key; ?>" name="prezime_dijeteta"
                                   value="<?php echo $pastorak['Last Name']; ?>" class="form-control"
                                   style="color:black;"><br/>
                        </div>

                        <div class="col-sm-4" style="padding-left: 0%;">
                            <label><?php echo __('Datum rođenja dijeteta'); ?></label>
                            <input type="text" id="rodjenje_pastorka<?php echo $key; ?>" name="rodjenje_dijete"
                                   value="<?php if ($pastorak['Birth Date'] != '1753-01-01 00:00:00.000') {
                                       echo date('d.m.Y', strtotime($pastorak['Birth Date']));
                                   } else {
                                       echo '';
                                   } ?>"><br/>
                        </div>

                    <?php }
                } ?>

            </div>
        </div>

    </div>

    <div class="col-sm-12" style="font-size:12px;">

        <div class="box" style="margin-bottom:1%;">
            <div class="head" style="height: 42px; padding-top: 0.5%; padding-bottom: 0.5%;">
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse" data-id="c1m"></a>
                </div>
                <h5><?php echo __('Podaci o rodbinskim odnosima'); ?></h5>
            </div>

            <div class="content" id="c1m" style="display: none;padding-bottom: 1%;">

                <label style="margin-bottom: 2%;"><?php echo __('Srodnici u banci'); ?></label>
                <?php if ($count_srodnici_banka > 0) {
                    foreach ($row_srodnici_banka as $key => $srodnik) { ?>

                        <div class="col-sm-12" style="padding-left: 0%;">
                            <div class="col-sm-4" style="padding-left: 0%;">
                                <label><?php echo __('Prezime srodnika'); ?></label>
                                <input disabled="disabled" type="text" id="ime_srodnika<?php echo $key; ?>"
                                       name="ime_srodnika" value="<?php echo $srodnik['First Name']; ?>"
                                       class="form-control"><br/>
                            </div>

                            <div class="col-sm-4" style="padding-left: 0%;">
                                <label><?php echo __('Ime srodnika'); ?></label>
                                <input disabled="disabled" type="text" id="prezime_srodnika<?php echo $key; ?>"
                                       name="prezime_srodnika" value="<?php echo $srodnik['Last Name']; ?>"
                                       class="form-control"><br/>
                            </div>

                            <div class="col-sm-4" style="padding-left: 0%;">
                                <label><?php echo __('Datum rođenja srodnika'); ?></label>
                                <input disabled="disabled" type="text" id="rodjenje_srodnika<?php echo $key; ?>"
                                       name="rodjenje_srodnika"
                                       value="<?php echo date('d.m.Y', strtotime($srodnik['Birth Date'])); ?>"
                                       class="form-control"><br/>
                            </div>
                        </div>

                    <?php }
                } ?>
                <br/>
                <hr>
                <br/>
                <div class="col-sm-12 wide-tt" style="font-size:12px;padding-left: 0%;" class="form-control "
                     style="width:100%;display: inline;"
                     onkeypress='return ((event.charCode >= 48 && event.charCode <= 57) || (event.charCode ==32))'><br/>
                    <div class="col-sm-4 srodnik tt-show" style="font-size:12px;padding-left: 0%;"
                         title="Nakon unosa podataka o srodnicima, iste niste u mogućnosti brisati niti editovati. Za sve promjene potrebno se obratiti Kadrovskoj službi ili poslati mail na adresu HRpodrska">
                        <label><?php echo __('Srodnik'); ?></label><br/>

                        <select id="ime_prezime_srodnik" name="ime_prezime_srodnik" class=""
                                style="outline:none;width:100%;" class="form-control" class="form-control"
                                style="width:100%;display: inline;"
                                onkeypress='return ((event.charCode >= 48 && event.charCode <= 57) || (event.charCode ==32))'><br/>>>
                            <?php echo _optionSrodnici('', $filtertdate) ?>
                        </select>
                    </div>

                    <div class="col-sm-4 tt-show" style="font-size:12px;padding-left: 0%;"
                         title="Nakon unosa podataka o srodnicima, iste niste u mogućnosti brisati niti editovati. Za sve promjene potrebno se obratiti Kadrovskoj službi ili poslati mail na adresu HRpodrska">
                        <label><?php echo __('Srodstvo'); ?></label><br/>
                        <select id="srodstvo_srodnik" name="srodstvo_srodnik" class="" style="outline:none;width:100%;"
                                class="form-control" onchange="insertSrodnik();">
                            <?php echo _optionSrodstvo('') ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-sm-12" style="font-size:12px;">

        <div class="box" style="margin-bottom:1%;">
            <div class="head" style="height: 42px; padding-top: 0.5%; padding-bottom: 0.5%;">
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse" data-id="c1l"></a>
                </div>
                <h5><?php echo __('Lični dokumenti'); ?></h5>
            </div>

            <div class="content" id="c1l" style="display: none;padding-bottom: 0%;">


                <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                    <div class="col-sm-4" style="font-size:12px;padding-left: 0%;">
                        <label><?php echo __('Broj lične karte'); ?></label>
                        <input type="text" id="br_karte" name="br_karte"
                               value="<?php echo $personal_row['ID Card No_']; ?>" class="form-control"
                               style="color:black;"><br/>
                    </div>

                    <div class="col-sm-4" style="font-size:12px;padding-left: 0%;">
                        <label><?php echo __('Državljanstvo 1'); ?></label>
                        <input type="text" id="drz1" name="drz1" value="<?php if ($citizen_row['Citizenship'] != '') {
                            echo _optionGetCounryDescNAV($citizen_row['Citizenship']);
                        } else {
                            echo '';
                        } ?>" class="form-control"><br/>
                    </div>
                </div>

                <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                    <div class="col-sm-2" style="font-size:12px;padding-left: 0%;">
                        <label><?php echo __('Vozačka dozvola'); ?></label>
                        <select id="vozacka" name="vozacka" class="" style="outline:none;width:100%;"
                                class="form-control">
                            <?php echo _optionVozackaNAV($row_personal['Driving Licence']) ?>
                        </select>
                    </div>

                    <div class="col-sm-2" style="font-size:12px;padding-left: 2%;">
                        <label><?php echo __('Kategorija'); ?></label>
                        <select id="kategorija" name="kategorija" class="" style="outline:none;width:100%;"
                                class="form-control">
                            <?php echo _optionTipVozackeNAV($row_personal['Driving Llicence Category']) ?>
                        </select>
                    </div>

                    <div class="col-sm-4" style="font-size:12px;padding-left: 0%;">
                        <label><?php echo __('Aktivan vozač'); ?></label>
                        <select id="akt_vozac" name="akt_vozac" class="" style="outline:none;width:100%;"
                                class="form-control">
                            <?php echo _optionAktivanVozacNAV($row_personal['Active Driver']) ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-12" style="font-size:12px;margin-top: 1%;padding-left: 0%;">
                    <div class="col-sm-4" style="font-size:12px;padding-left: 0%;">
                        <label><?php echo __('Darivalac krvi'); ?></label>
                        <?php if ($row_personal['Blood Donor'] == 0) {
                            $darivalac_krvi = 'NE';
                        } else {
                            $darivalac_krvi = 'DA';
                        } ?>
                        <input type="text" id="dar_krvi" name="dar_krvi" value="<?php echo $darivalac_krvi; ?>"
                               class="form-control" style="color:black;"><br/>
                    </div>

                    <div class="col-sm-4" style="font-size:12px;padding-left: 0%;">
                        <label style="padding-right: 3%;"><?php echo __('Krvna grupa'); ?></label>
                        <select id="krvna_grupa" name="krvna_grupa" class="" style="outline:none;width:100%;"
                                class="form-control">
                            <?php echo _optionBloodTypeNAV($row_personal['Blood Type']) ?>
                        </select>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="col-sm-12" style="font-size:12px;">

        <div class="box" style="margin-bottom:1%;">
            <div class="head" style="height: 42px; padding-top: 0.5%; padding-bottom: 0.5%;">
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse" data-id="c1k"></a>
                </div>
                <h5><?php echo __('Zdravstveno stanje'); ?></h5>
            </div>

            <div class="content" id="c1k" style="display: none;padding-bottom: 0%;">

                <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                    <div class="col-sm-2" style="font-size:12px;padding-left: 0%;">
                        <label><?php echo __('Invalid'); ?></label>
                        <?php if ($row_personal['Disabled Person'] == 0) {
                            $invalid = 'NE';
                        } else {
                            $invalid = 'DA';
                        } ?>
                        <input type="text" name="invalid" value="<?php echo $invalid; ?>" class="form-control"
                               style="color:black;"><br/>
                    </div>

                    <div class="col-sm-2" style="font-size:12px;padding-left: 0%;">
                        <label><?php echo __('Stepen invalidnosti'); ?></label>
                        <input type="text" name="stepen_invalidnosti"
                               value="<?php echo _optionGetDisabilityLevelNAV($row_personal['Disability Level']); ?>"
                               class="form-control" style="color:black;"><br/>
                    </div>
                    <div class="col-sm-2" style="font-size:12px;padding-left: 0%;">
                        <label><?php echo __('Hronične bolesti'); ?></label>
                        <?php if ($row_personal['Chronic Disease'] == 0) {
                            $chronic_disease = 'NE';
                        } else {
                            $chronic_disease = 'DA';
                        } ?>
                        <input type="text" name="hr_bolesti" value="<?php echo $chronic_disease; ?>"
                               class="form-control" style="color:black;"><br/>
                    </div>

                    <div class="col-sm-2" style="font-size:12px;;padding-left: 0%;">
                        <label style="width: 250px !important; max-width: 250px !important;"><?php echo __('Dijete sa posebnim potrebama'); ?></label>
                        <?php if ($row_personal['Disabled Child'] == 0) {
                            $disabled_child = 'NE';
                        } else {
                            $disabled_child = 'DA';
                        } ?>
                        <input type="text" name="d_posebne_potrebe" value="<?php echo $disabled_child; ?>"
                               class="form-control" style="color:black;">
                    </div>
                </div>


            </div>
        </div>

    </div>

    <div class="col-sm-12" style="font-size:12px;">

        <div class="box" style="margin-bottom:1%;">
            <div class="head" style="height: 42px; padding-top: 0.5%; padding-bottom: 0.5%;">
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse" data-id="c1j"></a>
                </div>
                <h5><?php echo __('Školovanje'); ?></h5>
            </div>

            <?php if ($count_education > 0 or $count_qualification > 0) { ?>
                <div class="content" id="c1j" style="display: none;padding-bottom: 0%;">
                    <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                        <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                            <label style=""><?php echo __('Stručna sprema'); ?></label>
                            <input type="text" id="str_sprema" name="str_sprema"
                                   value="<?php echo _inputEducationLevelNAV(@$rows_education['Education Level']); ?>"
                                   class="form-control" style="color:black;"><br/>
                        </div>

                        <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                            <label><?php echo __('Završena obrazovna ustanova'); ?></label>
                            <input type="text" id="ob_institucija" name="ob_institucija"
                                   value="<?php echo @$rows_education['School of Graduation']; ?>" class="form-control"
                                   style="color:black;"><br/>
                        </div>

                        <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                            <label><?php echo __('Zvanje'); ?></label>
                            <input type="text" id="zvanje" name="zvanje"
                                   value="<?php echo @$rows_education['Title Description']; ?>" class="form-control"
                                   style="color:black;"><br/>
                        </div>

                        <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                            <label><?php echo __('Struka'); ?></label>
                            <input type="text" id="struka" name="struka"
                                   value="<?php echo @$rows_education['Profession Description']; ?>"
                                   class="form-control" style="color:black;"><br/>
                        </div>
                    </div>
                    <div class="col-sm-12" style="font-size:12px;padding-left: 0%;">
                        <div class="col-sm-6" style="font-size:12px;padding-left: 0%;">
                            <label><?php echo __('Broj certifikata/licenci'); ?></label>
                            <input type="text" id="br_cert" name="br_cert" value="<?php echo $count_certifikati; ?>"
                                   class="form-control" style="color:black;"><br/>
                            <?php if ($count_certifikati > 0) {
                                foreach ($row_certifikati as $key => $certifikat) { ?>

                                    <div id="kvalOpt<?php echo $certifikat['Line No_']; ?>" class="col-sm-12"
                                         style="font-size:12px;padding-left: 0%;margin-top: 2%;">
                                        <div class="col-sm-5" style="padding-left: 0%;">
                                            <label><?php echo __('Naziv institucije'); ?></label>
                                            <select id="certifikat_kompanija<?php echo $certifikat['Line No_']; ?>"
                                                    name="certifikat_kompanija[]" class=""
                                                    style="outline:none;width:100%;" class="form-control">
                                                <?php echo _optionInstitutionCodeNAV($certifikat['Institution_Company']) ?>
                                            </select>
                                        </div>

                                        <div class="col-sm-6" style="padding-left: 0%;">
                                            <label><?php echo __('Opis'); ?></label>
                                            <select id="certifikat<?php echo $certifikat['Line No_']; ?>"
                                                    name="certifikat_opis[]" class="" style="outline:none;width:100%;"
                                                    class="form-control">
                                                <?php echo _optionCertifikatCodeNAV($certifikat['Description']) ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-1" style="padding-left: 0%;margin-top: 8%;">
                                            <a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
                                               class="table-btn1" data-widget="remove-kvalifikacija"
                                               data-id="kvalifikacija_remove:<?php echo $certifikat['Line No_']; ?>"
                                               data-text="<?php echo __('Dali ste sigurni da želite obrisati certifikat?'); ?>"
                                               title="Obriši"><i class="ion-android-close"></i></a>
                                        </div>
                                        <input type="hidden" name="line_no_certifikat[]"
                                               value="<?php echo $certifikat['Line No_']; ?>"/>
                                    </div>

                                <?php }
                            } ?>
                            <a id="unos_certifikata"
                               href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_cert_add.php' ?>"
                               data-widget="ajax-kvalifikacija" data-id="opt2" data-width="50%"
                               class="btn btn-sm btn-red" style="margin-bottom:2%;margin-top:2%;float: left;">Novi unos
                                <i class="ion-ios-plus-empty"></i></a>
                        </div>

                        <div class="col-sm-6" style="font-size:12px;padding-left: 0%;">
                            <label><?php echo __('Broj stranih jezika'); ?></label>
                            <input type="text" id="br_jezik" name="br_jezik" value="<?php echo $count_jezici; ?>"
                                   class="form-control" style="color:black;"><br/>
                            <?php if ($count_jezici > 0) {
                                foreach ($row_jezici as $key => $jezik) { ?>

                                    <div id="kvalOpt<?php echo $jezik['Line No_']; ?>" class="col-sm-12"
                                         style="font-size:12px;padding-left: 0%;margin-top: 2%;">
                                        <div class="col-sm-5" style="padding-left: 0%;">
                                            <label><?php echo __('Jezik'); ?></label>
                                            <select id="jezik<?php echo $jezik['Line No_']; ?>" name="jezik[]" class=""
                                                    style="outline:none;width:100%;" class="form-control">
                                                <?php echo _optionLanguageCodeNAV($jezik['Language Name']) ?>
                                            </select>
                                        </div>

                                        <div class="col-sm-6" style="padding-left: 0%;">
                                            <label><?php echo __('Nivo'); ?></label>
                                            <select id="nivo_jezik<?php echo $jezik['Line No_']; ?>" name="nivo_jezik[]"
                                                    class="" style="outline:none;width:100%;" class="form-control">
                                                <?php echo _optionLanguageLevelNAV($jezik['Language Level']) ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-1" style="padding-left: 0%;margin-top: 8%;">
                                            <a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
                                               class="table-btn1" data-widget="remove-kvalifikacija"
                                               data-id="kvalifikacija_remove:<?php echo $jezik['Line No_']; ?>"
                                               data-text="<?php echo __('Dali ste sigurni da želite obrisati jezik?'); ?>"
                                               title="Obriši" ><i class="ion-android-close"></i></a>
                                        </div>
                                        <input type="hidden" name="line_no[]"
                                               value="<?php echo $jezik['Line No_']; ?>"/>
                                    </div>


                                <?php }
                            } ?>
                            <a id="unos_jezika"
                               href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_lang_add.php' ?>"
                               data-widget="ajax-kvalifikacija" data-id="opt2" data-width="50%"
                               class="btn btn-sm btn-red" style="margin-bottom:2%;margin-top:2%;float: left;">Novi unos
                                <i class="ion-ios-plus-empty"></i></a>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>

    </div>

    <div class="col-sm-12" style="font-size:12px;">

        <div class="box" style="margin-bottom:1%;">
            <div class="head" style="height: 42px; padding-top: 0.5%; padding-bottom: 0.5%;">
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse" data-id="c1g"></a>
                </div>
                <h5><?php echo __('Radni staž'); ?></h5>
            </div>


            <div class="content" id="c1g" style="display: none;padding-bottom: 1%;">


                <div class="col-sm-12" style="padding-left: 0%;">
                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Prethodni radni staž (G)'); ?></label>
                        <input type="text" name="pr_staz" value="<?php echo $row_personal['Brought Years Total']; ?>"
                               class="form-control" disabled><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Prethodni radni staž (M)'); ?></label>
                        <input type="text" name="pr_staz" value="<?php echo $row_personal['Brought Months Total']; ?>"
                               class="form-control" disabled><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Prethodni radni staž (D)'); ?></label>
                        <input type="text" name="pr_staz" value="<?php echo $row_personal['Brought Days Total']; ?>"
                               class="form-control" disabled><br/>
                    </div>

                </div>

                <div class="col-sm-12" style="padding-left: 0%;">
                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Ukupni radni staž u Banci (G)'); ?></label>
                        <input type="text" name="ub_staz" value="<?php echo $row_personal['Current Years Total']; ?>"
                               class="form-control" disabled><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Ukupni radni staž u Banci (M)'); ?></label>
                        <input type="text" name="ub_staz" value="<?php echo $row_personal['Current Months Total']; ?>"
                               class="form-control" disabled><br/>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Ukupni radni staž u Banci (D)'); ?></label>
                        <input type="text" name="ub_staz" value="<?php echo $row_personal['Current Days Total']; ?>"
                               class="form-control" disabled><br/>
                    </div>
                </div>

                <div class="col-sm-12" style="padding-left: 0%;">
                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Ukupni radni staž (G)'); ?></label>
                        <input type="text" name="u_staz" value="<?php echo $row_personal['Years of Experience']; ?>"
                               class="form-control" disabled>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Ukupni radni staž (M)'); ?></label>
                        <input type="text" name="u_staz" value="<?php echo $row_personal['Months of Experience']; ?>"
                               class="form-control" disabled>
                    </div>

                    <div class="col-sm-4" style="padding-left: 0%;">
                        <label><?php echo __('Ukupni radni staž (D)'); ?></label>
                        <input type="text" name="u_staz" value="<?php echo $row_personal['Days of Experience']; ?>"
                               class="form-control" disabled>
                    </div>
                </div>


                <div class="col-sm-12" style="margin-top: 20px; padding-left: 0;">
                    <table class="table table-bordered my-table table-sm">
                        <thead>
                        <tr>
                            <th scope="col" style="font-size: small;">Datum početka rada</th>
                            <th scope="col" style="font-size: small;">Datum završetka rada</th>
                            <th scope="col" style="font-size: small;">Radni staž (G)</th>
                            <th scope="col" style="font-size: small;">Radni staž (M)</th>
                            <th scope="col" style="font-size: small;">Radni staž (D)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($work_experience as $exp){
                            echo "<tr>";

                            if (is_null($exp['Starting Date'])) {
                                echo "<td>Nije uneseno</td>";
                            }else{
                                echo "<td>" . date("d.m.Y", strtotime($exp['Starting Date'])) . "</td>";
                            }

                            if (is_null($exp['Ending Date'])) {
                                echo "<td>Nije uneseno</td>";
                            }else{
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

                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    <div class="col-sm-12" style="font-size:12px;">

        <div class="box" style="margin-bottom:1%;">
            <div class="head" style="height: 42px; padding-top: 0.5%; padding-bottom: 0.5%;">
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse" data-id="c1h"></a>
                </div>
                <h5><?php echo __('Fond solidarnosti i sindikat'); ?></h5>
            </div>

            <div class="content" id="c1h" style="display: none;padding-bottom: 0%;">

                <div class="col-sm-12" style="padding-left: 0%;">
                    <div class="col-sm-2" style="width: 21%;padding-left: 0%;">
                        <label><?php echo __('Član internog fonda solidarnosti'); ?></label>
                        <?php if ($row_personal['Internal Solidarity Fund'] == 0) {
                            $clan_solidarnosti = 'NE';
                        } else {
                            $clan_solidarnosti = 'DA';
                        } ?>
                        <input type="text" id="interni_fond" name="interni_fond"
                               value="<?php echo $clan_solidarnosti; ?>" class="form-control"
                               style="color:black; width: 51%"><br/>
                    </div>

                    <div class="col-sm-2" style="width: 12%;padding-left: 0%;">
                        <label><?php echo __('Član sindikata'); ?></label>
                        <?php if ($row_personal['Union Member'] == 0) {
                            $clan_sindikata = 'NE';
                        } else {
                            $clan_sindikata = 'DA';
                        } ?>
                        <input type="text" id="clan_sindikata" name="clan_sindikata"
                               value="<?php echo $clan_sindikata; ?>" class="form-control" style="color:black;"><br/>
                    </div>


                    <?php if ($count_unions > 0) {
                        foreach ($rows_unions as $key => $union) { ?>

                            <div class="col-sm-12" style="width:100%;padding-left: 0%;">
                                <label><?php echo __('Naziv sindikata'); ?></label>
                                <input type="text" id="sindikat<?php echo $key; ?>" name="sindikat"
                                       value="<?php echo _optionGetUnionNameNAV($union['Code']); ?>"
                                       class="form-control" style="color:black; width: 100%"><br/>
                            </div>


                        <?php }
                    } ?>


                </div>

            </div>
        </div>

    </div>

    <div class="col-sm-12" style="font-size:12px;">

        <div class="box" style="margin-bottom:1%;">
            <div class="head" style="height: 42px; padding-top: 0.5%; padding-bottom: 0.5%;">
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-down" data-widget="collapse" data-id="c1i"></a>
                </div>
                <h5><?php echo __('Poreska olakšica i prevoz'); ?></h5>
            </div>

            <div class="content" id="c1i" style="display: none;padding-bottom: 0%;">


                <div class="col-sm-4" style="margin-left: -15px;">
                    <label><?php echo __('Poreska kartica'); ?></label>
                    <?php if ($row_personal['Tax Deduction'] == 0) {
                        $poreska_kartica = 'NE';
                    } else {
                        $poreska_kartica = 'DA';
                    } ?>
                    <input type="text" id="Poreska_kartica" name="Poreska_kartica"
                           value="<?php echo $poreska_kartica; ?>" class="form-control" disabled><br/>
                </div>

                <div class="col-sm-4">
                    <label><?php echo __('Koeficijent olakšice'); ?></label>
                    <input type="text" id="Koeficijent_olaksice" name="Koeficijent_olaksice"
                           value="<?php echo number_format((float)$row_personal['Benefit Coefficient'], 2, '.', ''); ?>"
                           class="form-control" style="color:black;"><br/>
                </div>

                <div class="col-sm-4">
                    <label><?php echo __('Prevoz na odobrenoj relaciji'); ?></label>
                    <input type="text" id="prevoz_lokacija" name="prevoz_lokacija"
                           value="<?php echo number_format((float)$row_personal['Transport Amount Planned'], 2, '.', ''); ?>"
                           class="form-control" style="color:black;">
                </div>

            </div>
        </div>

    </div>
    </div>
    <button id="spasi" type="submit" class="btn btn-red btn-lg"
                            style="  display: flex; width:110px; background:linear-gradient(#2396bf, #006595 40%, #004961 3%, #004961); float: right;margin-right: 30px; margin-top:20px;"><?php echo __('Spasi!'); ?> <i
                                class="ion-ios-download-outline"></i></button>
    </div>

</section>
<!-- END - Main section -->


<?php
include $_themeRoot . '/footer.php';

if ($x_user['employee_no'] != $usr) {
    ?>
    <script>
        // $("input").attr("disabled", "disabled");
        // $("select").attr("disabled", "disabled");
        // //$("#spasi").css("display", "none");
        // $("#unos_certifikata").attr("disabled", "disabled");
        // $("#unos_jezika").attr("disabled", "disabled");

    </script>
    <?php
}
?>

<script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/locales/bootstrap-datepicker.bs.min.js"></script>

<script>
    var dtl = $("input, .tt-show");
    dtl.tooltip();

    var collapseItem = localStorage.getItem('collapseItem');

    if (collapseItem) {
        var storedNames = JSON.parse(collapseItem);
        var tabs = storedNames;

        for (x = 0; x < storedNames.length; x++) {
            $("#" + storedNames[x]).css('display', 'block');
        }
        var i = x;
    } else {
        var tabs = [];
        var i = 0;
    }

    $('.box-head-btn a').click(function () {
        spliced = 0;

        buttonID = $(this).attr('data-id');

        for (var y = 0; y < tabs.length; y++) {

            if (tabs[y] === buttonID) {
                tabs.splice(y, 1);
                localStorage.setItem('collapseItem', JSON.stringify(tabs));
                spliced = 1;
            }
        }

        if (spliced == 0) {

            tabs[i] = $(this).attr('data-id');
            localStorage.setItem('collapseItem', JSON.stringify(tabs));
            i++;
        }
    });


    jQuery.expr[':'].regex = function (elem, index, match) {
        var matchParams = match[3].split(','),
            validLabels = /^(data|css):/,
            attr = {
                method: matchParams[0].match(validLabels) ?
                    matchParams[0].split(':')[0] : 'attr',
                property: matchParams.shift().replace(validLabels, '')
            },
            regexFlags = 'ig',
            regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g, ''), regexFlags);
        return regex.test(jQuery(elem)[attr.method](attr.property));
    }


    $(document).ready(function () {

        $("#ime_prezime_srodnik").select2();

        var today = new Date();
        var startDate = new Date();

        $('#rodjenje_otac').datepicker({
            todayBtn: "linked",
            format: 'dd.mm.yyyy',
            language: 'bs',
            //startDate: startDate,
            //endDate: new Date('2017/12/31')
        });

        $('#rodjenje_majka').datepicker({
            todayBtn: "linked",
            format: 'dd.mm.yyyy',
            language: 'bs',
            //startDate: startDate,
            //endDate: new Date('2017/12/31')
        });


        $('#profile-form input').attr('spellcheck', 'false');
        $('.dialog-loader').hide();
        $("#ime_prezime").select2();
        var b1 = '<?php echo $_user['B_1_description'];?>';
        // if(b1!='KADROVSKI POSLOVI'){
        $("#profile-form input[type=text]").not("#lname,#clan_sindikata,#sindikat,#naziv_sindikata,#interni_fond,#aname,#Phone_No,#phone_mob,#email,#fname_lname_emergency_person,#relationship_emergency_person,#phone_emergency_person,#rodjenje_otac,#ime_majke,#prezime_majke,#prezime_oca,#rodjenje_majka,#m_djevojacko,#br_status,#krvna_grupa,#grad_rodjenja,#mjesto_rodjenja,#vozacka,#akt_vozac,#Address_CIPS,#municipality_name_cips,#city_cips,#postal_num_cips,#zip_cips,#ime_supruznika,#prezime_supruznika,#rodjenje_supruznik,#prevoz_lokacija,#code_sifra_rodjenje_drz,input:regex(id, .*dijet.*),input:regex(id, .*sind.*)").click(function () {
            $.alert({
                title: 'Upozorenje!',
                content: 'Za promjenu informacija potrebno dostaviti dokaz kadrovskoj službi ili poslati na mail HRpodrska',
                type: 'blue',
                icon: 'fa fa-warning',
                buttons: {
                    confirm: {
                        text: 'OK',
                        btnClass: 'btn-blue',
                        action: function () {
                            //   window.location.reload();
                        }
                    },

                }
            });

        });


        // $("input#code_sifra_rodjenje_drz, #grad_rodjenja,#code_sifra_rodjenje_drz").click(function () {
        //     $.alert({
        //         title: 'Upozorenje!',
        //         content: 'Za promjenu informacija potrebno dostaviti dokaz kadrovskoj službi ili poslati na mail HRpodrska',
        //         type: 'blue',
        //         icon: 'fa fa-warning',
        //         buttons: {
        //             confirm: {
        //                 text: 'OK',
        //                 btnClass: 'btn-blue',
        //                 action: function () {
        //                     //   window.location.reload();
        //                 }
        //             },
        //
        //         }
        //     });
        //
        // });


        $("#Address_CIPS,#municipality_name_cips,#city_cips,#postal_num_cips,#zip_cips").click(function () {
            $.alert({
                title: 'Upozorenje!',
                content: 'Za promjenu adrese potrebno dostaviti dokaz kadrovskoj službi ili poslati na mail adresu HRpodrska',
                type: 'blue',
                icon: 'fa fa-warning',
                buttons: {
                    confirm: {
                        text: 'OK',
                        btnClass: 'btn-blue',
                        action: function () {
                            //    window.location.reload();
                        }
                    },

                }
            });

        })


        $("#ime_supruznika,#prezime_supruznika,#rodjenje_supruznik").click(function () {
            $.alert({
                title: 'Upozorenje!',
                content: 'Za ažuriranje podataka potrebno dostaviti dokaz kadrovskoj službi ili poslati na mail adresu HRpodrska',
                type: 'blue',
                icon: 'fa fa-warning',
                buttons: {
                    confirm: {
                        text: 'OK',
                        btnClass: 'btn-blue',
                        action: function () {
                            //  window.location.reload();
                        }
                    },

                }
            });

        })

        $("#prevoz_lokacija").click(function () {
            $.alert({
                title: 'Upozorenje!',
                content: 'Potrebno dostavi dokument/potvrdu za pravdanje troškova prevoza',
                type: 'blue',
                icon: 'fa fa-warning',
                buttons: {
                    confirm: {
                        text: 'OK',
                        btnClass: 'btn-blue',
                        action: function () {
                            //   window.location.reload();

                        }
                    },

                }
            });

        })


        $('input:regex(id, .*sind.*)').click(function () {
            $.alert({
                title: 'Upozorenje!',
                content: 'Za članstvo u sindikatu potrebno dostaviti potpisanu izjavu kadrovskoj službi ili poslati na mail adresu HRpodrska',
                type: 'blue',
                icon: 'fa fa-warning',
                buttons: {
                    confirm: {
                        text: 'OK',
                        btnClass: 'btn-blue',
                        action: function () {
                            // window.location.reload();
                        }
                    },

                }
            });

        })
        $("#interni_fond").click(function () {
            $.alert({
                title: 'Upozorenje!',
                content: 'Za članstvo u fondu solidarnosti potrebno dostaviti potpisanu izjavu kadrovskoj službi ili poslati na mail adresu HRpodrska',
                type: 'blue',
                icon: 'fa fa-warning',
                buttons: {
                    confirm: {
                        text: 'OK',
                        btnClass: 'btn-blue',
                        action: function () {
                            //   window.location.reload();
                        }
                    },

                }
            });

        })

        $('input:regex(id, .*dijet.*)').click(function () {
            $.alert({
                title: 'Upozorenje!',
                content: 'Za unos djeteta potrebno dostaviti dokaz kadrovskoj službi ili poslati na mail adresu HRpodrska',
                type: 'blue',
                icon: 'fa fa-warning',
                buttons: {
                    confirm: {
                        text: 'OK',
                        btnClass: 'btn-blue',
                        action: function () {
                            // window.location.reload();
                        }
                    },

                }
            });

        })

        $('input:regex(id, .*pastor.*)').click(function () {
            $.alert({
                title: 'Upozorenje!',
                content: 'Za unos djeteta potrebno dostaviti dokaz kadrovskoj službi ili poslati na mail adresu HRpodrska',
                type: 'blue',
                icon: 'fa fa-warning',
                buttons: {
                    confirm: {
                        text: 'OK',
                        btnClass: 'btn-blue',
                        action: function () {
                            //  window.location.reload();
                        }
                    },

                }
            });

        })


        $("#profile-form :input").change(function () {

            $("#spasi").css("background", "red");
            $("#spasi").css("color", "white");
            //$(this).closest('form').data('changed', true);
        });

        $("#unos_jezika").click(function () {

            $("#spasi").css("background", "red");
            $("#spasi").css("color", "white");
            //$(this).closest('form').data('changed', true);
        });

        $("#unos_certifikata").click(function () {

            $("#spasi").css("background", "red");
            $("#spasi").css("color", "white");
            //$(this).closest('form').data('changed', true);
        });

    });
    $("#profile-form").validate({
        focusCleanup: true,
        submitHandler: function (form) {
            $('.dialog-loader').show();
            $(form).ajaxSubmit({
                url: "<?php echo $url . '/modules/default/ajax.php'; ?>",
                type: "post",
                success: function (data) {
                    var obj = jQuery.parseJSON(data);
                    $("#res").html(obj.msg);
                    $('.dialog-loader').hide();
                    $(".dialog").animate({scrollTop: 0}, 600);
                    if (obj.status == "oki") {

                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    }
                    window.scrollTo(0, 0);

                }
            });
        }
    });

    function insertParam(key, value) {
        key = encodeURI(key);
        value = encodeURI(value);

        var kvp = document.location.search.substr(1).split('&');

        var i = kvp.length;
        var x;
        while (i--) {
            x = kvp[i].split('=');

            if (x[0] == key) {
                x[1] = value;
                kvp[i] = x.join('=');
                break;
            }
        }

        if (i < 0) {
            kvp[kvp.length] = [key, value].join('=');
        }

        //this will reload the page, it's likely better to store this until finished
        document.location.search = kvp.join('&');
    }

    function fillMunName() {
        var ime = '';
        var kod = $("#code_sifra_rodjenje_mun option:selected").val();

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {request: "get-name-mun", request_id: kod},
            function (returnedData) {
                console.log('deneeee' + returnedData);
                pieces = returnedData.split(';');
                ime = pieces[0];
                city = pieces[1];

                $("#mun_name").val(ime);
                $("#grad_rodjenja").val(city);
                $("#mjesto_rodjenja").val(city);
            });


    }

    function insertSrodnik() {
        console.log("invoked function");
        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "insert-srodnik",
                request_id: '<?php echo $row['employee_no']; ?>',
                name: $("#ime_prezime_srodnik option:selected").text(),
                srodstvo: $("#srodstvo_srodnik option:selected").val(),
                broj_srodnika: $("#ime_prezime_srodnik option:selected").val()
            },
            function (returnedData) {
                console.log(returnedData);
//window.location.reload();
            });
    }

    function SyncNAV_user() {
        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "profile-sync-nav-portal-user",
                request_id: '<?php echo $row['employee_no']; ?>'
            },
            function (returnedData) {
                window.location.reload();
            });
    }

    function SyncNAV() {
        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {request: "profile-sync-nav-portal"},
            function (returnedData) {
//window.location.reload();
            });
    }

    function phoneCheck(event) {
        return ((event.charCode >= 48 && event.charCode <= 57) || (event.charCode == 32));
    }


    /* Phone Number checker */

    var emergency_ready = 0;
    country_rel_person = $("#country_rel_person");
    country_region_region_rel_person = $("#country_region_region_rel_person");
    phone_emergency_person = $("#phone_emergency_person");

    if (country_rel_person.val() == '' || country_region_region_rel_person.val() == '') {
        phone_emergency_person.attr('disabled', 'disabled');
        phone_emergency_person.val('');
    }

    if (country_rel_person.val() != '') {
        emergency_ready++;
    }
    if (country_region_region_rel_person.val() != '') {
        emergency_ready++;
    }

    country_rel_person.change(function () {
        if ($(this).val() == '') {
            emergency_ready--;
            phone_emergency_person.attr('disabled', 'disabled');
            phone_emergency_person.val('');
        } else {
            if (emergency_ready < 2) {
                emergency_ready++;
            }
        }
        console.log(emergency_ready);
        if (emergency_ready == 2) {
            phone_emergency_person.prop('disabled', false);
        }
    });
    country_region_region_rel_person.change(function () {
        if ($(this).val() == '') {
            emergency_ready--;
            phone_emergency_person.attr('disabled', 'disabled');
            phone_emergency_person.val('');
        } else {
            if (emergency_ready < 2) {
                emergency_ready++;
            }
        }
        if (emergency_ready == 2) {
            phone_emergency_person.prop('disabled', false);
        }
        console.log(emergency_ready);
    });


    /* Phone Number checker */

    var private_ready = 0;
    country_region_mobile = $("#country_region_mobile");
    country_region_region_mobile = $("#country_region_region_mobile");
    phone_mob = $("#phone_mob");

    if (country_region_mobile.val() == '' || country_region_region_mobile.val() == '') {
        phone_mob.attr('disabled', 'disabled');
        phone_mob.val('');
    }

    if (country_region_mobile.val() != '') {
        private_ready++;
    }
    if (country_region_region_mobile.val() != '') {
        private_ready++;
    }

    country_region_mobile.change(function () {
        if ($(this).val() == '') {
            private_ready--;
            phone_mob.attr('disabled', 'disabled');
            phone_mob.val('');
        } else {
            if (private_ready < 2) {
                private_ready++;
            }
        }

        if (private_ready == 2) {
            phone_mob.prop('disabled', false);
        }
    });
    country_region_region_mobile.change(function () {
        if ($(this).val() == '') {
            private_ready--;
            phone_mob.attr('disabled', 'disabled');
            phone_mob.val('');
        } else {
            if (private_ready < 2) {
                private_ready++;
            }
        }
        if (private_ready == 2) {
            phone_mob.prop('disabled', false);
        }
    });


    /* Phone Number checker */

    var slu_ready = 0;
    country_region = $("#country_region");
    country_region_region = $("#country_region_region");
    Phone_No = $("#Phone_No");

    if (country_region.val() == '' || country_region_region.val() == '') {
        Phone_No.attr('disabled', 'disabled');
        Phone_No.val('');
    }

    if (country_region.val() != '') {
        slu_ready++;
    }
    if (country_region_region.val() != '') {
        slu_ready++;
    }

    country_region.change(function () {
        if ($(this).val() == '') {
            slu_ready--;
            Phone_No.attr('disabled', 'disabled');
            Phone_No.val('');
        } else {
            if (slu_ready < 2) {
                slu_ready++;
            }
        }

        if (slu_ready == 2) {
            Phone_No.prop('disabled', false);
        }
    });
    country_region_region.change(function () {
        if ($(this).val() == '') {
            slu_ready--;
            Phone_No.attr('disabled', 'disabled');
            Phone_No.val('');
        } else {
            if (slu_ready < 2) {
                slu_ready++;
            }
        }
        if (slu_ready == 2) {
            Phone_No.prop('disabled', false);
        }

    });
</script>

<div class="dialog-loader"><i></i></div>

</body>
</html>