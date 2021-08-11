<div class="mp-inside">

    <div class="mp-i-h">
        <h4><?= ___('Poreska olakšica i prevoz') ?></h4>
<!--        <a href="?m=profile&p=insert-data&what=porez">-->
<!--            <div class="icon-w">-->
<!--                <i class="fas fa-plus"></i>-->
<!--            </div>-->
<!--        </a>-->
    </div>

    <?php
    foreach ($porez[0]['users__poreska_olaksica_i_prevoz'] as $por){
        ?>
        <div class="mp-i-row">
            <?php if ($_user['role'] == 4){ ?>
            <div class="edit-delete-row">
                <div class="edr-w" title="<?= ___('Uredite') ?>">
                    <a href="?m=profile&p=insert-data&what=porez&id=<?= $por['id'] ?>"><i class="far text-success fa-edit"></i></a>
                </div>
                <div class="edr-w" title="<?= ___('Obrišite') ?>">
                    <a href=""><i class="fas fa-trash text-danger"></i></a>
                </div>
            </div>
            <?php }?>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="poreska_kartica"><?= ___('Poreska olakšica') ?></label>
                        <input type="text" value="<?= $por['poreska_kartica'] ?>" class="form-control form-control-sm" id="poreska_kartica" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="koeficijent_olaksice"> <?= ___('Koeficijent olakšice') ?></label>
                        <input type="text" value="<?= number_format((float)$por['koeficijent_olaksice'], 2, '.', '') ?>" class="form-control form-control-sm" id="koeficijent_olaksice" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="koeficijent_slozenosti_poslova"><?= ___('Koeficijent složenosti posla') ?></label>
                        <input type="text" value="<?= $por['koeficijent_slozenosti_poslova'] ?>" class="form-control form-control-sm" id="koeficijent_slozenosti_poslova" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="demobilizirani_borac"> <?= ___('Demobilizirani borac') ?></label>
                        <input type="text" value="<?= $por['demobilizirani_borac'] ?>" class="form-control form-control-sm" id="demobilizirani_borac" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="demobilizirani_borac_m"> <?= ___('Broj mjeseci u odbrambeno-oslobodilačkom/domovinskom ratu') ?></label>
                        <input type="text" value="<?= $por['demobilizirani_borac_m'] ?>" class="form-control form-control-sm" id="demobilizirani_borac_m" readonly>
                    </div>
                </div>
            </div>

        </div>
        <?php
    }
    ?>

</div>