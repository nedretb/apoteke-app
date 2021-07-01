<?php
_pagePermission(5, false);
?>

<!-- START - Main section -->
<section class="full tasks-page">

    <br/>

    <div class="container-fluid">
        <div class="row" style="margin:20px;">
            <div class="col-sm-2">
                <div class="box">


                    <?php
                    $rok_cilj = '';
                    $procjena = '';
                    $evaluacija = '';
                    $query_deadline = $db->query("SELECT * FROM  " . $portal_objective_deadline . "  WHERE YEAR = " . date("Y"));
                    foreach ($query_deadline as $item) {
                        $year = $item['year'];
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


                    <?php
                    $get_image = $db->query("SELECT image FROM  " . $portal_users . "  WHERE user_id= '" . $_user['user_id'] . "'");
                    if ($_user['image'] != 'none') {

                    } ?>


                </div>
            </div>
            <div class="col-sm-2">
                <big><b><?php echo $_user['fname'] . ' ' . $_user['lname']; ?></b></big><br/>
                <?php echo _role($_user['role']); ?>
            </div>
            <div class="col-sm-2" style="width: 170px;">
                <div class="btn btn-filter active-task" style="margin-bottom: 15px;width: 130px;"><a style="color:black"
                                                                                                     class="active-black"
                                                                                                     href="?m=default&amp;p=mbo&f=0">
                        Moj MBO Profil</a></div>
            </div>
            <div class="col-sm-2">
                <div class="btn btn-filter active-task" style="margin-bottom: 15px;width: 130px;"><a style="color:black"
                                                                                                     class="active-black"
                                                                                                     href="?m=default&p=tasks&f=1">
                        Postavljanje ciljeva</a></div>
            </div>


        </div>

        <div class="row">
            <div class="col-sm-8">

                <span style="height:20px;width: 969px;padding-left: 10px;display:block; background-color:#006595;">To-do lista</span><br/><br/>
                <label style="height:20px; background:#006595 ;color: #ffffff;width: 969px;padding-left: 10px;">Rok</label><br/><br/>
                <div class="col-sm-4">


                    <?php
                    if ($_user['role'] == '1' || $_user['role'] == '2') {
                        echo '<label style="height:20px;color: black;width: 400px;">Moji ciljevi</label><br/>';
                        echo '<div class="tooltip"><a href="' . $url . '/?m=default&p=tasks" style="color:blue;"><label style="cursor:pointer;color:#00f;">' . $_user['fname'] . '&nbsp;' . $_user['lname'] . '</label></a><span class="tooltiptext"><img src="' . $_uploadUrl . '/' . $_user['image'] . '" style="width:100%;"></span></div><br/>';
                        echo '<label style="height:20px;color: red;width: 400px;">' . date('d/m/Y', strtotime($rok_cilj)) . '</label><br/>';
                        echo '<label style="height:20px;color: black;width: 400px;">Postavljanje ciljeva za Ime i prezime zaposlenika</label><br/>';
                        echo '<label style="height:20px;color: red;width: 400px;">' . date('d/m/Y', strtotime($rok_cilj)) . '</label><br/>';

                        $_user_role = $_user['role'];
                        $get_departments = $db->query("SELECT DISTINCT sector FROM  " . $portal_users . "  WHERE parent = " . $_user['employee_no']);
                        $get_users2 = $db->query("SELECT count(*) FROM  " . $portal_users . "  WHERE parent = " . $_user['employee_no']);
                        if ($get_users2->rowCount() < 0) {
                            foreach ($get_departments as $sector) {
                                echo '<label style="width:350px;"><b>' . $sector['sector'] . '</b></label><br/>';
                                $get_users = $db->query("SELECT * FROM  " . $portal_users . "  WHERE parent = " . $_user['employee_no'] . " AND sector= '" . $sector['sector'] . "'");
                                foreach ($get_users as $user) {

                                    echo '<div class="tooltip"><a href="' . $url . '/?m=tasks&p=all&u=' . $user['user_id'] . '" style="color:blue;"><label style="cursor:pointer;color:#00f;">' . $user['fname'] . '&nbsp;' . $user['lname'] . '</label></a><span class="tooltiptext"><img src="' . $_uploadUrl . '/' . $user['image'] . '" style="width:100%;"></span></div><br/>';
                                }

                            }
                            echo '<label style="height:20px;color: black;width: 400px;">Polugodišnja procjena</label><br/>';
                            echo '<label style="height:20px;color: red;width: 400px;">' . date('d/m/Y', strtotime($procjena)) . '</label><br/>';
                            echo '<label style="height:20px;color: black;width: 400px;">Evaluacija ciljeva krajem godine</label><br/>';
                            echo '<label style="height:20px;color: red;width: 400px;">' . date('d/m/Y', strtotime($evaluacija)) . '</label><br/><br/><br/>';
                        }
                    } else {
                        echo '<label style="height:20px;color: black;width: 400px;">Postavljanje ciljeva</label><br/>';
                        echo '<label style="height:20px;color: red;width: 400px;">' . date('d/m/Y', strtotime($rok_cilj)) . '</label><br/>';
                        echo '<label style="height:20px;color: black;width: 400px;">Polugodišnja procjena</label><br/>';
                        echo '<label style="height:20px;color: red;width: 400px;">' . date('d/m/Y', strtotime($procjena)) . '</label><br/>';
                        echo '<label style="height:20px;color: black;width: 400px;">Evaluacija ciljeva krajem godine</label><br/>';
                        echo '<label style="height:20px;color: red;width: 400px;">' . date('d/m/Y', strtotime($evaluacija)) . '</label><br/><br/><br/>';
                    } ?>


                </div>

                <div class="col-sm-8" style="margin-left: -60px;">

                
                    <label style="height:20px;color: black;width: 60px;">Legenda: </label>
                    <label style="height:20px;color: black;width: 90px;"><i class="fa fa-exclamation-circle"
                                                                            aria-hidden="true"
                                                                            style="color:red;margin-right:6px;"></i>Rok
                        istekao</label>
                    <label style="height:20px;color: black;width: 60px;"><i class="fa fa-circle-o" aria-hidden="true"
                                                                            style="color: darkorange;margin-right:3px;"></i>U
                        toku</label>
                    <label style="height:20px;color: black;width: 140px;"><i class="fa fa-clock-o" aria-hidden="true"
                                                                             style="color:blue;margin-right:6px;"></i>Poslano
                        na pregled</label>
                    <label style="height:20px;color: black;width: 100px;"><i class="fa fa-circle-o" aria-hidden="true"
                                                                             style="color:gray;margin-right:3px;"></i>Nije
                        spremno</label>
                    <label style="height:20px;color: black;width: 90px;"><i class="fa fa-check-circle"
                                                                            aria-hidden="true"
                                                                            style="color:green;margin-right:6px;"></i>Završeno</label>

                </div>

            </div>


            <div class="col-sm-4">
                <span style="background-color:#006595;height:20px;width: 400px;padding-left: 10px;display:block;">Linkovi</span><br/><br/>
                <label style="height:20px;color: black;width: 400px;"></label><br/><br/>
                <label style="height:20px;color: black;width: 400px;"><u></u></label><br/><br/><br/>

                <span style="background-color:#006595;height:20px;width: 400px;padding-left: 10px;display:block">Dobrodošli</span><br/><br/>
                <label style="height:20px;color: black;width: 400px;"><b>Dobrodošli u MBO sistem za upravljanje radnim
                        učinkom!</b></label><br/>
                <label style="height:20px;color: black;width: 400px;"><u>Sistem je namijenjen za postavljanje ciljeva,
                        upravljanje</u></label><br/>
                <label style="height:20px;color: black;width: 400px;"><u>radnim učinkom i planiranje razvoja
                        zaposlenika.</u></label><br/>
                <label style="height:20px;color: black;width: 400px;">_</label><br/>
                <label style="height:20px;color: black;width: 400px;">Više informacija o procesu upravljanja
                    radnim</label><br/>
                <label style="height:20px;color: black;width: 400px;"> učinkom možete dobiti na sljedećem <u>linku</u>.</label><br/>
                <a href="<?php echo $url . '/uploads/MBO smjernice.docx'; ?>">
                    <img border="0" alt="W3Schools" src="<?php echo $_uploadUrl . '/word_ciljevi.png'; ?>" width=""
                         height="">
                </a>
            </div>
        </div>

    </div>
    </div>

    <?php
    $limit = 20;
    $where = "WHERE user_id='" . $_user['user_id'] . "' AND year = " . date("Y");

    $query = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_tasks . "  " . $where . " ORDER BY task_type, date_created DESC");
    $total = $query->rowCount();
    ?>

    <div class="col-sm-6">
        <span style="height:20px;width: 967px;padding-left: 10px;display:block;margin-left:1px;">Moji Ciljevi</span>

        <?php if ($total < 0) {
            $ponder_sum = 0; ?>

            <table class="alt" id="obuke" style="font-family: 'Titillium Web', sans-serif;">
                <tr style="line-height: 5px;">
                    <th style="height:10px;width: 500px;">Naziv</th>
                    <th style="height:10px;width: 200px;">KPI</th>
                    <th style="height:10px;width: 100px;">Rok</th>
                    <th style="height:10px;width: 100px;">Ponder (%)</th>

                </tr>

                <?php
                foreach ($query as $item) {
                    if ($item['status'] != '4' and $item['status'] != '5' and $item['ponder'] != '0' and $item['ponder'] != '') { ?>

                        <tr>
                            <td><input readonly="readonly" type="text" class="rcorners1" style="width:500px;"
                                       name="oj[]" value="<?php echo $item['task_name']; ?>"></td>
                            <td><input readonly="readonly" type="text" class="rcorners1" style="width:200px;"
                                       name="oj[]" value="<?php echo $item['KPI']; ?>"></td>
                            <td><input readonly="readonly" type="text" class="rcorners1" style="width:100px;"
                                       name="item_description[]"
                                       value="<?php echo date('d/m/Y', strtotime($item['date_end'])); ?>"></td>
                            <td><input readonly="readonly" type="text" class="rcorners1" style="width:100px;"
                                       name="oj[]" value="<?php echo $item['ponder']; ?>"></td>
                        </tr>
                        <?php
                        $ponder_sum = $ponder_sum + $item['ponder'];
                    }
                } ?>

            </table>

            <?php echo '<label class = "mbo-font" style="width:200px;margin-left:854px;"><b>Ukupno</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $ponder_sum . ' %</label>';
        } else {

            echo '<div class="text-center">';
            echo __('Nema spašenih zadataka prema odabranim parametrima.');
        } ?>


        <br/><br/><br/>


    </div>
    </div>
    </div>
</section>
<!-- END - Main section -->

<?php

include $_themeRoot . '/footer.php';

?>
<script>

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
</script>


</body>
</html>
