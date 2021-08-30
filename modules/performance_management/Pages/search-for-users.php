<?php
_pagePermission(4, false);
include_once('modules/core/Model.php');
include_once('modules/core/VS.php');
include_once('modules/core/User.php');


?>
</div>
<!-- START - Main section -->

<style>
    .select2-container {
        box-sizing: border-box;
        display: inline-block;
        margin: 0;
        position: relative;
        vertical-align: middle;
        border-style: solid;
        border-width: 1px;
        border-color: #fff103;
    }
    .ui-datepicker-calendar {
        display: none;
    }
</style>

<div class="" style="padding-left: 15px;">
    <div class="header">
        <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
        <h4><span><?php echo __('Satnice'); ?></span></h4>
    </div>
</div>
<section>
    <div class="content clear">
        <?php
        if (isset($_POST['IDEmp'])) {$idemp =$_POST['IDEmp']; }
        if (isset($_POST['IDMonth']) and isset ($_POST['IDYear'] )) {
            $idm = $_POST['IDMonth'];
            $idy = $_POST['IDYear'];
            $month['id']=$idm;
            $year['id']=$idy;
            $get_year  = $db->query("SELECT id FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE user_id='41' AND year='".$_POST['IDYear']."'");
            foreach($get_year  as $yearvalue) {
                $filter_year = $yearvalue['id'];}

        }else{
            $now = new \DateTime('now');
            $currmonth = $now->format('m');
            $curryear = $now->format('Y');

            $get_year  = $db->query("SELECT id FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE user_id='41' AND year='".$curryear."' ");
            foreach($get_year  as $yearvalue) {
                $filter_year = $yearvalue['id'];}
            $month['id']=$currmonth;
            $year['id']=$curryear;
        }

        $employment_filter=$year['id']."-".$month['id']."-30 00:00:00.000";
        $filtertdate=$year['id']."-".$month['id']."-1 00:00:00.000";

        if(isset($_POST['IDB1']))
            $B_1 = $_POST['IDB1'];
        else
            $B_1 = '';

        if(isset($_POST['IDReg']))
            $region = $_POST['IDReg'];
        else
            $region = '';

        if(isset($_POST['IDStream']))
            $stream = $_POST['IDStream'];
        else
            $stream = '';

        if(isset($_POST['IDTeam']))
            $team = $_POST['IDTeam'];
        else
            $team = '';

        if(isset($_POST['ime_prezime']))
            $ime_prezime = $_POST['ime_prezime'];
        else
            $ime_prezime = '';
        ?>

        <div class="box" style="width:22%; display: block; float:left; margin-right:20px;">
            <div class="content">
                <table class="table table-hover">
                    <div class="row">
                        <div class="col-xs-12">
                            <form id="admin-form" method="post">
                                <input id="pg" type="hidden" name="pg" value="">
                                <label class="lable-admin1"><?php echo __('Mjesec-Godina'); ?></label>
                                <input readonly type="text" id="month" name="month" class="monthPicker" />
                                <input type="hidden" class="rcorners1" style = "outline:none;width:200px;" name="IDYear" min="2017" max="2300" <?php if (isset($_POST['IDYear']) and ($_POST['IDYear'])!=''){  ?> value="<?php  echo $_POST['IDYear'];?>"  <?php }else{ ?> value="<?php  echo date("Y");?>" <?php } ?>  required
                                       oninvalid="this.setCustomValidity('Molimo unesite godinu.')"
                                       onchange="this.setCustomValidity('')"><br>
                                <input type="hidden" class="rcorners1" style = "outline:none;width:200px;" name="IDMonth" min="1" max="12" <?php if (isset($_POST['IDMonth']) and ($_POST['IDMonth'])!='' ){  ?> value="<?php  echo $_POST['IDMonth'];?>"  <?php }else{ ?> value="<?php  echo date("n");?>" <?php } ?>  required
                                       oninvalid="this.setCustomValidity('Molimo unesite mjesec.')"
                                       onchange="this.setCustomValidity('')"><br>
                                <label class="lable-admin1"><?php echo __('Sektor'); ?></label>
                                <select id="B1" name="IDB1" class="rcorners1" style = "outline:none;width:200px;" class="form-control"  >
                                    <?php echo _optionB_1($B_1) ?>
                                </select><br/><br/>

                                <label class="lable-admin1"><?php echo __('Odjel'); ?></label>
                                <select id="regije" name="IDReg" class="rcorners1" style = "outline:none;width:200px;" class="form-control" >
                                    <?php echo _optionRegion($B_1,$region)?>
                                </select><br/><br/>

                                <label class="lable-admin1"><?php echo __('Grupa'); ?></label>
                                <select id="streams" name="IDStream" class="rcorners1" style = "outline:none;width:200px;" class="form-control" >
                                    <?php echo _optionStream($region,$stream)?>
                                </select><br/><br/>

                                <label class="lable-admin1"><?php echo __('Tim'); ?></label>
                                <select id="teams" name="IDTeam" class="rcorners1" style = "outline:none;width:200px;" class="form-control" >
                                    <?php echo _optionTeam($stream,$team)?>
                                </select><br/><br/>

                                <label class="lable-admin1"><?php echo __('Ime'); ?></label>
                                <select id="ime_prezime" name="ime_prezime" class="rcorners1" style = "outline:none;width:200px;" class="form-control" onchange="this.form.submit();">
                                    <?php echo _optionName($team,$stream,$region,$B_1,$ime_prezime,$filtertdate)?>
                                </select><br/>
                            </form>

                        </div>
                    </div>
                </table>
            </div>
        </div>

    </div>

</section>
<!-- END - Main section -->

<?php

include $_themeRoot.'/footer.php';

?>

<!--<script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script> -->
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
<script src="<?php echo $_pluginUrl; ?>/jquery-cookie-master/src/jquery.cookie.js"></script>



<script>
    $("#odustani").click(function(e){
        e.preventDefault();


        $("#B1").val("");
        $("#B1").trigger("change");
        $("#ime_prezime").val("");
        $("#ime_prezime").trigger("change");
        $("#regije").val("");
        $("#regije").trigger("change");
        $("#streams").val("");
        $("#streams").trigger("change");
        $("#teams").val("");
        $("#teams").trigger("change");
        $("#admin-form").submit();

    });
    $(".paginate a").click(function(e){
        e.preventDefault();

        link_action = $(this).attr("href");

        $("#admin-form").attr("action", link_action);
        $("#admin-form").submit();
    });

    function insertParam(key, value)
    {
        key = encodeURI(key); value = encodeURI(value);

        var kvp = document.location.search.substr(1).split('&');

        var i=kvp.length; var x; while(i--)
    {
        x = kvp[i].split('=');

        if (x[0]==key)
        {
            x[1] = value;
            kvp[i] = x.join('=');
            break;
        }
    }

        if(i<0) {kvp[kvp.length] = [key,value].join('=');}

        //this will reload the page, it's likely better to store this until finished
        document.location.search = kvp.join('&');
    }

    $("#B1").select2();
    $("#regije").select2();
    $("#streams").select2();
    $("#teams").select2();
    $("#ime_prezime").select2();

    $('#B1').on('change', function() {
        $.post("<?php echo $url.'/modules/admin_manager_verification/ajax.php'; ?>", { request: "get-regions", B_1 : this.value},
            function(returnedData){
                $('#regije').html(returnedData);
                $("#regije").select2();
            });
        $.post("<?php echo $url.'/modules/admin_manager_verification/ajax.php'; ?>", { request: "get-streams", region : ''},
            function(returnedData){
                $('#streams').html(returnedData);
                $("#streams").select2();
            });
        $.post("<?php echo $url.'/modules/admin_manager_verification/ajax.php'; ?>", { request: "get-teams", stream : ''},
            function(returnedData){
                $('#teams').html(returnedData);
                $("#teams").select2();
            });
        $.post("<?php echo $url.'/modules/admin_manager_verification/ajax.php'; ?>", { request: "get-users", b1 : this.value},
            function(returnedData){
                $('#ime_prezime').html(returnedData);
                $("#ime_prezime").select2();
            });
    })

    $('#regije').on('change', function() {
        $.post("<?php echo $url.'/modules/admin_manager_verification/ajax.php'; ?>", { request: "get-streams", region : this.value},
            function(returnedData){
                $('#streams').html(returnedData);
                $("#streams").select2();
            });
        $.post("<?php echo $url.'/modules/admin_manager_verification/ajax.php'; ?>", { request: "get-teams", stream : ''},
            function(returnedData){
                $('#teams').html(returnedData);
                $("#teams").select2();
            });
        $.post("<?php echo $url.'/modules/admin_manager_verification/ajax.php'; ?>", { request: "get-users", region : this.value},
            function(returnedData){
                $('#ime_prezime').html(returnedData);
                $("#ime_prezime").select2();
            });
    })

    $('#streams').on('change', function() {
        $.post("<?php echo $url.'/modules/admin_manager_verification/ajax.php'; ?>", { request: "get-teams", stream : this.value},
            function(returnedData){
                $('#teams').html(returnedData);
                $("#teams").select2();
            });
        $.post("<?php echo $url.'/modules/admin_manager_verification/ajax.php'; ?>", { request: "get-users", stream : this.value},
            function(returnedData){
                $('#ime_prezime').html(returnedData);
                $("#ime_prezime").select2();
            });
    })

    $('#teams').on('change', function() {
        $.post("<?php echo $url.'/modules/admin_manager_verification/ajax.php'; ?>", { request: "get-users", team : this.value},
            function(returnedData){
                $('#ime_prezime').html(returnedData);
                $("#ime_prezime").select2();
            });
    })
</script>

<script type="text/javascript">
    $(document).ready(function() {



        function myFunction(){
            if(document.getElementById('Abs').checked)
                var params = [{name:'Abs',value:'1'}];
            else
                params = [];

            $.each(params, function(i,param){
                $('<input />').attr('type', 'hidden')
                    .attr('name', param.name)
                    .attr('value', param.value)
                    .appendTo('#admin-form');
            });

            document.getElementById("admin-form").submit();

            return true;
        }

        // If cookie is set, scroll to the position saved in the cookie.
        if ( $.cookie("scroll") !== null ) {
            $(document).scrollTop( $.cookie("scroll") );
        }

        // When a button is clicked...
        $('a').on("click",function() {
            // Set a cookie that holds the scroll position.
            $.cookie("scroll", $(document).scrollTop() );

        });

    });


    $("#month").datepicker( {
        format: "m-yyyy",
        startView: "months",
        minViewMode: "months",
        language: 'bs',
        //todayBtn: false,
    });
    var year = '<?php if (isset($_POST['IDYear']) and ($_POST['IDYear'])!=''){echo $_POST['IDYear'];}else{ echo date("Y");}?>';
    var month = '<?php if (isset($_POST['IDMonth']) and ($_POST['IDMonth'])!=''){echo $_POST['IDMonth'];}else{ echo date("m");}?>';
    $("#month").datepicker("setDate" , new Date(year +'/'+month+'/01')).on('changeDate', function (ev) {

        var datum = $('#month').val();
        var arr = datum.split('-');

        $("input[name='IDMonth']").val(parseInt(arr[0]));
        $("input[name='IDYear']").val(arr[1]);
        $("#admin-form").submit();
    });


</script>



</body>
</html>
