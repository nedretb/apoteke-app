<?php
_pagePermission(4, false);
/*
<label class="lable-admin"><?php echo __('Personalni br.'); ?></label>
  <input type="number" class="rcorners1" name="IDEmp"><br>
 */
?>

<!-- START - Main section -->

<div class="header">

    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Verifikacija satnica'); ?></span></h4>
</div>

<section>
    <div class="content clear">


        <div class="box" style="width:22%; display: block; float:left; margin-right:20px;">
            <div class="content">
                <table class="table table-hover">


                    <div class="row">
                        <div class="col-xs-12">

                            <form id="admin-form" method="post">
                                <label class="lable-admin"><?php echo __('Godina'); ?></label>
                                <input type="number" class="rcorners1" name="IDYear" min="2017"
                                       max="2300" <?php if (isset($_POST['IDMonth']) and ($_POST['IDMonth']) != '' and isset($_POST['IDYear']) and ($_POST['IDYear']) != '') { ?> value="<?php echo $_POST['IDYear']; ?>"  <?php } ?>
                                       required
                                       oninvalid="this.setCustomValidity('Molimo unesite godinu.')"
                                       onchange="this.setCustomValidity('')"><br>
                                <label class="lable-admin"><?php echo __('Mjesec'); ?></label>
                                <input type="number" class="rcorners1" name="IDMonth" min="1"
                                       max="12" <?php if (isset($_POST['IDMonth']) and ($_POST['IDMonth']) != '' and isset($_POST['IDYear']) and ($_POST['IDYear']) != '') { ?> value="<?php echo $_POST['IDMonth']; ?>"  <?php } ?>
                                       required
                                       oninvalid="this.setCustomValidity('Molimo unesite mjesec.')"
                                       onchange="this.setCustomValidity('')"><br>
                                <label class="lable-admin"><?php echo __('B-1 sa regijama'); ?></label>
                                <select id="regije" name="IDReg" class="rcorners1" class="form-control">
                                    <?php echo _optionRegion(0); ?>
                                </select><br/>
                                <label class="lable-admin"><?php echo __('Naziv org. jed.'); ?></label>
                                <select id="poslovnice" name="IDPos" class="rcorners1" class="form-control">
                                    <?php echo _optionSector(-1); ?>
                                </select><br/>
                                <!--  <label class="lable-admin"><?php echo __('Org. jedinica'); ?></label>
    <input type="number" class="rcorners1" name="IDDep"><br> -->

                                <label class="lable-admin"><?php echo __('Ime i prezime'); ?></label>
                                <input type="text" class="rcorners1" name="ime_prezime">

                                <label class="lable-admin"><?php echo __('Verificirano:'); ?></label>
                                <select id="verifikacije" name="IDVer" class="rcorners1" class="form-control">
                                    <?php echo _OptionVerification(-1); ?>
                                </select><br/>

                                <div>
                                    <hr style="margin:5px">
                                    <button type="submit" style="margin-left:13px;width:125px;"
                                            class="btn btn-red pull-right btn-sm"><?php echo __('Izaberi!'); ?> <i
                                                class="ion-ios-download-outline"></i></button>
                                    <button onclick="myFunction()" style="width:125px;background-color:006595;"
                                            class="btn btn-red pull-right btn-sm"><?php echo __('Odustani!'); ?> <i
                                                class="ion-ios-download-outline"></i></button>
                                    <br/>
                                </div>

                            </form>


                            </select><br/>
                        </div>
                    </div>
                    <?php
                    if (isset($_POST['IDEmp'])) {
                        $idemp = $_POST['IDEmp'];
                    }
                    if (isset($_POST['IDMonth']) and isset ($_POST['IDYear'])) {
                        $idm = $_POST['IDMonth'];
                        $get_year = $db->query("SELECT id FROM  " . $portal_hourlyrate_year . "  WHERE user_id='41' AND year='" . $_POST['IDYear'] . "'");
                        foreach ($get_year as $yearvalue) {
                            $filter_year = $yearvalue['id'];
                        }
                    } else {
                        $now = new \DateTime('now');
                        $currmonth = $now->format('m');
                        $curryear = $now->format('Y');
                        $get_year = $db->query("SELECT id FROM  " . $portal_hourlyrate_year . "  WHERE user_id='41' AND year='" . $curryear . "' ");
                        foreach ($get_year as $yearvalue) {
                            $filter_year = $yearvalue['id'];
                        }
                        $month['id'] = $currmonth;
                        $year['id'] = $curryear;
                    }; ?>

                    <!-- START - Default section -->


            </div>


            <!-- END - Default calendar section -->
            <?php

            ?>
            </body>
            </html>


            </table>
        </div>

    </div>

    <?php

    ?>
    <?php

    $limit = 20;

    if ($_num) {

        $offset = ($_num - 1) * $limit;

    } else {

        $offset = 0;
        $_num = 1;

    }

    $where = "";
    $path = '?m=' . $_mod . '&p=' . $_page;

    if (isset($_GET['t'])) {
        $type = $_GET['t'];
        if ($type == 'inactive') {
            $where .= "WHERE status='1'";
        } else {
            $where .= "WHERE status='0' AND role='$type'";
        }
        $path .= '&t=' . $type;
    } else {
        $type = '';
        $where = "WHERE status='0'";
    }

    if ($_search) {
        $where .= " AND fname LIKE '%$_search%' OR lname LIKE '%$_search%'";
        $path .= '&q=' . $_search;
    }

    $path .= '&pg=';


    if (isset($_POST['ime_prezime']) /* and (empty($_POST['IDDep']) and ($_POST['IDDep'])=='' ) */ and (empty($_POST['IDReg']) or ($_POST['IDReg']) == '')) {
        $parent2_query = $db->query("SELECT department_code, parent2, parent3 FROM  " . $portal_users . "  where (fname + ' ' + lname)='" . $_POST['ime_prezime'] . "' group by department_code, parent2, parent3 ");
    }
    if ((isset($_POST['ime_prezime'])) and (isset($_POST['IDReg']) and ($_POST['IDReg']) != '') and (isset($_POST['IDPos']) and ($_POST['IDPos']) != '')) {
        $parent2_query = $db->query("SELECT department_code, parent2, parent3 FROM  " . $portal_users . "  where department_code =" . $_POST['IDPos'] . " and (fname + ' ' + lname)='" . $_POST['ime_prezime'] . "'  group by department_code, parent2, parent3 ");
    }
    if ((isset($_POST['ime_prezime'])) and (isset($_POST['IDReg']) and ($_POST['IDReg']) != '') and (empty($_POST['IDPos']) and ($_POST['IDPos']) == '')) {
        $parent2_query = $db->query("SELECT department_code, parent2, parent3 FROM  " . $portal_users . "  where B_1_regions =" . $_POST['IDReg'] . " and (fname + ' ' + lname)='" . $_POST['ime_prezime'] . "'  group by department_code, parent2, parent3 ");
    }
    if ((isset($_POST['ime_prezime'])) and (isset($_POST['IDReg']) and ($_POST['IDReg']) != '') and (isset($_POST['IDPos']) and ($_POST['IDPos']) != '')/* and (isset($_POST['IDDep']) and ($_POST['IDDep'])!='' )*/) {
        $parent2_query = $db->query("SELECT department_code, parent2, parent3 FROM  " . $portal_users . "  where department_code =" . $_POST['IDPos'] . " and (fname + ' ' + lname)='" . $_POST['ime_prezime'] . "' group by department_code, parent2, parent3 ");
    }

    if ((isset($_POST['IDReg']) and ($_POST['IDReg']) != '') and (empty($_POST['IDPos']) and ($_POST['IDPos']) == '') and (empty($_POST['ime_prezime']))/* and (empty($_POST['IDDep']) and ($_POST['IDDep'])=='' ) */) {
        $parent2_query = $db->query("SELECT department_code, parent2, parent3 FROM  " . $portal_users . "  where B_1_regions =" . $_POST['IDReg'] . "  group by department_code, parent2, parent3 ");
    }

    if ((isset($_POST['ime_prezime'])) /* and (isset($_POST['IDDep']) and ($_POST['IDDep'])!='' )*/) {
        $parent2_query = $db->query("SELECT department_code, parent2, parent3 FROM  " . $portal_users . "  where  (fname + ' ' + lname)=N'" . $_POST['ime_prezime'] . "' group by department_code, parent2, parent3 ");
    }

    if ((isset($_POST['IDReg']) and ($_POST['IDReg']) != '') /* and (isset($_POST['IDDep']) and ($_POST['IDDep'])!='' ) */) {
        $parent2_query = $db->query("SELECT department_code, parent2, parent3 FROM  " . $portal_users . "  where  B_1_regions =" . $_POST['IDReg'] . " group by department_code, parent2, parent3 ");
    }

    if (empty($_POST['ime_prezime'])  /*and empty ($_POST['IDDep'])*/ and empty ($_POST['IDReg'])) {
        $parent2_query = $db->query("SELECT department_code, parent2, parent3 FROM  " . $portal_users . "  where department_code is not null group by department_code, parent2, parent3 ");
        //$get2 = $db->query("SELECT count(distinct department_code, parent2, parent3) FROM  ".$portal_users." ");
        echo 'irma';
    }
    /* if ((empty($_POST['ime_prezime']) ) and (empty($_POST['IDReg']) or ($_POST['IDReg'])=='')/* and (isset($_POST['IDDep']) and ($_POST['IDDep'])!='' )) {
     $parent2_query = $db->query("SELECT department_code, parent2, parent3 FROM  ".$portal_users."  where department_code =".$_POST['IDDep']." group by department_code, parent2, parent3 ");
     }*/

    if ((empty($_POST['ime_prezime'])) and (isset($_POST['IDReg']) and ($_POST['IDReg']) != '') and (isset($_POST['IDPos']) and ($_POST['IDPos']) != '')) {
        $parent2_query = $db->query("SELECT department_code, parent2, parent3 FROM  " . $portal_users . "  where department_code =" . $_POST['IDPos'] . "  group by department_code, parent2, parent3 ");
    }

    if (isset($_POST['IDVer']) and ($_POST['IDVer']) !== '' and is_numeric($_POST['IDVer'])) {
        $parent2_query = $db->query("declare @ver int
set @ver = " . $_POST['IDVer'] . "

if (@ver=1)
begin
SELECT department_code, parent2, parent3,c0_intranet2.dbo.fn_user_id(parent2) user1,c0_intranet2.dbo.fn_user_id(parent3) user2, c0_intranet2.dbo.fn_verified_corrections(c0_intranet2.dbo.fn_user_id(parent2)," . $_POST['IDMonth'] . ") verified1,c0_intranet2.dbo.fn_verified_corrections(c0_intranet2.dbo.fn_user_id(parent3)," . $_POST['IDMonth'] . ") verified2   FROM  " . $portal_users . "  where (c0_intranet2.dbo.fn_verified_corrections(c0_intranet2.dbo.fn_user_id(parent2)," . $_POST['IDMonth'] . ") =@ver) or (c0_intranet2.dbo.fn_verified_corrections(c0_intranet2.dbo.fn_user_id(parent3)," . $_POST['IDMonth'] . ") =@ver)  group by department_code, parent2, parent3
end
else
begin
SELECT department_code, parent2, parent3,c0_intranet2.dbo.fn_user_id(parent2) user1,c0_intranet2.dbo.fn_user_id(parent3) user2, c0_intranet2.dbo.fn_verified_corrections(c0_intranet2.dbo.fn_user_id(parent2)," . $_POST['IDMonth'] . ") verified1,c0_intranet2.dbo.fn_verified_corrections(c0_intranet2.dbo.fn_user_id(parent3)," . $_POST['IDMonth'] . ") verified2   FROM  " . $portal_users . "  where (c0_intranet2.dbo.fn_verified_corrections(c0_intranet2.dbo.fn_user_id(parent2)," . $_POST['IDMonth'] . ") =@ver) and (c0_intranet2.dbo.fn_verified_corrections(c0_intranet2.dbo.fn_user_id(parent3)," . $_POST['IDMonth'] . ") =@ver)  group by department_code, parent2, parent3
end");
        echo 'denis';
    }


    //$result = $get2->fetch();
    // $total=$result[0];


    if ($parent2_query->rowCount() < 0){
    $i = 0;

    foreach ($parent2_query

    as $item4){
    $parent2_dis = $item4['parent2'];

    $parent3_dis = $item4['parent3'];
    $department = $item4['department_code'];


    $query = $db->query("SELECT * from  " . $portal_users . "  where (employee_no='" . $item4['parent2'] . "' or employee_no='" . $item4['parent3'] . "') ORDER BY department_code");


    $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "  WHERE (employee_no='" . $item4['parent2'] . "' or employee_no='" . $item4['parent3'] . "')  group BY department_code ");


    ?>


    <div class="box" style="width:76%; float:left;">
        <div class="row">

            <div class="col-sm-12">

                <table class="table table-hover">

                    <thead>
                    <tr>
                        <?php foreach ($queryg as $itemg) {
                            //  $department=$itemg['department_code'];
                        } ?>

                        <th width="40" style="display:block;" class="hidden-xs"><?php echo __('Per. br.'); ?></th>

                        <th width="60;" class="hidden-xs"></th>


                        <?php

                        if (isset($_POST['IDEmp'])) {
                            $idemp = $_POST['IDEmp'];
                        }
                        if (isset($_POST['IDMonth']) and isset ($_POST['IDYear'])) {
                            $idm = $_POST['IDMonth'];
                            $idy = $_POST['IDYear'];


                            $month['id'] = $idm;
                            $year['id'] = $idy;


                        } else {
                            $now = new \DateTime('now');
                            $currmonth = $now->format('m');
                            $curryear = $now->format('Y');
                            $month['id'] = $currmonth;
                            $year['id'] = $curryear;
                        } ?>
                        <th><?php echo __('Administratori satnica'); ?></th>

                        <th><?php echo __('Org. jedinica'); ?></th>

                        <th><?php echo __('Verifikacija korekcija satnica'); ?></th>


                    </tr>
                    </thead>

                    <tbody>


                    <?php


                    foreach ($query as $item) {
                        $i++;
                        $tools_id = $item['user_id'];
                        $emp_id = $item['employee_no'];
                        $department_name = $item['sector'];
                        $sql = "SELECT * FROM users WHERE user_id = $tools_id";
                        $sth = $db->query($sql);
                        $rowim = $sth->fetch();

                        $data = $rowim['image_no'];

                        $verified_id = '';
                        if (isset($_POST['IDMonth'])) {
                            $get_verified = $db->query("select verified_corrections as verified_corrections from  " . $portal_hourlyrate_month . "  where user_id = " . $item['user_id'] . " and month=" . $idm);
                            $verified = $get_verified->fetch();
                            $verified_id = $verified['verified_corrections'];


                        }

                        ?>

                        <div style="float:left; ">


                            <tr id="opt-<?php echo $tools_id; ?>">

                                <td class="hidden-xs"><?php echo $emp_id; ?></td>
                                <td class="text-center" class="hidden-xs">


                                    <?php if ($item['image'] != 'none') { ?>
                                        <img src="<?php echo $_timthumb . $_uploadUrl; ?>/<?php echo $item['image']; ?>"
                                             class="img-circle" style="width:100%;">
                                    <?php } else { ?>
                                        <img src="<?php echo $_themeUrl; ?>/images/noimage-user.png" class="img-circle">
                                    <?php } ?>

                                </td>


                                <td style="width:300px;"><?php echo $item['fname'] . ' ' . $item['lname']; ?>
                                    <br/><small><?php echo $item['position']; ?><br/><?php
                                        $yearid = $db->query("SELECT [id] FROM  " . $portal_hourlyrate_year . "  WHERE user_id='" . $item['user_id'] . "' AND year='" . $year['id'] . "'"); ?>


                                <td style="width:300px;"><?php echo $department; ?>
                                    <br/><small><?php echo $item['sector']; ?><br/><?php
                                        $yearid = $db->query("SELECT [id] FROM  " . $portal_hourlyrate_year . "  WHERE user_id='" . $item['user_id'] . "' AND year='" . $year['id'] . "'"); ?>

                                <td>


                                    <label class="lable-admin"><?php echo __('Verificirao/la'); ?></label>
                                    <input id="checked-<?php echo $tools_id; ?>" class="combo-box-admin" type="checkbox"
                                           name="Abs" disabled readonly <?php if ($verified_id == 1) {
                                        echo 'checked';
                                    } ?> > </input>
                                    <br/><small><?php echo ' ' ?><br/><?php

                                        ?></small>


                        </div>


                        <?php


                        if ($_user['role'] == '2' || $_user['role'] == '1' || $_user['role'] == '4' and (isset($_POST['IDMonth']) and isset ($_POST['IDYear']))) {
                            ?>


                            <?php

                        }


                        if (isset($_POST['IDEmp']) and ($_POST['IDEmp']) != '') {

                            $get_days = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $absence_year . "' AND month_id='" . $month['id'] . "' AND employee_no='" . $idemp . "' ORDER BY day");
                        } else {


                            $get_termination = $db->query("select day([termination_date]) as termination from  " . $portal_users . "  where user_id = " . $item['user_id']);
                            $termination = $get_termination->fetch();
                            $termination_id = $termination['termination'];
                        }


                        ?>


                        </tr>
                        <?php
                        if ($verified_id == 1) { ?>
                            <br>
                            <br>
                            &nbsp; &nbsp; <span
                                    style="color:#009900;float:right;border:2px solid #009900; padding:10px;"><i
                                        class="ion-android-checkmark-circle"></i> <?php echo __('Verificirano za ') . $idm . '/' . $idy; ?></span>

                        <?php }

                        ?>


                    <?php } ?>

                    </tbody>


                    <?php }
                    } else {
                        echo '<tr><td colspan="3" class="text-center">' . __('Još nije bilo unosa') . '</td></tr>';
                    } ?>


                </table>


                <div class="text-left">
                    <div class="btn-group">


                    </div>
                </div>
            </div>
        </div>
    </div>


</section>
<!-- END - Main section -->

<?php

include $_themeRoot . '/footer.php';


?>


<script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>

<script>

    $('#regije').on('change', function () {
        $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                request: "get-sectors",
                region: this.value
            },
            function (returnedData) {
                $('#poslovnice').html(returnedData);
            });


    })
</script>


</body>
</html>
