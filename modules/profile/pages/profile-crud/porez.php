<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Poreska olakšica i prevoz') ?> </p>
    </div>
    <div class="sh-right">
        <div class="inside-link">
            <a href="?m=profile&p=edit-profile&u=<?php echo $_user['employee_no']; ?>">
                <p> <i class="fas fa-chevron-left"></i> <?= ___('Nazad') ?> </p>
            </a>
        </div>
    </div>
</div>

<div class="container mp-shadow pt-2">
    <div class="mp-inside my-container">
        <form action="?m=profile&p=insert-data&what=porez<?= isset($_GET['id']) ? '&id='.$_GET['id'].'&u='.$_user['employee_no'] : '&u='.$_user['employee_no'] ?>" method="post">
            <div class="mp-i-row">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="poreska_kartica"> <?= ___('Poreska kartica') ?> </label>
                            <?= Form::select('poreska_kartica', ['Ne' => 'Ne', 'Da' => 'Da'], $porez['poreska_kartica'] ?? '', ['id' => 'poreska_kartica', 'class' => 'form-control', 'disabled'=>'true']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="koeficijent_olaksice"> <?= ___('Koeficijent olakšice') ?></label>
                            <?= Form::number('koeficijent_olaksice',isset($porez) ? number_format((float)$porez['koeficijent_olaksice'], 2, '.', '') : '', ['id' => 'clan_sindikata', 'class' => 'form-control', 'min' => '0', 'max' => '1', 'step' => '0.01', 'readonly' => 'true']) ?>
                        </div>
                    </div>
                </div>
                <div class="row text-right">
                    <button type="submit" class="my-submit"><?= ___('Ažurirajte podatke') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>