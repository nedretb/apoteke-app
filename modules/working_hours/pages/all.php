<?php
use IzvozSatnica as IS;
    $sistematizacije = IS::dajSistematizacije($_user['egop_ustrojstvena_jedinica']);
?>

<div class="container">
    <form>
        <div class="form-group">
            <label for="exampleInputEmail1"><?= ___('Organizaciona jedinica') ?></label>
            <?= Form::select('orgJed', $sistematizacije, '', ['class' => 'form-control select2', 'id' => 'exOrgJed']) ?>
            <small id="emailHelp" class="form-text text-muted"><?= ___('Odaberite organizacionu jedinicu za koju želite izvesti satnice') ?></small>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="mjesec"><?= ___('Mjesec') ?></label>
                    <?= Form::select('mjesec', IS::dajMjesece(), '', ['class' => 'form-control', 'id' => 'exMjesec']) ?>
                </div>
                <div class="col-md-6">
                    <label for="godina"><?= ___('Godina') ?></label>
                    <?= Form::select('godina', IS::dajGodine(), '', ['class' => 'form-control', 'id' => 'exGodina']) ?>
                </div>
            </div>
        </div>

        <div class="row text-right">
            <button type="submit" class="btn btn-secondary my-btn"><span style="color:#fff;"><?= ___('IZVOZ SATNICA') ?></span></button>
        </div>
    </form>
</div>