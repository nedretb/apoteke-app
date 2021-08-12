<div class="mp-inside">

    <div class="mp-i-h">
        <h4><?= ___('Školovanje') ?></h4>
<!--        <a href="?m=profile&p=insert-data&what=skolovanje">-->
<!--            <div class="icon-w">-->
<!--                <i class="fas fa-plus"></i>-->
<!--            </div>-->
<!--        </a>-->
    </div>

    <?php
    foreach ($skolovanje[0]['users__skolovanje'] as $skol){
        ?>
        <div class="mp-i-row">
<!--            <div class="edit-delete-row">-->
<!--                <div class="edr-w" title="--><?//= ___('Uredite') ?><!--">-->
<!--                    <a href="?m=profile&p=insert-data&what=skolovanje&id=--><?//= $skol['id'] ?><!--"><i class="far text-success fa-edit"></i></a>-->
<!--                </div>-->
<!--                <div class="edr-w" title="--><?//= ___('Obrišite') ?><!--">-->
<!--                    <a href=""><i class="fas fa-trash text-danger"></i></a>-->
<!--                </div>-->
<!--            </div>-->

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="strucna_sprema"><?= ___('Stručna sprema') ?></label>
                        <input type="text" value="<?= $skol['strucna_sprema'] ?>" class="form-control form-control-sm" id="strucna_sprema" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="zavrsena_obrazovna_ustanova"><?= ___('Šifra stručnog zvanja') ?></label>
                        <input type="text" value="<?= $skol['sifra_strucnog_zvanja'] ?>" class="form-control form-control-sm" id="zavrsena_obrazovna_ustanova" disabled="disabled">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="strucna_sprema"><?= ___('Završena obrazovna ustanova') ?></label>
                        <input type="text" value="<?= $skol['zvanje'] ?>" class="form-control form-control-sm" id="strucna_sprema" disabled="disabled">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

</div>