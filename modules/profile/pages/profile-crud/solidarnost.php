<?php
try{
    $sifreOpcine = SifreOpcine::select('sifra')->orderBy('sifra', 'ASC')->pluck("sifra");
    $opcine      = SifreOpcine::select('naziv_opcine')->orderBy('naziv_opcine', 'ASC')->pluck("naziv_opcine");
}catch (\Exception $e){

}
?>

<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Fond solidarnosti i sindikat') ?> </p>
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
        <form action="?m=profile&p=insert-data&what=solidarnost<?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" method="post">
            <div class="mp-i-row">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="clan_internog_fonda_solidarnosti"> <?= ___('Član internog fonda solidarnosti') ?> </label>
                            <?= Form::select('clan_internog_fonda_solidarnosti', ['Ne' => 'Ne', 'Da' => 'Da'], $solidarnost['clan_internog_fonda_solidarnosti'] ?? '', ['id' => 'clan_internog_fonda_solidarnosti', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="clan_sindikata"> <?= ___('Član sindikata') ?></label>
                            <?= Form::select('clan_sindikata', ['Ne' => 'Ne', 'Da' => 'Da'],$solidarnost['clan_sindikata'] ?? '', ['id' => 'clan_sindikata', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="naziv_sindikata"> <?= ___('Naziv sindikata') ?></label>
                            <?= Form::text('naziv_sindikata',$solidarnost['naziv_sindikata'] ?? '', ['id' => 'naziv_sindikata', 'class' => 'form-control']) ?>
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