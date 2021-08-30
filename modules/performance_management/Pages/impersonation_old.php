<link rel="stylesheet" href="theme/css/performance-management.css">
<?php

    // Selected values
    isset($_POST['standard_user']) ? $standard_user = $_POST['standard_user'] : $standard_user = '';
    isset($_POST['impersonator'])  ? $impersonator = $_POST['impersonator']   : $impersonator = '';


    /*******************************************************************************************************************
     *  Here depends on role, we can have two options:
     * *****************************************************************************************************************
     *      1. HR Admin - can choose user and it's impersonator
     *      2. Manager - Can only pick it's impersonator
     * ****************************************************************************************************************/

    $hr_admin = false;
?>
<form id="admin-form" method="post">
    <div class="split-on-right">
        <div class="choose-what-to-do">
            <h3>
                <?php echo ($hr_admin) ? 'Unesite / zamijenite impersonatora' : 'Odaberite vašeg imersonatora'; ?>
            </h3>

            <div class="just-a-row">
                <?php
                if($hr_admin){
                    ?>
                    <div class="inside-col">
                        <select id="standard_user" name="standard_user" class="rcorners1" style = "outline:none; width: 100%;" class="form-control" onchange="this.form.submit();">
                            <?php echo _optionName('','','','',$standard_user, '')?>
                        </select>
                    </div>
                    <?php
                }
                ?>
                <div class="inside-col">
                    <select id="impersonator" name="impersonator" class="rcorners1" style = "outline:none; width: 100%;" class="form-control" onchange="this.form.submit();">
                        <?php echo _optionName('','','','', $impersonator, '')?>
                    </select>
                </div>
            </div>
        </div>

        <div class="right-menu">
            <div class="right-menu-header">
                <h4>Dodatne opcije</h4>
            </div>

            <div class="right-menu-link">
                <a href="?m=performance_management&p=impersonation_list">Ovlaštenja za impersonaciju</a>
            </div>
            <div class="right-menu-link">
                <a href="#"><b>OSTALI LINKOVI</b></a>
            </div>
            <div class="right-menu-link">
                <a href="#">Pregled izvještaja</a>
            </div>
            <div class="right-menu-link">
                <a href="#">Pregled i export Sporazuma</a>
            </div>
            <div class="right-menu-link">
                <a href="#">Kalendar</a>
            </div>
            <div class="right-menu-link">
                <a href="#">Uzorci ciljeva</a>
            </div>
        </div>
    </div>
</form>
<?php include $_themeRoot.'/footer.php'; ?>

<script>
    $("#standard_user").select2();
    $("#impersonator").select2();

    $('#B1').on('change', function() {
        $.post("<?php echo $url.'/modules/admin_manager_verification/ajax.php'; ?>", { request: "get-users", b1 : this.value},
            function(returnedData){
                // $('#ime_prezime').html(returnedData);
                // $("#ime_prezime").select2();
            });
    });
</script>