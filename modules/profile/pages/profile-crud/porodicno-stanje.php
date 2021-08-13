<?php
try{
    $sifreOpcine = SifreOpcine::select('sifra')->orderBy('sifra', 'ASC')->pluck("sifra");
    $opcine      = SifreOpcine::select('naziv_opcine')->orderBy('naziv_opcine', 'ASC')->pluck("naziv_opcine");
}catch (\Exception $e){

}


?>

<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Podaci o porodičnom stanju') ?> </p>
    </div>
    <div class="sh-right">
        <div class="inside-link">
            <a href="?m=profile&p=edit-profile&u=<?php echo $_user['employee_no'];?>">
                <p> <i class="fas fa-chevron-left"></i> <?= ___('Nazad') ?> </p>
            </a>
        </div>
    </div>
</div>

<div class="container mp-shadow pt-2">
    <div class="mp-inside my-container">
        <form action="?m=profile&p=insert-data&what=porodicno-stanje<?= isset($_GET['id']) ? '&id='.$_GET['id'].'&u='.$_user['employee_no'] : '&u='.$_user['employee_no'] ?>" method="post">
            <div class="mp-i-row">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="bracni_status"> <?= ___('Bračni status') ?> </label>
                            <?= Form::select('bracni_status', PorodicnoStanje::stanje(), $porodica['bracni_status'] ?? '', ['id' => 'bracni_status', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supruznik_ime_prezime"> <?= ___('Ime i prezime supružnika') ?></label>
                            <?= Form::text('supruznik_ime_prezime',$porodica['supruznik_ime_prezime'] ?? '', ['id' => 'supruznik_ime_prezime', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supruznik_datum_rodjenja"> <?= ___('Datum rođenja supružnika') ?></label>
                            <?= Form::text('supruznik_datum_rodjenja', isset($porodica) ? ___formatDate($porodica['supruznik_datum_rodjenja']) : '', ['id' => 'supruznik_datum_rodjenja', 'class' => 'form-control datepicker']) ?>
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