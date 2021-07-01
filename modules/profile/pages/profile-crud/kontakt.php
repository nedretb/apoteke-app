<?php
try{
    $sifreOpcine = SifreOpcine::select('sifra')->orderBy('sifra', 'ASC')->pluck("sifra");
    $opcine      = SifreOpcine::select('naziv_opcine')->orderBy('naziv_opcine', 'ASC')->pluck("naziv_opcine");
}catch (\Exception $e){

}
?>

<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Kontakt informacije') ?> </p>
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
        <form action="?m=profile&p=insert-data&what=kontakt<?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" method="post">
            <div class="mp-i-row">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kucni_telefonski_broj"><?= ___('Kućni telefonski broj') ?></label>
                            <?= Form::select('kucni_telefonski_broj', ['+387' => '+387'], $kontakt['kucni_telefonski_broj'] ?? '', ['id' => 'kucni_telefonski_broj', 'class' => 'form-control', 'required' => 'required']) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kucni_regionalni_kod"><?= ___('Regionalni kod') ?></label>
                            <?= Form::select('kucni_regionalni_kod', Kontakt::range(30, 77), $kontakt['kucni_regionalni_kod'] ?? '', ['id' => 'kucni_regionalni_kod', 'class' => 'form-control', 'required' => 'required']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kucni_broj"><?= ___('Broj') ?></label>
                            <?= Form::number('kucni_broj', $kontakt['kucni_broj'] ?? '', ['id' => 'kucni_broj', 'class' => 'form-control', 'step' => 1, 'required' => 'required', 'max' => '10000000']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="privatni_mobitel_broj"><?= ___('Broj privatnog mobitela') ?></label>
                            <?= Form::select('privatni_mobitel_broj', ['+387' => '+387'], $kontakt['privatni_mobitel_broj'] ?? '', ['id' => 'privatni_mobitel_broj', 'class' => 'form-control', 'required' => 'required']) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="mobitel_regionalni_kod"><?= ___('Regionalni kod') ?></label>
                            <?= Form::select('mobitel_regionalni_kod', Kontakt::range(30, 77), $kontakt['mobitel_regionalni_kod'] ?? '', ['id' => 'mobitel_regionalni_kod', 'class' => 'form-control', 'required' => 'required', 'min' => '100000']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mobitel_broj"><?= ___('Broj') ?></label>
                            <?= Form::number('mobitel_broj', $kontakt['mobitel_broj'] ?? '', ['id' => 'mobitel_broj', 'class' => 'form-control', 'step' => 1, 'required' => 'required', 'max' => '10000000', 'min' => '100000']) ?>
                        </div>
                    </div>
                </div>

                <!----------------------------------------------------------------------------------------------------->

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="privatna_email_adresa"> <?= ___('Privatna email adresa') ?></label>
                            <?= Form::email('privatna_email_adresa',$kontakt['privatna_email_adresa'] ?? '', ['id' => 'privatna_email_adresa', 'class' => 'form-control']) ?>
                            <small id="privatna_email_adresaHelp" class="form-text text-muted"> <?= ___('Unesite privatnu email adresu službenika') ?> </small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ime_prezime_kontakt_osobe"> <?= ___('Kontakt u hitnom slučaju') ?></label>
                            <?= Form::text('ime_prezime_kontakt_osobe',$kontakt['ime_prezime_kontakt_osobe'] ?? '', ['id' => 'ime_prezime_kontakt_osobe', 'class' => 'form-control']) ?>
                            <small class="form-text text-muted"> <?= ___('Ime i prezime kontakt osobe u hitnom slučaju') ?> </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="odnos_sa_kontakt_osobe"> <?= ___('Srodstvo') ?></label>
                            <?= Form::select('odnos_sa_kontakt_osobe', [], $kontakt['odnos_sa_kontakt_osobe'] ?? '', ['id' => 'odnos_sa_kontakt_osobe', 'class' => 'form-control']) ?>
                            <small class="form-text text-muted"> <?= ___('U kakvom je odnosu radnik sa kontakt osobom za hitni slučaj') ?> </small>
                        </div>
                    </div>
                </div>

                <!----------------------------------------------------------------------------------------------------->

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kontakt_osoba_broj_telefona"><?= ___('Broj telefona kontakt osobe') ?></label>
                            <?= Form::select('kontakt_osoba_broj_telefona', ['+387' => '+387'], $kontakt['kontakt_osoba_broj_telefona'] ?? '', ['id' => 'kontakt_osoba_broj_telefona', 'class' => 'form-control', 'required' => 'required']) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kontakt_osoba_regionalni_kod"><?= ___('Regionalni kod') ?></label>
                            <?= Form::select('kontakt_osoba_regionalni_kod', Kontakt::range(30, 77), $kontakt['kontakt_osoba_regionalni_kod'] ?? '', ['id' => 'kontakt_osoba_regionalni_kod', 'class' => 'form-control', 'required' => 'required']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kontakt_osoba_broj"><?= ___('Broj') ?></label>
                            <?= Form::number('kontakt_osoba_broj', $kontakt['kontakt_osoba_broj'] ?? '', ['id' => 'kontakt_osoba_broj', 'class' => 'form-control', 'step' => 1, 'required' => 'required', 'max' => '10000000', 'min' => '100000']) ?>
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