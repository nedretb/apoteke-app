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
        if(isset($_POST['ime_prezime']))
            $ime_prezime = $_POST['ime_prezime'];
        else
            $ime_prezime = '';
        ?>

        <form id="admin-form" method="post">
            <label class="lable-admin1"><?php echo __('Ime'); ?></label>
            <select id="ime_prezime" name="ime_prezime" class="rcorners1" style = "outline:none;width:200px;" class="form-control" onchange="this.form.submit();">
                <?php echo _optionName('','','','',$ime_prezime, '')?>
            </select><br/>
        </form>

    </div>

</section>
<!-- END - Main section -->

<?php include $_themeRoot.'/footer.php'; ?>


<script>
    $("#ime_prezime").select2();

    $('#B1').on('change', function() {
        $.post("<?php echo $url.'/modules/admin_manager_verification/ajax.php'; ?>", { request: "get-users", b1 : this.value},
            function(returnedData){
                // $('#ime_prezime').html(returnedData);
                // $("#ime_prezime").select2();
            });
    })
</script>

</body>
</html>
