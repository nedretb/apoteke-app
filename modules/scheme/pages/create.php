<?php

    if(isset($_GET['id'])){
        if(isset($request)){
            Sistematizacija::where('id = '.$_GET['id'])->update($request->get());
        }
        $orgJed = Sistematizacija::where('id = '.$_GET['id'])->first();
    }else{
        if(isset($request)) Sistematizacija::insert($request->get());
    }

    $parents = Sistematizacija::select('id, s_title')->orderBy('id', 'ASC')->pluck("id", "s_title");
?>


<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Unos nove organizacione jedinice') ?> </p>
    </div>
    <div class="sh-right">
        <div class="inside-link">
            <a href="?m=scheme&p=preview">
                <p> <i class="fas fa-chevron-left"></i> <?= ___('Nazad') ?> </p>
            </a>
        </div>
    </div>
</div>

<div class="container mp-shadow pt-2">
    <div class="mp-inside my-container">
        <form action="?m=scheme&p=create<?= isset($_GET['id']) ? '&id='.$_GET['id'] : '' ?>" method="post">
            <div class="mp-i-row">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="s_no"><?= ___('Oznaka po pravilniku') ?></label>
                            <?= Form::text('s_no', $orgJed['s_no'] ?? '', ['id' => 's_no', 'class' => 'form-control', 'required' => 'required']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="s_title"><?= ___('Naziv organizacione jedinice') ?></label>
                            <?= Form::text('s_title', $orgJed['s_title'] ?? '', ['id' => 's_title', 'class' => 'form-control', 'required' => 'required']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="s_parent"><?= ___('Nadređena organizaciona jedinica') ?></label>
                            <?= Form::select('s_parent', $parents, $orgJed['s_parent'] ?? '', ['id' => 's_parent', 'class' => 'form-control']) ?>
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