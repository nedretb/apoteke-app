<?php
$userRole = $_user['role'];
if(!empty($_GET['u'])){
    $_user = $db->query("select * from [c0_intranet2_apoteke].[dbo].[users] where employee_no=".$_GET['u'])->fetch();
}else{
    $emp_no = $_user['employee_no'];
}
    $stanovanje = Stanovanje::getData($_user['employee_no']);
    $kontakt    = Kontakt::getData($_user['employee_no']);
    $roditelji  = Roditelji::getData($_user['employee_no']);
    $porodica   = PorodicnoStanje::getData($_user['employee_no']);
    $porodicad  = PodaciDjeca::getData($_user['employee_no']);
    $rodbina    = Rodbina::getData($_user['employee_no']);
    $licniDok   = LicniDokumenti::getData($_user['employee_no']);
    $zdravstv   = ZdravstvenoStanje::getData($_user['employee_no']);
    $skolovanje = Skolovanje::getData($_user['employee_no']);
    $solidarn   = Solidarnost::getData($_user['employee_no']);
    $porez      = Porez::getData($_user['employee_no']);

    $rodjenje   = Rodjenje::getData($_user['employee_no']);

    $podredjeni = $db->query("SELECT fname, lname, employee_no, rukovodioc FROM [c0_intranet2_apoteke].[dbo].[users] where egop_ustrojstvena_jedinica=".$_user['egop_ustrojstvena_jedinica']." and employee_no<>".$_user['employee_no']);
    $rukovodiocOdjela = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[systematization] where id=".$_user['egop_ustrojstvena_jedinica'])->fetch()['rukovodioc_emp_no'];

?>

<link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

<div class="my-profile">
    <div class="mp-left mp-shadow">
        <div class="mp-inside">
            <div class="mp-image-data">
                <div class="mp-id-image">
                    <div class="mp-id-i-w">
                        <img src="theme/images/profile-images/<?= $_user['image'] ?>" id="main-photo">

                        <label for="main-photo-input">
                            <div class="input-image-shadow t-3">
                                <h1>240 x 240</h1>
                            </div>
                        </label>
                        <input type="file" id="main-photo-input" class="photo-input" path="theme/images/profile-images/" object-id="12" photo-name="main-photo" name="photo-input" url="modules/api/files/">
                    </div>
                </div>
                <div class="mp-id-data">
                    <h4><?= $_user['fname'].' '.$_user['lname']; ?></h4>
                    <p> <?= ___('Jedinstveni matični broj') ?> - <?= $_user['JMB'] ?> </p>
                    <?php if ($_user['djevojacko_prezime'] != null){  ?>
                        <p> <?= ___('Djevojačko prezime') ?> - <?= $_user['djevojacko_prezime'] ?> </p>
                    <?php }?>
                </div>
            </div>

            <!--------------------------------------------------------------------------------------------------------->

            <div class="mp-l-rest">
                <div class="mp-lr-l">
                    <?= ___('Naziv radnog mjesta') ?>
                </div>
                <div class="mp-lr-r">
                    <h5><?= $_user['egop_radno_mjesto'] ?></h5>
                </div>
            </div>
            <div class="mp-l-rest">
                <div class="mp-lr-l">
                    <?= ___('Personalni broj') ?>
                </div>
                <div class="mp-lr-r">
                    <h5><?= $_user['employee_no'] ?></h5>
                </div>
            </div>
            <div class="mp-l-rest">
                <div class="mp-lr-l">
                    <?= ___('Organizaciona jedinica') ?>
                </div>
                <div class="mp-lr-r">
                    <h5><?= $_user['B_1_description'] ?></h5>
                </div>
            </div>

            <!--------------------------------------------------------------------------------------------------------->

            <div class="mp-l-rest">
                <div class="mp-lr-l">
                    <?= ___('Šifra pozicije') ?>
                </div>
                <div class="mp-lr-r">
                    <h5><?= $_user['position_code'] ?></h5>
                </div>
            </div>

            <div class="mp-l-rest">
                <div class="mp-lr-l">
                    <?= ___('Datum zaposlenja') ?>
                </div>
                <div class="mp-lr-r">
                    <h5><?php echo date('d.m.Y', strtotime($_user['employment_date'])); ?></h5>
                </div>
            </div>
            <div class="mp-l-rest">
                <div class="mp-lr-l">
                    <?= ___('Status') ?>
                </div>
                <div class="mp-lr-r">
                    <h5><?php if ($_user['status'] == 1){ echo 'Aktivan';} else{ echo 'Neaktivan';}; ?></h5>
                </div>
            </div>
            <div class="mp-l-rest">
                <div class="mp-lr-l">
                    <?= ___('Broj nadređenog') ?>
                </div>
                <div class="mp-lr-r">
                    <h5><?php echo $_user['parent']; ?></h5>
                </div>
            </div>
            <div class="mp-l-rest">
                <div class="mp-lr-l">
                    <?= ___('Broj sati') ?>
                </div>
                <div class="mp-lr-r">
                    <h5><?php echo $_user['br_sati']; ?></h5>
                </div>
            </div>
            <div class="mp-l-rest">
                <div class="mp-lr-l">
                    <?= ___('Dežurstva') ?>
                </div>
                <div class="mp-lr-r">
                    <h5><?php echo $_user['dezurstva']; ?></h5>
                </div>
            </div>
            <div class="mp-l-rest">
                <div class="mp-lr-l">
                    <?= ___('Zdravstveni radnik') ?>
                </div>
                <div class="mp-lr-r">
                    <select style="width: 60%;margin-right: 5px;" id="zdravstveni_radnik" class="form-control">
                        <option value="null">Odaberi..</option>
                        <?php
                            if($_user['zdravstveni_radnik'] == 'Da'){
                                echo '<option selected="selected" value="Da">Da</option>';
                                echo '<option value="Ne">Ne</option>';
                            }
                            elseif ($_user['zdravstveni_radnik'] == 'Ne'){
                                echo '<option value="Da">Da</option>';
                                echo '<option selected="selected" value="Ne">Ne</option>';
                            }
                            else{
                                echo '<option value="Da">Da</option>';
                                echo '<option value="Ne">Ne</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <?php if ($_user['employee_no'] == $rukovodiocOdjela or $_user['role'] == 4){ ?>
            <div class="mp-l-rest">
                <div class="mp-lr-l">
                    <?= ___('Zamjenik') ?>
                </div>
                <div class="mp-lr-r" style="display: flex;">
                    <select style="width: 60%;margin-right: 5px;" id="zamjenik" class="form-control">
                        <option value="null">Odaberi..</option>
                        <?php
                            foreach ($podredjeni as $p){
                                if ($p['rukovodioc'] == 'DA'){
                                    echo '<option selected="selected" value="'.$p['employee_no'].'">'.$p['fname'].' '.$p['lname'].'</option>';
                                }
                                else{
                                    echo '<option value="'.$p['employee_no'].'">'.$p['fname'].' '.$p['lname'].'</option>';
                                }
                            }
                        ?>
                    </select>
                    <button class="my-btn" style="top: 10% !important; color: white !important; width: auto !important; height: 30px;" onclick="ukloniZamjenika();" title="Ukloni zamjenika">Briši</button>
                </div>
            </div>
            <?php }?>
        </div>

        <div class="mp-inside">
            <div class="mp-i-h">
                <h4><?= ___('Podaci o rođenju') ?></h4>
                <a href="?m=profile&p=insert-data&what=rodjenje<?= (isset($rodjenje) and $rodjenje) ? '&id='.$rodjenje['id'] : '' ?>">
<!--                    <div class="icon-w">-->
<!--                        <i class="fas fa-plus"></i>-->
<!--                    </div>-->
                </a>
            </div>

            <div class="mp-i-row">
                <div class="form-group row">
                    <label for="naziv_opstine_rodjenja" class="col-sm-4 col-form-label mt-2"><?= ___('Država rođenja') ?></label>
                    <div class="col-sm-8">
                        <input type="text" value="<?= $rodjenje['sifra_drzave_rodjenja'] ?>" class="form-control form-control-sm" id="naziv_opstine_rodjenja" disabled="disabled" >
                    </div>
                </div>
                <div class="form-group row">
                    <label for="naziv_opstine_rodjenja" class="col-sm-4 col-form-label mt-2"><?= ___('Naziv općine rođenja') ?></label>
                    <div class="col-sm-8">
                        <input type="text" value="<?= $rodjenje['naziv_opstine_rodjenja'] ?>" class="form-control form-control-sm" id="naziv_opstine_rodjenja" disabled="disabled">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="mjesto_rodjenja" class="col-sm-4 col-form-label mt-2"><?= ___('Mjesto rođenja') ?></label>
                    <div class="col-sm-8">
                        <input type="text" value="<?= $rodjenje['mjesto_rodjenja'] ?>" class="form-control form-control-sm" id="mjesto_rodjenja" disabled="disabled" >
                    </div>
                </div>
                <div class="form-group row">
                    <label for="datum_rodjena" class="col-sm-4 col-form-label mt-2"><?= ___('Datum rođenja') ?></label>
                    <div class="col-sm-8">
                        <input  type="text" value="<?= isset($rodjenje) ? ___formatDate($rodjenje['datum_rodjena']) : '' ?>" class="form-control form-control-sm" id="datum_rodjena" disabled="disabled">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="grad_rodjenja" class="col-sm-4 col-form-label mt-2"><?= ___('Grad rođenja') ?></label>
                    <div class="col-sm-8">
                        <input type="text"  value="<?= $rodjenje['grad_rodjenja'] ?>"class="form-control form-control-sm" id="grad_rodjenja" disabled="disabled" >
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="mp-right mp-shadow">
        <?php include $root . '/modules/' . $_mod . '/pages/profile-includes/stanovanje.php'; ?>

        <?php include $root . '/modules/' . $_mod . '/pages/profile-includes/kontakt.php'; ?>

        <?php include $root . '/modules/' . $_mod . '/pages/profile-includes/porodicno-stanje.php'; ?>

        <?php include $root . '/modules/' . $_mod . '/pages/profile-includes/podaci-djeca.php'; ?>

        <?php include $root . '/modules/' . $_mod . '/pages/profile-includes/licni-dokumenti.php'; ?>

        <?php include $root . '/modules/' . $_mod . '/pages/profile-includes/zdravstveno-stanje.php'; ?>

        <?php include $root . '/modules/' . $_mod . '/pages/profile-includes/skolovanje.php'; ?>

        <?php include $root . '/modules/' . $_mod . '/pages/profile-includes/radni-staz.php'; ?>

        <?php include $root . '/modules/' . $_mod . '/pages/profile-includes/solidarnost.php'; ?>

        <?php include $root . '/modules/' . $_mod . '/pages/profile-includes/porez.php'; ?>
    </div>

    <script src="<?= 'theme/js/upload-images.js'; ?>"></script>
</div>

<?php

include $_themeRoot . '/footer.php';

?>

<!--<script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script> -->
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
<script src="<?php echo $_pluginUrl; ?>/jquery-cookie-master/src/jquery.cookie.js"></script>

<script>

    function ukloniZamjenika() {
        $.ajax({
            type: 'POST',
            url: "<?php echo $url . '/modules/profile/pages/zamjenik.php'; ?>",
            data: {type: 'remove',
                glavni_lik: <?php echo $_user['employee_no']; ?>,
                org_jed: <?php echo $_user['egop_ustrojstvena_jedinica']; ?>
            },
            success:function (data){
                let response = JSON.parse(data);
                if (response == 'removed'){
                    window.location.replace('?m=profile&p=edit-profile');
                }
            }
        });
    }


    $('#zamjenik').on('change', function (){
        $.ajax({
            type: 'POST',
            url: "<?php echo $url . '/modules/profile/pages/zamjenik.php'; ?>",
            data: {
                type: 'add',
                employee_no: $('#zamjenik').val(),
                org_jed: <?php echo $_user['egop_ustrojstvena_jedinica']; ?>
            },
            success:function (data){
                let response = JSON.parse(data);
                console.log(response);
            }
        });
    });

    $('#zdravstveni_radnik').on('change', function (){
        console.log($('#zdravstveni_radnik').val());
        $.ajax({
            type: 'POST',
            url: "<?php echo $url . '/modules/profile/pages/zamjenik.php'; ?>",
            data: {
                type: 'zdravstveni_radnik',
                employee_no: <?php echo $_user['employee_no']; ?>,
                val: $('#zdravstveni_radnik').val()
            },
            success:function (data){
                let response = JSON.parse(data);
                console.log(response);
            }
        });
    });

</script>