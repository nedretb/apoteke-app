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
            <?php if ($userRole == 4){ ?>
<!--            <div class="edit-delete-row">-->
<!--                <div class="edr-w" title="--><?//= ___('Uredite') ?><!--">-->
<!--                    <a href="?m=profile&p=insert-data&what=porez&u=--><?php //echo $_user['employee_no']; ?><!--&id=--><?//= $por['id'] ?><!--"><i class="far text-success fa-edit"></i></a>-->
<!--                </div>-->
<!--                <div class="edr-w" title="--><?//= ___('Obrišite') ?><!--">-->
<!--                    <a href=""><i class="fas fa-trash text-danger"></i></a>-->
<!--                </div>-->
<!--            </div>-->
            <?php }?>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="poreska_kartica"><?= ___('Poreska olakšica') ?></label>
                        <input type="text" value="<?= $por['poreska_kartica'] ?>" class="form-control form-control-sm" id="poreska_kartica" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="koeficijent_olaksice"> <?= ___('Koeficijent olakšice') ?></label>
                        <input type="text" value="<?= number_format((float)$por['koeficijent_olaksice'], 2, '.', '') ?>" class="form-control form-control-sm" id="koeficijent_olaksice" disabled="disabled">
                    </div>
                </div>
            </div>

        </div>
        <?php
    }
    ?>

</div>