<div class="mp-inside">

    <div class="mp-i-h">
        <h4><?= ___('Zdravstveno stanje') ?></h4>
        <a href="?m=profile&p=insert-data&what=zdravstveno-stanje">
<!--            <div class="icon-w">-->
<!--                <i class="fas fa-plus"></i>-->
<!--            </div>-->
        </a>
    </div>

    <?php
    foreach ($zdravstv[0]['users__zdravstveno_stanje'] as $zdr){
        $stepen = StepenInvalidnosti::select("id, category")->where('id = '.$zdr['stepen_invalidnosti'])->first();

        ?>
        <div class="mp-i-row">
            <div class="edit-delete-row">
                <div class="edr-w" title="<?= ___('Uredite') ?>">
                    <a href="?m=profile&p=insert-data&what=zdravstveno-stanje&id=<?= $zdr['id'] ?>"><i class="far text-success fa-edit"></i></a>
                </div>
<!--                <div class="edr-w" title="--><?//= ___('Obrišite') ?><!--">-->
<!--                    <a href=""><i class="fas fa-trash text-danger"></i></a>-->
<!--                </div>-->
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="invalid_"><?= ___('Invalid') ?></label>
                        <input type="text" value="<?= $zdr['invalid_'] ?>" class="form-control form-control-sm" id="invalid_" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="stepen_invalidnosti"><?= ___('Stepen invalidnosti') ?></label>
                        <input type="text" value="<?= $stepen['category'] ?>" class="form-control form-control-sm" id="stepen_invalidnosti" disabled="disabled">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="dijete_sa_posebnim_potrebama"> <?= ___('Socijalni uslovi') ?></label>
                        <input type="text" value="<?= $zdr['socijalni_status'] ?>" class="form-control form-control-sm" id="dijete_sa_posebnim_potrebama" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="broj_djece"> <?= ___('Broj djece') ?></label>
                        <input type="text" value="<?= $zdr['broj_djece'] ?>" class="form-control form-control-sm" id="broj_djece" readonly>
                    </div>
                </div>
            </div>

        </div>
        <?php
    }
    ?>

</div>