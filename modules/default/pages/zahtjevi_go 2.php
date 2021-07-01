<?php
// _pagePermission(2, false);


?>

<!-- START - Main section -->
<br/>
<section class="full">

    <div class="container-fluid">


        <div class="row">

            <div class="col-sm-6">
                <h2>
                    <?php echo __('Rješenja o korištenju godišnjeg odmora'); ?><br/><br/>
                </h2>
            </div>
            <div class="col-sm-6 text-right"><br/>

            </div>

        </div>

        <?php

        $limit = 20;

        if ($_num) {

            $offset = ($_num - 1) * $limit;

        } else {

            $offset = 0;
            $_num = 1;

        }

        if ($_user['role'] == 0) {
            $where = "WHERE ";
        } else {
            $where = "WHERE parent_id='" . $_user['user_id'] . "' AND";
        }

        $path = '?m=' . $_mod . '&p=' . $_page;

        if (isset($_GET['t'])) {
            $type = $_GET['t'];
            $where .= " status != '0'";
            $path .= '&t=' . $type;
        } else {
            $type = '';
            $where .= " status='0'";
        }

        if (isset($_GET['u'])) {
            $usr = $_GET['u'];
            if ($usr != '') {
                $where .= " AND user_id='" . $usr . "'";
                $path .= '&u=' . $usr;
            } else {
                $usr = '';
                $where .= "";
            }
        } else {
            $usr = '';
            $where .= "";
        }

        if (isset($_GET['d'])) {
            $dt = $_GET['d'];
            if ($dt != '') {
                $where .= " AND date_created LIKE '%$dt%'";
                $path .= '&d=' . $dt;
            } else {
                $dt = '';
                $where .= "";
            }

        } else {
            $dt = '';
            $where .= "";
        }

        $path .= '&pg=';

        $queryq = $db->query("select cast(timestamp as bigint) as myid, *  from [NAV RAIFFEISEN].[dbo].[RAIFFAISEN BANK" . '$' . "Vacation Ground 2]
  where [Employee No_]=" . $_user['employee_no'] . " and Duration != 0");
        $query = $queryq->fetchAll(PDO::FETCH_ASSOC);
        // var_dump($query);

        // include('table_used.php');
        // $query = $db->query("SELECT * FROM $database_used.[requests] WHERE employee_no = '$_user[employee_no]'  ");
        // $total = $query->rowCount();


        ?>

        <div class="row">
            <div class="col-sm-4"><br/>
                <?php if (1 == 2) { ?>
                    <a href="/<?php echo $_conf['app_location_module']; ?>/?m=travel_requests&p=all"
                       class="btn btn-filter <?php if ($type == '') {
                           echo 'active';
                       } ?>"><?php echo __('Aktivni'); ?></a>
                    <a href="/<?php echo $_conf['app_location_module']; ?>/?m=travel_requests&p=all&t=3"
                       class="btn btn-filter <?php if ($type == '3') {
                           echo 'active';
                       } ?>"><?php echo __('Arhiva'); ?></a>
                <?php } ?>
                <a href="/<?php echo $_conf['app_location_module']; ?>/?m=default&p=zahtjevi_go"
                   class="btn btn-filter <?php if ($type == '') {
                       echo 'active';
                   } ?>"><?php echo __('Rješenja'); ?></a>
            </div>
            <?php if (1 == 2) { ?>
                <div class="col-sm-8 pull-right">
                    <form action="" method="get" class="">
                        <input type="hidden" name="m" value="<?php echo $_mod; ?>">
                        <input type="hidden" name="p" value="<?php echo $_page; ?>">
                        <?php if (isset($_GET['t'])) { ?>
                            <input type="hidden" name="t" value="<?php echo $_GET['t']; ?>">
                        <?php } ?>
                        <?php if ($dt != '' || $usr != '') { ?>
                            <a href="<?php echo $url . '/?m=' . $_mod . '&p=' . $_page; ?>"
                               class="btn-search pull-right"><i class="ion-android-close"></i></a>
                        <?php } ?>


                        <button type="submit" class="btn-search pull-right"><i class="ion-android-search"></i></button>
                        <select name="u" class="form-control pull-right" style="max-width:150px;">
                            <option value=""><?php echo __('Odaberi'); ?></option>
                            <?php
                            $_user_role = $_user['role'];
                            $get_users = $db->query("SELECT * FROM [c0_intranet2].[dbo].[users] WHERE role > '$_user_role'");
                            if ($get_users->rowCount() < 0) {
                                foreach ($get_users as $user) {
                                    if ($usr == $user['user_id']) {
                                        $sel = 'selected="selected"';
                                    } else {
                                        $sel = '';
                                    }
                                    echo '<option value="' . $user['user_id'] . '" ' . $sel . '>' . $user['fname'] . ' ' . $user['lname'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <input type="text" name="d" class="form-control input-date pull-right"
                               value="<?php echo $dt; ?>" style="max-width:300px;">
                    </form>
                </div>
            <?php } ?>
        </div>
        <br/>

        <?php

        if ($query) {

            foreach ($query as $item) {

                ?>

                <div class="box box-lborder box-lborder-<?php echo $border; ?>">
                    <div class="content">
                        <div class="row">


                            <div class="col-sm-5">
                                <?php echo __('Rješenje o korištenju godišnjeg odmora'); ?>

                                <br/>

                                <?php echo __('Od:'); ?>
                                <b><?php echo date('d.m.Y', strtotime($item['Starting Date of I part'])); ?></b> &nbsp;
                                <?php echo __('Do:'); ?>
                                <b><?php echo date('d.m.Y', strtotime($item['Ending Date of I part'])); ?></b>

                            </div>
                            <div class="col-sm-2">
                                <b><?php echo $_user['fname'] . ' ' . $_user['lname']; ?></b><br/>
                                <small><?php echo $user['position']; ?></small>
                            </div>
                            <div class="col-sm-2">
                                <?php echo __('Rješenje kreirano:'); ?></b><br/>
                                <?php echo date('d.m.Y', strtotime($item['Insert Date'])); ?>
                            </div>
                            <div class="col-sm-3 text-right">
                                <?php
                                if (date('Y', strtotime($item['Insert Date'])) == 2018) { ?>
                                    <a target="_blank"
                                       href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_vacation_request_pdf2018_new.php?id=' . $item['myid']; ?>"
                                       style="width:180px;" class="table-btn"><i style="font-size:16px;"
                                                                                 class="ion-ios-copy-outline"></i>
                                        Preuzmite rješenje </a>
                                <?php } else { ?>

                                    <a target="_blank"
                                       href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_vacation_request_pdf_new.php?id=' . $item['myid']; ?>"
                                       style="width:180px;" class="table-btn"><i style="font-size:16px;"
                                                                                 class="ion-ios-copy-outline"></i>
                                        Preuzmite rješenje </a>

                                <?php } ?>
                            </div>


                        </div>
                    </div>
                </div>

            <?php }
        } else { ?>
            <div class="text-center">
                <?php
                ?>
                <?php echo __('Nema spašenih rješenja prema odabranim parametrima.'); ?>
                <?php

                ?>

            </div>
        <?php } ?>

        <div class="text-center">
            <div class="btn-group">
                <?php echo _pagination($path, $_num, $limit, $total); ?>
            </div>
        </div>


    </div>


</section>
<!-- END - Main section -->

<?php

include $_themeRoot . '/footer.php';

?>


</body>
</html>
