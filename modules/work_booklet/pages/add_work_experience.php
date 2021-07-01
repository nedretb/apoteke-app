<?php
error_reporting(-1);
_pagePermission(4, false);
date_default_timezone_set('Europe/Sarajevo');

if (isset($_GET['new'])){
    $exp_data = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where [Employee No_]=".$_GET['new'])->fetch();
}
elseif (isset($_GET['edit'])){
    $exp_data = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where id=".$_GET['edit'])->fetch();
    //$name_surname = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where [Employee No_]=".$exp_data['Employee No_'])->fetch();
}

?>


<form method="post" action="?m=work_booklet&p=save_work_experience&<?php if (isset($_GET['new'])){ echo 'save=1';} else{ echo 'edit=1';} ?>">
    <br><br>

    <input hidden id="edit" value="<?php if(isset($_GET['edit'])){ echo $_GET['edit'];} else{echo 0;}?>">
    <input hidden id="endate" value="<?php echo date("d.m.Y", strtotime($exp_data['Starting Date'])); ?>">
    <div id="respons" class="alert alert-warning">
        Molimo vas unesite tačan raspon datuma
    </div>

    <div class="simple-header">
        <div class="sh-left">
            <p class="sh-click" val="tabs-1">
                Prethodno radno iskustvo
            </p>
        </div>
        <div class="sh-right">
            <a href="/apoteke-app/?m=work_booklet&p=add-new&edit=<?php echo $exp_data['Employee No_']; ?>">
                <p> <i class="fas fa-chevron-left"></i> Nazad </p>
            </a>
        </div>
    </div>

    <br><br>

    <div class="row">
        <div class="col-md-4">
            <label>Šifra zaposlenika</label>
            <input required id="emp_no" name="emp_no" class="form-control" type="text" value="<?php echo $exp_data['Employee No_'];?>" readonly>
        </div>

        <div class="col-md-4">
            <label>Ime i prezime zaposlenika</label>
            <input required id="emp_name" name="name_surname" class="form-control" type="text" value="<?php echo $exp_data['First Name']. " " .$exp_data['Last Name'];?>" readonly>
        </div>

        <div class="col-md-4">
            <label>Poslodovac</label>
            <input required id="employer" name="employer" class="form-control" type="text" value="<?php if (isset($_GET['edit'])){ echo $exp_data['Employer'];} ?>">
        </div>
    </div>

    <br>
    <div class="row">
        <div class="col-md-4">
            <label>Datum početka rada</label>
            <input name="datef" class="form-control" type="text" id="dateFrom" autocomplete="off" value="<?php if (isset($_GET['edit'])){ echo date("d.m.Y", strtotime($exp_data['Starting Date']));} ?>">
        </div>

        <div class="col-md-4">
            <label>Datum završetka rada</label>
            <input name="datet" class="form-control" type="text" id="dateToto" autocomplete="off" value="<?php if (isset($_GET['edit'])){ echo date("d.m.Y", strtotime($exp_data['Ending Date']));} ?>">
        </div>

        <div class="col-md-4">
            <label>Koeficijent</label>
            <input required name="coefficient" id="coefficient" class="form-control" type="number" min="0" max="1" step="0.01" value="<?php if(isset($_GET['edit'])){ echo number_format((float)$exp_data['Coefficient'], 2, '.', '');}?>">
        </div>
    </div>
    <br>
    <div class="row">

        <div class="col-md-4">
            <label>Godina radnog iskustva</label>
            <input readonly id="exp_y" name="exp_y" class="form-control" type="number" min="0"  max="50" step="1" autocomplete="off" value="<?php if (isset($_GET['edit'])){ echo $exp_data['previous_exp_y'];} ?>">
        </div>

        <div class="col-md-4">
            <label>Mjeseci radnog iskustva</label>
            <input readonly id="exp_m" name="exp_m" class="form-control" type="number" min="0" max="12" step="1" autocomplete="off" value="<?php if (isset($_GET['edit'])){ echo $exp_data['previous_exp_m'];;} ?>">
        </div>

        <div class="col-md-4">
            <label>Dana radnog iskustva</label>
            <input readonly id="exp_d" name="exp_d" class="form-control" type="number" min="0" max="31" step="1" autocomplete="off" value="<?php if(isset($_GET['edit'])){ echo $exp_data['previous_exp_d'];;}?>">
        </div>
    </div>
    <br><br>
    <div class="row">
        <div class="col-md-12 text-right">
            <button type="submit" style="font-size:16px; padding-right:7px;" class="btn btn-sm btn-secondary">Spasite informacije</button>
        </div>
    </div>
    <br><br>
</form>

<?php

include $_themeRoot . '/footer.php';

?>

<!--<script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script> -->
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
<script src="<?php echo $_pluginUrl; ?>/jquery-cookie-master/src/jquery.cookie.js"></script>

<script src="modules/work_booklet/pages/js/script2.js"></script>