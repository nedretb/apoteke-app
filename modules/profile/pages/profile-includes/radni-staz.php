<?php
    $rs = $db->query("select * from [c0_intranet2_apoteke].[dbo].[users__radni_staz] where employee_no=".$_user['employee_no'])->fetch();
?>
<div class="mp-inside">

    <div class="mp-i-h">
        <h4><?= ___('Radni staž') ?></h4>
    </div>

    <div class="mp-i-row">

        <div class="form-group row">
            <label for="inputEmail3" class="col-sm-3 col-form-label mt-2"><?= ___('Doneseni radni staž: ') ?></label>
            <div class="col-sm-9">
                <input type="email" class="form-control form-control-sm" id="inputEmail3" value="<?php echo $rs['doneseni_radni_staz_g']."g ".$rs['doneseni_radni_staz_m']."m ".$rs['doneseni_radni_staz_d']."d" ?>" readonly>
            </div>
        </div>
        <div class="form-group row">
            <label for="inputEmail3" class="col-sm-3 col-form-label mt-2"><?= ___('Radni staž u kompaniji: ') ?></label>
            <div class="col-sm-9">
                <input type="email" class="form-control form-control-sm" id="inputEmail3" value="<?php echo $rs['radni_staz_u_kompaniji_g']."g ".$rs['radni_staz_u_kompaniji_m']."m ".$rs['radni_staz_u_kompaniji_d']."d" ?>" readonly>
            </div>
        </div>
        <div class="form-group row">
            <label for="inputEmail3" class="col-sm-3 col-form-label mt-2"><?= ___('Ukupan radni staž: ') ?></label>
            <div class="col-sm-9">
                <input type="email" class="form-control form-control-sm" id="inputEmail3" value="<?php echo $rs['ukupan_radni_staz_g']."g ".$rs['ukupan_radni_staz_m']."m ".$rs['ukupan_radni_staz_d']."d" ?>" readonlysa>
            </div>
        </div>

<!--        <table class="table table-bordered my-table table-sm">-->
<!--            <thead>-->
<!--                <tr>-->
<!--                    <th scope="coll" width="60px" class="text-center">#</th>-->
<!--                    <th scope="col"><small>--><?//= ___('Datum početka rada') ?><!--</small></th>-->
<!--                    <th scope="col"><small>--><?//= ___('Datum završetka rada') ?><!--</small></th>-->
<!--                    <th scope="col"><small>--><?//= ___('Ukupan radni staž') ?><!--</small></th>-->
<!--                </tr>-->
<!--            </thead>-->
<!--            <tbody>-->
<!--            <tr>-->
<!--                <td class="text-center">1.</td>-->
<!--                <td>01.01.2020</td>-->
<!--                <td>31.12.2020</td>-->
<!--                <td>1g 3m 16d</td>-->
<!--            </tr>-->
<!--            <tr>-->
<!--                <td class="text-center">1.</td>-->
<!--                <td>01.01.2020</td>-->
<!--                <td>31.12.2020</td>-->
<!--                <td>1g 3m 16d</td>-->
<!--            </tr>-->
<!--            </tbody>-->
<!--        </table>-->
    </div>
</div>