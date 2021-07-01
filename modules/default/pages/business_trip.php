<?php
_pagePermission(5, false);

$get = $db->query("SELECT * FROM  " . $portal_users . "  WHERE user_id='" . $_user['user_id'] . "'");
if ($get->rowCount() < 0) {
    $row = $get->fetch();


    ?>

    <!-- START - Main section -->
    <section class="full">

        <div class="container-fluid">


            <div class="row">

                <div class="col-sm-6">
                    <h2>
                        <?php echo __('Moja službena putovanja'); ?><br/><br/>
                    </h2>
                </div>
                <div class="col-sm-6 text-right"><br/>
                    <div class="pull-right">

                        <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_business_trip_add.php'; ?>"
                           data-widget="ajax" data-id="opt2"
                           class="btn btn-red btn-lg"><?php echo __('Dodaj službeno putovanje'); ?> <i
                                    class="ion-ios-plus-empty"></i></a>
                    </div>
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

            $where = "WHERE user_id='" . $_user['user_id'] . "'";
            $path = '?m=' . $_mod . '&p=' . $_page;

            if (isset($_GET['t'])) {
                $type = $_GET['t'];
                $where .= " AND is_archive='1'";
                $path .= '&t=' . $type;
            } else {
                $type = '';
                $where .= " AND is_archive='0'";
            }

            $path .= '&pg=';

            $query = $db->query("SELECT TOP " . $limit . " * FROM [c0_intranet2_apoteke].[dbo].[business_trip] " . $where . " ORDER BY date_created DESC");
            $get2 = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[business_trip] " . $where . " ");
            $result = $get2->fetch();
            //$total=$result[0];
            $total = $get2->rowCount();

            $query2 = $db->query("SELECT TOP " . $limit . " * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['parent'] . "'");
            $get22 = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[business_trip] " . $where . "");
            $total2 = $get2->rowCount();

            foreach ($query2 as $item2) {
                $parent_f = $item2['fname'];
                $parent_l = $item2['lname'];

            }


            $query3 = $db->query("SELECT TOP " . $limit . " * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['stream_parent'] . "'");

            foreach ($query3 as $item3) {
                $hr_f = $item3['fname'];
                $hr_l = $item3['lname'];

            }

            $query4 = $db->query("SELECT TOP " . $limit . " * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['admin'] . "'");

            foreach ($query4 as $item4) {
                $admin_f = $item4['fname'];
                $admin_l = $item4['lname'];

            }

            $query5 = $db->query("SELECT TOP " . $limit . " * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['parentMBO2'] . "'");

            foreach ($query5 as $item5) {
                $parent2_f = $item5['fname'];
                $parent2_l = $item5['lname'];

            }

            $query4 = $db->query("SELECT TOP " . $limit . " * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['to_admin'] . "'");

            foreach ($query4 as $item4) {
                $admin_f = $item4['fname'];
                $admin_l = $item4['lname'];

            }

            $query4 = $db->query("SELECT TOP " . $limit . " * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['to_admin2'] . "'");

            foreach ($query4 as $item4) {
                $admin2_f = $item4['fname'];
                $admin2_l = $item4['lname'];

            }


            ?>

            <a href="/app/?m=default&p=business_trip" class="btn btn-filter <?php if ($type == '') {
                echo 'active';
            } ?>"><?php echo __('Aktivni'); ?></a>
            <a href="/app/?m=default&p=business_trip&t=3" class="btn btn-filter <?php if ($type == '3') {
                echo 'active';
            } ?>"><?php echo __('Arhiva'); ?></a>

            <br/><br/>

            <?php

            if ($total < 0) {

                foreach ($query as $item) {
                    $tools_id = $item['request_id'];

                    $border = '';

                    if ($item['country_ino'] == 1) {

                        if ((($item['status_hr'] == 1) or ($item['status_admin2_response'] == 1) or ($item['status_admin_response'] == 1)) and ($item['country_ino'] == 1)) {
                            $border = 'green';
                        } elseif ((($item['status_hr'] == 2) or ($item['status_admin2_response'] == 2) or ($item['status_admin_response'] == 2)) and ($item['country_ino'] == 1)) {
                            $border = 'red';
                        } elseif ((($item['status_hr'] == 3) or ($item['status_admin2_response'] == 3) or ($item['status_admin_response'] == 3)) and ($item['country_ino'] == 1)) {
                            $border = 'gray';
                        } elseif (($item['status_hr'] == 0) and ($item['status_admin2_response'] == 0) and ($item['status_admin_response'] == 0) and ($item['country_ino'] == 1)) {
                            $border = 'blue';
                        }


                    }

                    if ($item['country_ino'] != 1) {

                        if ((($item['status'] == 0) and ($item['status_parent2'] == 0)) and ($item['status_hr'] == 0) and ($item['status_admin2_response'] == 0 and $item['status_admin_response'] == 0) and ($item['country_ino'] != 1)) {
                            $border = 'blue';
                        } elseif ((($item['status'] == 1) or ($item['status_parent2'] == 1)) and ($item['status_hr'] == 0) and ($item['status_admin2_response'] == 0 and $item['status_admin_response'] == 0) and ($item['country_ino'] != 1)) {
                            $border = 'blue';
                        } elseif ((($item['status'] == 0) and ($item['status_parent2'] == 0)) and ($item['status_hr'] == 1) and ($item['status_admin2_response'] == 0 and $item['status_admin_response'] == 0) and ($item['country_ino'] != 1)) {
                            $border = 'blue';
                        } elseif (($item['status'] == 1) and ($item['status_hr'] == 1) and ($item['status_admin2_response'] == 0 and $item['status_admin_response'] == 0) and ($item['country_ino'] != 1)) {
                            $border = 'green';
                        } elseif (($item['status'] == 1) and ($item['status_hr'] == 0) and ($item['status_admin2_response'] == 1 or $item['status_admin_response'] == 1) and ($item['country_ino'] != 1)) {
                            $border = 'green';
                        } elseif (($item['status'] == 0) and ($item['status_hr'] == 0) and ($item['status_admin2_response'] == 1 or $item['status_admin_response'] == 1) and ($item['country_ino'] != 1)) {
                            $border = 'green';
                        } elseif ((($item['status'] == 2) or ($item['status_parent2'] == 2) or ($item['status_hr'] == 2) or ($item['status_admin_response'] == 2) or ($item['status_admin2_response'] == 2)) and ($item['country_ino'] != 1)) {
                            $border = 'red';
                        } elseif ((($item['status'] == 2) or ($item['status_parent2'] == 2)) and ($item['status_hr'] == 0) and ($item['country_ino'] != 1)) {
                            $border = 'red';
                        } elseif ((($item['status'] == 3) or ($item['status_parent2'] == 3) or ($item['status_hr'] == 3)) and ($item['country_ino'] != 1)) {
                            $border = 'gray';
                        }
                    }



                    $parent = _user($item['parent_id']);
                    $parent_tr = _user($item['parent']);
                    $_hr = _user($item['hr']);


                    ?>

                    <div class="box box-lborder box-lborder-<?php echo $border; ?>" id="opt-<?php echo $tools_id; ?>">
                        <div class="content">
                            <div class="row">
                                <div class="col-sm-4">
                                    <?php echo __('Zahtjev za službeno putovanje kreirano'); ?>
                                    <br/>
                                    <?php echo __('Od:'); ?>
                                    <b><?php echo date('d/m/Y', strtotime($item['h_from'])); ?></b> &nbsp;
                                    <?php echo __('Do:'); ?>
                                    <b><?php echo date('d/m/Y', strtotime($item['h_to'])); ?></b><br/>


                                    <?php if ($item['comment'] != '') { ?><br/>
                                        <b><?php echo $_user['fname'] . ' ' . $_user['lname']; ?></b>

                                        <?php echo $item['comment'];
                                    } ?>

                                    <?php if ($item['comment_parent'] != '') { ?><br/>
                                        <b><?php echo $parent_f . ' ' . $parent_l; ?></b>

                                        <?php echo $item['comment_parent'];
                                    } ?>

                                    <?php if ($item['comment_parent2'] != '') { ?><br/>
                                        <b><?php echo $parent2_f . ' ' . $parent2_l; ?></b>

                                        <?php echo $item['comment_parent2'];
                                    } ?>


                                    <?php if ($item['comment_hr'] != '') { ?><br/>
                                        <b><?php echo $hr_f . ' ' . $hr_l; ?></b>

                                        <?php echo $item['comment_hr'];
                                    } ?>

                                    <?php if ($item['comment_admin'] != '') { ?><br/>
                                        <b><?php echo $admin_f . ' ' . $admin_l; ?></b>

                                        <?php echo $item['comment_admin'];
                                    } ?>


                                    <blockquote class="comment-list">
                                        <?php if ((($item['status_hr'] == 1) or ($item['status_admin2_response'] == 1) or ($item['status_admin_response'] == 1)) and ($item['country_ino'] == 1)) { ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php echo __('Odobreno'); ?></span>
                                        <?php } else if ((($item['status_hr'] == 2) or ($item['status_admin2_response'] == 2) or ($item['status_admin_response'] == 2)) and ($item['country_ino'] == 1)) { ?>
                                            &nbsp; &nbsp; <span style="color:#990000;"><i
                                                        class="ion-android-close"></i> <?php echo __('Odbijeno'); ?></span>
                                        <?php } else if (($item['status_hr'] == 0) and ($item['status_admin2_response'] == 0) and ($item['status_admin_response'] == 0) and ($item['country_ino'] == 1)) { ?>
                                            &nbsp; &nbsp; <span style="color:#ffaa00;"><i
                                                        class="ion-android-time"></i> <?php echo __('Na odobrenju uprave...'); ?></span>
                                        <?php } else if ((($item['status'] == 2) or ($item['status_hr'] == 2) or ($item['status_admin2_response'] == 2) or ($item['status_admin_response'] == 2)) and ($item['country_ino'] != 1)) { ?>
                                            &nbsp; &nbsp; <span style="color:#990000;"><i
                                                        class="ion-android-close"></i> <?php echo __('Odbijeno'); ?></span>
                                        <?php } else if (($item['status'] == 0) and ($item['status_hr'] == 0) and ($item['status_admin2_response'] == 0 and $item['status_admin_response'] == 0) and ($item['country_ino'] != 1)) { ?>
                                            &nbsp; &nbsp; <span style="color:#ffaa00;"><i
                                                        class="ion-android-time"></i> <?php echo __('Na odobrenju nadređenog...'); ?></span>
                                        <?php } else if (($item['status'] == 1) and ($item['status_hr'] == 0) and ($item['status_admin2_response'] == 0 and $item['status_admin_response'] == 0) and ($item['country_ino'] != 1)) { ?>
                                            &nbsp; &nbsp; <span style="color:#ffaa00;"><i
                                                        class="ion-android-time"></i> <?php echo __('Na odobrenju uprave...'); ?></span>
                                        <?php } else if ((($item['status'] == 1) or ($item['status_parent2'] == 1)) and ($item['status_hr'] == 1) and ($item['country_ino'] != 1)) { ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php echo __('Odobreno'); ?></span>
                                        <?php } else if (($item['status'] == 1) and ($item['status_hr'] == 0) and ($item['status_admin2_response'] == 1 or $item['status_admin_response'] == 1) and ($item['country_ino'] != 1)) { ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php echo __('Odobreno od administratora'); ?></span>
                                        <?php } else if (($item['status'] == 0) and ($item['status_hr'] == 0) and ($item['status_admin2_response'] == 1 or $item['status_admin_response'] == 1) and ($item['country_ino'] != 1)) { ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php echo __('Odobreno od administratora'); ?></span>
                                        <?php } ?>


                                </div>


                                <div class="col-sm-2">


                                    <?php echo __('Zahtjev kreiran:'); ?><br/>
                                    <?php echo date('d/m/Y', strtotime($item['date_created'])); ?>
                                </div>
                                <div class="col-sm-4">
                                    <?php echo __('Status:'); ?><br/>
                                    <?php if ($item['date_response'] != '1970-01-01') { ?>

                                        <?php if ($item['status'] == 1) {
                                            ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php echo __('Odobrio/la') . ' ' . $parent_f . ' ' . $parent_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_response'])); ?></span>
                                            <br>
                                        <?php } else if ($item['status'] == 2) { ?>
                                            &nbsp; &nbsp; <span style="color:#990000;"><i
                                                        class="ion-android-close"></i> <?php echo __('Odbio/la') . ' ' . $parent_f . ' ' . $parent_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_response'])); ?></span>
                                            <br>
                                        <?php } ?>

                                        <?php if ($item['status_parent2'] == 1) {
                                            ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php echo __('Odobrio/la') . ' ' . $parent2_f . ' ' . $parent2_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_response_parent2'])); ?></span>
                                            <br>
                                        <?php } else if ($item['status_parent2'] == 2) { ?>
                                            &nbsp; &nbsp; <span style="color:#990000;"><i
                                                        class="ion-android-close"></i> <?php echo __('Odbio/la') . ' ' . $parent2_f . ' ' . $parent2_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_response'])); ?></span>
                                            <br>
                                        <?php } ?>

                                        <?php if ($item['status_parent2_edit'] == 1) { ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php echo __('Editovao/la') . ' ' . $parent2_f . ' ' . $parent2_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_parent2_edit'])); ?></span>
                                            <br>
                                        <?php } else if ($item['status_parent2_edit'] == 2) { ?>
                                            &nbsp; &nbsp; <span style="color:#990000;"><i
                                                        class="ion-android-close"></i> <?php echo __('Odbio/la') . ' ' . $parent2_f . ' ' . $parent2_l . ' ' . "---" . ' ' . date('d/m/Y', strtotime($item['date_parent2_edit'])); ?></span>
                                            <br>
                                        <?php } ?>


                                        <?php if ($item['status_admin2_response'] == 1) { ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php if (isset($admin2_f) and isset($admin2_l)) {
                                                    echo __('Odobrio/la') . ' ' . $admin2_f . ' ' . $admin2_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_response_status_admin2']));
                                                } ?></span><br>
                                        <?php } else if ($item['status_admin2_response'] == 2) { ?>
                                            &nbsp; &nbsp; <span style="color:#990000;"><i
                                                        class="ion-android-close"></i>   <?php echo __('Odbio/la') . ' ' . $admin2_f . ' ' . $admin2_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_response_status_admin2'])); ?></span>
                                            <br>
                                        <?php } ?>

                                        <?php if ($item['status_admin_response'] == 1) { ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php if (isset($admin_f) and isset($admin_l)) {
                                                    echo __('Odobrio/la') . ' ' . $admin_f . ' ' . $admin_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_status_response_admin']));
                                                } ?></span><br>
                                        <?php } else if ($item['status_admin_response'] == 2) { ?>
                                            &nbsp; &nbsp; <span style="color:#990000;"><i
                                                        class="ion-android-close"></i>   <?php echo __('Odbio/la') . ' ' . $admin_f . ' ' . $admin_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_status_response_admin'])); ?></span>
                                            <br>
                                        <?php } ?>


                                        <?php if ($item['status_hr'] == 1) { ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php echo __('Odobrio/la') . ' ' . $hr_f . ' ' . $hr_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_response_hr'])); ?></span>
                                            <br>
                                        <?php } else if ($item['status_hr'] == 2) { ?>
                                            &nbsp; &nbsp; <span style="color:#990000;"><i
                                                        class="ion-android-close"></i> <?php echo __('Odbio/la') . ' ' . $hr_f . ' ' . $hr_l . ' ' . "---" . ' ' . date('d/m/Y', strtotime($item['date_response_hr'])); ?></span>
                                            <br>
                                        <?php } ?>

                                        <?php if ($item['status_user_edit'] == 1) { ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php echo __('Editovao/la') . ' ' . $_user['fname'] . ' ' . $_user['lname'] . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_user_edit'])); ?></span>
                                            <br>
                                        <?php } else if ($item['status_user_edit'] == 2) { ?>
                                            &nbsp; &nbsp; <span style="color:#990000;"><i
                                                        class="ion-android-close"></i> <?php echo __('Odbio/la') . ' ' . $_user['fname'] . ' ' . $_user['lname'] . ' ' . "---" . ' ' . date('d/m/Y', strtotime($item['date_user_edit'])); ?></span>
                                            <br>
                                        <?php } ?>

                                        <?php if ($item['status_parent_edit'] == 1) { ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php echo __('Editovao/la') . ' ' . $parent_f . ' ' . $parent_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_parent_edit'])); ?></span>
                                            <br>
                                        <?php } else if ($item['status_parent_edit'] == 2) { ?>
                                            &nbsp; &nbsp; <span style="color:#990000;"><i
                                                        class="ion-android-close"></i> <?php echo __('Odbio/la') . ' ' . $_user['fname'] . ' ' . $_user['lname'] . ' ' . "---" . ' ' . date('d/m/Y', strtotime($item['date_parent_edit'])); ?></span>
                                            <br>
                                        <?php } ?>

                                        <?php if ($item['status_hr_edit'] == 1) { ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php echo __('Editovao/la') . ' ' . $hr_f . ' ' . $hr_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_hr_edit'])); ?></span>
                                            <br>
                                        <?php } else if ($item['status_hr_edit'] == 2) { ?>
                                            &nbsp; &nbsp; <span style="color:#990000;"><i
                                                        class="ion-android-close"></i> <?php echo __('Odbio/la') . ' ' . $hr_f . ' ' . $hr_l . ' ' . "---" . ' ' . date('d/m/Y', strtotime($item['date_hr_edit'])); ?></span>
                                            <br>
                                        <?php } ?>

                                        <?php if ($item['status_admin'] == 1) { ?>
                                            &nbsp; &nbsp; <span style="color:#009900;"><i
                                                        class="ion-android-checkmark-circle"></i> <?php echo __('Editovao/la') . ' ' . $admin_f . ' ' . $admin_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_response_admin'])); ?></span>
                                            <br>
                                        <?php } else if ($item['status_admin'] == 2) { ?>
                                            &nbsp; &nbsp; <span style="color:#990000;"><i
                                                        class="ion-android-close"></i> <?php echo __('Odbio/la') . ' ' . $admin_f . ' ' . $admin_l . ' ' . "---" . ' ' . date('d/m/Y H:i', strtotime($item['date_response_admin'])); ?></span>
                                            <br>
                                        <?php } ?>
                                        <br/>

                                    <?php } else {
                                        echo
                                        '&nbsp;';
                                    } ?>

                                </div>
                                <div class="col-sm-2 text-right">
                                    <?php if ($item['lock'] != 'N' and $item['status'] != 2 and ($item['status_admin_response'] != 2) and ($item['status_admin2_response'] != 2)) { ?>
                                        <!--  <a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>" class="table-btn alt" data-widget="remove" data-id="requests_remove:<?php echo $tools_id; ?>" data-text="<?php echo __('Dali ste sigurni da želite poništiti zahtjev?'); ?>"><i class="ion-android-close"></i></a> -->
                                        <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view&id=' . $tools_id; ?>"
                                           class="table-btn alt" title="Pregled"><i class="ion-eye"></i></a>
                                        <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_user&id=' . $tools_id; ?>"
                                           class="table-btn alt" title="Izmjene"><i class="ion-edit"></i></a>
                                    <?php } else { ?>
                                        <!--  <a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>" class="table-btn alt" data-widget="remove" data-id="requests_archive:<?php echo $tools_id; ?>" data-text="<?php echo __('Dali ste sigurni da želite arhivirati zahtjev?'); ?>"><i class="ion-folder"></i></a> -->
                                        <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view&id=' . $tools_id; ?>"
                                           class="table-btn alt" title="Pregled"><i class="ion-eye"></i></a>

                                    <?php } ?>

                                </div>
                            </div>
                        </div>
                    </div>

                <?php }
            } else { ?>
                <div class="text-center">
                    <?php echo __('Nema spašenih zahtjeva prema odabranim parametrima.'); ?>
                </div>
            <?php } ?>

            <div class="text-center">
                <div class="btn-group">
                    <?php echo _pagination($path, $_num, $limit, $total); ?>
                </div>
            </div>


        </div>


    </section>

    <?php
} else {
    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Pogrešan ID stranice, molimo kontaktirajte administratora.') . '</div>';
}
?>
<!-- END - Main section -->

<?php

include $_themeRoot . '/footer.php';

?>

</body>
</html>
