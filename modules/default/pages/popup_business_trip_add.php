<?php


require_once '../../../configuration.php';
include_once $root . '/modules/settings/functions.php';
include_once $root . '/modules/default/functions.php';
$_user = _user(_decrypt($_SESSION['SESSION_USER']));
//echo $_user['user_id'];


$user_query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE user_id='" . $_user['user_id'] . "'");

foreach ($user_query as $uquery) {

    $firstname = $uquery['fname'];
    $lastname = $uquery['lname'];
    $position = $uquery['position'];
    $departmentcode = $uquery['department_code'];
    $sector = $uquery['sector'];
    $jmb = $uquery['JMB'];
    $employment_date = $uquery['employment_date'];

}

?>


<div class="container">
    <div class="header">
        <a class="btn close" style="width:20px;height:25px;" data-widget="close-ajax" data-id="opt2"><i
                    class="ion-android-close" style="color:white;"></i></a>
        <h3 style="text-align:center;" class="form-control-tran">
            <span><?php echo __('ZAHTJEV ZA SLUŽBENO PUTOVANJE'); ?> </span></h3>
    </div>
</div>
<br>
<section>
    <div class="container" style="box-shadow: 0px 0px 79px -5px rgba(0,0,0,0.75);padding-top:20px;">

        <div id="res"></div>

        <form action="popup_form" id="popup_form" method="post">

            <input type="hidden" name="request" value="business-trip-request-add"/>


            <label class="lable-trip" style="font-weight:bold; "><?php echo __('Ime i prezime:'); ?></label>
            <textarea name="fnamelname" class="form-control-trip"
                      style="height:35px!important;padding:5px;text-align:center;"
                      readonly><?php echo $firstname . ' ' . $lastname; ?></textarea>

            <label class="lable-trip" style="font-weight:bold;"><?php echo __('Zvanje / Položaj:'); ?></label>
            <textarea name="position" class="form-control-trip"
                      style="height:35px!important;padding:5px;text-align:center;"
                      readonly><?php echo $position ?></textarea>

            <label class="lable-trip" style="font-weight:bold;"><?php echo __('JMBG:'); ?></label>
            <textarea name="jmbg" class="form-control-trip" style="height:35px!important;padding:5px;text-align:center;"
                      readonly><?php echo $jmb ?></textarea>

            <div class="form-row">
                <label class="lable-trip" style="font-weight:bold;"><?php echo __('Destinacija:'); ?></label>
                <select name="countryino" id="countryino" class="form-control-trip"
                        style="height:35px!important;width:452px;float:left;padding:5px;text-align:center;background-color:white;"
                        required>
                    <?php echo _optionCountry(0); ?>
                </select>


                <textarea name="destination" class="form-control-trip" placeholder="Grad"
                          style="height:35px!important;width:452px;padding:5px;background-color:white;"
                          required></textarea>
            </div>


            <label class="lable-trip" style="font-weight:bold;"><?php echo __('Svrha putovanja:'); ?></label>
            <textarea name="purpose_trip" class="form-control-trip"
                      style="height:35px!important;padding:5px;background-color:white;" required></textarea>

            <div style="display:block;width:1300px;height:30px;">
                <label class="lable-trip"
                       style="font-weight:bold;width:570px;"><?php echo __('Datum boravka na putu (od-do):'); ?></label>


                <div class="input-group input-daterange" style="float:left; width:285px;display:block;">
                    <input type="text" name="DateFrom" class="form-control" placeholder="OD"
                           style="height:35px!important;padding:5px;text-align:center;background-color:white;" title="*"
                           required>
                </div>

                <div class="input-group input-daterange" style="float:left; width:285px;display:block;">
                    <input type="text" name="DateTo" class="form-control" placeholder="DO"
                           style="height:35px!important;padding:5px;text-align:center;background-color:white;" title="*"
                           required>
                </div>

            </div>

            <div style="style:1px solid black;display:block;width:1400px;">
                <label class="lable-trip2" type="hidden" style="font-weight:bold;width:570px;">Vrijeme:</label>
                <input class="form-control-trip-white" name="TimeFrom"
                       style="height:35px!important;padding:5px;width:285px;float:left;" placeholder="00:00 AM"
                       type="time" title="*" required></input>
                <input class="form-control-trip-white" name="TimeTo"
                       style="height:35px!important;padding:5px;width:285px;float:left;" placeholder="00:00 AM"
                       type="time" title="*" required></input>
            </div>
            <br>


            <label class="lable-trip2" style="font-weight:bold;">Detalji vezani za sastanak/seminar (i sl.):</label>
            <br>
            <textarea class="form-control-trip-white" name="reasons"
                      style="height:35px!important;padding:5px;margin:0px;"></textarea>

            <label class="lable-trip2" style="font-weight:bold;">Vrijeme zakazano za održavanje
                sastanaka/seminara:</label>
            <input class="form-control-trip-white" name="TimeOfSeminar" style="height:35px!important;padding:5px;"
                   placeholder="00:00" type="time" pattern="(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}"
                   title="Format datuma mora biti 00:00" required></input>


            <label class="lable-trip2"
                   style="font-weight:bold;"><?php echo __('Datum kada je utvrđena potreba za sl.put:'); ?></label>
            <div class="input-group input-daterange" style="float:right; width:570px;display:block;height:40px;">
                <input type="text" name="DateDetermined" class="form-control"
                       style="height:35px!important;padding:5px;text-align:center;background-color:#fff;" title="*"
                       required><br><br>
            </div>

            <div>
                <label class="lable-trip2" style="font-weight:bold;">Da li je provjerena mogućnost učešća na
                    sastanku/seminaru putem video konekcije?</label>
                <input type="radio" class="form-control-cbox" style="margin:0px;" name="check_list1" value="DA"
                       style="float:left;">DA</input>
                <input type="radio" class="form-control-cbox" style="margin:0px;" name="check_list1" value="NE"
                       style="float:left;">NE</input>
                <input type="radio" class="form-control-cbox" name="check_list1" value="Ništa" checked="checked"
                       required>Ništa od navedenog</input><br>
            </div>
            <br>


            <label class="lable-trip2" style="font-weight:bold;">Da li postoji mogućnost da se promjeni termin?</label>
            <input type="radio" class="form-control-cbox" style="margin:0px;" name="check_list2" value="DA">DA</input>
            <input type="radio" class="form-control-cbox" style="margin:0px;" name="check_list2" value="NE">NE</input>
            <input type="radio" class="form-control-cbox" name="check_list2" value="Ništa" checked="checked" required>Ništa
            od navedenog</input><br>


            <br>
            <label class="lable-trip2" style="font-weight:bold;">Prevozno sredstvo:</label>

            <input list="transport" name="transport" placeholder="Odaberi..." class="form-control-trip-white"
                   style="height:35px!important;padding:5px;text-align:center;background-color:white;" title="*"
                   required>
            <datalist id="transport">
                <option value="Avion">
                <option value="Službeno auto">
                <option value="Privatno auto">
                <option value="Autobus">
                <option value="Brod">
                <option value="Ostalo">
            </datalist>


            <label class="lable-trip2" style="font-weight:bold;">Detalji vezani za prevozno sredstvo:</label>
            <input class="form-control-trip-white" name="transport_details"
                   style="height:35px!important;padding:5px;"></input>


            <label class="lable-trip2" style="font-weight:bold;">Broj zaposlenika koji trebaju da učestvuju na
                sastanku/seminaru:</label>
            <input class="form-control-trip-white" type="number" name="numberOfEmployee"
                   style="height:35px!important;padding:5px;"></input>

            <br>
            <label class="lable-trip2" style="font-weight:bold;">Da li je isključena mogućnost putovanja sl.
                automobilom?</label>
            <input type="radio" class="form-control-cbox" name="check_list3" value="DA" required>DA</input>
            <input type="radio" class="form-control-cbox" name="check_list3" value="NE" required>NE</input>
            <input type="radio" class="form-control-cbox" name="check_list3" value="Ništa" checked="checked" required>Ništa
            od navedenog</input><br>


            <br>

            <label class="lable-trip2" style="font-weight:bold;">Napomena vezana za odabir i troškove prevoznog
                sredstva:</label>
            <input class="form-control-trip-white" name="transport_notes"
                   style="height:35px!important;padding:5px;"></input>

            <label class="lable-trip2" style="font-weight:bold;">Smještaj:</label>


            <input list="atribut" name="atribut" placeholder="Odaberi..." class="form-control-trip-white"
                   style="height:35px!important;padding:5px;text-align:center;background-color:white;" title="*"
                   required>
            <datalist id="atribut">
                <option value="Privatni smještaj">
                <option value="Hotel">

            </datalist>

            <label class="lable-trip2" style="font-weight:bold;">Detalji vezani za smještaj:</label>
            <input class="form-control-trip-white" name="accommodation_details"
                   style="height:35px!important;padding:5px;" title="*" placeholder="00.00 BAM" required></input>

            <br>
            <label class="lable-trip2" style="font-weight:bold;">Da li je predviđen individualni smještaj ili je isti
                vezan za grupaciju?</label>
            <input type="radio" class="form-control-cbox" name="check_list4" value="Individualni"
                   required>Individualni</input>
            <input type="radio" class="form-control-cbox" name="check_list4" value="Grupacija"
                   required>Grupacija</input>
            <input type="radio" class="form-control-cbox" name="check_list4" value="Ništa" checked="checked" required>Ništa
            od navedenog</input><br>


            <br>
            <label class="lable-trip2" style="font-weight:bold;">Da li je smještaj u hotelu sa kojim Grupacija ima
                potpisan ugovor?</label>
            <input type="radio" class="form-control-cbox" name="check_list5" value="DA" required>DA</input>
            <input type="radio" class="form-control-cbox" name="check_list5" value="NE" required>NE</input>
            <input type="radio" class="form-control-cbox" name="check_list5" value="Ništa" checked="checked" required>Ništa
            od navedenog</input><br>


            <br>
            <label class="lable-trip2" style="font-weight:bold;">Napomena vezana za odabir troškova smještaja i
                prekoračenje limita:</label>
            <input class="form-control-trip-white" name="limit_notes"
                   style="height:35px!important;padding:5px;"></input>

            <br>
            <label class="lable-trip2" style="font-weight:bold;">Da li se zahtjeva akontacija?</label>
            <input type="radio" class="form-control-cbox" name="check_list6" value="DA" required>DA</input>
            <input type="radio" class="form-control-cbox" name="check_list6" value="NE" required>NE</input>
            <input type="radio" class="form-control-cbox" name="check_list6" value="Ništa" checked="checked" required>Ništa
            od navedenog</input><br>

            <br>


            <label class="form-control-tran"
                   style=" width:1140px; float:left; margin-bottom:20px;font-weight:bold;"><?php echo __('Obračun akontacije'); ?></label>

            <content style="width:400px; padding:5px;height:680px;float:left;">

                <div style="width:390px; height:150px; padding:3px; float:left;">
                    <label class="lable-trip2" style="font-weight:bold;"><h4 style="margin:0px;"> I DNEVNICE </h4>
                    </label>
                </div>


                <div style="width:390px; height:157px; padding:3px; float:left;">
                    <label class="lable-trip2"><h4 style="margin:0px;font-weight:bold;"> II SMJEŠTAJ </h4></label>
                </div>


                <div style="width:390px; height:157px; padding:3px; float:left;">
                    <label class="lable-trip2"><h4 style="margin:0px;font-weight:bold;"> III IZDACI ZA TROŠKOVE
                            PRIJEVOZA </h4></label>
                </div>


                <div style="width:390px; height:157px; padding:3px; float:left;">
                    <label class="lable-trip2"><h4 style="margin:0px;font-weight:bold;"> IV OSTALI TROŠKOVI </h4>
                    </label>
                </div>


                <div style="width:390px; height:50px; padding:3px; float:left;">
                    <label class="lable-trip2"><h4 style="margin:0px;font-weight:bold;padding:3px;"> UKUPNO I, II, III,
                            IV </h4></label>
                </div>
            </content>


            <!--  //********************DNEVNICE************************************************************************// -->

            <content style="width:740px; padding:5px;float:left;background: #008000;">


                <a href="#" id="add1" rel="clone"
                   style="color:White;float:left;font-weight:bold;"><?php echo __('Dodaj polje'); ?> <i
                            class="ion-plus"></i></a>

                <div style="width:150px; text-align:center; margin-left:60px; display:block;float:left;">
                    <label style="border:1px solid #DCDCDC; width:150px;margin:0px;font-weight:bold;background:White;">
                        Država </label>
                </div>

                <div style="width:100px; text-align:center; margin-left:10px; display:block;float:left">
                    <label style="border:1px solid #DCDCDC; width:100px;margin:0px;font-weight:bold;background:White;">
                        Broj </label>
                </div>

                <div style="width:150px; text-align:center; margin-left:10px; display:block;float:left;">
                    <label style="border:1px solid #DCDCDC; width:150px;margin:0px;font-weight:bold;background:White;">
                        Iznos </label>
                </div>

                <div style="width:150px;  text-align:center; margin-left:10px; display:block;float:left;">
                    <label style="border:1px solid #DCDCDC; width:150px;margin:0px;font-weight:bold;background:White;">
                        UKUPNO </label>
                </div>
            </content>


            <div style="width:740px; height:123px; text-align:center;display:block;float:left;">
                <div id="clone">

                    <select name="country[]" id="drzava"
                            style="border:1px solid #DCDCDC; width:150px; margin-left:145px; float:left;">
                        <?php echo _optionCountry(0); ?>
                    </select>

                    <input name="task[]" id="the_number_id"
                           style="border:1px solid #DCDCDC; width:100px; float:left;margin-left:10px;">

                    <select name="amount[]" id="the_amount_id"
                            style="border:1px solid #DCDCDC; width:150px; margin-left:10px; float:left;">
                        <?php echo _optionCountryWage(0); ?>
                    </select>

                    <input type="number" name="task4[]" id="the_input_id"
                           style="border:1px solid #DCDCDC; width:150px; margin-left:10px; float:left;"><br/>

                </div>

            </div>

            <!--  //********************SMJEŠTAJ************************************************************************// -->

            <content style="width:740px; padding:5px;float:left;background: #008000;">
                <!--  <a href="#" id="add2" rel="clone2" style="color:White;float:left;font-weight:bold;" ><?php echo __('Dodaj polje'); ?> <i class="ion-plus"></i></a> -->
                <div style="width:100px; text-align:center; margin-left:300px; display:block;float:left">
                    <label style="border:1px solid #DCDCDC; width:100px;margin:0px;font-weight:bold;background:White;">
                        Broj </label>
                </div>


                <div style="width:150px; text-align:center; margin-left:10px; display:block;float:left;">
                    <label style="border:1px solid #DCDCDC; width:150px;margin:0px;font-weight:bold;background:White;">
                        Iznos </label>
                </div>

                <div style="width:150px;  text-align:center; margin-left:10px; display:block;float:left;">
                    <label style="border:1px solid #DCDCDC; width:150px;margin:0px;font-weight:bold;background:White;">
                        UKUPNO </label>
                </div>
            </content>

            <div style="width:740px; height:123px; text-align:center;display:block;float:left;">
                <div id="clone2">
                    <input type="number" name="accommodation1[]" id="the_number_id1"
                           style="border:1px solid #DCDCDC; width:100px; float:left;margin-left:305px;">
                    <input type="number" name="accommodation2[]" id="the_amount_id1"
                           style="border:1px solid #DCDCDC; width:150px; margin-left:10px; float:left;">
                    <input type="number" name="accommodation4[]" id="the_input_id1"
                           style="border:1px solid #DCDCDC; width:150px; margin-left:10px; float:left;"><br/>
                </div>

            </div>

            <!--  //********************TROŠKOVI PRIJEVOZA************************************************************************// -->

            <content style="width:740px; padding:5px;float:left;background: #008000;">
                <!--  <a href="#" id="add3" rel="clone3" style="color:White;float:left;font-weight:bold;" ><?php echo __('Dodaj polje'); ?> <i class="ion-plus"></i></a> -->
                <div style="width:100px; text-align:center; margin-left:300px; display:block;float:left">
                    <label style="border:1px solid #DCDCDC; width:100px;margin:0px;font-weight:bold;background:White;">
                        Broj </label>
                </div>


                <div style="width:150px; text-align:center; margin-left:10px; display:block;float:left;">
                    <label style="border:1px solid #DCDCDC; width:150px;margin:0px;font-weight:bold;background:White;">
                        Iznos </label>
                </div>

                <div style="width:150px;  text-align:center; margin-left:10px; display:block;float:left;">
                    <label style="border:1px solid #DCDCDC; width:150px;margin:0px;font-weight:bold;background:White;">
                        UKUPNO </label>
                </div>
            </content>

            <div style="width:740px; height:123px; text-align:center;display:block;float:left;">
                <div id="clone3">
                    <input type="number" name="transport1[]" id="the_number_id2"
                           style="border:1px solid #DCDCDC; width:100px; float:left;margin-left:305px;">
                    <input type="number" name="transport2[]" id="the_amount_id2"
                           style="border:1px solid #DCDCDC; width:150px; margin-left:10px; float:left;">
                    <input type="number" name="transport4[]" id="the_input_id2"
                           style="border:1px solid #DCDCDC; width:150px; margin-left:10px; float:left;"><br/>
                </div>

            </div>

            <!--  //********************OSTALI TROŠKOVI************************************************************************// -->

            <content style="width:740px; padding:5px;float:left;background: #008000;">
                <!-- <a href="#" id="add4" rel="clone4" style="color:White;float:left;font-weight:bold;" ><?php echo __('Dodaj polje'); ?> <i class="ion-plus"></i></a> -->
                <div style="width:100px; text-align:center; margin-left:300px; display:block;float:left">
                    <label style="border:1px solid #DCDCDC; width:100px;margin:0px;font-weight:bold;background:White;">
                        Broj </label>
                </div>


                <div style="width:150px; text-align:center; margin-left:10px; display:block;float:left;">
                    <label style="border:1px solid #DCDCDC; width:150px;margin:0px;font-weight:bold;background:White;">
                        Iznos </label>
                </div>

                <div style="width:150px;  text-align:center; margin-left:10px; display:block;float:left;">
                    <label style="border:1px solid #DCDCDC; width:150px;margin:0px;font-weight:bold;background:White;">
                        UKUPNO </label>
                </div>
            </content>

            <div style="width:740px; height:123px; text-align:center;display:block;float:left;">
                <div id="clone4">
                    <input type="number" name="otherCosts1[]" id="the_number_id3"
                           style="border:1px solid #DCDCDC; width:100px; float:left;margin-left:305px;">
                    <input type="number" name="otherCosts2[]" id="the_amount_id3"
                           style="border:1px solid #DCDCDC; width:150px; margin-left:10px; float:left;">
                    <input type="number" name="otherCosts4[]" id="the_input_id3"
                           style="border:1px solid #DCDCDC; width:150px; margin-left:10px; float:left;"><br/>
                </div>

            </div>


            <div style="width:100px;text-align:center; margin-left:310px; display:block;float:left;">
                <textarea name="number_f" type="number" id="number_total"
                          style="border:1px solid #DCDCDC; text-align:center; width:98px; height:30px; margin:0px;box-shadow: 0px 0px 5px 3px rgba(0,0,0,0.75);"></textarea>
            </div>


            <div style="width:150px;text-align:center; margin-left:10px; display:block;float:left;">
                <textarea name="amount_f" id="amount_total" type="number"
                          style="border:1px solid #DCDCDC; text-align:center; width:148px;height:30px;margin:0px;box-shadow: 0px 0px 5px 3px rgba(0,0,0,0.75);"></textarea>
            </div>

            <div style="width:150px; text-align:center; margin-left:10px; display:block;float:left;">
                <textarea name="total_f" id="total" type="number"
                          style="border:1px solid #DCDCDC;text-align:center; width:148px;height:30px; margin:0px;box-shadow: 0px 0px 5px 3px rgba(0,0,0,0.75);"></textarea>
            </div>


            <br>
            <br>
            <label class="lable-trip"
                   style="width:900px; margin-top:20px;font-weight:bold;text-align:right;"><?php echo __('Datum obračuna:'); ?></label>
            <div class="input-group input-daterange" style="float:right; width:200px;display:block;">
                <input type="text" name="DateOfCalculation" class="form-control-trip-white"
                       style="height:35px!important;padding:5px;text-align:center;margin-top:20px;width:200px;float:left;"
                       title="*" required><br>
            </div>


            <br>

            <div style="width:1100px; display:block; float:right; ">
                <button type="submit" id="btnSubmit"
                        style="margin-top:20px; margin-bottom:20px;width:200px;margin-left:30px;"
                        class="btn btn-red pull-right"><?php echo __('Uredu'); ?> <i
                            class="ion-ios-download-outline"></i></button>


            </div>


        </form>


        <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>

        <!-- Bootstrap -->
        <script src="<?php echo $_jsUrl; ?>/bootstrap.min.js"></script>

        <!-- Bootstrap datepicker -->
        <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

        <!-- jQuery confirm -->
        <script src="<?php echo $_pluginUrl; ?>/jquery-confirm/jquery-confirm.min.js"></script>

        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>


        <script>


            $(function () {
                var today = new Date();
                var startDate = new Date();
                $('.input-daterange').datepicker({
                    todayBtn: "linked",
                    format: 'dd/mm/yyyy'
                });
                $(document).ready(function () {
                    $('.dialog-loader').hide();
                });
            });
            $("#popup_form").validate({
                focusCleanup: true,
                submitHandler: function (form) {
                    $('.dialog-loader').show();
                    $(form).ajaxSubmit({
                        url: "<?php echo $url . '/modules/default/ajax.php'; ?>",
                        type: "post",
                        success: function (data) {
                            $("#popup_form")[0].reset();
                            $("#res").html(data);
                            $('.dialog-loader').hide();
                        }
                    });
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


            /*************SUMA UKUPNO******************************/


            $('body').on('keyup', 'input:regex(id, .*the_input_id.*)', function () {
                console.log('irma');
                var input1 = parseFloat($(this).val());
                input1 = Math.round(input1 * 100) / 100;
                updateTotal1(input1);

            });

            var updateTotal1 = function (input) {
                var total = 0;
                $('input:regex(id, .*the_input_id.*)').each(function (index) {
                    console.log('denis', parseFloat($(this).val()));
                    total = total + (parseFloat($(this).val()) || 0);
                    total = Math.round(total * 100) / 100;


                });


                $('#total').text(total);

            };

            $('#total').text(0);


            /*************SUMA IZNOS******************************/


            $('body').on('keyup', 'input:regex(id, .*the_amount_id.*)', function () {
                console.log('irma');
                var input2 = parseFloat($(this).val());
                input2 = Math.round(input2 * 100) / 100;
                updateTotal2(input2);

            });


            var updateTotal2 = function (input) {
                var amount = 0;
                $('input:regex(id, .*the_amount_id.*)').each(function (index) {
                    console.log('denis', parseFloat($(this).val()));
                    amount = amount + (parseFloat($(this).val()) || 0);
                });

                $('select:regex(id, .*the_amount_id.*)').each(function (index) {
                    console.log('denis', parseFloat($(this).val()));
                    amount = amount + (parseFloat($(this).val()) || 0);
                    amount = Math.round(amount * 100) / 100;
                });


                $('#amount_total').text(amount);

            };

            $('#amount_total').text(0);


            /*************SUMA BROJ******************************/


            $('body').on('keyup', 'input:regex(id, .*the_number_id.*)', function () {
                //updateTotal();
                console.log('irma');
                var input2 = parseFloat($(this).val());
                input2 = Math.round(input2 * 100) / 100;
                updateTotal3(input2);

            });

            var updateTotal3 = function (input) {
                var number = 0;
                $('input:regex(id, .*the_number_id.*)').each(function (index) {
                    console.log('denis', parseFloat($(this).val()));
                    number = number + (parseFloat($(this).val()) || 0);
                    number = Math.round(number * 100) / 100;
                });


                $('#number_total').text(number);

            };

            $('#number_total').text(0);


            /*************CALC ROWS******************************/


            $(document).ready(function () {
                //this calculates values automatically
                // sum();
                $('body').on('keydown keyup', 'input:regex(id, .*the_number_id.*)', function () {

                    var input2 = parseFloat($(this).val());


                    var parent = $(this).parent();
                    var denis1 = parent.find('input:regex(id, .*the_amount_id.*)');
                    var input3 = parseFloat(denis1.val());
                    var denis = parent.find('input:regex(id, .*the_input_id.*)');
                    denis.val(input2 * input3);

                    updateTotal1(denis.val());

                    //sum();
                });
                $('body').on('keydown keyup', 'input:regex(id, .*the_amount_id.*)', function () {
                    var input2 = parseFloat($(this).val());
                    var parent = $(this).parent();
                    var denis1 = parent.find('input:regex(id, .*the_number_id.*)');
                    var input3 = parseFloat(denis1.val());
                    var denis = parent.find('input:regex(id, .*the_input_id.*)');
                    denis.val(input2 * input3);

                    updateTotal1(denis.val());
                });

                $('body').on('change', 'select:regex(id, .*the_amount_id.*)', function () {
                    var input2 = parseFloat($(this).val());
                    var parent = $(this).parent();
                    var denis1 = parent.find('input:regex(id, .*the_number_id.*)');
                    var input3 = parseFloat(denis1.val());
                    var denis = parent.find('input:regex(id, .*the_input_id.*)');
                    denis.val(input2 * input3);

                    updateTotal1(denis.val());
                });

                $('body').on('keydown keyup', 'input:regex(id, .*the_number_id.*)', function () {

                    var input2 = parseFloat($(this).val());
                    var parent = $(this).parent();
                    var denis1 = parent.find('select:regex(id, .*the_amount_id.*)');
                    var input3 = parseFloat(denis1.val());
                    var denis = parent.find('input:regex(id, .*the_input_id.*)');
                    denis.val(input2 * input3);

                    updateTotal1(denis.val());

                    //sum();
                });

            });

            function sum() {
                var num1 = document.getElementById('the_amount_id1').value;

                var num2 = document.getElementById('the_number_id1').value;

                var result = parseFloat(num1) + parseInt(num2);

                var result1 = parseFloat(num2) - parseInt(num1);

                if (!isNaN(result)) {
                    document.getElementById('the_input_id1').value = result;

                }
            }


            /*********************************SELECT COUNTRY*************************************/

            $('body').on('change', 'select:regex(id, .*drzava.*)', function () {

                var parent = $(this).parent();
                var denis = parent.find('#the_amount_id');

                console.log(denis, 'the_amount_id');

                $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                        request: "get-tariff",
                        region: this.value
                    },
                    function (returnedData) {
                        denis.html(returnedData);
                        var input2 = parseInt(denis.val());
                        updateTotal2(input2);


                    });
            })


            $('body').on('change', 'select:regex(id, .*drzava_ino.*)', function () {

                var parent = $(this).parent();
                var denis = parent.find('#the_amount_id');

                console.log(denis, 'the_amount_id');

                $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                        request: "get-tariff",
                        region: this.value
                    },
                    function (returnedData) {
                        denis.html(returnedData);
                        var input2 = parseInt(denis.val());
                        updateTotal2(input2);


                    });
            })


            /*******************Count***************************/
            var clicks = 0;
            $('body').on('click', '#add1', function () {


                var click_id = $(this).attr('rel');
                if (clicks < 4)
                    $('#' + click_id).clone(true).insertAfter('#' + click_id).find("input:text").val("").attr('required', false).end().fadeIn();
                else {
                    $.alert({
                        title: 'Upozorenje!',
                        content: 'Broj polja je ograničen!',
                        type: 'red',
                        icon: 'fa fa-warning',
                    });
                }
                clicks++;
            });

            /*******************Count1***************************/
            var clicks1 = 0;
            $('body').on('click', '#add2', function () {


                var click_id = $(this).attr('rel');
                if (clicks1 < 4)
                    $('#' + click_id).clone(true).insertAfter('#' + click_id).find("input:text").val("").attr('required', false).end().fadeIn();
                else {
                    $.alert({
                        title: 'Upozorenje!',
                        content: 'Broj polja je ograničen!',
                        type: 'red',
                        icon: 'fa fa-warning',
                    });
                }
                clicks1++;
            });

            /*******************Count2***************************/
            var clicks2 = 0;
            $('body').on('click', '#add3', function () {


                var click_id = $(this).attr('rel');
                if (clicks2 < 4)
                    $('#' + click_id).clone(true).insertAfter('#' + click_id).find("input:text").val("").attr('required', false).end().fadeIn();
                else {
                    $.alert({
                        title: 'Upozorenje!',
                        content: 'Broj polja je ograničen!',
                        type: 'red',
                        icon: 'fa fa-warning',
                    });
                }
                clicks2++;
            });

            /*******************Count3***************************/
            var clicks3 = 0;
            $('body').on('click', '#add4', function () {


                var click_id = $(this).attr('rel');
                if (clicks3 < 4)
                    $('#' + click_id).clone(true).insertAfter('#' + click_id).find("input:text").val("").attr('required', false).end().fadeIn();
                else {
                    $.alert({
                        title: 'Upozorenje!',
                        content: 'Broj polja je ograničen!',
                        type: 'red',
                        icon: 'fa fa-warning',
                    });
                }
                clicks3++;
            });


        </script>


    </div>
    <div class="dialog-loader"><i></i></div>
</section>
