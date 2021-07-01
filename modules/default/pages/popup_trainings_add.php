<?php


require_once '../../../configuration.php';
include_once $root . '/modules/settings/functions.php';
$_user = _user(_decrypt($_SESSION['SESSION_USER']));


//echo $_user['user_id'];


$user_query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE user_id='" . $_user['user_id'] . "'");

foreach ($user_query as $uquery) {

    $firstname = $uquery['fname'];
    $lastname = $uquery['lname'];
    $position = $uquery['position'];
    $departmentcode = $uquery['department_code'];
    $sector = $uquery['sector'];

    $employment_date = $uquery['employment_date'];


}

?>


<div class="container">
    <div class="header">
        <a class="btn close" style="width:20px;height:25px;" data-widget="close-ajax" data-id="opt2"><i
                    class="ion-android-close"></i></a>
        <h3><span><?php echo __('Obrazac za prijavu na eksterni seminar'); ?> </span></h3>
        <h5>
            <span><?php echo _('(Popunjava radnik koji podnosi prijavu. Prijava se može izvršiti nakon što je zahtjev odobren. Napomena: ako se radi o grupnoj obuci, popunjava se samo jedan obrazac)'); ?> </span>
        </h5>
    </div>
</div>
<section>
    <div class="container" style="box-shadow: 0px 0px 79px -5px rgba(0,0,0,0.75);padding-top:20px;">

        <div id="res"></div>

        <form id="popup_form" method="post">

            <input type="hidden" name="request" value="trainings-request-add"/>
            <label class="form-control-tran"><?php echo __('Podaci o radniku:'); ?></label>
            <div style="display:block;width:1250px;">

                <div style="float:left; width:440px;">
                    <label style="float:left;vertical-align:center;font-weight:bold;"><?php echo __('Ime i prezime:'); ?></label>
                    <textarea name="fnamelname" class="form-control"
                              style="width:430px;height:35px!important;padding:5px;resize: none;"
                              readonly><?php echo $firstname . ' ' . $lastname; ?></textarea>
                </div>

                <div style="float:left; width:300px;display:block;">
                    <label style="font-weight:bold;"><?php echo __('Datum početka (SBBH): (dd/mm/gg) '); ?></label>
                    <div class="input-group input-daterange">
                        <textarea name="empdate" class="form-control"
                                  style="height:35px!important;padding:5px;width:290px;resize: none;"
                                  readonly><?php echo date('d/m/Y', strtotime($employment_date)); ?></textarea>

                    </div>
                </div>

                <div style="float:left; width:400px;display:block;">
                    <label style="float:left;vertical-align:center;font-weight:bold;"><?php echo __('ID OJ:'); ?></label>
                    <textarea name="idoj" class="form-control" style="height:35px!important;padding:5px;resize: none;"
                              readonly><?php echo $departmentcode ?></textarea>
                </div>
            </div>

            <div style="display:block;width:1250px;">

                <div style="float:left; width:740px;">
                    <label style="float:left;vertical-align:center;font-weight:bold;"><?php echo __('Naziv radnog mjesta:'); ?></label>
                    <textarea name="position" class="form-control"
                              style="height:35px!important;padding:5px;width:730px;resize: none;"
                              readonly><?php echo $position ?></textarea>
                </div>

                <div style="float:left; width:400px; display:block;">
                    <label style="float:left;vertical-align:center;font-weight:bold;"><?php echo __('Naziv OJ:'); ?></label>
                    <textarea name="oj" class="form-control" style="height:35px!important;padding:5px;resize: none;"
                              readonly><?php echo $sector ?></textarea>
                </div>
            </div>
            </br>
            </br>
            </br>
            </br>
            </br>
            </br>
            </br>


            <label class="form-control-tran"><?php echo __('Podaci o usavršavanju:'); ?></label>


            <div style="float:left; width:200px; height:140px;display:block;border:1px solid gray;padding:2px;">
                <label style="font-weight:bold;"><?php echo __('Vrsta usavršavanja:'); ?></label> <br/>

                <input type="radio" class="form-control-cbox" name="check_list" value="Obavezno" title="Obavezno polje"
                       title="Obavezno polje" required>Obavezno </input> <br>
                <input type="radio" class="form-control-cbox" name="check_list" value="Funkcionalne vještine"
                       title="Obavezno polje" required>Funkcionalne vještine </input><br>
                <input type="radio" class="form-control-cbox" name="check_list" value="IT usavršavanje"
                       title="Obavezno polje" required> IT usavršavanje </input> <br>

            </div>

            <div style="float:left; width:300px; height:140px;border:1px solid gray;padding:2px;">

                <input type="radio" class="form-control-cbox" name="check_list" value="Konferencija"
                       title="Obavezno polje" required> Konferencija </input> <br>
                <input type="radio" class="form-control-cbox" name="check_list" value="Seminar" title="Obavezno polje"
                       required> Seminar </input><br>
                <input type="radio" class="form-control-cbox" name="check_list" value="Rukovodstvene vještine"
                       title="Obavezno polje" required> Rukovodstvene vještine </input> <br>
                <input type="radio" class="form-control-cbox" name="check_list" value="Ostalo" title="Obavezno polje"
                       required> Ostalo </input><br><textarea type="text" name="ostalo"
                                                              style="width:220px;height:30px!important;resize: none;margin-left:55px;"></textarea>
                <br>
            </div>

            <div style="float:left; width:600px; height:170px; display:block; ">
                <label style="float:left;vertical-align:center;padding-left:5px;font-weight:bold;">Razlozi za pohađanje
                    usavršavanja (zašto trebate pohađati to eksterno usavršavanje):</label> <br>
                <textarea class="form-control" maxlength="250" title="Obavezno polje / Max 100 karaktera" name="reasons"
                          style="width:640px;height:35px!important;padding:5px;resize: none;" required></textarea><br/>

                <label style="float:left;vertical-align:center;padding-left:5px;font-weight:bold;">Očekivani ishod
                    usavršavanja (šta očekujete da se unaprijedi/promijeni nakon usavršavanja):</label> <br>
                <textarea class="form-control" maxlength="250" title="Obavezno polje / Max 100 karaktera" name="outcome"
                          style="width:640px;height:35px!important;padding:5px;resize: none;" required></textarea><br/>

            </div>

            <div>
                <label style="float:left;vertical-align:center;font-weight:bold;"><?php echo __('Naziv seminara:'); ?></label>
                <textarea name="nameofseminar" class="form-control"
                          style="height:35px!important;padding:5px;resize: none;" maxlength="100"
                          title="Obavezno polje / Max 100 karaktera" required></textarea><br/>
            </div>
            <label style="float:left;vertical-align:center;font-weight:bold;"><?php echo __('Naziv certifikata, kvalifikacije, itd. koju stičete nakon usavršavanja (ako je bitno):'); ?></label>
            <textarea name="nameofcertif" class="form-control" style="height:35px!important;padding:5px;resize: none;"
                      maxlength="100" title="Max 100 karaktera"></textarea><br/>


            <div style="float:left; width:290px; display:block;">
                <label style="font-weight:bold;">Troškovi kotizacije sa PDV (BAM valuta): </label><br/>
                <textarea name="costsPDV" type="number" maxlength="10" id="the_input_id" class="form-control"
                          style="height:35px!important;width:280px;padding:5px;resize: none;"
                          placeholder="Ukoliko nema troška, molimo unesite 0.00." title="Obavezno polje"
                          required></textarea><br/>
            </div>


            <div style="float:left; width:580px; display:block;">
                <label style="font-weight:bold;"><?php echo __('Datum početka usavršavanja:'); ?></label>
                <div class="input-group input-daterange">
                    <input type="text" name="DateFrom" class="form-control"
                           style="height:35px!important;width:570px;padding:5px;" title="Obavezno polje" required>
                </div>
            </div>

            <div style="float:left; width:270px; display:block;">
                <label style="font-weight:bold;"><?php echo __('Datum završetka usavršavanja:'); ?></label>
                <div class="input-group input-daterange">
                    <input type="text" name="DateTo" class="form-control"
                           style="height:35px!important;width:270px;padding:5px;" title="Obavezno polje" required>
                </div>
            </div>


            <div style="display:block;margin-bottom:20px;float:left;">
                <div style="width:290px;float:left;">
                    <label style="font-weight:bold;"> Dnevnica: </label>
                    <textarea name="wage" class="form-control" maxlength="10" type="number" id="the_input_id1"
                              style="height:35px!important;width:280px;padding:5px;resize: none;" title="Obavezno polje"
                              required></textarea><br/>
                </div>

                <div style="float:left; width:580px;">
                    <label style="font-weight:bold;"> Smještaj: </label><br/>
                    <textarea name="accommodation" class="form-control" maxlength="10" id="the_input_id2" type="number"
                              style="height:35px!important;width:570px;padding:5px;resize: none;" title="Obavezno polje"
                              required></textarea><br/>
                </div>


                <div style=" width:270px; float:left;">
                    <label style="font-weight:bold;">Prevoz: </label><br/>
                    <textarea name="transport" class="form-control" maxlength="10" id="the_input_id3" type="number"
                              style="height:35px!important;width:270px;padding:5px;resize: none;" title="Obavezno polje"
                              required></textarea>

                </div>


                <div style="width:270px; display:block;float:right;">
                    <label style="font-weight:bold;">Total troškovi cca: </label><br/>
                    <textarea name="totalcosts" type="number" id="total" maxlength="10" class="form-control"
                              style="height:35px!important;width:270px;padding:5px;resize: none;" title="Obavezno polje"
                              required></textarea></br>

                </div>
            </div>
            </br>


            <div style="width:650px; display:block;float:left;">
                <label style="vertical-align:center;font-weight:bold;">Organizator:</label>
                <textarea name="organizer" class="form-control"
                          style="height:35px!important;width:640px;padding:5px;resize: none;"
                          title="Obavezno polje / Max 100 karaktera" maxlength="100" required></textarea>
            </div>

            <div style=" width:490px; float:left;">
                <label style="display:block;font-weight:bold;"><?php echo __('Mjesto održavanja usavršavanja: (grad, država)'); ?></label>
                <select name="country" class="form-control" style="height:35px!important;width:485px;padding:5px;"
                        title="Obavezno polje" required>
                    <?php echo _optionCountry($row['country']); ?>
                </select>

            </div>

            <div style=" width:1140px; float:left;">

                <label style="vertical-align:center;font-weight:bold;">Napomena /Zahtjev za izuzetak (npr., za radnike
                    koji su u radnom odnosu manje od šest mjeseci)</label>
                <textarea name="remark" type="text" class="form-control"
                          style="height:35px!important;padding:5px;resize: none;"
                          title="Obavezno polje / Max 100 karaktera" maxlength="100"></textarea>

            </div>
            <div style=" width:1140px; float:left;">
                <input class="form-control"
                       value="Odabirom Uredu, saglasan/a sam i elektronski potpisujem Zahtjev za trening i potvrđujem tačnost navedenih podataka."
                       style="height:35px!important;padding:5px;color:#ff0000;;" readonly></input><br/>
            </div>


            <label class="form-control-tran"
                   style=" width:1140px; float:left;"><?php echo __('Odobrenja: (potrebno popuniti prije registracije/prijave na seminar)'); ?></label>

            <div style="width:400px; height:140px; display:block;float:left;border:1px solid #DCDCDC;padding:5px;">
                <label style="font-weight:bold;">Radnik: </label>
            </div>

            <div style="float:left; width:740px; height:140px; display:block;border:1px solid #DCDCDC;padding:5px;">
                <label style="font-weight:bold;">Datum, potpis: <?php echo date('d/m/Y', strtotime('now')); ?> </label>
                <br/>
                <br/>
                <br/>
                <label style="font-weight:bold;">Ime: <?php echo $firstname . ' ' . $lastname; ?> </label>

            </div>

            <div style="float:left; width:400px; height:180px; display:block;border:1px solid #DCDCDC;padding:5px;">
                <label style="font-weight:bold;"> Nadređeni rukovodilac: </label></br></br>
                <label style="font-size:12px;"> Ukoliko je podnosilac B-1, onda član Uprave </label>
            </div>

            <div style="float:left; width:740px; height:180px; display:block;border:1px solid #DCDCDC;padding:5px;">
                <label style="font-weight:bold;font-size:12px;">Potvrđujem, nakon razgovora sa radnikom, da je
                    usavršavanje potrebno za profesionalni razvoj radnika i da su ispunjeni osnovni uslovi iz Pravila za
                    eksterno usavršavanje, osim u slučaju da postoje iznimke za koje su razlozi već navedeni:</label>
                <br/>
                <br/>
                <label style="font-weight:bold;">Datum, potpis: </label>
                <br/>
                <br/>
                <br/>
                <label style="font-weight:bold;">Ime: </label>

            </div>

            <div style="width:400px; height:180px; display:block;border:1px solid #DCDCDC;float:left;padding:5px; ">
                <label style="font-weight:bold;">Direktor Sektora za Upravljanje ljudskim resursima </label></br>
                <label style="font-weight:bold;">/ Menadžer za HR Controlling i edukaciju ljudskih resursa </label>

            </div>

            <div style="float:left; width:740px; height:180px; display:block;border:1px solid #DCDCDC;padding:5px;">
                <br/>
                <br/>
                <br/>
                <label style="font-weight:bold;">Datum, potpis: </label>
                <br/>
                <br/>
                <br/>
                <label style="font-weight:bold;">Ime: </label> </br>

            </div>
            <br> <br/>
            <br/>
            <br/>

            <div style="display:block;">
                <label style="vertical-align:center;text-align:center;font-weight:bold;font-size:12px;">Za plaćanje
                    troškova usavršavanja – za više informacija molimo obratite se Sektoru za Upravljanje ljudskim
                    resursima </label><br>
                <label style="vertical-align:center;font-weight:bold;font-size:12px;">Nakon odobrenja – molimo pošaljite
                    original Obrazac Sektoru za Upravljanje ljudskim resursima</label><br>
            </div>
            <br/>
            <div style=" width:1140px; float:left;">

                <label style="vertical-align:center;font-weight:bold;">Komentar: </label>
                <textarea name="comment" class="form-control" style="height:35px!important;padding:5px;resize: none;"
                          maxlength="100" title="Max 100 karaktera"></textarea><br/>

            </div>


            <button type="submit" style="margin-top:20px; margin-bottom:20px;width:200px;margin-left:30px;"
                    class="btn btn-red pull-right"><?php echo __('Uredu'); ?> <i class="ion-ios-download-outline"></i>
            </button>


        </form>

        <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>

        <!-- Bootstrap -->
        <script src="<?php echo $_jsUrl; ?>/bootstrap.min.js"></script>

        <!-- Bootstrap datepicker -->
        <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>


        <script>

            /*************SUMA TROŠKOVA******************************/


            $(function () {
                var today = new Date();
                var startDate = new Date();
                $('.input-daterange').datepicker({
                    todayBtn: "linked",
                    format: 'dd/mm/yyyy',
                    startDate: startDate
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


        </script>


    </div>
    <div class="dialog-loader"><i></i></div>
</section>


<script>
    $('#the_input_id').keyup(function () {
        updateTotal();
    });

    $('#the_input_id1').keyup(function () {
        updateTotal();
    });
    $('#the_input_id2').keyup(function () {
        updateTotal();
    });
    $('#the_input_id3').keyup(function () {
        updateTotal();
    });


    var updateTotal = function () {
        var input1 = parseFloat($('#the_input_id').val());
        var input2 = parseFloat($('#the_input_id1').val());
        var input3 = parseFloat($('#the_input_id2').val());
        var input4 = parseFloat($('#the_input_id3').val());
        if (isNaN(input1) || isNaN(input2) || isNaN(input3) || isNaN(input4)) {
            $('#total').text('Svi troškovi moraju biti uneseni');
        } else {
            $('#total').text(input1 + input2 + input3 + input4);
            //total = Math.round(total*100)/100;
        }
    };

</script>