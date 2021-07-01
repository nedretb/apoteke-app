<?php

foreach (glob($root . '/CORE/classes/Models/*.php') as $filename) require_once $filename;

if(isset($request)){ // Search systematisation by name while there is only an ID
    if($request->egop_ustrojstvena_jedinica){
        $temp = $request->egop_ustrojstvena_jedinica;
        $orgJed = Sistematizacija::whereArr(['s_title' => $request->egop_ustrojstvena_jedinica])->first();

        if($orgJed) $request->egop_ustrojstvena_jedinica = $orgJed['id'];
        else $request->egop_ustrojstvena_jedinica = '';
    }
}

if($_user['role'] == 4){
    $profiles = Profile::select('employee_no, fname, lname, egop_radno_mjesto, egop_ustrojstvena_jedinica, nadredjeni')->whereArr(isset($request) ? $request->get() : [])->get();
}else{
    $profiles = Profile::getEmployees($_user, isset($request) ? $request->get() : []);
}

?>

<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Pregled svih državnih službenika') ?> </p>
    </div>
    <div class="sh-right">
        <div class="inside-link">
            <a href="#">
                <p> <i class="fas fa-chevron-left"></i> <?= ___('Nazad') ?> </p>
            </a>
        </div>
    </div>
</div>

<form action="" method="post">
    <div class="employee_filters">
        <div class="input-fields">
            <?= Form::text('employee_no', isset($request) ? $request->employee_no : '', ['class' => 'form-control', 'placeholder' => 'Šifra zaposlenika']) ?>
            <?= Form::text('fname', isset($request) ? $request->fname : '', ['class' => 'form-control', 'placeholder' => 'Ime']) ?>
            <?= Form::text('lname', isset($request) ? $request->lname : '', ['class' => 'form-control', 'placeholder' => 'Prezime']) ?>
            <?= Form::text('egop_radno_mjesto', isset($request) ? $request->egop_radno_mjesto : '', ['class' => 'form-control', 'placeholder' => 'Radno mjesto']) ?>
            <?= Form::text('egop_ustrojstvena_jedinica', isset($temp) ? $temp : '', ['class' => 'form-control', 'placeholder' => 'Organizaciona jedinica']) ?>
        </div>

        <div class="submit-button">
            <button class="btn btn-dark"><b><?= ___('Pretražite') ?></b></button>
        </div>
    </div>

</form>

<table class="table table-bordered">
    <thead>
    <tr>
        <th scope="col" class="text-center" width="60px">#</th>
        <th scope="col" width="160px"><small><?= ___('Šifra zaposlenika') ?></small></th>
        <th scope="col"><small><?= ___('Ime i prezime') ?></small></th>
        <th scope="col"><small><?= ___('Radno mjesto') ?></small></th>
        <th scope="col"><small><?= ___('Organizaciona jedinica') ?></small></th>
        <th scope="col" width="120px" class="text-center"><small><?= ___('Akcije') ?></small></th>
    </tr>
    </thead>
    <tbody>
        <?php
            $counter = 1;
            foreach ($profiles as $profile){
                ?>
                <tr>
                    <td class="text-center"><small><?= $counter++ ?></small></td>
                    <td><small><?= $profile['employee_no'] ?></small></td>
                    <td><small><?= $profile['fname'] ?> <?= $profile['lname'] ?></small></td>
                    <td><small><?= $profile['egop_radno_mjesto'] ?></small></td>
                    <td>
                        <small>
                            <?php
                                if(!empty($profile['egop_ustrojstvena_jedinica'])){
                                    try{
                                        $syst = Sistematizacija::select('s_title')->where('id = '.$profile['egop_ustrojstvena_jedinica'])->first();
                                    }catch (\Exception $e){}
                                }
                            ?>
                            <?= $syst['s_title'] ?? '' ?>
                        </small>
                    </td>

                    <td class="text-center"><a class="my-btn" href="#"><?= ___('Pregled') ?></a></td>
                </tr>
                <?php
            }
        ?>
        <tr>

        </tr>
    </tbody>
</table>