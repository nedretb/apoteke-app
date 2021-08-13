<?php $provjera = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users__podaci_o_porodicnom_stanju] where employee_no=".$_user['employee_no']);
?>
<div class="mp-inside">

    <div class="mp-i-h">
        <h4><?= ___('Podaci o porodičnom stanju') ?></h4>
        <?php if ($userRole == 4 and !($provjera->rowCount() < 0)){ ?>
            <a href="?m=profile&p=insert-data&what=porodicno-stanje&u=<?php echo $_user['employee_no']; ?>">
                <div class="icon-w">
                    <i class="fas fa-plus"></i>
                </div>
            </a>
        <?php }?>
    </div>

    <?php
    foreach ($porodica[0]['users__podaci_o_porodicnom_stanju'] as $por){
        ?>
        <div class="mp-i-row">
            <div class="edit-delete-row">
                <div class="edr-w" title="<?= ___('Uredite') ?>">
                    <a href="?m=profile&p=insert-data&what=porodicno-stanje&u=<?php echo $_user['employee_no']; ?>&id=<?= $por['id'] ?>"><i class="far text-success fa-edit"></i></a>
                </div>
                <!--                <div class="edr-w" title="--><?//= ___('Obrišite') ?><!--">-->
                <!--                    <a href=""><i class="fas fa-trash text-danger"></i></a>-->
                <!--                </div>-->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="bracni_status"><?= ___('Bračni status') ?></label>
                        <input type="text" value="<?= $por['bracni_status'] ?>" class="form-control form-control-sm" id="bracni_status" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supruznik_ime_prezime"><?= ___('Ime i prezime supružnika') ?></label>
                        <input type="text" value="<?= $por['supruznik_ime_prezime'] ?>" class="form-control form-control-sm" id="supruznik_ime_prezime" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supruznik_datum_rodjenja"> <?= ___('Datum rođenja supružnika') ?></label>
                        <input type="text" value="<?= date('d.m.Y', strtotime($por['supruznik_datum_rodjenja'])) ?>" class="form-control form-control-sm" id="supruznik_datum_rodjenja" readonly>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>