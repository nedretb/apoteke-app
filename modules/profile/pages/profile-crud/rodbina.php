<?php

try{
    $srodnici = Profile::select('fname, lname, employee_no')->orderBy('lname', 'ASC')->pluck("employee_no", "fname", "lname");
}catch (\Exception $e) {}

?>

<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Podaci o rodbinskim odnosima') ?> </p>
    </div>
    <div class="sh-right">
        <div class="inside-link">
            <a href="?m=profile&p=edit-profile">
                <p><i class="fas fa-chevron-left"></i> <?= ___('Nazad') ?> </p>
            </a>
        </div>
    </div>
</div>

<div class="container mp-shadow pt-2">
    <div class="mp-inside my-container">
        <form action="?m=profile&p=insert-data&what=rodbina<?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" method="post">
            <div class="mp-i-row">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="srodnik"> <?= ___('Srodnik') ?> </label>
                            <?= Form::select('srodnik', $srodnici, $rodbina['srodnik'] ?? '', ['id' => 'srodnik', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="srodstvo"> <?= ___('Srodstvo') ?> </label>
                            <?= Form::select('srodstvo', Rodbina::srodstvo(), $rodbina['srodstvo'] ?? '', ['id' => 'srodstvo', 'class' => 'form-control']) ?>
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