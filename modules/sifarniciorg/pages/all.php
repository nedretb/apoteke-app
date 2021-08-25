<?php
//_pagePermission(4, false);

$sifrarnici = array(
    'zakonski' => "Zakonski broj dana godišnjeg odmora",
    'kategorija_invalidnosti' => "Kategorija invalidnosti",
    'godina_rada' => "Godine rada",
    'koeficijent_slozenosti' => "Koeficijent složenosti posla",
    'demo_borac' => "Status demobiliziranog borca"
);
?>
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid black !important;
    }

    .myButton {

        background-color: #006595;

        display: inline-block;
        cursor: pointer;
        color: #ffffff;
        font-family: Arial;
        font-size: 12px;
        padding: 10px 20px;
        
        text-decoration: none;

    }
    .myButton a:hover {
color:white !important;
    }
    .myButton:hover {
color:white !important;
    }



    .myButton:active {
        position: relative;
        top: 1px;
    }
</style>


<!-- START - Main section -->
<section class="full">

    <div class="container-fluid">


        <div class="row">

            <div class="col-sm-12 text-center">
                <h2>
                    <?php echo __('Šifarnici'); ?>

                </h2>
            </div>
            <div class="col-sm-4 text-right"><br />

            </div>

        </div>


        <div class="row">

            <?php


            ?>
        </div>
        <div class="row">
            <div class='col-6' style='width:auto;display:inline;'>
                <table class="table table-hover">
                    <thead>
                    <th>Naziv šifarnika</th>
                    <th style='text-align:center;'>Akcije</th>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($sifrarnici as $key => $value) {
                        ?>
                        <tr>
                            <td>
                                <?php echo $value; ?>
                            </td>
                            <td style='text-align:center; padding:5px;'>
                                <a href="/apoteke-app/?m=sifarniciorg&p=<?php echo $key; ?>" class="myButton">Otvori</a>


                            </td>
                        </tr>
                        <?php
                    }
                    ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</section>
<!-- END - Main section -->

<?php

include $_themeRoot . '/footer.php';

?>

<script>
    $("#ime_prezime").select2();

    $(function() {

        $('form#form').validate({
            focusCleanup: true
        });

    });
</script>


</body>

</html>