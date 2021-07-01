<?php

try{
    $srodnici = Profile::select('fname, lname, employee_no')->orderBy('lname', 'ASC')->pluck("employee_no", "fname", "lname");
}catch (\Exception $e) {}

?>

<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Lični dokumenti') ?> </p>
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
        <form action="?m=profile&p=insert-data&what=licni-dokumenti<?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" method="post">
            <div class="mp-i-row">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="broj_licne_karte"> <?= ___('Broj lične karte') ?> </label>
                            <?= Form::text('broj_licne_karte', $dokumenti['broj_licne_karte'] ?? '', ['id' => 'broj_licne_karte', 'class' => 'form-control', 'readonly'=>'true']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="drzavljanstvo"> <?= ___('Državljanstvo') ?> </label>
                            <?= Form::select('drzavljanstvo', LicniDokumenti::drzavljanstvo(), $dokumenti['drzavljanstvo'] ?? '', ['id' => 'drzavljanstvo', 'class' => 'form-control', 'disabled'=>'true']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="darivalac_krvi"> <?= ___('Darivalac krvi') ?> </label>
                            <?= Form::select('darivalac_krvi', ['Ne' => 'Ne', 'Da' => 'Da'], $dokumenti['darivalac_krvi'] ?? '', ['id' => 'darivalac_krvi', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="krvna_grupa"> <?= ___('Krvna grupa') ?> </label>
                            <?= Form::text('krvna_grupa',$dokumenti['krvna_grupa'] ?? '', ['id' => 'krvna_grupa', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vozacka_dozvola"> <?= ___('Vozačka dozvola') ?> </label>
                            <?= Form::select('vozacka_dozvola', ['Ne' => 'Ne', 'Da' => 'Da'], $dokumenti['vozacka_dozvola'] ?? '', ['id' => 'vozacka_dozvola', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kategorija"> <?= ___('Kategorija') ?> </label>
                            <?= Form::select('kategorija', LicniDokumenti::kategorije(), $dokumenti['kategorija'] ?? '', ['id' => 'kategorija', 'class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="aktivan_vozac"> <?= ___('Aktivan vozač') ?> </label>
                            <?= Form::select('aktivan_vozac', ['Ne' => 'Ne', 'Da' => 'Da'], $dokumenti['aktivan_vozac'] ?? '', ['id' => 'aktivan_vozac', 'class' => 'form-control']) ?>
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