<div class="mp-inside">

    <div class="mp-i-h">
        <h4><?= ___('Podaci o porodičnom stanju - djeca') ?></h4>
<!--        <a href="?m=profile&p=insert-data&what=podaci-djeca">-->
<!--            <div class="icon-w">-->
<!--                <i class="fas fa-plus"></i>-->
<!--            </div>-->
<!--        </a>-->
    </div>

    <?php
    foreach ($porodicad[0]['users__podaci_o_djeci'] as $por){
        ?>
        <div class="mp-i-row">
<!--            <div class="edit-delete-row">-->
<!--                <div class="edr-w" title="--><?//= ___('Uredite') ?><!--">-->
<!--                    <a href="?m=profile&p=insert-data&what=podaci-djeca&id=--><?//= $por['id'] ?><!--"><i class="far text-success fa-edit"></i></a>-->
<!--                </div>-->
                <!--                <div class="edr-w" title="--><?//= ___('Obrišite') ?><!--">-->
                <!--                    <a href=""><i class="fas fa-trash text-danger"></i></a>-->
                <!--                </div>-->
<!--            </div>-->

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="bracni_status"><?= ___('Ime i prezime djeteta') ?></label>
                        <input type="text" value="<?= $por['ime_prezime_djeteta'] ?>" class="form-control form-control-sm" id="bracni_status" disabled="disabled">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supruznik_ime_prezime"><?= ___('Pol') ?></label>
                        <input type="text" value="<?= $por['spol'] ?>" class="form-control form-control-sm" id="supruznik_ime_prezime" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supruznik_datum_rodjenja"> <?= ___('Datum rođenja djeteta') ?></label>
                        <input type="text" value="<?= date('d.m.Y', strtotime($por['datum_rodjenja'])) ?>" class="form-control form-control-sm" id="supruznik_datum_rodjenja" disabled="disabled">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>