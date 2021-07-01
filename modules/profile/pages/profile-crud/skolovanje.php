<?php

try{
    $stepenInv = StepenInvalidnosti::select('id, category')->orderBy('id', 'ASC')->pluck("id", "category");
}catch (\Exception $e) {}

?>

<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Školovanje') ?> </p>
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
        <form action="?m=profile&p=insert-data&what=skolovanje<?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" method="post">
            <div class="mp-i-row">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="strucna_sprema"> <?= ___('Stručna sprema') ?> </label>
                            <?= Form::text('strucna_sprema', $skolovanje['strucna_sprema'] ?? '', ['id' => 'invalid_', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="zavrsena_obrazovna_ustanova"> <?= ___('Završena obrazovna ustanova') ?> </label>
                            <?= Form::text('zavrsena_obrazovna_ustanova', $skolovanje['zavrsena_obrazovna_ustanova'] ?? '', ['id' => 'zavrsena_obrazovna_ustanova', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="zvanje"> <?= ___('Zvanje') ?> </label>
                            <?= Form::text('zvanje', $skolovanje['zvanje'] ?? '', ['id' => 'zvanje', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="struka"> <?= ___('Struka') ?> </label>
                            <?= Form::text('struka',$skolovanje['struka'] ?? '', ['id' => 'struka', 'class' => 'form-control']) ?>
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