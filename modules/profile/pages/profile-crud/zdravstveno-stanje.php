<?php

try{
    $stepenInv = StepenInvalidnosti::select('id, category')->orderBy('id', 'ASC')->pluck("id", "category");
}catch (\Exception $e) {}

$socijalniStatus = [
        'bez' => 'Bez',
        'roditelj_malodobno' => 'Roditelj, staratelj ili usvojitelj sa malodobnim djetetom/djecom',
        'roditelj_dijete_posebne_potrebe' => 'Roditelju, staratelju ili usvojitelju hendikepiranog djeteta',
];
?>

<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Zdravstveno stanje') ?> </p>
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
        <form action="?m=profile&p=insert-data&what=zdravstveno-stanje<?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" method="post">
            <div class="mp-i-row">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="invalid_"> <?= ___('Invalid') ?> </label>
                            <?= Form::select('invalid_', ['Ne' => 'Ne', 'Da' => 'Da'], $zdravstv['invalid_'] ?? '', ['id' => 'invalid_', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="stepen_invalidnosti"> <?= ___('Stepen invalidnosti') ?> </label>
                            <?= Form::select('stepen_invalidnosti', $stepenInv, $zdravstv['stepen_invalidnosti'] ?? '', ['id' => 'stepen_invalidnosti', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="socijalni_status"> <?= ___('Socijalni status') ?> </label>
                            <?= Form::select('socijalni_status', $socijalniStatus, $zdravstv['socijalni_status'] ?? '', ['id' => 'socijalni_status', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="broj_djece"> <?= ___('Broj djece') ?> </label>
                            <?= Form::number('broj_djece', $zdravstv['broj_djece'] ?? '0', ['id' => 'broj_djece', 'class' => 'form-control', 'min' => 0 ]) ?>
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

<script>

    $( document ).ready(function() {
        if ($('#socijalni_status').val() == 'roditelj_dijete_posebne_potrebe'){
            $('#broj_djece').attr('readonly', 'true');
        }
        else if($('#socijalni_status').val() == 'bez'){
            $('#broj_djece').val(0).attr('readonly', 'true');
        }
        else {
            $('#broj_djece').removeAttr('readonly');
        }
    });

    $('#socijalni_status').on('change', function (){
        if ($('#socijalni_status').val() == 'roditelj_dijete_posebne_potrebe'){
            $('#broj_djece').val(1).attr('readonly', 'true');
        }
        else if($('#socijalni_status').val() == 'bez'){
            $('#broj_djece').val(0).attr('readonly', 'true');
        }
        else {
            $('#broj_djece').val(0).removeAttr('readonly');
        }
    });

</script>