<?php
_pagePermission(5, false);
?>

<!-- START - Main section -->
<section class="full tasks-page">

    <?php
    $limit = 40;
    $usr = $_user['user_id'];
    $sector_type = $_user['department_code_type'];
    ?>
    <br/>

    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-3" style="margin-top: 10px;">
                <div class="btn btn-filter <?php if ($_page == 'zaduznice') {
                    echo 'active-task';
                } ?>" style="margin-bottom: 15px;width: 130px;margin-right:7px;"><a style="color:black"
                                                                                    class="<?php if ($_page == 'zaduznice') {
                                                                                        echo 'active-white';
                                                                                    } ?>"
                                                                                    href="?m=default&amp;p=zaduznice<?php echo '&u=' . $usr ?>">Zadužnice</a>
                </div>
                <div class="btn btn-filter <?php if ($_page == 'razduznice') {
                    echo 'active-task-red-razduznice';
                } ?>" style="margin-bottom: 15px;width: 147px;margin-left:20px;margin-right: 15px;"><a
                            style="color:black" class="<?php if ($_page == 'razduznice') {
                        echo 'active-white';
                    } ?>" href="?m=default&amp;p=razduznice<?php echo '&u=' . $usr ?>">Razdužnice</a></div>

            </div>

            <?php


            if ($sector_type == 0)
                $query = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_zaduznice_items . "  order by item_id");
            else
                $query = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_zaduznice_items . "  where OJ<>3 order by item_id");
            $query1 = $db->query("SELECT TOP 1 * FROM  " . $portal_zaduznice_header . "  WHERE user_id = '" . $usr . "'");
            $header = $query1->fetch();
            $potvrda_hr_zaduznica = $header['potvrda_hr_zaduznica'];
            $potvrda_hr_razduznica = $header['potvrda_hr_razduznica'];
            $potvrda_hr_steta = $header['potvrda_hr_steta'];
            $poslano_radniku_zaduznica = $header['poslano_radniku_zaduznica'];
            $poslano_radniku_razduznica = $header['poslano_radniku_razduznica'];
            $radnik_potpisao_zaduznica = $header['radnik_potpisao_zaduznica'];
            $radnik_odbio_zaduznica = $header['radnik_odbio_zaduznica'];


            if ($sector_type == 0)
                $query_status = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_zaduznice_status . "  WHERE user_id = " . $usr . " order by item_id");
            else
                $query_status = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_zaduznice_status . "  WHERE user_id = " . $usr . " and OJ<>3 order by item_id");
            $zaduznice_statusi = $query_status->fetchAll();
            ?>

            <div class="row">

                <div class="col-sm-12" style="margin-top:20px;">

                    <?php if ($radnik_potpisao_zaduznica) { ?>
                        <span class="potvrda" style="margin-left:15px;margin-right:15px;">Zadužnica potpisana!</span>
                        <br/>
                    <?php } ?>

                    <?php if ($radnik_odbio_zaduznica) { ?>
                        <span class="potvrda" style="margin-left:15px;margin-right:15px;">Zadužnica odbijena!</span>
                        <br/>
                    <?php } ?>

                    <span style="display:block;padding:10px;text-align:center;margin-left:15px;margin-right:15px;">ZADUŽNICA</span>
                    <span style="height:45px;padding-left:20px;display:block;background:white;color:black;font-size: 14px;padding-top: 4px;margin-left:15px;margin-right:15px;">Radnik <u><?php echo _user($usr)['fname'] . ' ' . _user($usr)['lname'] . '</u>, JMBG <u>' . _user($usr)['JMB'] . '</u>, OJ <u>' . _user($usr)['sector'] . '</u> je počeo sa radom u Sberbank BH d.d. dana <u>' . date('d/m/Y', strtotime(_user($usr)['employment_date'])) . '</u>. Ovim se potvrđuju obaveze radnika, tj. zaduženje tehničke imovine i proizvoda Sberbank BH d.d. na dan <u>' . date('d/m/Y') . '</u>'; ?></span>

                </div>
            </div>
        </div>
    </div>
    <br/>

    <form action="zaduznice.php" method="get" class="" target="_blank">
        <input type="hidden" name="user_id" value="<?php echo $usr; ?>"/>
        <input type="hidden" name="radnik_ime"
               value="<?php echo _user($usr)['fname'] . ' ' . _user($usr)['lname']; ?>"/>
        <input type="hidden" name="JMBG" value="<?php echo _user($usr)['JMB']; ?>"/>
        <input type="hidden" name="employment_date"
               value="<?php echo date('d/m/Y', strtotime(_user($usr)['employment_date'])); ?>"/>
        <input type="hidden" name="sector" value="<?php echo _user($usr)['sector']; ?>"/>

        <div class="col-sm-12">
            <table class="alt" id="obuke">
                <tr>
                    <th style="height:20px;width: 500px;background: #D9D9D9;color:black;">NAZIV OJ</th>
                    <th style="height:20px;width: 600px;background: #D9D9D9;color:black;">Vrsta/Iznos</th>
                    <th style="height:20px;width: 60px;">Zaduženo (DA/NE)</th>
                    <th style="height:20px;width: 300px;">Datum zaduženja</th>
                    <th style="height:20px;width: 300px;">Ime i prezime odgovorne osobe</th>
                    <th style="height:20px;width: 300px;">Zapisnik</th>
                    <th style="height:20px;width: 60px;">Radnik saglasan</th>
                </tr>

                <?php
                foreach ($query as $key => $item) {
                    if ($zaduznice_statusi[$key]['zaduzen'] == 1) {
                        if ($item['OJ'] == 1) $oj = _section_by_department(_employee(_user($usr)['parent'])['department_code']);
                        else if ($item['OJ'] == 2) $oj = 'Lični/Osobni bankar';
                        else if ($item['OJ'] == 3) $oj = _section_by_department($_user['department_code']);
                        else
                            $oj = _section_by_department($item['OJ']); ?>
                        <tr>
                            <td><input readonly="readonly" type="text" class="rcorners1" style="width:300px;"
                                       name="oj[]" value="<?php echo $oj; ?>"></td>
                            <td><input readonly="readonly" type="text" class="rcorners1" style="width:300px;"
                                       name="item_description[]" value="<?php echo $item['item_description']; ?>"></td>
                            <td><input readonly="readonly" type="text" class="rcorners1" style="width:60px;"
                                       name="zaduzeno[]"
                                       value="<?php echo _transformZaduzeno($zaduznice_statusi[$key]['zaduzen']); ?>">
                            </td>
                            <td><input readonly="readonly" type="text" class="rcorners1" name="datum[]"
                                       value="<?php if ($zaduznice_statusi[$key]['datum'] == '') {
                                           echo '';
                                       } else {
                                           echo date('d/m/Y', strtotime($zaduznice_statusi[$key]['datum']));
                                       } ?>"></td>
                            <td><input readonly="readonly" type="text" class="rcorners1" name="odgovorna_osoba[]"
                                       value="<?php echo _user($zaduznice_statusi[$key]['odgovorna_osoba'])['fname'] . ' ' . _user($zaduznice_statusi[$key]['odgovorna_osoba'])['lname']; ?>">
                            </td>
                            <td><textarea id="<?php echo 'zapisnik-' . $item['item_id'] . '-' . $usr; ?>"
                                          readonly="readonly"
                                          name="zapisnik[]" <?php if ($zaduznice_statusi[$key]['saglasan'] == 0) echo 'style="border-color: red"'; ?> rows="3"
                                          cols="40"
                                          spellcheck="false"><?php echo $zaduznice_statusi[$key]['zapisnik']; ?></textarea>
                            </td>
                            <td><select id="<?php echo 'saglasan-' . $item['item_id'] . '-' . $usr; ?>"
                                        name="saglasan[]" class="rcorners1" class="form-control"
                                        style="width:60px;outline:none;">
                                    <?php echo _optionSaglasan($zaduznice_statusi[$key]['saglasan']); ?>
                        </tr>

                    <?php }
                } ?>

            </table>
            <br/>

            <br/> <br/>

            <a id="link_potvrda" href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
               style="float:right;display:<?php if ($poslano_radniku_zaduznica and !$radnik_potpisao_zaduznica) {
                   echo '';
               } else {
                   echo 'none';
               } ?>" class="table-btn alt1" data-widget="send" data-id="radnik_potpisuje_zaduznica:<?php echo $usr; ?>"
               data-text="<?php echo __('Da li ste sigurni da želite potpisati?'); ?>"
               data-response="<?php echo __('Uspješno potpisano!'); ?>"><i class="fa fa-arrow-circle-right"
                                                                           style="float:left;color:green;"></i><label
                        style="font-size:17px;cursor: pointer;">Potpiši</label></a><br><br><br>
            <a id="link_potvrda" href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
               style="float:right;display:<?php if ($poslano_radniku_zaduznica and !$radnik_potpisao_zaduznica) {
                   echo '';
               } else {
                   echo 'none';
               } ?>" class="table-btn alt1" data-widget="send" data-id="radnik_odbija_zaduznica:<?php echo $usr; ?>"
               data-text="<?php echo __('Da li ste sigurni da želite odbiti zadužnicu?'); ?>"
               data-response="<?php echo __('Uspješno odbijeno!'); ?>"><i class="fa fa-arrow-circle-right"
                                                                          style="float:left;color:green;"></i><label
                        style="font-size:17px;cursor: pointer;">Odbij zadužnicu</label></a><br><br><br>

        </div>

        <button type="submit" style="margin-bottom:40px;width:200px;float:right;height:45px;margin-right:15px;"
                class="btn btn-red pull-right"><?php echo __('Printaj!'); ?> <i class="ion-ios-copy-outline"></i>
        </button>
    </form>
    </div>

</section>
<!-- END - Main section -->

<?php

include $_themeRoot . '/footer.php';

?>
<script>
    $(document).ready(function () {
        //$("select option:not(:selected)").prop("disabled", true);
        $("#select_input").select2();
        var poslano_radniku = '<?php echo $poslano_radniku_zaduznica; ?>';
        var radnik_potpisao = '<?php echo $radnik_potpisao_zaduznica; ?>';
        if ((radnik_potpisao != '' && radnik_potpisao != '0') || (poslano_radniku != '1')) {
            $("select option:not(:selected)").prop("disabled", true);
            //$("input").prop('disabled', true);
            $("textarea").attr('readonly', 'readonly');
        }
    });

    jQuery.expr[':'].regex = function (elem, index, match) {
        var matchParams = match[3].split(','),
            validLabels = /^(data|css):/,
            attr = {
                method: matchParams[0].match(validLabels) ?
                    matchParams[0].split(':')[0] : 'attr',
                property: matchParams.shift().replace(validLabels, '')
            },
            regexFlags = 'ig',
            regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g, ''), regexFlags);
        return regex.test(jQuery(elem)[attr.method](attr.property));
    }

    $('select:regex(id, .*saglasan.*)').on('change', function () {
        $.post("<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>", {
                request: "change-saglasan",
                saglasan: this.value,
                saglasan_id: this.id
            },
            function (returnedData) {
                window.location.reload();
            });
    })

    $('textarea:regex(id, .*zapisnik.*)').on('change', function () {

        $.post("<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>", {
                request: "change-zaduznica-zapisnik",
                zapisnik: this.value,
                zapisnik_id: this.id
            },
            function (returnedData) {

            });
    })
</script>

</body>
</html>
