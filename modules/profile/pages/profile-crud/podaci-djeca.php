<?php
var_dump($_GET['id']);
?>
<div class="simple-header">
<div class="sh-left">
    <p> <?= ___('Podaci o porodičnom stanju - djeca') ?> </p>
</div>
<div class="sh-right">
    <div class="inside-link">
        <a href="?m=profile&p=edit-profile">
            <p> <i class="fas fa-chevron-left"></i> <?= ___('Nazad') ?> </p>
        </a>
    </div>
</div>
</div>

<div class="container mp-shadow pt-2">
    <div class="mp-inside my-container">
        <form action="?m=profile&p=insert-data&what=podaci-djeca<?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" method="post">
            <div class="mp-i-row">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="ime_prezime_djeteta"> <?= ___('Ime i prezime djeteta') ?> </label>
                            <?= Form::text('ime_prezime_djeteta',$porodicad['ime_prezime_djeteta'] ?? '', ['id' => 'ime_prezime_djeteta', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="datum_rodjenja"> <?= ___('Datum rođenja djeteta') ?></label>
                            <?= Form::text('datum_rodjenja', isset($porodicad) ? ___formatDate($porodicad['datum_rodjenja']) : '', ['id' => 'datum_rodjenja', 'class' => 'form-control datepicker']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="spol"> <?= ___('Pol') ?></label>
                            <?= Form::select('spol', PodaciDjeca::stanje(), $porodicad['spol'] ?? '', ['id' => 'spol', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                </div>
                <div class="row text-right">
                    <button type="submit" class="my-submit"><?= ___('Ažurirajte podatke') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
