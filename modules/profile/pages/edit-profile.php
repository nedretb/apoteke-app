<?php
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
                    <?= ___('Pozicija') ?>
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
                    <?= ___('Zamjenik') ?>
                </div>
                <div class="mp-lr-r">
                    <h5><?php echo $_user['zamjenik']; ?></h5>
                </div>
            </div>
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