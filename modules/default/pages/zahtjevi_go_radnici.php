<?php
require 'CORE/config-urls.php';
//filter data
$_POST['sektor'] = $_POST['IDB1'];
$_POST['odjel'] = $_POST['IDReg'];
$_POST['grupa'] = $_POST['IDStream'];
$_POST['tim'] = $_POST['IDTeam'];
$_POST['zaposlenik'] = $_POST['ime_prezime'];

$options['sektor'] = '';
$options['odjel'] = '';
$options['grupa'] = '';
$options['tim'] = '';
$options['zaposlenik'] = '';

//filter
if (!empty($_POST['year'])) {
    $year = $_POST['year'];
    $yearq = " and Year = $year";
} else {
    $year = '';
    $yearq = '';
}
if (!empty($_POST['IDB1'])) {
    $B_1 = $_POST['IDB1'];
    $B_1q = "and ecl.[Sector Description] like N'$B_1' COLLATE Latin1_General_CI_AI";
} else {
    $B_1 = '';
    $B_1q = '';
}
if (!empty($_POST['IDReg'])) {
    $region = $_POST['IDReg'];
    $regionq = "and ecl.[Department Cat_ Description] like N'$region' COLLATE Latin1_General_CI_AI";
} else {
    $region = '';
    $regionq = '';
}
if (!empty($_POST['IDStream'])) {
    $stream = $_POST['IDStream'];
    $streamq = "and ecl.[Group Description] like N'$stream' COLLATE Latin1_General_CI_AI";
} else {
    $stream = '';
    $streamq = '';
}
if (!empty($_POST['IDTeam'])) {
    $team = $_POST['IDTeam'];
    $teamq = "and ecl.[Team Description] like N'$team' COLLATE Latin1_General_CI_AI";
} else {
    $team = '';
    $teamq = '';
}
if (!empty($_POST['ime_prezime'])) {
    $ime_prezime = str_replace('  ', '', $_POST['ime_prezime']);
    $ime_prezimeq = "and v.[Employee No_] = $ime_prezime ";
} else {
    $ime_prezime = '';
    $ime_prezimeq = '';
}
//paginacija
$limit = 10;
if (!empty($_POST['pg'])) {
    $page = $_POST['pg'];
} else {
    $page = 1;
}

$offset = ($page - 1) * $limit;

//get data for table

$queryqa = $db->query("
    select * from  " . $nav_vacation_ground2 . "  as v
    left join  " . $nav_employee_contract_ledger . "  as ecl on v.[Employee No_]=ecl.[Employee No_]
    left join  " . $portal_users . "  as u on v.[Employee No_] = u.employee_no
    where ecl.Active= 1 and ecl.[Show Record] = 1 and Duration > 0 and (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO))
    $yearq $ime_prezimeq $B_1q $regionq $streamq $teamq
    order by v.[Employee No_]
    offset $offset rows
    FETCH NEXT $limit ROWS ONLY;");
$data = $queryqa->fetchAll();

$totalq = $db->query("select count(*) from  " . $nav_vacation_ground2 . "  as v
    left join  " . $nav_employee_contract_ledger . "  as ecl on v.[Employee No_]=ecl.[Employee No_]
    left join  " . $portal_users . "  as u on v.[Employee No_] = u.employee_no
    where ecl.Active= 1 and ecl.[Show Record] = 1 and Duration > 0 and (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8)  or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO))
    $yearq $ime_prezimeq $B_1q $regionq $streamq $teamq
    ");
$total = $totalq->fetch();
$pg_max = ceil($total[0] / $limit);
if ($pg_max == 0) {
    $pg_max = 1;
}

?>

<!-- START - Main section -->
<br/>
<style>
    .select2-container {
        box-sizing: border-box;
        display: inline-block;
        margin: 0;
        position: relative;
        vertical-align: middle;
        border-style: solid;
        border-width: 1px;
        border-color: #006595;
    }

    .myrow {
        margin-top: 15px;
        padding-bottom: 135px;
    }

    .buttons {
        padding: 20px 0 10px 0;
        display: flex;
        justify-content: space-around;
    }

    .lable-admin1 {
        width: 60px;
    }

    table {
        margin: 20px;
        margin-bottom: 0 !important;
    }

    .table-btn {
        padding: 3px 10px;
        width: auto;
    }

    .right_box {
        width: calc(100% - 350px);
        padding-right: 50px;
        float: right;
    }

    .btn-group.paginate {
        padding: 0 20px 20px 20px;
    }

    .pagination > li > a {
        padding: 14px 20px;
        cursor: pointer;
    }

    h2 {
        text-align: center;
    }
</style>


<section>
<h2 style="display:inline-block;">
        <?php echo __('Rješenja o korištenju godišnjeg odmora za zaposlenike'); ?><br/><br/>
    </h2>
<div class="zahtjevi-radnici" style="display:flex; width:100%; flex-direction:space-around; flex-direction:row;"> 
    <!-- Lijeva strana -->
    <div class="box" style="width:50%; float:left; margin-right:20px;">
        <div class="content">
            <div class="myrow">
                <div class="col-xs-12">
                    <form id="filteri" method="post" autocomplete="off">
                        <input type="hidden" name="pg" id="pg">

                        <label class="lable-admin1"><?php echo __('Godina'); ?></label>
                        <input placeholder=" Odaberi..." type="text" id="year" name="year"
                               class="monthPicker select2-container" style="margin:20px 0;width:200px;height:40px;"
                               value="<?php echo $year; ?>"/>
                        <br>

                        <label class="lable-admin1"><?php echo __('Sektor'); ?></label>
                        <select id="B1" name="IDB1" class="rcorners1" style="outline:none;width:200px;"
                                class="form-control" onchange="myselect();">
                            <?php echo $options['sektor']; ?>
                        </select><br/><br/>


                        <label class="lable-admin1"><?php echo __('Odjel'); ?></label>
                        <select id="regije" name="IDReg" class="rcorners1" style="outline:none;width:200px;"
                                class="form-control" onchange="myselect();">
                            <?php echo $options['odjel']; ?>
                        </select><br/><br/>

                        <label class="lable-admin1"><?php echo __('Grupa'); ?></label>
                        <select id="streams" name="IDStream" class="rcorners1" style="outline:none;width:200px; "
                                class="form-control" onchange="myselect();">
                            <?php echo $options['grupa']; ?>
                        </select><br/><br/>

                        <label class="lable-admin1"><?php echo __('Tim'); ?></label>
                        <select id="teams" name="IDTeam" class="rcorners1" style="outline:none;width:200px; background-color:#6DACC9 ; color:white;"
                                class="form-control" onchange="myselect();">
                            <?php echo $options['tim']; ?>
                        </select><br/><br/>

                        <label class="lable-admin1"><?php echo __('Ime'); ?></label>
                        <select id="ime_prezime" name="ime_prezime" class="rcorners1" style="outline:none;width:200px; background-color:#6DACC9 ; color:white;"
                                class="form-control" onchange="this.form.submit();">
                            <?php echo $options['zaposlenik']; ?>
                        </select><br/>

                        <div class="buttons">
                            <button type="submit" style="width:125px;"
                                    class="btn btn-red pull-left btn-sm"><?php echo __('Izaberi!'); ?> <i
                                        class="ion-ios-download-outline"></i></button>
                            <a href="/apoteke-app/?m=default&p=zahtjevi_go_radnici" id="odustani"
                               style="width:125px;margin-left:5px !important;background-color:006595;"
                               class="btn btn-red pull-left btn-sm"><?php echo __('Izbriši filtere!'); ?> <i
                                        class="ion-ios-download-outline"></i></a><br/>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Kraj lijeve strane -->
    <!-- Lista rjesenja  -->

    <div style=" width:50%;" class="box right_box">
        <?php if ($total[0] == 0) {
            echo "<h3 style='padding-left:20px;'>Nema podataka!</h3>";
        } else { ?>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">Ime i prezime uposlenika</th>
                    <th scope="col">Datum kreiranja rješenja</th>
                    <th scope="col">Period korištenja GO</th>
                    <th scope="col">Preuzimanje rješenja</th>
                </tr>
                </thead>
                <tbody>
                <!-- Redovi -->
                <?php $i = 1;
                foreach ($data as $one) { ?>
                    <tr>
                        <td><?php echo $one['First Name'] . ' ' . $one['Last Name']; ?></td>
                        <td><?php echo date("d.m.Y", strtotime($one['Insert Date'])); ?></td>
                        <td><?php echo date("d.m.Y", strtotime($one['Starting Date of I part'])) . ' - ' . date("d.m.Y", strtotime($one['Ending Date of I part'])); ?></td>
                        <td>
                            <?php
                            if ($one['Year'] == 2018) { ?>
                                <a target="_blank"
                                   href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_vacation_request_pdf2018_new.php?id=' . $one['id']; ?>"
                                   class="table-btn"><i style="font-size:16px;" class="ion-ios-copy-outline"></i>
                                    Preuzmite rješenje </a>
                            <?php } else if ($one['Year'] == 2019) { ?>
                                <a target="_blank"
                                   href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_vacation_request_pdf_new.php?id=' . $one['id']; ?>"
                                   class="table-btn"><i style="font-size:16px;" class="ion-ios-copy-outline"></i>
                                    Preuzmite rješenje </a>
                            <?php } else if ($one['Year'] == 2020) { ?>
                                <a target="_blank"
                                   href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_vacation_request_pdf_new2020.php?id=' . $one['id']; ?>"
                                   class="table-btn"><i style="font-size:16px;" class="ion-ios-copy-outline"></i>
                                    Preuzmite rješenje </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                <!-- kraj ispisa redova -->
                </tbody>
            </table>
            <br>
            <!-- paginacija -->
            <div class="text-left">
                <div class="btn-group paginate">
                    <ul class="pagination">
                        <li class="page-item">
                            <a class="page-link" onclick="$('#pg').val($(this).data('pg'));$('#filteri').submit();"
                               data-pg="<?php if ($page > 1) echo $page - 1; else echo 1; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item"><a
                                    class="page-link"><?php echo "Stranica " . $page . ' od ' . $pg_max; ?></a></li>

                        <li class="page-item">
                            <a class="page-link" onclick="$('#pg').val($(this).data('pg'));$('#filteri').submit();"
                               data-pg="<?php if ($page < $pg_max) echo $page + 1; else echo $pg_max; ?>"
                               aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>
    </div>
    <!-- kraj liste -->
</section>
<!-- END - Main section -->

<?php

include $_themeRoot . '/footer.php';

?>

</body>
</html>
<script>
    $("#B1").select2();
    $("#regije").select2();
    $("#streams").select2();
    $("#teams").select2();
    $("#ime_prezime").select2();

    $("#year").datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    });

    function myselect() {

        $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                request: "get-organization",
                sektor: $("#B1").val(),
                odjel: $('#regije').val(),
                grupa: $('#streams').val(),
                tim: $('#teams').val(),
                zaposlenik: $("#ime_prezime").val()
            },
            function (returned) {
                let data = JSON.parse(returned);

                $('#B1').html(data.sektor);
                $("#B1").select2();

                $('#regije').html(data.odjel);
                $("#regije").select2();

                $('#streams').html(data.grupa);
                $("#streams").select2();

                $('#teams').html(data.tim);
                $("#teams").select2();

                $('#ime_prezime').html(data.zaposlenik);
                $("#ime_prezime").select2();
            });
    }


    $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
            request: "get-organization",
            sektor: '<?php  if (!empty($_POST['IDB1'])) echo $_POST['IDB1']; else echo ''; ?>',
            odjel: '<?php  if (!empty($_POST['IDReg'])) echo $_POST['IDReg']; else echo ''; ?>',
            grupa: '<?php  if (!empty($_POST['IDStream'])) echo $_POST['IDStream']; else echo ''; ?>',
            tim: '<?php  if (!empty($_POST['IDTeam'])) echo $_POST['IDTeam']; else echo ''; ?>',
            zaposlenik: '<?php  if (!empty($_POST['ime_prezime'])) echo $_POST['ime_prezime']; else echo ''; ?>'
        },

        function (returned) {
            let data = JSON.parse(returned);

            $('#B1').html(data.sektor);
            $("#B1").select2();

            $('#regije').html(data.odjel);
            $("#regije").select2();

            $('#streams').html(data.grupa);
            $("#streams").select2();

            $('#teams').html(data.tim);
            $("#teams").select2();

            $('#ime_prezime').html(data.zaposlenik);
            $("#ime_prezime").select2();
        });


</script>