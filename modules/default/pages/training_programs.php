<?php
_pagePermission(5, false);
?>

<!-- START - Main section -->
<section class="full tasks-page">

    <?php

    if (isset($_GET['f']))
        $faza = $_GET['f'];
    else
        $faza = '1';

    $limit = 100;
    $usr = $_user['user_id'];


    $query_training_program_header = $db->query("SELECT * FROM  " . $portal_training_program_header . "  WHERE user_id = " . $usr . " AND obrazac_type=" . $faza);
    foreach ($query_training_program_header as $item) {
        $obrazac_type = $item['obrazac_type'];
        $obrazac_naslov = $item['obrazac_naslov'];
        $obrazac = $item['obrazac'];
        $potpisao_radnik = $item['potpisao_radnik'];
        $potpisao_mentor = $item['potpisao_mentor'];
        $date_to = $item['date_to'];
        $potpisao_radnik_datum = $item['potpisao_radnik_datum'];
    }
    $query_training_program_header_1 = $db->query("SELECT * FROM  " . $portal_training_program_header . "  WHERE user_id = " . $usr . " AND obrazac_type=1");
    foreach ($query_training_program_header_1 as $item) {
        $obrazac_1 = $item['obrazac'];
    }
    $query_training_program_header_2 = $db->query("SELECT * FROM  " . $portal_training_program_header . "  WHERE user_id = " . $usr . " AND obrazac_type=2");
    foreach ($query_training_program_header_2 as $item) {
        $obrazac_2 = $item['obrazac'];
    }
    $query_training_program_header_3 = $db->query("SELECT * FROM  " . $portal_training_program_header . "  WHERE user_id = " . $usr . " AND obrazac_type=3");
    foreach ($query_training_program_header_3 as $item) {
        $obrazac_3 = $item['obrazac'];
    }
    $query_training_program_header_4 = $db->query("SELECT * FROM  " . $portal_training_program_header . "  WHERE user_id = " . $usr . " AND obrazac_type=4");
    foreach ($query_training_program_header_4 as $item) {
        $obrazac_4 = $item['obrazac'];
    }
    ?>
    <br/>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12" style="margin-bottom:10px;">
                <div class="box">
                    <div class="col-sm-6 content1">
                        <img style="width:240px;"
                             src="<?php echo $_uploadUrl; ?>/<?php echo _settings('logo'); ?>"><br/><br/>
                        <label><b><?php if (isset($obrazac)) echo $obrazac_naslov; ?></b></label>
                    </div>

                    <div class="col-sm-6">
                        <img style="" src="<?php echo $_uploadUrl; ?>/<?php echo _settings('training_program'); ?>">
                    </div>

                </div>
            </div>

            <div class="btn btn-filter <?php if ($faza == '1') {
                echo 'active-task';
            } ?>"
                 style="margin-left:15px;margin-bottom: 15px;width: 130px;margin-right:7px;<?php if (!isset($obrazac_1)) {
                     echo 'display:none;';
                 } ?>"><a style="color:black" class="<?php if ($faza == '1') {
                    echo 'active-white';
                } ?>" href="?m=default&amp;p=training_programs&f=1">Plan i program</a></div>
            <a style="color:black" href="?m=default&amp;p=training_programs&f=2"><i class="fa fa-arrow-right"
                                                                                    style="position: absolute;margin-top: 9px;"
                                                                                    aria-hidden="true"></i></a>
            <div class="btn btn-filter <?php if ($faza == '2') {
                echo 'active-task';
            } ?>"
                 style="margin-left:15px;margin-bottom: 15px;width: 80px;margin-right:7px;<?php if (!isset($obrazac_2)) {
                     echo 'display:none;';
                 } ?>"><a style="color:black" class="<?php if ($faza == '2') {
                    echo 'active-white';
                } ?>" href="?m=default&amp;p=training_programs&f=2">Test</a></div>
            <a style="color:black" href="?m=default&amp;p=training_programs&f=4"><i class="fa fa-arrow-right"
                                                                                    style="position: absolute;margin-top: 9px;"
                                                                                    aria-hidden="true"></i></a>
            <div class="btn btn-filter <?php if ($faza == '4') {
                echo 'active-task';
            } ?>"
                 style="margin-bottom: 15px;width: 147px;margin-left:20px;margin-right: 15px;<?php if (!isset($obrazac_4)) {
                     echo 'display:none;';
                 } ?>"><a style="color:black" class="<?php if ($faza == '4') {
                    echo 'active-white';
                } ?>" href="?m=default&amp;p=training_programs&f=4">Evaluacija mentora</a></div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <?php if ($faza == 4) { ?>
                    <table class="alt" id="obuke">

                        <tr>
                            <td><b><?php echo __('Mentor:'); ?></b></td>
                            <td><?php echo _employee(_user($usr)['parent'])['fname'] . " " . _employee(_user($usr)['parent'])['lname']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('Radno mjesto mentora:'); ?></b></td>
                            <td><?php echo _employee(_user($usr)['parent'])['position'] ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('Naziv organizacione jedinice'); ?></b></td>
                            <td><?php echo _user($usr)['sector']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('Prezime i ime radnika:'); ?></b></td>
                            <td><?php echo _user($usr)['lname'] . ' ' . _user($usr)['fname']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('Period trajanja obuke/treninga (od - do):'); ?></b></td>
                            <td><?php echo date('d/m/Y', strtotime(_user($usr)['employment_date'])) . ' - ' . date('d/m/Y', strtotime("+6 months", strtotime(_user($usr)['employment_date']))); ?></td>
                        </tr>
                    </table>
                <?php } else { ?>
                    <table class="alt" id="obuke">

                        <tr>
                            <td><b><?php echo __('Prezime i ime radnika:'); ?></b></td>
                            <td><?php echo _user($usr)['lname'] . ' ' . _user($usr)['fname']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('Radno mjesto radnika:'); ?></b></td>
                            <td><?php echo _user($usr)['position']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('Naziv organizacione jedinice'); ?></b></td>
                            <td><?php echo _user($usr)['sector']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('Mentor:'); ?></b></td>
                            <td><?php echo _employee(_user($usr)['parent'])['fname'] . " " . _employee(_user($usr)['parent'])['lname']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('Period trajanja obuke/treninga (od - do):'); ?></b></td>
                            <td><?php echo date('d/m/Y', strtotime(_user($usr)['employment_date'])) . ' - ' . (($date_to == '') ? date('d/m/Y', strtotime("+6 months", strtotime(_user($usr)['employment_date']))) : date('d/m/Y', strtotime($date_to))); ?></td>
                        </tr>
                    </table>
                <?php } ?>

                <br/>

                <?php if ($faza == 1) { ?>
                    <span style="height:25px;padding-left:20px;display:block;background:white;color:black;font-size: 12px;padding-top: 4px;">*<b>Napomena:</b> Mentorstvo novog/preraspoređenog radnika, bez prethodnog iskustva na sličnim poslovima, može trajati najviše 6 mjeseci, dok mentorstvo za preraspoređenoga radnika, sa prethodnim iskustvom na sličnim poslovima, može trajati najviše  3 mjeseca.</span>
                <?php } ?>
                <?php if ($faza == 3) { ?>
                    <span style="height:145px;padding-left:20px;display:block;background:white;color:black;font-size: 12px;padding-top: 4px;">Molimo da ocijenite zaposlenika u toku i nakon završetka obuke.<br><b>Legenda:</b><br><b>5</b> - Značajno iznad očekivanja<br><b>4</b> - Iznad očekivanja <br><b>3</b> - U skladu sa očekivanjima<br><b>2</b> - Ispod očekivanja <br><b>1</b> - Značajno ispod očekivanja <br> Nije relevantno</span>
                <?php } ?>
                <?php if ($faza == 4) { ?>
                    <span style="height:110px;padding-left:20px;display:block;background:white;color:black;font-size: 12px;padding-top: 4px;">Molimo da ocijenite mentora nakon završetka obuke.<br><b>Legenda:</b><br>U potpunosti se slažem<br>Slažem se<br>Nisam siguran/na<br>Ne slažem se</span>
                <?php } ?>
            </div>

        </div>

    </div>
    </div>
    <br/>

    <?php

    $query = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_training_program_textovi . "  WHERE obrazac = '" . $obrazac . "' order by item_id");
    $query1 = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_training_program_status . "  WHERE obrazac = '" . $obrazac . "' AND user_id = '" . $usr . "' order by item_id");
    $training_program_statusi = $query1->fetchAll();

    $query_broj_status_mentor = $db->query("SELECT COUNT(*) as broj FROM  " . $portal_training_program_status . "  WHERE obrazac = '" . $obrazac . "' AND user_id = '" . $usr . "'");
    $broj_status_mentor1 = $query_broj_status_mentor->fetch();
    $broj_status_mentor = $broj_status_mentor1['broj'];
    ?>

    <?php if ($potpisao_mentor) { ?>
        <span class="potvrda" style="margin-left:15px;margin-right:15px;margin-top:0px;">Obrazac potpisao mentor!</span>
        <br/>
    <?php } ?>

    <?php if ($potpisao_radnik) { ?>
        <span class="potvrda"
              style="margin-left:15px;margin-top:-10px;margin-right:15px;">Obrazac potpisao radnik!</span><br/>
    <?php } ?>


    <div class="col-sm-12">
        <table class="alt" id="obuke">
            <?php if ($faza == 1 and $obrazac != 'b1_b2_dir_regije_po_centrala') { ?>
                <tr id="obuke-red1">
                    <th style="height:20px;width: 500px;"></th>
                    <th style="height:20px;width: 600px;">PLAN I PROGRAM OBUKE/TRENINGA</th>
                    <th style="height:20px;width: 200px;"></th>
                    <th style="height:20px;width: 300px;"></th>
                </tr>
                <tr>
                    <th style="height:20px; background: #c7bebe;color: black;width: 500px;">Tema</th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 600px;">Opis</th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 200px;">Status</th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Komentar</th>
                </tr>
            <?php } ?>

            <?php if ($faza == 1 and $obrazac == 'b1_b2_dir_regije_po_centrala') { ?>
                <tr id="obuke-red1">
                    <th style="height:20px;width: 200px;"></th>
                    <th style="height:20px;width: 200px;"></th>
                    <th style="height:20px;width: 500px;"></th>
                    <th style="height:20px;width: 600px;">PLAN I PROGRAM OBUKE/TRENINGA</th>
                    <th style="height:20px;width: 200px;"></th>
                    <th style="height:20px;width: 300px;"></th>
                </tr>
                <tr>
                    <th style="height:20px; background: #c7bebe;color: black;width: 200px;">Odgovorna OJ</th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 200px;">Područje</th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 500px;">Tema</th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 600px;">Opis</th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 200px;">Status</th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Komentar</th>
                </tr>
            <?php } ?>
            <?php if ($faza == 2) { ?>
                <tr id="obuke-red1">
                    <th style="height:20px;width: 500px;"></th>
                    <th style="height:20px;width: 600px;">TEST – SISTEMSKO TESTIRANJE</th>
                    <th style="height:20px;width: 200px;"></th>
                    <th style="height:20px;width: 300px;"></th>
                </tr>
                <tr>
                    <th style="height:20px; background: #c7bebe;color: black;width: 500px;">Tema</th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 600px;">Opis</th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 200px;">Status</th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Komentar</th>
                </tr>
            <?php } ?>
            <?php if ($faza == 4) { ?>
                <tr id="obuke-red1">
                    <th style="height:20px;width: 500px;"></th>
                    <th style="height:20px;width: 600px;">PRAĆENJE I EVALUACIJA MENTORA</th>
                    <th style="height:20px;width: 200px;"></th>
                </tr>
                <tr>
                    <th style="height:20px; background: #c7bebe;color: black;width: 500px;">Tačke za ocjenu
                        zaposlenika
                    </th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 600px;">Komentar</th>
                    <th style="height:20px; background: #c7bebe;color: black;width: 200px;">Ocjena mentora</th>
                </tr>
            <?php } ?>

            <?php
            foreach ($query as $key => $item) {
                $desc = str_replace(" ·         ", "<br>  · ", $item['item_description'], $count);
                $desc = str_replace("·         ", "· ", $desc, $count);
                ?>
                <tr>

                    <?php if ($faza == 1 and $obrazac != 'b1_b2_dir_regije_po_centrala') { ?>
                        <td><?php echo $item['item_tema']; ?></td>
                        <td><?php echo $desc; ?></td>
                        <td><select id="status" name="<?php echo $item['item_id'] . '-' . $usr . '-' . $faza; ?>"
                                    class="rcorners1" class="form-control" style="width:140px;outline:none;">
                                <?php echo _optionObukaStatus($training_program_statusi[$key]['status']); ?>
                            </select></td>
                        <td><textarea id="komentar" name="<?php echo $item['item_id'] . '-' . $usr . '-' . $faza; ?>"
                                      rows="3" cols="40"
                                      spellcheck="false"><?php echo $training_program_statusi[$key]['komentar']; ?></textarea>
                        </td>
                    <?php } ?>
                    <?php if ($faza == 1 and $obrazac == 'b1_b2_dir_regije_po_centrala') { ?>
                        <td><?php echo $item['Odgovorna_OJ']; ?></td>
                        <td><?php echo $item['Podrucje']; ?></td>
                        <td><?php echo $item['item_tema']; ?></td>
                        <td><?php echo $desc; ?></td>
                        <td><select id="status" name="<?php echo $item['item_id'] . '-' . $usr . '-' . $faza; ?>"
                                    class="rcorners1" class="form-control" style="width:140px;outline:none;">
                                <?php echo _optionObukaStatus($training_program_statusi[$key]['status']); ?>
                            </select></td>
                        <td><textarea id="komentar" name="<?php echo $item['item_id'] . '-' . $usr . '-' . $faza; ?>"
                                      rows="3" cols="40"
                                      spellcheck="false"><?php echo $training_program_statusi[$key]['komentar']; ?></textarea>
                        </td>
                    <?php } ?>
                    <?php if ($faza == 2) { ?>
                        <td><?php echo $item['item_tema']; ?></td>
                        <td><?php echo $desc; ?></td>
                        <td><select id="status" name="<?php echo $item['item_id'] . '-' . $usr . '-' . $faza; ?>"
                                    class="rcorners1" class="form-control" style="width:180px;outline:none;">
                                <?php echo _optionObukaTestStatus($training_program_statusi[$key]['status']); ?>
                            </select></td>
                        <td><textarea id="komentar" name="<?php echo $item['item_id'] . '-' . $usr . '-' . $faza; ?>"
                                      rows="3" cols="40"
                                      spellcheck="false"><?php echo $training_program_statusi[$key]['komentar']; ?></textarea>
                        </td>
                    <?php } ?>
                    <?php if ($faza == 4) { ?>
                        <td><?php echo $item['item_tema']; ?></td>
                        <td <?php if ($key == $broj_status_mentor - 1) {
                            echo 'colspan="2"';
                        } ?>><textarea id="coment" name="<?php echo $item['item_id'] . '-' . $usr . '-' . $faza; ?>"
                                       rows="2" cols="80"
                                       spellcheck="false"><?php echo $training_program_statusi[$key]['komentar_zavrsni']; ?></textarea>
                        </td>
                        <td <?php if ($key == $broj_status_mentor - 1) {
                            echo 'style="display:none;"';
                        } ?>><select id="ocjena_mentora"
                                     name="<?php echo $item['item_id'] . '-' . $usr . '-' . $faza; ?>" class="rcorners1"
                                     class="form-control"
                                     style="width:190px;outline:none;<?php if ($training_program_statusi[$key]['ocjena_mentora'] != '' and $training_program_statusi[$key]['ocjena_mentora'] != '0') echo 'background:#2e9e12;color:white;'; ?>">
                                <?php echo _optionObukaOcjenaMentora($training_program_statusi[$key]['ocjena_mentora']); ?>
                            </select></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
        <br/>

        <?php if ($faza == 1 and !$potpisao_radnik and $potpisao_mentor) { ?>
            <span style="padding-left:10px;padding-right:10px;">Odabirom "Potpiši obrazac", saglasan/a sam i elektronski potpisujem Plan i program obuke,  potvrđujem tačnost i potvrđujem da sam zavšio/la gore navedenu obuku.</span>
        <?php } ?>
        <?php if ($faza == 2 and !$potpisao_radnik and $potpisao_mentor) { ?>
            <span style="padding-left:10px;padding-right:10px;">Odabirom "Potpiši obrazac", elektronski potpisujem Test-sistemsko testiranje,  potvrđujem tačnost i potvrđujem da sam zavšio/la gore navedeni test.</span>
        <?php } ?>
        <?php if ($faza == 4 and !$potpisao_radnik) { ?>
            <span style="padding-left:10px;padding-right:10px;">Odabirom "Potpiši obrazac", elektronski potpisujem Obrazac za praćenje i evaluaciju mentora,  i potvrđujem gore navedene podatke.</span>
        <?php } ?>


        <br/> <br/>
        <?php if ($faza == 1 || $faza == 2) { ?>
            <a id="link_potvrda" href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
               style="float:right;margin-top:-50px;display:<?php if (!$potpisao_radnik and $potpisao_mentor) {
                   echo '';
               } else {
                   echo 'none';
               } ?>" class="table-btn alt1" data-widget="send"
               data-id="potpisuje_radnik_obuka:<?php echo $_user['user_id'] . '-' . $faza; ?>"
               data-text="<?php echo __('Da li ste sigurni da želite potpisati obrazac?'); ?>"
               data-response="<?php echo __('Obrazac uspješno potpisan'); ?>"><i class="fa fa-arrow-circle-right"
                                                                                 style="float:left;color:green;"></i><label
                        style="font-size:17px;cursor: pointer;">Potpiši obrazac</label></a><br><br><br>
        <?php } ?>
        <?php if ($faza == 4) { ?>
            <a id="link_potvrda" href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
               style="float:right;margin-top:-50px;display:<?php if (!$potpisao_radnik) {
                   echo '';
               } else {
                   echo 'none';
               } ?>" class="table-btn alt1" data-widget="send"
               data-id="potpisuje_radnik_obuka_eval:<?php echo $_user['user_id'] . '-' . $faza; ?>"
               data-text="<?php echo __('Da li ste sigurni da želite potpisati obrazac?'); ?>"
               data-response="<?php echo __('Obrazac uspješno potpisan'); ?>"><i class="fa fa-arrow-circle-right"
                                                                                 style="float:left;color:green;"></i><label
                        style="font-size:17px;cursor: pointer;">Potpiši obrazac</label></a><br><br><br>
        <?php } ?>

    </div>
    <div class="col-sm-12">
        <?php if ($potpisao_radnik) {
            if ($faza != 4)
                $evaluator = 'Radnik';
            else
                $evaluator = 'Evaluator';
            ?>
            <div class="col-sm-6">
                <br/> <span
                        style="margin-left:0px;background:none;color:black;float:left;"><b><?php echo $evaluator; ?></b> </span><br/>
                <span style="margin-left:0px;width:100px;background:none;color:black;float:left;"><?php echo 'Ime i prezime :'; ?></span><br/><br/>
                <span style="margin-left:0px;width:100px;background:none;color:black;float:left;"><u><?php echo $_user['fname'] . ' ' . $_user['lname']; ?></u> </span><br/><br/><br/>

                <span style="margin-left:0px;width:100px;background:none;color:black;float:left;"><?php echo 'Datum, potpis :'; ?></span><br/><br/>
                <span style="margin-left:0px;width:100px;background:none;color:black;float:left;"><u><?php echo ($potpisao_radnik_datum != '') ? date('d/m/Y', strtotime($potpisao_radnik_datum)) : ''; ?></u> </span><br/><br/><br/>
            </div>
        <?php } ?>
    </div>
    </div>

</section>
<!-- END - Main section -->

<?php
include $_themeRoot . '/footer.php';
?>
<script>

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

    $('select:regex(id, .*status.*)').on('change', function () {
        $.post("<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>", {
                request: "change-obuka-status",
                status: this.value,
                obuka: this.name
            },
            function (returnedData) {
                window.location.reload();
            });
    })


    $('textarea:regex(id, .*komentar.*)').on('change', function () {

        $.post("<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>", {
                request: "change-obuka-komentar",
                komentar: this.value,
                obuka: this.name
            },
            function (returnedData) {

            });
    })

    $('textarea:regex(id, .*coment.*)').on('change', function () {

        $.post("<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>", {
                request: "change-obuka-coment",
                komentar: this.value,
                obuka: this.name
            },
            function (returnedData) {

            });
    })

    $('select:regex(id, .*ocjena_mentora.*)').on('change', function () {
        $.post("<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>", {
                request: "change-obuka-ocjena_mentora",
                ocjena: this.value,
                obuka: this.name
            },
            function (returnedData) {
                window.location.reload();
            });
    })

    var faza_finished = '<?php echo $potpisao_radnik; ?>';
    if (faza_finished == faza_finished) {
        $("select:not(#ocjena_mentora) option:not(:selected)").prop("disabled", true);
        $("input").prop('disabled', true);
        //$("textarea").prop('disabled', true);
    }

</script>
</body>
</html>
