<?php
_pagePermission(5, false);

error_reporting(0);

foreach (glob($root . '/modules/profile/pages/classes/*.php') as $filename) require_once $filename;

$kontakt    = Kontakt::getData($_user['employee_no']);
$kontakt = isset($kontakt[0]['users__kontakt_informacije']) ? $kontakt[0]['users__kontakt_informacije'][0] : null;

//$sys = Sistematizacija::getIDs(1);
//foreach ($sys as $s ){
//
//    $get_parent = $db->query("select employee_no from [c0_intranet2_apoteke].[dbo].[users] where rukovodioc='DA' and egop_ustrojstvena_jedinica=".$s)->fetchAll();
//    if ($get_parent[0]['employee_no'] != null){
//            $update_children = $db->query("update [c0_intranet2_apoteke].[dbo].[users] set parent=".$get_parent[0]['employee_no']." where rukovodioc='NE' and egop_ustrojstvena_jedinica=".$s);
//    }
////    var_dump($get_parent[0]['employee_no']);
//}
//var_dump($sys);

?>

<!-- START - Main section -->
<body class="bg-rf notable">
<section class="full" style="margin-top:15px;">
    <?php
        if (isset($_GET['u'])) $_user = _user($_GET['u']);
        $get = $db->query("SELECT * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['employee_no'] . "'");
        if ($get->rowCount() < 0)
            $row_personal = $get->fetch();

        $x_user = _user(_decrypt($_SESSION['SESSION_USER']));
    ?>

    <div class="container-fluid tooltip-static">
        <div id="res"></div>
        <div class="row">
            <?php
                if ($x_user['user_id'] == $_user['user_id']){
            ?>
        <div class="col-sm-6 satnice-months" style="float: right">

        <div class="box">
        <div class="head">
        <h3><?php echo __('Satnice'); ?>
        <?php if (($_user['role'] == '0') or ($_user['role'] == '4')){
        ?>

        <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_year_add_new.php'; ?>" data-widget="ajax"
           data-id="opt2" data-width="200" class="btn btn-warning btn-sm pull-right"
           style="background: #006595;color: black;    width: 115px;
    display: flex;line-height:90%;padding-bottom:2%;padding-top:2%;margin-top: -1.3%;"><?php echo __('Dodaj godinu'); ?>

            <i style="line-height: 2vw;" class="ion-ios-plus-empty"></i></a>

        </h3>
        </div>
        <div class="content">

            <div class="row">
                <?php

                $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE user_id='" . $_user['user_id'] . "'ORDER BY year ASC");
                $get_y = $db->query("SELECT COUNT(*) FROM  " . $portal_hourlyrate_year . "  WHERE user_id='" . $_user['user_id'] . "'");
                $result = $get_y->rowCount();

                if ($result < 0) {

                    foreach ($get_year as $year) {
                        echo '<div class="col-xs-3 col-sm-3" id="opt-year-' . $year['id'] . '">';
                        $get_month = $db->query("SELECT * FROM  " . $portal_hourlyrate_month . "  WHERE year_id='" . $year['id'] . "' AND user_id='" . $_user['user_id'] . "' ORDER BY month ASC");
                        echo '<h4>' . $year['year'];
                        echo '</h4>';
                        if ($get_month->rowCount() < 0) {
                            echo '<ul >';
                            foreach ($get_month as $month) {
                                $blue = '';
                                if ($month['month'] == date("n") and $year['year'] == date("Y")) {
                                    $blue = "class='blue'";
                                }
                                echo '<li ' . $blue . ' id="opt-month-' . $month['id'] . '"><a  href="' . $url . '/?m=' . $_mod . '&p=hourlyrate_days&year=' . $year['id'] . '&month=' . $month['id'] . '">' . _nameMonth($month['month']) . '</a>';
                                echo '</li>';
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<div class="text-center">' . __('Još niste počeli unositi satnice') . '</div>';
                }
                ?>
            </div>
        </div>
        </div>
        <?php } ?>

        <?php if (($_user['role'] == '1') or ($_user['role'] == '2') or ($_user['role'] == '3') or ($_user['role'] == '5')){ ?>

        </h3>
        </div>
            <div class="content">

                <div class="row ">
                    <?php

                    $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE user_id='" . $_user['user_id'] . "'ORDER BY year ASC");
                    $get_y = $db->query("SELECT COUNT(*) FROM  " . $portal_hourlyrate_year . "  WHERE user_id='" . $_user['user_id'] . "'");
                    $result = $get_y->rowCount();


                    if ($result < 0) {


                        foreach ($get_year as $year) {

                            echo '<div class="col-xs-3 col-sm-3" id="opt-year-' . $year['id'] . '">';

                            $get_month = $db->query("SELECT * FROM  " . $portal_hourlyrate_month . "  WHERE year_id='" . $year['id'] . "' AND user_id='" . $_user['user_id'] . "' ORDER BY month ASC");

                            echo '<h4>' . $year['year'];

                            echo '</h4>';

                            if ($get_month->rowCount() < 0) {

                                echo '<ul>';
                                foreach ($get_month as $month) {
                                    $blue = '';

                                    if ($month['month'] == date("n") and $year['year'] == date("Y")) {
                                        $blue = "class='blue'";
                                    }

                                    echo '<li ' . $blue . ' id="opt-month-' . $month['id'] . '"><a style="color:white;" href="' . $url . '/?m=' . $_mod . '&p=hourlyrate_days&year=' . $year['id'] . '&month=' . $month['id'] . '">' . _nameMonth($month['month']) . '</a>';
                                    echo '</li>';
                                }
                                echo '</ul>';

                            }

                            echo '</div>';

                        }

                    } else {
                        echo '<div class="text-center">' . __('Još niste počeli unositi satnice') . '</div>';
                    }

                    ?>
                </div>

            </div>
        <?php } ?>
            <div class="col-sm-14" style="    margin-top: 25px;">
                <div class="box" show=tr>

                    <div class="head">
                        <h3><?php echo __('Korekcije'); ?>


                        </h3>
                    </div>
                    <div class="content">

                        <div class="row">
                            <?php

                            $get_yearc = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE user_id='" . $_user['user_id'] . "'ORDER BY year ASC");
                            $get_yc = $db->query("SELECT COUNT(*) FROM  " . $portal_hourlyrate_year . "  WHERE user_id='" . $_user['user_id'] . "'");
                            $resultc = $get_yc->rowCount();


                            if ($resultc < 0) {


                                foreach ($get_yearc as $yearc) {

                                    echo '<div class="col-xs-3 col-sm-3" id="opt-year-' . $yearc['id'] . '">';

                                    $get_monthc = $db->query("SELECT * FROM  " . $portal_hourlyrate_month_correctoins . "  WHERE year_id='" . $yearc['id'] . "' AND user_id='" . $_user['user_id'] . "' ORDER BY month ASC");
                                    $get_monthc = $db->query("SELECT * FROM  " . $portal_hourlyrate_month_correctoins . "  WHERE year_id='" . $yearc['id'] . "' AND user_id='" . $_user['user_id'] . "' ORDER BY month ASC");
                                    echo '<h4>' . $yearc['year'];


                                    echo '</h4>';

                                    if ($get_monthc->rowCount() < 0) {

                                        echo '<ul>';
                                        foreach ($get_monthc as $monthc) {

                                            if (($monthc['month'] < date("n") or (date("Y") > $yearc['year'])) and ($yearc['year'] <= date("Y"))) {
                                                echo '<li id="opt-month-' . $month['id'] . '"><a style="color:white;" href="' . $url . '/?m=' . $_mod . '&p=hourlyrate_days_corrections&year=' . $yearc['id'] . '&month=' . $monthc['month'] . '">' . _nameMonth($monthc['month']) . '</a>';
                                                echo '</li>';
                                            }

                                        }
                                        echo '</ul>';

                                    }
                                    if (($get_monthc->rowCount() < 12) and ($_user['role'] == '4')) {

                                    }
                                    echo '</div>';

                                }

                            } else {
                                echo '<div class="text-center">' . __('Još niste počeli unositi satnice') . '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php
                if ($x_user['user_id'] == $_user['user_id']){
                ?>
            </div>
        <?php
        }
        ?>
            <?php if ($_user['role'] != 4){ ?>
        </div>
    </div>
<?php } else {
    ?>
    </div>
    <?php
} ?>

    <div class="col-sm-6" style="float: left">
        <div class="box">
            <div class="content" id="c1a">
                <div class="profile-basic">
                    <div class="pb-img">
                        <img src="theme/images/profile-images/<?= $_user['image'] ?>">
                    </div>
                    <div class="pb-data">
                        <h3><?= $_user['fname'] . ' ' . $_user['lname']; ?></h3>
                        <h4>
                            <p> <?= ___('Personalni broj') ?> </p>
                            <span>Z<?= $_user['employee_no'] ?></span>
                        </h4>
                        <h4>
                            <p> <?= ___('Jedinstveni matični broj') ?> </p>
                            <span><?= $_user['JMB'] ?></span>
                        </h4>
                    </div>
                </div>

                <div class="profile-basic pb-row">
                    <div class="pb-left">
                        <p> <?= ___('Broj telefona') ?></p>
                    </div>
                    <!-- TODO : Pozivni broj telefona !? -->
                    <div class="pb-right">
                        <h4>
                            <?= isset($kontakt) ? ($kontakt['kucni_telefonski_broj'].' '.$kontakt['kucni_regionalni_kod'].' '.$kontakt['kucni_broj']) : '' ?>
                        </h4>
                    </div>
                </div>
                <div class="profile-basic pb-row">
                    <div class="pb-left">
                        <p> <?= ___('Broj mobitela') ?> </p>
                    </div>
                    <!-- TODO : Pozivni broj telefona !? -->
                    <div class="pb-right">
                        <h4><?= isset($kontakt) ? ($kontakt['privatni_mobitel_broj'].' '.$kontakt['mobitel_regionalni_kod'].' '.$kontakt['mobitel_broj']) : '' ?></h4>
                    </div>
                </div>
                <div class="profile-basic pb-row">
                    <div class="pb-left">
                        <p> <?= ___('Službeni email') ?> </p>
                    </div>
                    <div class="pb-right">
                        <h4><?= $_user['email_company']; ?></h4>
                    </div>
                </div>
                <div class="profile-basic pb-row">
                    <div class="pb-left">
                        <p><?= ___('Datum zaposlenja') ?></p>
                    </div>
                    <div class="pb-right">
                        <h4><?php echo date('d.m.Y', strtotime($_user['employment_date'])); ?></h4>
                    </div>
                </div>

                <!---------------------------------------------------------------------------------------------------->

                <div class="profile-basic pb-row">
                    <div class="pb-left">
                        <p><?= ___('Naziv radnog mjesta') ?></p>
                    </div>
                    <div class="pb-right">
                        <h4><?= $_user['egop_radno_mjesto']; ?></h4>
                    </div>
                </div>
                <div class="profile-basic pb-row">
                    <div class="pb-left">
                        <p><?= ___('Organizaciona jedinica') ?></p>
                    </div>
                    <div class="pb-right">
                        <h4><?= $_user['egop_ustrojstvena_jedinica']; ?></h4>
                    </div>
                </div>
                <div class="profile-basic pb-row">
                    <div class="pb-left">
                        <p><?= ___('Organ državne službe') ?></p>
                    </div>
                    <div class="pb-right">
                        <h4> <?= ___('Ministarstvo komunikacija i transporta') ?> </h4>
                    </div>
                </div>

                <!---------------------------------------------------------------------------------------------------->

                <div class="profile-basic pb-row">
                    <div class="pb-left">
                        <p><?= ___('Mjesto rada') ?></p>
                    </div>
                    <div class="pb-right">
                        <h4> Trg BiH 1, 71000 Sarajevo </h4>
                    </div>
                </div>

                <!---------------------------------------------------------------------------------------------------->
                <?php
                if($_user['role'] == 4){
                    ?>
                    <div class="profile-basic pb-row">
                        <div class="pb-left">
                            <p><?= ___('Dodatni linkovi') ?></p>
                        </div>
                        <div class="pb-right">
                            <h4>
                                <a href="?m=default&p=employees">Pregled radnika</a>,
                                <a href="?m=scheme&p=preview">Sistematizacija</a>
                            </h4>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

</section>
<!-- END - Main section -->

<?php

include $_themeRoot . '/footer.php';

if ($x_user['user_id'] != $_user['user_id']) {
    ?>
    <script>
        $("input").attr("disabled", "disabled");
        $("select").attr("disabled", "disabled");
    </script>
    <?php
}
?>

<script>

    var dtl = $("input");
    dtl.tooltip();
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

</script>

</body>
</html>
