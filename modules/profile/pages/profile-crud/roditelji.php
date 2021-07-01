<?php
try{
    $sifreOpcine = SifreOpcine::select('sifra')->orderBy('sifra', 'ASC')->pluck("sifra");
    $opcine      = SifreOpcine::select('naziv_opcine')->orderBy('naziv_opcine', 'ASC')->pluck("naziv_opcine");
}catch (\Exception $e){

}
?>

<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Podaci o roditeljima') ?> </p>
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
        <form action="?m=profile&p=insert-data&what=roditelji<?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" method="post">
            <div class="mp-i-row">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="otac_ime_prezime"> <?= ___('Ime i prezime oca') ?> </label>
                            <?= Form::text('otac_ime_prezime',$roditelj['otac_ime_prezime'] ?? '', ['id' => 'otac_ime_prezime', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="otac_datum_rodjenja"> <?= ___('Datum rođenja oca') ?></label>
                            <?= Form::text('otac_datum_rodjenja', isset($roditelj) ? ___formatDate($roditelj['otac_datum_rodjenja']) : '', ['id' => 'otac_datum_rodjenja', 'class' => 'form-control datepicker']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="majka_ime_prezime"> <?= ___('Ime i prezime majke') ?></label>
                            <?= Form::text('majka_ime_prezime',$roditelj['majka_ime_prezime'] ?? '', ['id' => 'majka_ime_prezime', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="majka_datum_rodjenja_"> <?= ___('Datum rođenja majke') ?></label>
                            <?= Form::text('majka_datum_rodjenja_',isset($roditelj) ? ___formatDate($roditelj['majka_datum_rodjenja_']) : '', ['id' => 'majka_datum_rodjenja_', 'class' => 'form-control datepicker']) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="majka_djevojacko_prezime"> <?= ___('Majčino djevojačko prezime') ?></label>
                            <?= Form::text('majka_djevojacko_prezime',$roditelj['majka_djevojacko_prezime'] ?? '', ['id' => 'majka_djevojacko_prezime', 'class' => 'form-control']) ?>
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