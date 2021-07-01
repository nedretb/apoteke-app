<div class="mp-inside">

    <div class="mp-i-h">
        <h4><?= ___('Podaci o roditeljima') ?></h4>
        <a href="?m=profile&p=insert-data&what=roditelji">
            <div class="icon-w">
                <i class="fas fa-plus"></i>
            </div>
        </a>
    </div>

    <?php
    foreach ($roditelji[0]['users__podaci_o_roditeljima'] as $roditelj){
        ?>
        <div class="mp-i-row">
            <div class="edit-delete-row">
                <div class="edr-w" title="<?= ___('Uredite') ?>">
                    <a href="?m=profile&p=insert-data&what=roditelji&id=<?= $roditelj['id'] ?>"><i class="far text-success fa-edit"></i></a>
                </div>
<!--                <div class="edr-w" title="--><?//= ___('Obrišite') ?><!--">-->
<!--                    <a href=""><i class="fas fa-trash text-danger"></i></a>-->
<!--                </div>-->
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="otac_ime_prezime"><?= ___('Ime i prezime oca') ?></label>
                        <input type="email" value="<?= $roditelj['otac_ime_prezime'] ?>" class="form-control form-control-sm" id="otac_ime_prezime" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="otac_datum_rodjenja"> <?= ___('Datum rođenja oca') ?></label>
                        <input type="email" value="<?= $roditelj['otac_datum_rodjenja'] ?>" class="form-control form-control-sm" id="otac_datum_rodjenja" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="majka_ime_prezime"><?= ___('Ime i prezime majke') ?></label>
                        <input type="email" value="<?= $roditelj['majka_ime_prezime'] ?>" class="form-control form-control-sm" id="majka_ime_prezime" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="majka_datum_rodjenja_"> <?= ___('Datum rođenja majke') ?></label>
                        <input type="email" value="<?= $roditelj['majka_datum_rodjenja_'] ?>" class="form-control form-control-sm" id="majka_datum_rodjenja_" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="majka_djevojacko_prezime"> <?= ___('Majčino djevojačko prezime') ?></label>
                        <input type="email" value="<?= $roditelj['majka_djevojacko_prezime'] ?>" class="form-control form-control-sm" id="majka_djevojacko_prezime" readonly>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>