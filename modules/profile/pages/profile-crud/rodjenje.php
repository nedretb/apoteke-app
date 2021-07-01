<?php
try{
    $sifreOpcine = SifreOpcine::select('sifra')->orderBy('sifra', 'ASC')->pluck("sifra");
    $opcine      = SifreOpcine::select('naziv_opcine')->orderBy('naziv_opcine', 'ASC')->pluck("naziv_opcine");

    $sifreDrzave = Drzava::select('sifra')->orderBy('sifra', 'ASC')->pluck("sifra");
    $grad        = SifreOpcine::select('grad')->orderBy('grad', 'ASC')->pluck("grad");
}catch (\Exception $e){

}
?>

<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Podaci o rođenju') ?> </p>
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
        <form action="?m=profile&p=insert-data&what=rodjenje<?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" method="post">
            <div class="mp-i-row">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sifra_opstine_rodjenja"> <?= ___('Šifra općine rođenja') ?> </label>
                            <?= Form::select('sifra_opstine_rodjenja', $sifreOpcine, $rodjenje['sifra_opstine_rodjenja'] ?? '', ['id' => 'sifra_opstine_rodjenja select2', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="naziv_opstine_rodjenja"> <?= ___('Naziv općine rođenja') ?></label>
                            <?= Form::select('naziv_opstine_rodjenja', $opcine, $rodjenje['naziv_opstine_rodjenja'] ?? '', ['id' => 'naziv_opstine_rodjenja', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mjesto_rodjenja"> <?= ___('Mjesto rođenja') ?></label>
                            <?= Form::text('mjesto_rodjenja',$rodjenje['mjesto_rodjenja'] ?? '', ['id' => 'mjesto_rodjenja', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="datum_rodjena"> <?= ___('Datum rođenja') ?></label>
                            <?= Form::text('datum_rodjena',isset($rodjenje) ? ___formatDate($rodjenje['datum_rodjena']) : '', ['id' => 'datum_rodjena', 'class' => 'form-control datepicker']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sifra_drzave_rodjenja"> <?= ___('Šifra države rođenja') ?></label>
                            <?= Form::select('sifra_drzave_rodjenja', $sifreDrzave,$rodjenje['sifra_drzave_rodjenja'] ?? '', ['id' => 'sifra_drzave_rodjenja', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="grad_rodjenja"> <?= ___('Grad rođenja') ?></label>
                            <?= Form::select('grad_rodjenja', $grad,$rodjenje['grad_rodjenja'] ?? '', ['id' => 'grad_rodjenja', 'class' => 'form-control']) ?>
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