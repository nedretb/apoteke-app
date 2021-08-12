<div class="mp-inside">

    <div class="mp-i-h">
        <h4><?= ___('Fond solidarnosti i sindikat') ?></h4>
<!--        <a href="?m=profile&p=insert-data&what=solidarnost">-->
<!--            <div class="icon-w">-->
<!--                <i class="fas fa-plus"></i>-->
<!--            </div>-->
<!--        </a>-->
    </div>

    <?php
    foreach ($solidarn[0]['users__fond_solidarnosti_i_sindikat'] as $solidarnost){
        ?>
        <div class="mp-i-row">
<!--            <div class="edit-delete-row">-->
<!--                <div class="edr-w" title="--><?//= ___('Uredite') ?><!--">-->
<!--                    <a href="?m=profile&p=insert-data&what=solidarnost&id=--><?//= $solidarnost['id'] ?><!--"><i class="far text-success fa-edit"></i></a>-->
<!--                </div>-->
<!--                <div class="edr-w" title="--><?//= ___('Obrišite') ?><!--">-->
<!--                    <a href=""><i class="fas fa-trash text-danger"></i></a>-->
<!--                </div>-->
<!--            </div>-->

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="clan_internog_fonda_solidarnosti"><?= ___('Član internog fonda solidarnosti') ?></label>
                        <input type="text" value="<?= $solidarnost['clan_internog_fonda_solidarnosti'] ?>" class="form-control form-control-sm" id="clan_internog_fonda_solidarnosti" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="clan_sindikata"> <?= ___('Član sindikata') ?></label>
                        <input type="text" value="<?= $solidarnost['clan_sindikata'] ?>" class="form-control form-control-sm" id="clan_sindikata" disabled="disabled">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

</div>