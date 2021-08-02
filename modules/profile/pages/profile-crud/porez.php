<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Poreska olakšica i prevoz') ?> </p>
    </div>
    <div class="sh-right">
        <div class="inside-link">
            <a href="?m=profile&p=edit-profile">
                <p> <i class="fas fa-chevron-left"></i> <?= ___('Nazad') ?> </p>
            </a>
        </div>
    </div>
</div>

<div class="container mp-shadow pt-2">
    <div class="mp-inside my-container">
        <form action="?m=profile&p=insert-data&what=porez<?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" method="post">
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
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="koeficijent_slozenosti_poslova"><?= ___('Koeficijent složenosti posla') ?></label>
                            <?= Form::number('koeficijent_slozenosti_poslova',isset($porez) ? number_format((float)$porez['koeficijent_slozenosti_poslova'], 2, '.', '') : '', ['id' => 'clan_sindikata', 'class' => 'form-control', 'min' => '0', 'max' => '10', 'step' => '0.01']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="demobilizirani_borac"> <?= ___('Demobilizirani borac') ?></label>
                            <?= Form::select('demobilizirani_borac', ['Ne' => 'Ne', 'Da' => 'Da'], $porez['demobilizirani_borac'] ?? '', ['id' => 'demobilizirani_borac', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="demobilizirani_borac_m"><?= ___('Broj mjeseci u odbrambeno-oslobodilačkom/domovinskom ratu') ?></label>
                            <?= Form::number('demobilizirani_borac_m',isset($porez) ? $porez['demobilizirani_borac_m'] : '', ['id' => 'demobilizirani_borac_m', 'class' => 'form-control', 'min' => '0', 'max' => '44']) ?>
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
    if ($('#demobilizirani_borac').val() == 'Da'){
        $('#demobilizirani_borac_m').prop('disabled', false);
    }
    else{
        $('#demobilizirani_borac_m').prop('disabled', true);
        $('#demobilizirani_borac_m').val(0);
    }

    $('#demobilizirani_borac').on('change', function (){
        if($('#demobilizirani_borac').val() == 'Da'){
            $('#demobilizirani_borac_m').prop('disabled', false);
        }
        else{
            $('#demobilizirani_borac_m').prop('disabled', true);
            $('#demobilizirani_borac_m').val(0);
        }
    });
</script>