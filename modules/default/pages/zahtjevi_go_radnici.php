<?php
require 'CORE/config-urls.php';
//filter data
$orgJed = isset($_POST['org_jed']) ? $_POST['org_jed'] : '';



//filter
if (!empty($_POST['year'])) {
    $year = $_POST['year'];
    $yearq = " and godina = $year";
} else {
    $year = '';
    $yearq = '';
}
if (!empty($_POST['org_jed'])) {
    $B_1 = $_POST['IDB1'];
    $org_jedq = "and egop_ustrojstvena_jedinica=".$_POST['org_jed'];
} else {
    $B_1 = '';
    $org_jedq = '';
}

if (!empty($_POST['ime_prezime'])) {
    $name_s = explode(' ', $_POST['ime_prezime']);
    $ime_prezime = str_replace('  ', '', $_POST['ime_prezime']);
    $ime_prezimeq = " and (v.fname = N'".$name_s[0]."' and v.lname=N'".$name_s[1]."')  ";
} else {
    $ime_prezime = '';
    $ime_prezimeq = '';
}
//paginacija
$limit = 5;
if (!empty($_POST['pg'])) {
    $page = $_POST['pg'];
} else {
    $page = 1;
}

$offset = ($page - 1) * $limit;

$fgr = $db->query("select * from [c0_intranet2_apoteke].[dbo].[users] as v join [c0_intranet2_apoteke].[dbo].[rjesenja_go] as b on v.employee_no=b.employee_no where b.odobreno=1 ".$ime_prezimeq." ". $org_jedq." ".$yearq." order by v.employee_no offset ".$offset. " rows fetch next ".$limit." rows only");
$total = $db->query("select count(*) from [c0_intranet2_apoteke].[dbo].[users] as v join [c0_intranet2_apoteke].[dbo].[rjesenja_go] as b on v.employee_no=b.employee_no where b.odobreno=1 ".$ime_prezimeq." ". $org_jedq." ".$yearq);

$pg_max = ceil($total->fetch()[0] / $limit);
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
        width: auto !important;
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
    <div>
        <div class="row">
            <div class="col-md-8">
                <h2 style="display:inline-block;"><?php echo __('Rješenja o korištenju godišnjeg odmora za zaposlenike'); ?></h2>
            </div>
            <div class="col-md-3" style="position: relative; margin-top: 20px;">
                <button onclick="kreirajRjesenja();" style="width:auto;" class="btn btn-red pull-left btn-sm"><?php echo __('Kreiraj rješenja za godišnji odmor'); ?><i style="color: white;" class="ion-ios-download-outline"></i></button><br/><br/>
            </div>
        </div>
    </div>
<br><br>
    <div class="zahtjevi-radnici" style="display:flex; width:100%; flex-direction:space-around; flex-direction:row;">
        <!-- Lijeva strana -->
        <div class="box" style="float:left; margin-right:20px;">
            <div class="content">
                <div class="myrow">
                    <div class="col-xs-12">
                        <form id="filteri" method="post" autocomplete="off">
                            <input type="hidden" name="pg" id="pg">

                            <label style="width: 100%"  class="lable-admin1"><?php echo __('Godina'); ?></label>
                            <input placeholder=" Odaberi..." type="text" id="year" name="year"
                                   class="monthPicker select2-container" style="margin:20px 0;width:200px;height:40px;"
                                   value="<?php echo $year; ?>"/>
                            <br>

                            <label style="width: 100%" class="lable-admin1"><?php echo __('Organizaciona jedinica'); ?></label>
                            <select id="org_jed" name="org_jed" class="rcorners1" style="outline:none;width:200px;"
                                    class="form-control">
                                <?php echo _optionB_1($orgJed) ?>
                            </select><br/><br/>

                            <label style="width: 100%"  class="lable-admin1"><?php echo __('Ime zaposlenika'); ?></label>
                            <select id="ime_prezime" name="ime_prezime" class="rcorners1" style="outline:none;width:200px; background-color:#6DACC9 ; color:white;"
                                    class="form-control" onchange="this.form.submit();">
                                <?php echo _optionName('', '', '', $orgJed, $ime_prezime, $filtertdate) ?>
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

        <div class="box right_box">
            <?php if (count($fgr) == 0) {
                echo "<h3 style='padding-left:20px;'>Nema podataka!</h3>";
            } else {?>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Ime i prezime zaposlenika</th>
                        <th scope="col">Datum kreiranja rješenja</th>
                        <th scope="col">Period korištenja GO</th>
                        <th scope="col">Preuzimanje rješenja</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Redovi -->
                    <?php $i = 1;
                    foreach ($fgr as $one) { ?>
                        <tr>
                            <td><?php echo $one['fname'] . ' ' . $one['lname']; ?></td>
                            <td><?php echo date("d.m.Y", strtotime($one['datum_kreiranja_rjesenja'])); ?></td>
                            <td><?php echo date("d.m.Y", strtotime($one['datum_od'])) . ' - ' . date("d.m.Y", strtotime($one['datum_do'])); ?></td>
                            <td>
                                <a target="_blank"
                                   href="<?php echo $url . '/modules/' . $_mod . '/pages/rjesenjeGo.php?employee_no=' . $one['employee_no'].'&year='.$one['godina']; ?>"
                                   class="table-btn"><i style="font-size:16px;" class="ion-ios-copy-outline"></i>
                                    Preuzmite rješenje </a>
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
    $("#org_jed").select2();
    $("#regije").select2();
    $("#streams").select2();
    $("#teams").select2();
    $("#ime_prezime").select2();

    $("#year").datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    });


    $('#org_jed').on('change', function (){
        console.log($('#org_jed').val());
        $.ajax({
            type: 'POST',
            url: "<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>",
            data: {request: 'get-organization',
                org_jed: $('#org_jed').val(), },
            success:function (data){
                let response = JSON.parse(data);
                $("#ime_prezime").html(response);
                $("#ime_prezime").select2();
            }
        });
    });

    function kreirajRjesenja(){
        console.log('wwwwwww');

        $.ajax({
            url: 'modules/default/pages/kreirajRjesenjaGo.php',
            method: 'POST',
            data: {},
            success: function success(response){
                console.log(response);
                location.reload();

                // window.location.replace('?m=default&p=zahtjevi_go_radnici&edit=');
            }
        });
    }
    //function myselect() {
    //
    //    $.post("<?php //echo $url . '/modules/admin_manager_verification/ajax.php'; ?>//", {
    //            request: "get-organization",
    //            sektor: $("#org_jed").val(),
    //            odjel: $('#regije').val(),
    //            grupa: $('#streams').val(),
    //            tim: $('#teams').val(),
    //            zaposlenik: $("#ime_prezime").val()
    //        },
    //        function (returned) {
    //            let data = JSON.parse(returned);
    //
    //            $('#B1').html(data.sektor);
    //            $("#B1").select2();
    //
    //            $('#regije').html(data.odjel);
    //            $("#regije").select2();
    //
    //            $('#streams').html(data.grupa);
    //            $("#streams").select2();
    //
    //            $('#teams').html(data.tim);
    //            $("#teams").select2();
    //
    //            $('#ime_prezime').html(data.zaposlenik);
    //            $("#ime_prezime").select2();
    //        });
    //}
    //
    //
    //$.post("<?php //echo $url . '/modules/admin_manager_verification/ajax.php'; ?>//", {
    //        request: "get-organization",
    //        sektor: '<?php // if (!empty($_POST['IDB1'])) echo $_POST['IDB1']; else echo ''; ?>//',
    //        odjel: '<?php // if (!empty($_POST['IDReg'])) echo $_POST['IDReg']; else echo ''; ?>//',
    //        grupa: '<?php // if (!empty($_POST['IDStream'])) echo $_POST['IDStream']; else echo ''; ?>//',
    //        tim: '<?php // if (!empty($_POST['IDTeam'])) echo $_POST['IDTeam']; else echo ''; ?>//',
    //        zaposlenik: '<?php // if (!empty($_POST['ime_prezime'])) echo $_POST['ime_prezime']; else echo ''; ?>//'
    //    },
    //
    //    function (returned) {
    //        let data = JSON.parse(returned);
    //
    //        $('#B1').html(data.sektor);
    //        $("#B1").select2();
    //
    //        $('#regije').html(data.odjel);
    //        $("#regije").select2();
    //
    //        $('#streams').html(data.grupa);
    //        $("#streams").select2();
    //
    //        $('#teams').html(data.tim);
    //        $("#teams").select2();
    //
    //        $('#ime_prezime').html(data.zaposlenik);
    //        $("#ime_prezime").select2();
    //    });


</script>