<?php
_pagePermission(5, false);
//  error_reporting(E_ALL);
?>
</div>
<style>
    body .dialog-main {
        width: 350px !important;
        margin-left: -15% !important;

    }

    .sortable thead tr th {
        cursor: pointer;
    }

    #sorttable_sortfwdind {
        margin-left: 10px;
        padding-right: 4px;
    }

    .select2-dropdown.select2-dropdown--above {
        width: 150px !important;
    }


</style>
<!-- START - Main section -->

<section class="full tasks-page" style="width: 95%; margin:0 auto;">

    <?php

    $limit = 5000;
    $where = "WHERE user_id='" . $_user['user_id'] . "'";
    $where1 = " where 1=1";
    $path = '?m=' . $_mod . '&p=' . $_page;
    $path .= '&pg=';

    if (isset($_GET['pod_primjene']) and $_GET['pod_primjene'] != '0') {
        $pod_primjene = $_GET['pod_primjene'];
        if ($pod_primjene == 'Svi')
            $pod_primjene_query = " and [department name] = ''";
        else
            $pod_primjene_query = " and [department name] = N'" . $_GET['pod_primjene'] . "'";
    } else {
        $pod_primjene = '0';
        $pod_primjene_query = "";
    }

    if (isset($_GET['naz_praznik']) and $_GET['naz_praznik'] != '0') {
        $naz_praznik = $_GET['naz_praznik'];
        $naz_praznik_query = " and [holiday_name] = N'" . $_GET['naz_praznik'] . "'";
    } else {
        $naz_praznik = '0';
        $naz_praznik_query = "";
    }

    if (isset($_GET['date'])) {
        $date = $_GET['date'];
        $date1 = date("Y/m/d", strtotime(str_replace("/", "-", $_GET['date'])));
        $date_query = " and [date] = '" . $date1 . "'";
    } else {
        $date = '';
        $date_query = "";
    }

    if (isset($_GET['pomicni']) and $_GET['pomicni'] != '-1') {
        $pomicni = $_GET['pomicni'];
        $pomicni_query = " and [pomicni] = " . $_GET['pomicni'];
    } else {
        $pomicni = '-1';
        $pomicni_query = "";
    }


    if (isset($_GET['org_jed'])) {
        $_GET['org_jed'] = str_replace("'", "", $_GET['org_jed']);

        $org_jed_query = " and [department name] = N'$_GET[org_jed]' ";
    } else {
        $org_jed_query = "";
    }

    if (isset($_GET['naz_entiteta']) and $_GET['naz_entiteta'] != '0') {
        $naz_entiteta = $_GET['naz_entiteta'];
        if ($naz_entiteta == 'FBIH')
            $naz_entiteta_query = " and [department name] = 'Federacija BIH'";

        if ($naz_entiteta == 'RS')
            $naz_entiteta_query = " and [department name] = 'Republika Srpska'";

        if ($naz_entiteta == 'BD')
            $naz_entiteta_query = " and [department name] = N'Brčko Distrikt'";
    }


    ?>

    <br/> <br/>
    <?php
    if (!isset($naz_entiteta_query)) {
        $naz_entiteta_query = '';
    }

    $query = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_holidays_per_department . " " . $where1 . $pod_primjene_query . $naz_praznik_query . $date_query . $pomicni_query . $naz_entiteta_query . $org_jed_query);


    $get2 = $db->query("SELECT distinct [department name] FROM  " . $portal_holidays_per_department . "  where [department name]='Republika Srpska' or [department name]='Federacija BIH' or [department name]= N'Brčko Distrikt' order by [department name]");
    $streams = $get2->fetchAll();


    ?>
    <?php

    ?>

    <div class="row">
        <div class="col-md-1" style="width:14%; padding-top: 4px;">

            <br/>
            <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_holiday_add.php' ?>" data-widget="ajax"
               data-id="opt2" data-width="1500" class="btn btn-sm btn-red"
               style="margin-bottom:5px; line-height: 28px;   width: 100%;display:<?php if (isset($_GET['u'])) {
                   echo 'none';
               } else {
                   echo '';
               } ?>">Novi unos <i style="    padding-top: 5px;" class="ion-ios-plus-empty"></i></a>

        </div>
        <div class="col-md-2">
            <label><?php echo __('Područje primjene'); ?></label>
            <select style="padding:0px !important; " name="pod_primjene" id="pod_primjene" class="form-control">
                <?php echo _optionStreamTeamWithGF($pod_primjene); ?>
                <option <?php if (isset($_GET['pod_primjene']) and $_GET['pod_primjene'] == '') echo 'selected'; ?>
                        value=''>Svi
                </option>
                <option <?php if (isset($_GET['pod_primjene']) and $_GET['pod_primjene'] == 'centrala') echo 'selected'; ?>
                        value='centrala'>CENTRALA
                </option>
            </select>
        </div>
        <div class="col-md-2" style="width:14%!important;">
            <label><?php echo __('Datum'); ?></label>
            <div id="dt">
                <input type="text" name="date" class="form-control" id="date" style="height: 39px;"
                       placeholder="dd.mm.yyyy"
                       value="<?php echo $date; ?>">
            </div>
        </div>
        <div class="col-md-2" style="width:14%!important;">
            <label><?php echo __('Naziv praznika'); ?></label>
            <select style="padding:0px !important; " name="naz_praznik" id="naz_praznik" class="form-control">
                <?php echo _optionNazivPraznika($naz_praznik); ?>
            </select>
        </div>
        <div class="col-md-1" style="width:10%!important;">
            <label><?php echo __('Pomični'); ?></label>
            <select style="padding:0px !important; " name="pomicni" id="pomicni" class="form-control">
                <?php echo _OptionPomicni($pomicni); ?>
            </select>
        </div>

        <div class="col-md-2" style="width:14%!important;">
            <label class=""><?php echo __('Entitet:'); ?></label>
            <select style="padding:0px !important; " name="naz_entiteta" id="entity"
                    class="form-control " data-placeholder="Odaberi">
                <option value="..">Odaberi...</option>
                <option <?php if (@$_GET['naz_entiteta'] == 'FBIH') {
                    echo 'selected="selected"';
                } else {
                    echo '';
                } ?> value="FBIH">Federacija BiH
                </option>
                <option <?php if (@$_GET['naz_entiteta'] == 'RS') {
                    echo 'selected="selected"';
                } else {
                    echo '';
                } ?> value="RS">Republika Srpska
                </option>
                <option <?php if (@$_GET['naz_entiteta'] == 'BD') {
                    echo 'selected="selected"';
                } else {
                    echo '';
                } ?> value="BD">Brčko Distrikt
                </option>
            </select>

        </div>
        <div class="col-md-2">
            <label class=""><?php echo __('Org. jedinica:'); ?></label>
            <select style="padding:0px !important; " name="org_jed" id="org_jed"
                    class="form-control " data-placeholder="Odaberi">
                <option value="..">Odaberi...</option>
                <?php

                $d = $db->prepare('SELECT Code, Description FROM ' . $_conf['nav_database'] . '.[RAIFFAISEN BANK$ORG Dijelovi] WHERE Active = 1');
                $d->execute();
                $f = $d->fetchAll();

                foreach ($f as $k => $v) {

                    ?>
                    <option value="<?php echo $v['Description']; ?>"><?php echo $v['Description']; ?></option>
                    <?php
                }
                ?>
            </select>

        </div>
    </div>

    <div>

        <table class="alt col-sm-12" style="margin-top:15px;">
            <tr>
                <th style="height:20px;width: 600px;">Kalendar praznici</th>
                <th style="height:20px;width: 300px;"></th>
                <th style="height:20px;width: 500px;"></th>
                <th style="height:20px;width: 200px;"></th>
                <th style="height:20px;width: 20px;"></th>
                <th style="height:20px;width: 20px;"></th>

            </tr>
        </table>
        <table class="alt col-sm-12 sortable">
            <thead>
            <tr>
                <th style="height:20px; background: #c7bebe;color: black;width: 600px;">Područje primjene</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 300px;">Datum</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 500px;">Naziv praznika</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 200px;">Pomični</th>
                <th style="height:20px; background: #c7bebe;color: black;width: 20px;"></th>
                <th style="height:20px; background: #c7bebe;color: black;width: 20px;"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($query as $item) {
                if ($item['department name'] == '')
                    $dep = 'Svi';
                else
                    $dep = $item['department name'];
                ?>
                <tr>
                    <td><?php echo $dep; ?></td>
                    <td sorttable_customkey="<?php echo date('d/m/Y', strtotime($item['date'])); ?>"><?php echo date('d.m.Y', strtotime($item['date'])); ?></td>
                    <td><?php echo $item['holiday_name']; ?></td>
                    <td><?php if ($item['Pomicni'] == '1') {
                            echo 'DA';
                        } else {
                            echo 'NE';
                        } ?></td>

                    <td>
                        <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_holiday_edit.php?id=' . $item['id']; ?>"
                           class="table-btn" data-widget="ajax" data-id="opt2" data-width="200"><i class="ion-edit"></i></a>
                    </td>
                    <td><a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>" class="table-btn1"
                           data-widget="remove-praznik" data-id="holiday_remove:<?php echo $item['id']; ?>"
                           data-text="<?php echo __('Dali ste sigurni da želite obrisati praznik (brisanje će uticati na kreirane satnice)?'); ?>"
                           title="Obriši" style="display:<?php if (isset($_GET['u'])) {
                            echo 'none';
                        } else {
                            echo '';
                        } ?>"><i class="ion-android-close"></i></a></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <br/> <br/>
    </div>

    <br/>
    </div>
    </div>
</section>
<!-- END - Main section -->

<?php
include $_themeRoot . '/footer.php';
?>
<script>

    var today = new Date();
    var startDate = new Date();
    $('#date').datepicker({
        todayBtn: "linked",
        format: 'dd.mm.yyyy',
        language: 'bs'
        //startDate: startDate,
        //endDate: new Date('2017/12/31')
    });


    $("#pod_primjene").select2();
    $("#naz_praznik").select2();
    $("#org_jed").select2();
    $("#entity").select2();
    $("#pomicni").select2();

    function insertParam2(key, value) {
        key = encodeURI(key);
        value = encodeURI(value);

        var kvp = document.location.search.substr(1).split('&');

        var i = kvp.length;
        var x;
        while (i--) {
            x = kvp[i].split('=');

            if (x[0] == key) {
                x[1] = value;
                kvp[i] = x.join('=');
                break;
            }
        }

        if (i < 0) {
            kvp[kvp.length] = [key, value].join('=');
        }

        //this will reload the page, it's likely better to store this until finished
        document.location.search = kvp.join('&');
    }

    $("#pod_primjene").on('change', function (e) {
        insertParam2('pod_primjene', this.value);
        //setTimeout(function(){ window.location.reload(); }, 1000);

    });
    $("#naz_praznik").on('change', function (e) {
        insertParam2('naz_praznik', this.value);
        //setTimeout(function(){ window.location.reload(); }, 1000);

    });
    $("#pomicni").on('change', function (e) {
        insertParam2('pomicni', this.value);
        //setTimeout(function(){ window.location.reload(); }, 1000);

    });
    $("#date").on('change', function (e) {
        insertParam2('date', this.value);
        //setTimeout(function(){ window.location.reload(); }, 1000);

    });
    $("#org_jed").on('change', function (e) {
        insertParam2('org_jed', this.value);
        //setTimeout(function(){ window.location.reload(); }, 1000);

    });

    $("#entity").on('change', function (e) {
        insertParam2('naz_entiteta', this.value);
        //setTimeout(function(){ window.location.reload(); }, 1000);

    });
</script>
<script type="text/javascript" src="theme/js/sorttable.js"></script>

</body>
</html>
