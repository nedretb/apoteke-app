<link rel="stylesheet" href="theme/css/performance-management.css">

<?php

//require_once 'modules/performance_management/table-scripts/pm_kalenadar.php';
if(isset($_POST['kalendar_faza']) or isset($_POST['kalendar_faza_update']) or isset($_GET['kalendar_delete'])){ require_once 'modules/performance_management/Pages/scripts/save_mbo.php'; }
function obrniDatum($datum){
    $od_arr = explode( '-', $datum);
    return $od_arr[2].'.'.$od_arr['1'].'.'.$od_arr[0];
}

try{
    $datumi = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] order by id DESC")->fetchAll();
}catch (PDOException $e){}

?>

<form id="admin-form" method="post">
    <div class="split-on-right">
        <?php
        if(isset($_GET['kalendar_edit'])){
            // Uredimo uzorak kalendara
            $datum = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where id = ".$_GET['kalendar_edit'])->fetch();
            ?>
            <div class="choose-what-to-do">
                <!-- Ispis svih ciljeva koji su postavljeni od strane korisnika :: Zadani sami sebi -->
                <h3>
                    Kalendar
                </h3>

                <input type="hidden" name="id" value="<?php echo $datum['id']; ?>">

                <div class="single-element">
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Faza
                            </div>
                            <select name="kalendar_faza_update">
                                <option value="0">Odaberite fazu</option>
                                <option value="1" <?php echo ($datum['faza'] == 1) ? 'selected' : '' ?>>Faza planiranja</option>
                                <option value="2" <?php echo ($datum['faza'] == 2) ? 'selected' : '' ?>>Faza revidiranja</option>
                                <option value="3" <?php echo ($datum['faza'] == 3) ? 'selected' : '' ?>>Faza evaluacije</option>
                            </select>
                        </div>
                    </div>
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Datum od
                            </div>
                            <input type="text" required name="kalendar_datum_od_update" class="form-control" style="height:35px;" id="dateOD1" placeholder="dd.mm.yyyy" title="" value="<?php echo obrniDatum($datum['datum_od']); ?>" autocomplete="off">
                        </div>
                        <div class="inside-col">
                            <div class="label-for">
                                Datum do
                            </div>
                            <input type="text" required name="kalendar_datum_do_update" class="form-control" style="height:35px;" id="dateDO1" placeholder="dd.mm.yyyy" title="" value="<?php echo obrniDatum($datum['datum_do']); ?>" autocomplete="off">
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="save-button">
                            <input type="submit" value="SPREMITE">
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }else{
            // Prikažimo uzorak kalendara - unesimo novi!
            ?>
            <div class="choose-what-to-do">
                <!-- Ispis svih ciljeva koji su postavljeni od strane korisnika :: Zadani sami sebi -->
                <h3>
                    Kalendar
                </h3>

                <div class="single-element">
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Faza
                            </div>
                            <select name="kalendar_faza">
                                <option value="0">Odaberite fazu</option>
                                <option value="1">Faza planiranja</option>
                                <option value="2">Faza revidiranja</option>
                                <option value="2">Faza evaluacije</option>
                            </select>
                        </div>
                    </div>
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Datum od
                            </div>
                            <input type="text" required name="kalendar_datum_od" class="form-control" style="height:35px;" id="dateOD1" placeholder="dd.mm.yyyy" title="" value="" autocomplete="off">
                        </div>
                        <div class="inside-col">
                            <div class="label-for">
                                Datum do
                            </div>
                            <input type="text" required name="kalendar_datum_do" class="form-control" style="height:35px;" id="dateDO1" placeholder="dd.mm.yyyy" title="" value="" autocomplete="off">
                        </div>
                    </div>


                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Email / Poruka
                            </div>
                            <textarea name="email_message"></textarea>
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="save-button">
                            <input type="submit" value="SPREMITE">
                        </div>
                    </div>
                </div>


                <h3>Historija</h3>

                <?php
                foreach($datumi as $datum){
                    ?>
                    <div class="single-element">
                        <div class="just-a-row">
                            <div class="inside-col">
                                <div class="label-for">
                                    Faza
                                </div>
                                <select name="">
                                    <option value="0">Odaberite fazu</option>
                                    <option value="1" <?php echo ($datum['faza'] == 1) ? 'selected' : '' ?>>Faza planiranja</option>
                                    <option value="2" <?php echo ($datum['faza'] == 2) ? 'selected' : '' ?>>Faza revidiranja</option>
                                    <option value="2" <?php echo ($datum['faza'] == 3) ? 'selected' : '' ?>>Faza evaluacije</option>
                                </select>
                            </div>
                        </div>
                        <div class="just-a-row">
                            <div class="inside-col">
                                <div class="label-for">
                                    Datum od
                                </div>
                                <input type="text" required name="" class="form-control" style="height:35px;" id="dateOD1" placeholder="dd.mm.yyyy" title="" value="<?php echo obrniDatum($datum['datum_od']); ?>" autocomplete="off">
                            </div>
                            <div class="inside-col">
                                <div class="label-for">
                                    Datum do
                                </div>
                                <input type="text" required name="" class="form-control" style="height:35px;" id="dateDO1" placeholder="dd.mm.yyyy" title="" value="<?php echo obrniDatum($datum['datum_do']); ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="edit-or-delete">
                            <div class="action-button action-button-blue" title="Uredite !">
                                <a href="?m=performance_management&p=mbo_kalendar&kalendar_edit=<?php echo $datum['id']; ?>">Uredite <i class="ion-edit"></i></a>
                            </div>
                            <div class="action-button" title="Obrišite !">
                                <a href="?m=performance_management&p=mbo_kalendar&kalendar_delete=<?php echo $datum['id']; ?>">Obrišite <i class="ion-ios-trash"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>

        <div class="right-menu">
            <div class="right-menu-header">
                <h4>Dodatne opcije</h4>
            </div>
            <!-- Privilegija koja je ostavljena samo za HR-Admina i rukovodioce -->
            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>
</form>
<?php include $_themeRoot.'/footer.php'; ?>

<script>
    $(document).ready(function() {
        $('#dateOD1').datepicker({
            todayBtn: "linked",
            format: 'dd.mm.yyyy',
            language: 'bs',
            //startDate: startDate,
            //endDate: new Date(year + '/12/31')
        });
        $('#dateDO1').datepicker({
            todayBtn: "linked",
            format: 'dd.mm.yyyy',
            language: 'bs',
            startDate: $("#dateOD1").val(),
            //endDate: new Date(year + '/12/31')
        });

        $("#dateOD1").on('change', function (e) {
            $("#dateDO1").datepicker("destroy");
            $('#dateDO1').datepicker({
                //todayBtn: "linked",
                defaultViewDate: new Date('2017/05/01'),
                format: 'dd.mm.yyyy',
                language: 'bs',
                startDate: $("#dateOD1").val()
                //endDate: new Date(year + '/12/31')

            });
            $("#dateDO1").datepicker("setDate", $("#dateOD1").val());
        });

    });
</script>