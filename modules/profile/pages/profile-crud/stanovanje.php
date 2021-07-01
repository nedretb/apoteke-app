<?php
try{
    $sifreOpcine = SifreOpcine::select('sifra')->orderBy('sifra', 'ASC')->pluck("sifra");
    $opcine      = SifreOpcine::select('naziv_opcine')->orderBy('naziv_opcine', 'ASC')->pluck("naziv_opcine");
}catch (\Exception $e){

}
?>

<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Podaci o stanovanju') ?> </p>
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
        <form action="?m=profile&p=insert-data&what=stanovanje<?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" method="post">
            <div class="mp-i-row">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="adresa"><?= ___('Adresa stanovanja') ?></label>
                            <?= Form::text('adresa', $stanovanje['adresa'] ?? '', ['id' => 'adresa', 'class' => 'form-control', 'aria-describedby' => 'adresaHelp']) ?>
                            <small id="adresaHelp" class="form-text text-muted"> <?= ___('Adresa na kojoj stanuje (CIPS)') ?> </small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sifra_opcine"> <?= ___('Šifra općine') ?></label>
                            <?= Form::select('sifra_opcine', $sifreOpcine, $stanovanje['sifra_opcine'] ?? '', ['id' => 'sifra_opcine', 'class' => 'form-control']) ?>
                            <small id="sifra_opstineHelp" class="form-text text-muted"> <?= ___('Šifra općine (CIPS)') ?> </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="naziv_opcine"> <?= ___('Naziv općine') ?></label>
                            <?= Form::select('naziv_opcine', $opcine, $stanovanje['naziv_opcine'] ?? '', ['id' => 'naziv_opcine', 'class' => 'form-control']) ?>
                            <small id="naziv_opcineHelp" class="form-text text-muted"> <?= ___('Naziv općine (CIPS)') ?> </small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="grad"> <?= ___('Grad') ?></label>
                            <?= Form::select('grad', $opcine, $stanovanje['grad'] ?? '', ['id' => 'grad', 'class' => 'form-control']) ?>
                            <small id="gradHelp" class="form-text text-muted"> <?= ___('Grad (CIPS)') ?> </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="postanski_broj"> <?= ___('Poštanski broj') ?></label>
                            <?= Form::text('postanski_broj', $stanovanje['postanski_broj'] ?? '', ['id' => 'postanski_broj', 'class' => 'form-control', 'aria-describedby' => 'postanski_brojHelp']) ?>
                            <small id="postanski_brojHelp" class="form-text text-muted"> <?= ___('Poštanski broj (CIPS)') ?> </small>
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