<div class="mp-inside">

    <div class="mp-i-h">
        <h4><?= ___('Lični dokumenti') ?></h4>
        <?php if ($_user['role'] == 4) { ?>
<!--        <a href="?m=profile&p=insert-data&what=licni-dokumenti">-->
<!--            <div class="icon-w">-->
<!--                <i class="fas fa-plus"></i>-->
<!--            </div>-->
<!--        </a>-->
        <?php } ?>
    </div>

    <?php
    foreach ($licniDok[0]['users__licni_dokumenti'] as $ld){
        ?>
        <div class="mp-i-row">
            <?php if ($_user['role'] == 4){ ?>
            <div class="edit-delete-row">
                <div class="edr-w" title="<?= ___('Uredite') ?>">
                    <a href="?m=profile&p=insert-data&what=licni-dokumenti&id=<?= $ld['id'] ?>"><i class="far text-success fa-edit"></i></a>
                </div>
<!--                <div class="edr-w" title="--><?//= ___('Obrišite') ?><!--">-->
<!--                    <a href=""><i class="fas fa-trash text-danger"></i></a>-->
<!--                </div>-->
            </div>
            <?php }?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="broj_licne_karte"><?= ___('Broj lične karte') ?></label>
                        <input type="text" value="<?= $ld['broj_licne_karte'] ?>" class="form-control form-control-sm" id="broj_licne_karte" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="drzavljanstvo"> <?= ___('Državljanstvo ') ?></label>
                        <input type="text" value="<?= $ld['drzavljanstvo'] ?>" class="form-control form-control-sm" id="drzavljanstvo" disabled="disabled">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="darivalac_krvi"><?= ___('Darivalac krvi') ?></label>
                        <input type="text" value="<?= $ld['darivalac_krvi'] ?>" class="form-control form-control-sm" id="darivalac_krvi" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="krvna_grupa"> <?= ___('Krvna grupa') ?></label>
                        <input type="text" value="<?= $ld['krvna_grupa'] ?>" class="form-control form-control-sm" id="krvna_grupa" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="vozacka_dozvola"><?= ___('Vozačka dozvola') ?></label>
                        <input type="text" value="<?= $ld['vozacka_dozvola'] ?>" class="form-control form-control-sm" id="vozacka_dozvola" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="kategorija"> <?= ___('Kategorija') ?></label>
                        <input type="text" value="<?= $ld['kategorija'] ?>" class="form-control form-control-sm" id="kategorija" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="aktivan_vozac"> <?= ___('Aktivan vozač') ?></label>
                        <input type="text" value="<?= $ld['aktivan_vozac'] ?>" class="form-control form-control-sm" id="aktivan_vozac" readonly>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>


</div>