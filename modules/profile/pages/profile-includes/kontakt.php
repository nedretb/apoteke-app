<div class="mp-inside">

    <div class="mp-i-h">
        <h4><?= ___('Kontakt informacije') ?></h4>
        <?php if ($_user['role'] == 4){ ?>
<!--        <a href="?m=profile&p=insert-data&what=kontakt">-->
<!--            <div class="icon-w">-->
<!--                <i class="fas fa-plus"></i>-->
<!--            </div>-->
<!--        </a>-->
        <?php }?>
    </div>

    <?php
    foreach ($kontakt[0]['users__kontakt_informacije'] as $kont) {
        ?>
        <div class="mp-i-row">
            <div class="edit-delete-row">
                <div class="edr-w" title="<?= ___('Uredite') ?>">
                    <a href="?m=profile&p=insert-data&what=kontakt&u=<?php echo $_user['employee_no']; ?>&id=<?= $kont['id'] ?>"><i class="far text-success fa-edit"></i></a>
                </div>

<!--                <div class="edr-w" title="--><?//= ___('Obrišite') ?><!--">-->
<!--                    <a href=""><i class="fas fa-trash text-danger"></i></a>-->
<!--                </div>-->
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kucni_telefonski_broj"><?= ___('Kućni telefonski broj') ?></label>
                        <input type="text" value="<?= $kont['kucni_telefonski_broj'].' '.$kont['kucni_regionalni_kod'].' '.$kont['kucni_broj'] ?>" class="form-control form-control-sm" id="kucni_telefonski_broj" readonly>
                        <small class="form-text text-muted"> <?= ___('+387 3X XXX-XXX') ?> </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="privatni_mobitel_broj"> <?= ___('Broj privatnog mobitela') ?></label>
                        <input type="text" value="<?= $kont['privatni_mobitel_broj'].' '.$kont['mobitel_regionalni_kod'].' '.$kont['mobitel_broj'] ?>" class="form-control form-control-sm" id="privatni_mobitel_broj" readonly>
                        <small class="form-text text-muted"> <?= ___('+387 6X XXX-XXX') ?> </small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="privatna_email_adresa"> <?= ___('Privatna email adresa') ?></label>
                        <input type="text" value="<?= $kont['privatna_email_adresa'] ?>" class="form-control form-control-sm" id="privatna_email_adresa" readonly>
                        <small class="form-text text-muted"> <?= ___('') ?> </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ime_prezime_kontakt_osobe"> <?= ___('Kontakt u hitnom slučaju') ?></label>
                        <input type="text" value="<?= $kont['ime_prezime_kontakt_osobe'] ?>" class="form-control form-control-sm" id="ime_prezime_kontakt_osobe" readonly>
                        <small class="form-text text-muted"> <?= ___('Ime i prezime kontakt osobe u hitnom slučaju') ?> </small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="odnos_sa_kontakt_osobe"> <?= ___('Srodstvo') ?></label>
                        <input type="text" value="<?= $kont['odnos_sa_kontakt_osobe'] ?>" class="form-control form-control-sm" id="odnos_sa_kontakt_osobe" readonly>
                        <small class="form-text text-muted"> <?= ___('U kakvom je odnosu radnik sa kontakt osobom za hitni slučaj') ?> </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kontakt_osoba_broj_telefona"> <?= ___('Broj telefona') ?></label>
                        <input type="text" value="<?= $kont['kontakt_osoba_broj_telefona'].' '.$kont['kontakt_osoba_regionalni_kod'].' '.$kont['kontakt_osoba_broj'] ?>" class="form-control form-control-sm" id="kontakt_osoba_broj_telefona" readonly>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>