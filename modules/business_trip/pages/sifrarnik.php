<?php
_pagePermission(4, false);
$name = $_GET['name'];
$sifrarnici = array(
    'svrha'=>"Svrha",
    'valuta'=>'Valuta',
    'primanje_sredstava'=>'Način uplate akontacije',
    'vrsta_transporta'=>'Sredstvo transporta',
    'vrsta_smjestaja'=>'Smještaj',
    'osiguranje'=>'Osiguranje',
    'viza'=>'Viza',
    'cijena_goriva_postotak'=>'Cijena goriva i postotak'
);
//insertovanje opcije
if(isset($_GET['add'])){

    if(strlen(trim($_GET['add'])) == 0){
        header('Location: /apoteke-app/?m=business_trip&p=sifrarnik&name=cijena_goriva_postotak&err=1');
        //break;
    }

    $provjeraq = $db->query("SELECT count(*) as aa FROM [c0_intranet2_apoteke].[dbo].[sifrarnici] where naziv_instance='".$_GET['add']."' and name = '".$_GET['name']."'");
    $provjera = $provjeraq->fetch();

    if($provjera['aa'] >  0){
        header('Location: /apoteke-app/?m=business_trip&p=sifrarnik&name='.$_GET['name'].'&postoji=1');
        //break;
    }

    if($name == 'cijena_goriva_postotak'){
        preg_match('/([0-9]*(\.[0-9]+)?) KM x ([0-9]*(\%)?)/', $_GET['add'], $matches, PREG_OFFSET_CAPTURE);

        if (empty($matches) or strpos($_GET['add'], ',')) {
            header('Location: /apoteke-app/?m=business_trip&p=sifrarnik&name=cijena_goriva_postotak&err=1');
            // break;
        }else{
            if($matches[4][0]){
                if($matches[4][0] != '%'){
                    header('Location: /apoteke-app/?m=business_trip&p=sifrarnik&name=cijena_goriva_postotak&err=1');
                    //break;
                }
            }else{
                header('Location: /apoteke-app/?m=business_trip&p=sifrarnik&name=cijena_goriva_postotak&err=1');
                //break;
            }
        }
        $_GET['add'] = $matches[0][0];
    }

    try{

        $add = $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[sifrarnici] (name, ime, naziv_instance,active)
          VALUES ('$name','".$sifrarnici[$name]."','".$_GET['add']."',1)");

    }catch (exception $e) {
        var_dump($e);
    }
}
//brisanje opcije
if(isset($_GET['del'])){
    try{
        $add = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[sifrarnici] SET active=0 WHERE id =".$_GET['del']);
    }catch (exception $e) {
        var_dump($e);
    }
}
// vracanje izbrisanih
if(isset($_GET['vrati'])){
    try{
        $add = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[sifrarnici] SET active=1 WHERE id =".$_GET['vrati']);
    }catch (exception $e) {
        var_dump($e);
    }
}
//hvatanje podataka
try{
    $instance = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[sifrarnici] where active = 1 and name = '$name'");
}catch (exception $e) {
    var_dump($e);
}

try{
    $deleted = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[sifrarnici] where active = 0 and name = '$name'");
}catch (exception $e) {
    var_dump($e);
}

?>
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid black !important;
    }
</style>
<!-- START - Main section -->
<section class="full">

    <div class="container-fluid">


        <div class="row">

            <div class="col-sm-12 text-center">
                <h2>
                    <?php echo __('Šifrarnik - ').$sifrarnici[$name]; ?>
                </h2>

                <!-- <div class="btn btn-red btn-lg pull-right">Dodaj novu opciju!
                    <i class="ion-ios-download-outline"></i>
                </div> -->
            </div>
            <div class="col-sm-4 text-right"><br/>

            </div>

        </div>
        <?php if(isset($_GET['err'])){ ?>
            <div class="alert alert-danger">Greška! Provjerite unos.</div>
        <?php } ?>
        <?php if(isset($_GET['postoji'])){ ?>
            <div class="alert alert-danger">Unos postoji!</div>
        <?php } ?>
        <div class="row box" style = "padding:25px;">
            <div class="col-6" style="float:left;width:20%;line-height:44px;font-size:16px;">Unesite naziv:</div>
            <div class="col-6" style="float:left;width:60%;">
                <input type="text" class="form-control" name='naziv_instance' id='naziv_instance'>
            </div>
            <div class="col-6" style="float:left;width:20%;text-align:center;line-height:44px;" >
                <input type='submit' value="Spremi" class='btn btn-red' onclick='submitajovosranje();'>
            </div>
            <?php if($name == 'cijena_goriva_postotak'){ ?>
                <div class="col-sm-12 pt-3" style="padding-top:15px;"><b>Primjer unosa: 2.23 KM x 15%</b></div>
            <?php } ?>
        </div>
        <div class = "row">
            <div class='col-6' style='width:auto;display:inline;'>
                <table class="table table-hover">
                    <thead>
                    <th>Aktivne opcije</th>
                    <th style='text-align:center;width:200px;'>Akcije</th>
                    </thead>
                    <tbody>
                    <?php
                    foreach($instance as $instanca){
                        ?>
                        <tr>
                            <td>
                                <?php echo $instanca['naziv_instance']; ?>
                            </td>
                            <td style='text-align:center;'>
                                <a class='table-btn bt-admins' onclick="window.location.href ='/apoteke-app/?m=business_trip&p=sifrarnik&name=<?php echo $name;?>&del=<?php echo $instanca['id'];?>'"><i class='ion-android-close'></i></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class = "row">
            <div class='col-6' style='width:auto;display:inline;'>
                <table class="table table-hover">
                    <thead>
                    <th>Neaktivne opcije</th>
                    <th style='text-align:center;width:200px;'>Akcije</th>
                    </thead>
                    <tbody>
                    <?php
                    foreach($deleted as $one){
                        ?>
                        <tr>
                            <td>
                                <?php echo $one['naziv_instance']; ?>
                            </td>
                            <td style='text-align:center;'>
                                <a class='table-btn bt-admins' onclick="window.location.href ='/apoteke-app/?m=business_trip&p=sifrarnik&name=<?php echo $one['name'];?>&vrati=<?php echo $one['id'];?>'"><i class='ion-checkmark'></i></a>
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

include $_themeRoot.'/footer.php';

?>

<script>
    $("#ime_prezime").select2();

    $(function(){

        $('form#form').validate({
            focusCleanup:true
        });

    });

    function submitajovosranje(){
        let a = $('#naziv_instance').val();
        if ((a!='')){
            window.location.href ='/apoteke-app/?m=business_trip&p=sifrarnik&name=<?php echo $name.'&add=';?>'+a;
        }
    }

</script>


</body>
</html>
