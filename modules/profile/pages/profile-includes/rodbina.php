<div class="mp-inside">

    <div class="mp-i-h">
        <h4><?= ___('Podaci o rodbinskim odnosima') ?></h4>
        <a href="?m=profile&p=insert-data&what=rodbina">
            <div class="icon-w">
                <i class="fas fa-plus"></i>
            </div>
        </a>
    </div>

    <?php
        foreach ($rodbina[0]['users__podaci_o_rodbinskim_odnosima'] as $rodb){
            $srodnik = Profile::select("fname, lname")->where('employee_no = '.$rodb['srodnik'])->first();
        ?>
            <div class="mp-i-row">
                <div class="edit-delete-row">
                    <div class="edr-w" title="<?= ___('Uredite') ?>">
                        <a href="?m=profile&p=insert-data&what=rodbina&id=<?= $rodb['id'] ?>"><i class="far text-success fa-edit"></i></a>
                    </div>
<!--                    <div class="edr-w" title="--><?//= ___('Obrišite') ?><!--">-->
<!--                        <a href=""><i class="fas fa-trash text-danger"></i></a>-->
<!--                    </div>-->
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="examplsrodnikInputEmail1"><?= ___('Srodnik') ?></label>
                            <input type="text" value="<?= $srodnik['fname'].' '.$srodnik['lname'] ?>" class="form-control form-control-sm" id="srodnik" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="srodstvo"> <?= ___('Srodstvo') ?></label>
                            <input type="text" value="<?= $rodb['srodstvo'] ?>" class="form-control form-control-sm" id="srodstvo" readonly>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }
    ?>
</div>