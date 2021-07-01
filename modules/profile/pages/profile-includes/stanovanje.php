<div class="mp-inside">

    <div class="mp-i-h">
        <h4><?= ___('Podaci o stanovanju') ?></h4>
        <a href="?m=profile&p=insert-data&what=stanovanje">
<!--            <div class="icon-w">-->
<!--                <i class="fas fa-plus"></i>-->
<!--            </div>-->
        </a>
    </div>

    <?php
    foreach ($stanovanje[0]['users__podaci_o_stanovanju'] as $stan){
        ?>
        <div class="mp-i-row">
            <div class="edit-delete-row">
<!--                <div class="edr-w" title="--><?//= ___('Uredite') ?><!--">-->
<!--                    <a href="?m=profile&p=insert-data&what=stanovanje&id=--><?//= $stan['id'] ?><!--"><i class="far text-success fa-edit"></i></a>-->
<!--                </div>-->
<!--                <div class="edr-w" title="--><?//= ___('Obrišite') ?><!--">-->
<!--                    <a href=""><i class="fas fa-trash text-danger"></i></a>-->
<!--                </div>-->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="adresa"><?= ___('Adresa stanovanja') ?></label>
                        <input type="text" value="<?= $stan['adresa'] ?>" class="form-control form-control-sm" id="adresa" readonly aria-describedby="adresaHelp">
                        <small id="adresaHelp" class="form-text text-muted"> <?= ___('Adresa na kojoj stanuje (CIPS)') ?> </small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sifra_opcine"> <?= ___('Šifra općine') ?></label>
                        <input type="text" value="<?= $stan['sifra_opcine'] ?>" class="form-control form-control-sm" id="sifra_opcine" aria-describedby="sifra_opcineHelp" readonly>
                        <small id="sifra_opcine" class="form-text text-muted"> <?= ___('Šifra općine (CIPS)') ?> </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="naziv_opcine"> <?= ___('Naziv općine') ?></label>
                        <input type="text" value="<?= $stan['naziv_opcine'] ?>" class="form-control form-control-sm" id="naziv_opcine" aria-describedby="naziv_opcineHelp" readonly>
                        <small id="naziv_opcineHelp" class="form-text text-muted"> <?= ___('Naziv općine (CIPS)') ?> </small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="grad"> <?= ___('Grad') ?></label>
                        <input type="text" value="<?= $stan['grad'] ?>" class="form-control form-control-sm" id="grad" aria-describedby="gradHelp" readonly>
                        <small id="gradHelp" class="form-text text-muted"> <?= ___('Grad (CIPS)') ?> </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="postanski_broj"> <?= ___('Poštanski broj') ?></label>
                        <input type="text" value="<?= $stan['postanski_broj'] ?>" class="form-control form-control-sm" id="postanski_broj" aria-describedby="postanski_brojHelp" readonly>
                        <small id="postanski_brojHelp" class="form-text text-muted"> <?= ___('Poštanski broj (CIPS)') ?> </small>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>