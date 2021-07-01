<?php
  _pagePermission([2,4], false);
  $user_id = _decrypt($_SESSION['SESSION_USER']);
// Registracija Sl put vise dana -> reg se 1 dan u biti
// trazenje id a od pocetnog dana
if (isset($_GET['dateOD']) and isset($_GET['dateDO'])){
    $date_OD = $_GET['dateOD'];
    $date_DO = $_GET['dateDO'];
    
    $djelovi_datuma = explode('.', $date_OD);
    $godina_poc = (int) $djelovi_datuma[2];
    $mjesec_poc = (int) $djelovi_datuma[1];
    $dan_poc    = (int) $djelovi_datuma[0];

    $year_id = $db->query("SELECT TOP 1 id FROM  ".$portal_hourlyrate_year."  where year = $godina_poc and user_id = $user_id");
    $year_id = $year_id->fetch();
    $year_id = $year_id['id'];

    $get_id = $db->query("SELECT top 1 id from  ".$portal_hourlyrate_day."  where day = $dan_poc and month_id = $mjesec_poc and year_id = $year_id");
    $get_id = $get_id->fetch();
    $_GET['id'] = $get_id['id'];
}
// 73 normalni sl put, 81 < 4
$dayreq = $db->query("SELECT * from  ".$portal_hourlyrate_day."  where id=".$_GET['id']);
foreach($dayreq as $pf){
    $dayreq = $pf;
}
//Planiranje sl puta
if($_POST){

    try {
        $ts = time();      
    $provjeraq = $db->query("SELECT count(*) as aa FROM  ".$portal_sl_put."  where request_id='".$_POST['request_id']."'");
    foreach ($provjeraq as $one){
    $provjera = $one['aa'];
    }
    if ($one['aa']==0){  
$data = $db->query("INSERT INTO  ".$portal_sl_put."  (
 [request_id]
,[status]
,[svrha]
,[pocetak_datum]
,[pocetak_vrijeme]
,[kraj_datum]
,[kraj_vrijeme]
,[polazna_drzava]
,[grad_polaska]
,[odredisna_drzava]
,[odredisni_grad]
,[odredisna_drzava2]
,[odredisni_grad2]
,[odredisna_drzava3]
,[odredisni_grad3]
,[razlog_putovanja]
,[napomena]
,[iznos_akontacije]
,[valuta]
,[datum_akontacije]
,[primanje_sredstva]
,[akontacija_napomena]
,[vrsta_transporta]
,[transport_pocetak_datum]
,[transport_pocetak_vrijeme]
,[transport_kraj_datum]
,[transport_kraj_vrijeme]
,[transport_polazna_drzava]
,[transport_grad_polaska]
,[transport_odredisna_drzava]
,[transport_odredisni_grad]
,[transport_napomena]
,[vrsta_smjestaja]
,[smjestaj_pocetak_datum]
,[smjestaj_pocetak_vrijeme]
,[smjestaj_kraj_datum]
,[smjestaj_kraj_vrijeme]
,[smjestaj_drzava]
,[smjestaj_grad]
,[smjestaj_adresa]
,[osiguranje]
,[osiguranje_pocetak_datum]
,[osiguranje_pocetak_vrijeme]
,[osiguranje_kraj_datum]
,[osiguranje_kraj_vrijeme]
,[dokument_broj]
,[viza]
,[osiguranje_napomena]
,[lock]
,[status_hr]
,[created_at])
 VALUES ("."'".
     $_POST['request_id'] ."','".
     $_POST['status'] ."','".
     $_POST['svrha'] ."','".
     prebaciDatumStandard($_POST['pocetak_datum']) ."','".
     $_POST['pocetak_vrijeme'] ."','".
     prebaciDatumStandard($_POST['kraj_datum']) ."','".
     $_POST['kraj_vrijeme'] ."','".
     $_POST['polazna_drzava'] ."','".
     $_POST['grad_polaska'] ."','".
     $_POST['odredisna_drzava'] ."','".
     $_POST['odredisni_grad'] ."','".
     $_POST['odredisna_drzava2'] ."','".
     $_POST['odredisni_grad2'] ."','".
     $_POST['odredisna_drzava3'] ."','".
     $_POST['odredisni_grad3'] ."','".
     $_POST['razlog_putovanja'] ."','".
     $_POST['napomena'] ."','".
     $_POST['iznos_akontacije'] ."','".
     $_POST['valuta'] ."','".
     prebaciDatumStandard($_POST['datum_akontacije']) ."','".
     $_POST['primanje_sredstva'] ."','".
     $_POST['akontacija_napomena'] ."','".
     $_POST['vrsta_transporta'] ."','".
     prebaciDatumStandard($_POST['transport_pocetak_datum']) ."','".
     $_POST['transport_pocetak_vrijeme'] ."','".
     prebaciDatumStandard($_POST['transport_kraj_datum']) ."','".
     $_POST['transport_kraj_vrijeme'] ."','".
     $_POST['transport_polazna_drzava'] ."','".
     $_POST['transport_grad_polaska'] ."','".
     $_POST['transport_odredisna_drzava'] ."','".
     $_POST['transport_odredisni_grad'] ."','".
     $_POST['transport_napomena'] ."','".
     $_POST['vrsta_smjestaja'] ."','".
     prebaciDatumStandard($_POST['smjestaj_pocetak_datum']) ."','".
     $_POST['smjestaj_pocetak_vrijeme'] ."','".
     prebaciDatumStandard($_POST['smjestaj_kraj_datum']) ."','".
     $_POST['smjestaj_kraj_vrijeme'] ."','".
     $_POST['smjestaj_drzava'] ."','".
     $_POST['smjestaj_grad'] ."','".
     $_POST['smjestaj_adresa'] ."','".
     $_POST['osiguranje'] ."','".
     prebaciDatumStandard($_POST['osiguranje_pocetak_datum']) ."','".
     $_POST['osiguranje_pocetak_vrijeme'] ."','".
     prebaciDatumStandard($_POST['osiguranje_kraj_datum']) ."','".
     $_POST['osiguranje_kraj_vrijeme'] ."','".
     $_POST['dokument_broj'] ."','".
     $_POST['viza'] ."','".
    $_POST['osiguranje_napomena'] ."',
    0 ,
    0 ,
    $ts )");
        $ts = time();
        $operater = _decrypt($_SESSION['SESSION_USER']);
    $insert_log = $db->query("INSERT INTO  ".$portal_sl_put_logs."  (
        sl_put_request_id
    , operation
    , user_id
    , vrijeme
    )
    VALUES (
    '".$_GET['id']."',
    'snimanje',
    $operater ,
    $ts
    )");
    } else {
        $data = $db->query("UPDATE  ".$portal_sl_put."  SET 
            [request_id] ='".$_POST['request_id'] ."'
           ,[status]='".$_POST['status'] ."'
           ,[svrha]='".$_POST['svrha'] ."'
           ,[pocetak_datum]='".prebaciDatumStandard($_POST['pocetak_datum']) ."'
           ,[pocetak_vrijeme]='".$_POST['pocetak_vrijeme'] ."'
           ,[kraj_datum]='".prebaciDatumStandard($_POST['kraj_datum']) ."'
           ,[kraj_vrijeme]='".$_POST['kraj_vrijeme'] ."'
           ,[polazna_drzava]='".$_POST['polazna_drzava'] ."'
           ,[grad_polaska]='".$_POST['grad_polaska'] ."'
           ,[odredisna_drzava]='".$_POST['odredisna_drzava'] ."'
           ,[odredisni_grad]='".$_POST['odredisni_grad'] ."'
           ,[odredisna_drzava2]='".$_POST['odredisna_drzava2'] ."'
           ,[odredisni_grad2]='".$_POST['odredisni_grad2'] ."'
           ,[odredisna_drzava3]='".$_POST['odredisna_drzava3'] ."'
           ,[odredisni_grad3]='".$_POST['odredisni_grad3'] ."'
           ,[razlog_putovanja]='".$_POST['razlog_putovanja'] ."'
           ,[napomena]='".$_POST['napomena'] ."'
           ,[iznos_akontacije]=".$_POST['iznos_akontacije'] ."
           ,[valuta]='".$_POST['valuta'] ."'
           ,[datum_akontacije]='".prebaciDatumStandard($_POST['datum_akontacije']) ."'
           ,[primanje_sredstva]='".$_POST['primanje_sredstva'] ."'
           ,[akontacija_napomena]='".$_POST['akontacija_napomena'] ."'
           ,[vrsta_transporta]='".$_POST['vrsta_transporta'] ."'
           ,[transport_pocetak_datum]='".prebaciDatumStandard($_POST['transport_pocetak_datum']) ."'
           ,[transport_pocetak_vrijeme]='".$_POST['transport_pocetak_vrijeme'] ."'
           ,[transport_kraj_datum]='".prebaciDatumStandard($_POST['transport_kraj_datum'])."'
           ,[transport_kraj_vrijeme]='".$_POST['transport_kraj_vrijeme'] ."'
           ,[transport_polazna_drzava]='".$_POST['transport_polazna_drzava'] ."'
           ,[transport_grad_polaska]='".$_POST['transport_grad_polaska'] ."'
           ,[transport_odredisna_drzava]='".$_POST['transport_odredisna_drzava'] ."'
           ,[transport_odredisni_grad]='".$_POST['transport_odredisni_grad'] ."'
           ,[transport_napomena]='".$_POST['transport_napomena'] ."'
           ,[vrsta_smjestaja]='".$_POST['vrsta_smjestaja'] ."'
           ,[smjestaj_pocetak_datum]='".prebaciDatumStandard($_POST['smjestaj_pocetak_datum'])."'
           ,[smjestaj_pocetak_vrijeme]='".$_POST['smjestaj_pocetak_vrijeme'] ."'
           ,[smjestaj_kraj_datum]='".prebaciDatumStandard($_POST['smjestaj_kraj_datum'])."'
           ,[smjestaj_kraj_vrijeme]='".$_POST['smjestaj_kraj_vrijeme'] ."'
           ,[smjestaj_drzava]='".$_POST['smjestaj_drzava'] ."'
           ,[smjestaj_grad]='".$_POST['smjestaj_grad'] ."'
           ,[smjestaj_adresa]='".$_POST['smjestaj_adresa'] ."'
           ,[osiguranje]='".$_POST['osiguranje'] ."'
           ,[osiguranje_pocetak_datum]='".prebaciDatumStandard($_POST['osiguranje_pocetak_datum'])."'
           ,[osiguranje_pocetak_vrijeme]='".$_POST['osiguranje_pocetak_vrijeme'] ."'
           ,[osiguranje_kraj_datum]='".prebaciDatumStandard($_POST['osiguranje_kraj_datum'])."'
           ,[osiguranje_kraj_vrijeme]='".$_POST['osiguranje_kraj_vrijeme'] ."'
           ,[dokument_broj]='".$_POST['dokument_broj'] ."'
           ,[viza]='".$_POST['viza'] ."'
           ,[osiguranje_napomena]='".$_POST['osiguranje_napomena'] ."'
        WHERE request_id =" .$_POST['request_id']
           );
           $ts = time();
           $operater = _decrypt($_SESSION['SESSION_USER']);
            $insert_log = $db->query("INSERT INTO  ".$portal_sl_put_logs."  (
                sl_put_request_id
            , operation
            , user_id
            , vrijeme
            )
            VALUES (
            '".$_GET['id']."',
            'snimanje',
            $operater ,
            $ts
            )");
    }
}
catch (exception $e) {
    var_dump($e);
}
}
// postavljanje datuma na selektovani
$year = $db->query("SELECT * from  ".$portal_hourlyrate_year."  where id=".$dayreq['year_id']." and user_id=".$dayreq['user_id']);
foreach($year as $pff){
    $year = $pff['year'];
}
$pocetak_sl_put = $dayreq['day'].'.'.$dayreq['month_id'].'.'.$year;

//sifrarnici
$sifrarnik_svrha= $db->query("SELECT * from  ".$portal_sifrarnici."  where active = 1 and name ='svrha'");
$sifrarnik_valuta= $db->query("SELECT * from  ".$portal_sifrarnici."  where active = 1 and name ='valuta'");
$sifrarnik_primanje_sredstava= $db->query("SELECT * from  ".$portal_sifrarnici."  where active = 1 and name ='primanje_sredstava'");
$sifrarnik_vrsta_transporta= $db->query("SELECT * from  ".$portal_sifrarnici."  where active = 1 and name ='vrsta_transporta'");
$sifrarnik_vrsta_smjestaja= $db->query("SELECT * from  ".$portal_sifrarnici."  where active = 1 and name ='vrsta_smjestaja'");
$sifrarnik_osiguranje= $db->query("SELECT * from  ".$portal_sifrarnici."  where active = 1 and name ='osiguranje'");
$sifrarnik_viza= $db->query("SELECT * from  ".$portal_sifrarnici."  where active = 1 and name ='viza'");

$sifrarnik_drzave = $db->query("SELECT * from  ".$portal_countries."  ");

    foreach($sifrarnik_drzave as $one){
       $opcije_drzave.= "<option value='".$one['country_id']."'>".$one['name']."</option> ";
    }
//provjera da li postoji isti sl put
$provjeraq = $db->query("SELECT count(*) as aa FROM  ".$portal_sl_put."  where request_id='".$_GET['id']."'");
foreach ($provjeraq as $one){
$provjeraa = $one['aa'];
}
if ($provjeraa == 1){
    $putq = $db->query("SELECT * FROM  ".$portal_sl_put."  where request_id='".$_GET['id']."'");
 }
foreach ($putq as $onee){
    $put = $onee;
}
//kraj sl puta
if (isset($date_DO)){
    $kraj_sl_put = $date_DO;
} else if(isset($put)){
    $kraj_sl_put =  prebaciDatumBih($put['kraj_datum']);
} else{
    $kraj_sl_put = $pocetak_sl_put;
}
//vec na obradi
$na_obradi = $db->query("SELECT count(*) as aa FROM  ".$portal_sl_put."  where na_obradi=1 and request_id='".$_GET['id']."'");
$na_obradi=$na_obradi->fetch();

$postoji = $db->query("SELECT * FROM  ".$portal_sl_put."  where request_id='".$_GET['id']."'");
$postoji=$postoji->fetch();
 ?>
 
<style>
.col-sm-12{
    margin-top:-8px;
}
.head{
    margin-top:0px;
    margin-bottom:5px;
}
.linee{
    border: 0;
    height: 1px;
    background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));
    margin: 10px 0 !important;
}
.naslov_holder{
    padding-left:25px;
}
label.radio > input:checked + img,
      label.radio > input:checked + i,
      label.radio > input:checked + span{
        	border-color: <?php echo _settings('color_button_bg'); ?>;
      }
      label.radio > input:checked + span{
        color: <?php echo _settings('color_button_bg'); ?>;
      }
.form-control{
    background-color:#f5f5f5;
}
.plus {
  --t:2px;   /* Thickness */
  --l:40px;  /* size of the symbol */
  --s:10px;  /* space around the symbol */
  --c1:#fff; /* Plus color*/
  --c2:#000; /* background color*/

  display:inline-block;
  width:var(--l);
  height:var(--l);
  padding:var(--s);
  box-sizing:border-box; /*Remove this if you don't want space to be included in the size*/
  
  background:
    linear-gradient(var(--c1),var(--c1)) content-box,
    linear-gradient(var(--c1),var(--c1)) content-box,
    var(--c2);
  background-position:center;
  background-size: 100% var(--t),var(--t) 100%;
  background-repeat:no-repeat;
}

.radius {
  border-radius:50%;
}
</style>
<!-- START - Main section -->
<section class="full">
<?php if ($data){
        ?>
        <div class="container row alert alert-success" role="alert" style='margin-top:10px;'>
            Uspješno snimljeni podaci!
        </div>
        <?php
    }
    ?>
<div class='container row box' style='text-align:center;padding:10px;margin-top:15px;height:50px;'>
    <h3 style='margin:20px;display:inline;padding:20px;'>ZAHTJEV ZA SLUŽBENO PUTOVANJE</h3>
    <div style="display:inline;float:right;"><a href="/apoteke-app/?m=business_trip&p=all&pg=1" style="background-color:#f5f5f5" class="btn box-head-btn">X</a></div>
</div>
<!-- odrediste putovanja -->
    <form method='POST' id='forma_sl_put'>
    <input type='hidden' name='request_id' value="<?php echo $_GET['id'] ?>">
    <input type='hidden' name='status' value="<?php echo $_GET['status'] ?>">

        <div class="container row box" style='margin-top:15px;'>
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Odredište putovanja</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c1"></a>
                </div>
            </div>
            <div class='content' id='c1'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Svrha</label>
                        <select name="svrha" class="form-control" >
                            <option value=" " selected>Odaberi...</option>
                            <?php 
                            foreach($sifrarnik_svrha as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['svrha']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $one['naziv_instance'] ?>"><?php echo $one['naziv_instance'] ?></option>
                                <?php
                            }
                            ?>			
                        </select>
                    </div>
                </div>
                <div class="col-sm-12" style='margin-top:15px;'>
                    <div class="col-sm-6">
                        <label>Početak datum:</label>
                        <input type="text" name="pocetak_datum" value="<?php echo $pocetak_sl_put ?>" readonly class="form-control"><br/>
                    </div>

                    <div class="col-sm-6">
                        <label>Početak vrijeme:</label>
                        <input class="time-input form-control"  type="text" name="pocetak_vrijeme"  value="<?php if(isset($put)) echo $put['pocetak_vrijeme']; else echo '';?>" class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Kraj datum:</label>
                        <input type="text" name="kraj_datum" value="<?php echo $kraj_sl_put ?>" readonly class="form-control"><br/>
                    </div>

                    <div class="col-sm-6">
                        <label>Kraj vrijeme:</label>
                        <input class="time-input form-control"  type="text" name="kraj_vrijeme" id='kraj_vrijeme' value="<?php if(isset($put)) echo $put['kraj_vrijeme']; else echo '';?>" class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Polazna država:</label>
                        <select name="polazna_drzava" id="polazna_drzava" class="form-control" >
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>				
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label>Grad polaska:</label>
                        <input type="text" name="grad_polaska" id='grad_polaska' value="<?php if(isset($put)) echo $put['grad_polaska']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Odredišna država:</label>
                        <select name="odredisna_drzava" class="form-control" id="odredisna_drzava">
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>			
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label>Grad odredišta:</label>
                        <input type="text" name="odredisni_grad" id='odredisni_grad' value="<?php if(isset($put)) echo $put['odredisni_grad']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Odredišna država 2:</label>
                        <select name="odredisna_drzava2" class="form-control"  id="odredisna_drzava2">
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>			
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label>Grad odredišta 2:</label>
                        <input type="text" name="odredisni_grad2" id='odredisni_grad2' value="<?php if(isset($put)) echo $put['odredisni_grad2']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Odredišna država 3:</label>
                        <select name="odredisna_drzava3" class="form-control"  id="odredisna_drzava3">
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>			
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label>Grad odredišta 3:</label>
                        <input type="text" name="odredisni_grad3" id='odredisni_grad3' value="<?php if(isset($put)) echo $put['odredisni_grad3']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Razlog putovanja:</label>
                        <textarea  name="razlog_putovanja" id='razlog_putovanja' value="<?php if(isset($put)) echo $put['razlog_putovanja']; else echo '';?>"  class="form-control"></textarea><br/>
                    </div>
                    <div class="col-sm-6">
                        <label>Napomena:</label>
                        <textarea  name="napomena" id='napomena' value="<?php if(isset($put)) echo $put['napomena']; else echo '';?>"  class="form-control"></textarea><br/>
                    </div>
                </div>
            </div>
        </div>

        <!-- akontacija -->

        <div class="container row box" style='margin-top:15px;'>
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Dodavanje akontacije</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c2"></a>
                </div>
            </div>
            <div class='content' id='c2'>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Iznos akontacije:</label>
                        <input type="number" name="iznos_akontacije" id='iznos_akontacije'  value="<?php if(isset($put)) echo $put['iznos_akontacije']; else echo '';?>"
                         min='1' max='9999' onKeyDown="if(this.value.length==4)return false;"  class="form-control"></textarea><br/>
                    </div>
                    <div class="col-sm-6">
                        <label>Valuta:</label>
                        <select name="valuta" class="form-control" >
                            <option value=" "  selected>Odaberi...</option>
                            <?php 
                            foreach($sifrarnik_valuta as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['valuta']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected;?> value="<?php echo $one['naziv_instance'] ?>"><?php echo $one['naziv_instance'] ?></option>
                                <?php
                            }
                            ?>				
                        </select>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Akontacija do datuma:</label>
                        <input type="text" name="datum_akontacije" id='akontacija_datum' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $pocetak_sl_put;
                        }  else echo $pocetak_sl_put;?>"
                         class="form-control"><br/>
                    </div>
                    <div class="col-sm-6">
                        <label>Primanje sredstava:</label>
                        <select name="primanje_sredstva" class="form-control" >
                            <option value=" "  selected>Odaberi...</option>
                            <?php 
                            foreach($sifrarnik_primanje_sredstava as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['primanje_sredstva']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $one['naziv_instance'] ?>"><?php echo $one['naziv_instance'] ?></option>
                                <?php
                            }
                            ?>				
                        </select>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Napomena:</label>
                        <textarea  name="akontacija_napomena" id='akontacija_napomena' value="<?php if(isset($put)) echo $put['akontacija_napomena']; else echo '';?>"  class="form-control"></textarea><br/>
                    </div>
                </div>
            </div>
        </div>

    <!-- transport -->

    <div class="container row box" style='margin-top:15px;'>
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Dodavanje transporta</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c3"></a>
                </div>
            </div>
            <div class='content' id='c3'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Sredstvo transporta:</label>
                        <select name="vrsta_transporta" class="form-control" >
                            <option value=" " >Odaberi...</option>
                            <?php 
                            foreach($sifrarnik_vrsta_transporta as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['vrsta_transporta']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $one['naziv_instance'] ?>"><?php echo $one['naziv_instance'] ?></option>
                                <?php
                            }
                            ?>				
                        </select>
                    </div>
                </div>
                <div class="col-sm-12" style='margin-top:15px;'>
                    <div class="col-sm-6">
                        <label>Početak datum:</label>
                        <input type="text" name="transport_pocetak_datum" id='transport_pocetak' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $pocetak_sl_put;
                        }  else echo $pocetak_sl_put;?>"
                        class="form-control"><br/>
                    </div>

                    <div class="col-sm-6">
                        <label>Početak vrijeme:</label>
                        <input class="time-input form-control"  type="text" name="transport_pocetak_vrijeme" id='transport_pocetak_vrijeme' value="<?php if(isset($put)) echo $put['transport_pocetak_vrijeme']; else echo '';?>" class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Kraj datum:</label>
                        <input type="text" name="transport_kraj_datum" id='transport_kraj' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $kraj_sl_put;
                        }  else echo $kraj_sl_put;?>"
                         class="form-control"><br/>
                    </div>

                    <div class="col-sm-6">
                        <label>Kraj vrijeme:</label>
                        <input class="time-input form-control"  type="text" name="transport_kraj_vrijeme" id='transport_kraj_vrijeme' value="<?php if(isset($put)) echo $put['transport_kraj_vrijeme']; else echo '';?>" class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Polazna država:</label>
                        <select name="transport_polazna_drzava" class="form-control" id="transport_polazna_drzava">
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>				
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label>Grad polaska:</label>
                        <input type="text" name="transport_grad_polaska" id='grad_polaska' value="<?php if(isset($put)) echo $put['transport_grad_polaska']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Odredišna država:</label>
                        <select name="transport_odredisna_drzava" class="form-control" id="transport_odredisna_drzava">
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>				
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label>Grad odredišta:</label>
                        <input type="text" name="transport_odredisni_grad" id='odredisni_grad' value="<?php if(isset($put)) echo $put['transport_odredisni_grad']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Napomena:</label>
                        <textarea  name="transport_napomena" id='transport_napomena' value="<?php if(isset($put)) echo $put['transport_napomena']; else echo '';?>"  class="form-control"></textarea><br/>
                    </div>
                </div>
            </div>
        </div>

        <!-- smjestaj -->

        <div class="container row box" style='margin-top:15px;'>
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Dodavanje smještaja</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c4"></a>
                </div>
            </div>
            <div class='content' id='c4'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Izaberite smještaj:</label>
                        <select name="vrsta_smjestaja" class="form-control" >
                            <option value=" "  selected>Odaberi...</option>
                            <?php 
                            foreach($sifrarnik_vrsta_smjestaja as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['vrsta_smjestaja']) $selected = 'selected';
                                ?>
                                <option <?php echo $seleted; ?> value="<?php echo $one['naziv_instance']; ?>"><?php echo $one['naziv_instance']; ?></option>
                                <?php
                            }
                            ?>		
                        </select>
                    </div>
                </div>
                <div class="col-sm-12" style='margin-top:15px;'>
                    <div class="col-sm-6">
                        <label>Početak datum:</label>
                        <input type="text" name="smjestaj_pocetak_datum" id='smjestaj_pocetak_datum' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $pocetak_sl_put;
                        }  else echo $pocetak_sl_put;?>"
                         class="form-control"><br/>
                    </div>

                    <div class="col-sm-6">
                        <label>Početak vrijeme:</label>
                        <input class="time-input form-control"  type="text" name="smjestaj_pocetak_vrijeme" id='smjestaj_pocetak_vrijeme' value="<?php if(isset($put)) echo $put['smjestaj_pocetak_vrijeme']; else echo '';?>" class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Kraj datum:</label>
                        <input type="text" name="smjestaj_kraj_datum" id='smjestaj_kraj_datum' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $kraj_sl_put;
                        }  else echo $kraj_sl_put;?>"
                         class="form-control"><br/>
                    </div>

                    <div class="col-sm-6">
                        <label>Kraj vrijeme:</label>
                        <input class="time-input form-control"  type="text" name="smjestaj_kraj_vrijeme" id='smjestaj_kraj_vrijeme' value="<?php if(isset($put)) echo $put['smjestaj_kraj_vrijeme']; else echo '';?>" class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Odredišna država:</label>
                        <select name="smjestaj_drzava" class="form-control" id="smjestaj_drzava">
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>				
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label>Odredišni grad:</label>
                        <input type="text" name="smjestaj_grad" id='grad_polaska' value="<?php if(isset($put)) echo $put['smjestaj_grad']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Naziv/Adresa:</label>
                        <textarea  name="smjestaj_adresa" id='smjestaj_adresa' value="<?php if(isset($put)) echo $put['smjestaj_adresa']; else echo '';?>"  class="form-control"></textarea><br/>
                    </div>
                </div>
            </div>
        </div>

        <!-- osiguranje viza -->

        <div class="container row box" style='margin-top:15px;'>
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Postavljanje osiguranja-vize</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c5"></a>
                </div>
            </div>
            <div class='content' id='c5'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Osiguranje:</label>
                        <select name="osiguranje" class="form-control" >
                            <option value=" "  selected>Odaberi...</option>
                            <?php 
                            foreach($sifrarnik_osiguranje as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['osiguranje']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $one['naziv_instance']; ?>"><?php echo $one['naziv_instance']; ?></option>
                                <?php
                            }
                            ?>				
                        </select>
                    </div>
                </div>
                <div class="col-sm-12" style='margin-top:15px;'>
                    <div class="col-sm-6">
                        <label>Početak datum:</label>
                        <input type="text" name="osiguranje_pocetak_datum" id='osiguranje_pocetak_datum' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $pocetak_sl_put;
                        }  else echo $pocetak_sl_put;?>"
                         class="form-control"><br/>
                    </div>

                    <div class="col-sm-6">
                        <label>Početak vrijeme:</label>
                        <input class="time-input form-control"  type="text" name="osiguranje_pocetak_vrijeme" id='osiguranje_pocetak_vrijeme' value="<?php if(isset($put)) echo $put['osiguranje_pocetak_vrijeme']; else echo '';?>" class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Kraj datum:</label>
                        <input type="text" name="osiguranje_kraj_datum" id='osiguranje_kraj_datum' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $kraj_sl_put;
                        }  else echo $kraj_sl_put;?>"
                         class="form-control"><br/>
                    </div>

                    <div class="col-sm-6">
                        <label>Kraj vrijeme:</label>
                        <input class="time-input form-control"  type="text" name="osiguranje_kraj_vrijeme" id='osiguranje_kraj_vrijeme' value="<?php if(isset($put)) echo $put['osiguranje_kraj_vrijeme']; else echo '';?>" class="form-control"><br/>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Dokument (pasoš) broj:</label>
                        <input type="text" name="dokument_broj" id='dokument_broj' value="<?php if(isset($put)) echo $put['dokument_broj']; else echo '';?>" class="form-control"><br/>
                    </div>
                    <div class="col-sm-6">
                        <label>Viza:</label>
                        <select name="viza" class="form-control" >
                            <option value=" "  selected>Odaberi...</option>
                            <?php 
                            foreach($sifrarnik_viza as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['viza']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $one['naziv_instance']; ?>"><?php echo $one['naziv_instance']; ?></option>
                                <?php
                            }
                            ?>				
                        </select>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Napomena:</label>
                        <textarea  name="osiguranje_napomena" id='osiguranje_napomena' value="<?php if(isset($put)) echo $put['osiguranje_napomena']; else echo '';?>"  class="form-control"></textarea><br/>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class='container row box' style='margin-bottom:15px;padding:20px;text-align:center'>
    <?php if (!$_GET['view'] and $dayreq['review_status'] != 1){ 
        ?>
        <button class="btn btn-red btn-lg" style="" onclick='planiraj();'>Spasi <i class="ion-ios-download-outline"></i></button>
        <?php
    }
    ?>    
    
    <form method="post" action="/apoteke-app/?m=business_trip&p=all&pg=1" style="display:inline;">

    <?php if($na_obradi['aa'] != 1 and $postoji){ ?>
    <input type ="hidden" name="obrada" value ="1">
    <input type ="hidden" name="id" value ="<?php echo $_GET['id']?>">
        <button type="submit" class="btn btn-red btn-lg" onclick="obrada(event)">Pošalji nalog na obradu! <i class="ion-ios-download-outline"></i></button>
    </form>
    <?php } ?>
    </div>

</section>
<!-- END - Main section -->

<?php

  include $_themeRoot.'/footer.php';

  function prebaciDatumStandard($datum){
    if($datum!='' and $datum!=' '){
        $niz = explode('.',$datum);
    return ((int)$niz[2]).'-'.((int)$niz[1]).'-'.((int)$niz[0]);
    }
    else return null;
  }
  function prebaciDatumBih($datum){
    if($datum!='' and $datum!=' '){
        $niz = explode('-',$datum);
    return ((int)$niz[2]).'.'.((int)$niz[1]).'.'.((int)$niz[0]);
  }
  else return null;
  }
 ?>

 <script>
$( document ).ready(function(){
    $('input').attr('maxlength', '60');
    $('textarea').attr('maxlength', '499');


    $('#razlog_putovanja').val('<?php echo $put['razlog_putovanja']; ?>');
    $('#napomena').val('<?php echo $put['napomena']; ?>');
    $('#akontacija_napomena').val('<?php echo $put['akontacija_napomena']; ?>');
    $('#transport_napomena').val('<?php echo $put['transport_napomena']; ?>');
    $('#smjestaj_adresa').val('<?php echo $put['smjestaj_adresa']; ?>');
    $('#osiguranje_napomena').val('<?php echo $put['osiguranje_napomena']; ?>');


    let view = <?php if ($_GET['view']) echo $_GET['view']; else echo 0;?>;
    if (view == 1){
        $('input').attr('readonly', 'readonly');
        $('input').attr('maxlength', '60');
        $('textarea').attr('readonly', 'readonly');
        $('textarea').attr('maxlength', '499');
        $('select').attr('readonly', 'readonly');
    }

    $('input').attr('autocomplete', 'off');
    $('textarea').attr('autocomplete', 'off');
    $('select').attr('autocomplete', 'off');

   

    
    
    $('#akontacija_datum').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    // endDate: '<?php echo $kraj_sl_put; ?>',
    // startDate: '<?php echo $pocetak_sl_put; ?>'
    });

    $('#transport_pocetak').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    //endDate: '<?php echo $kraj_sl_put; ?>',
    //startDate: '<?php echo $pocetak_sl_put; ?>'
    });

    $('#transport_kraj').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    //endDate: '<?php echo $kraj_sl_put; ?>',
    //startDate: '<?php echo $pocetak_sl_put; ?>'
    });
    
    $('#smjestaj_pocetak_datum').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    //endDate: '<?php echo $kraj_sl_put; ?>',
    //startDate: '<?php echo $pocetak_sl_put; ?>'
    });
        
    $('#smjestaj_kraj_datum').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    //endDate: '<?php echo $kraj_sl_put; ?>',
    //startDate: '<?php echo $pocetak_sl_put; ?>'
    });

    $('#osiguranje_pocetak_datum').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    //startDate: startDate,
    //endDate: new Date('2017/12/31')
    });

    $('#osiguranje_kraj_datum').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    //startDate: startDate,
    //endDate: new Date('2017/12/31')
    });

    //drzavice
    let polazna_drzava = '<?php if ($put)  echo $put['polazna_drzava']; else echo 0;?>';
            $('#polazna_drzava').val(polazna_drzava);
            $('#polazna_drzava').trigger('change');

            let odredisna_drzava = '<?php if ($put)  echo $put['odredisna_drzava']; else echo 0;?>';
            $('#odredisna_drzava').val(odredisna_drzava);
            $('#odredisna_drzava').trigger('change');

            let odredisna_drzava2 = '<?php if ($put)  echo $put['odredisna_drzava2']; else echo 0;?>';
            $('#odredisna_drzava2').val(odredisna_drzava2);
            $('#odredisna_drzava2').trigger('change');

            let odredisna_drzava3 = '<?php if ($put)  echo $put['odredisna_drzava3']; else echo 0;?>';
            $('#odredisna_drzava3').val(odredisna_drzava3);
            $('#odredisna_drzava3').trigger('change');
            
            let transport_polazna_drzava = '<?php if ($put)  echo $put['transport_polazna_drzava']; else echo 0;?>';
            $('#transport_polazna_drzava').val(transport_polazna_drzava);
            $('#transport_polazna_drzava').trigger('change');
                        
            let transport_odredisna_drzava = '<?php if ($put)  echo $put['transport_odredisna_drzava']; else echo 0;?>';
            $('#transport_odredisna_drzava').val(transport_odredisna_drzava);
            $('#transport_odredisna_drzava').trigger('change');
                                    
            let smjestaj_drzava = '<?php if ($put)  echo $put['smjestaj_drzava']; else echo 0;?>';
            $('#smjestaj_drzava').val(smjestaj_drzava);
            $('#smjestaj_drzava').trigger('change');
            
});

function planiraj(){
    $( "#forma_sl_put" ).submit();
    $('input').attr('required', 'true');
    $('textarea').attr('required', 'true');
    $('select').attr('required', 'true');

    
}

function obrada(event){

}


//when the window has been completed loaded, we search for all textbox with time-input CSS class
window.onload = function(e){ 
	//perform a for loop to add the event handler
	Array.from(document.getElementsByClassName("time-input")).forEach(
		function(element, index, array) {
			//Add the event handler to the time input
			element.addEventListener("blur", inputTimeBlurEvent);
		}
	);
}

inputTimeBlurEvent = function(e){
	var newTime = "";
	var timeValue = e.target.value;
	var numbers = [];
	var splitTime = [];
	
	//1st condition: if the value entered is empty, we set the default value
	if(timeValue.trim() == ""){
		e.target.value = "00:00";
		return;
	}
	
	//2nd condition: only allow numbers, dot and double dot. If not match set the default value. Example => 23a55
	var regex = /^[0-9.:]+$/;
	if( !regex.test(timeValue) ) {
		e.target.value = "00:00";
		return;
	}
	
	//3rd condition: replace the dot with double dot. Example => 23.55
	e.target.value = e.target.value.replace(".", ":").replace(/\./g,"");
	timeValue = e.target.value;
	
	//4th condition: auto add double dot if the input entered by user contains numbers only (no dot or double dot symbol found)
	//example => 2344 or 933
	if(timeValue.indexOf(".") == -1 && timeValue.indexOf(":") == -1){
		//check if the length is more than 4 we strip it up to 4
		if(timeValue.trim().length > 4){
			timeValue = timeValue.substring(0,4);
		}
		var inputTimeLength = timeValue.trim().length;
		numbers = timeValue.split('');
		switch(inputTimeLength){
			//Example => 23
			case 2:
				if(parseInt(timeValue) <= 0){
					e.target.value = "00:00";
				}else if(parseInt(timeValue) >= 24){
					e.target.value = "00:00";
				}else{
					e.target.value = timeValue + ":00";
				}
				break;
			//Example => 234
			case 3:
				newTime = "0" + numbers[0] + ":";
				if(parseInt(numbers[1] + numbers[2]) > 59){
					newTime += "00";
				}else{
					newTime += numbers[1] + numbers[2];
				}
				e.target.value = newTime;
				break;
			//Example 2345
			case 4:
				if(parseInt(numbers[0] + numbers[1]) >= 24){
					newTime = "00:";
				}else{
					newTime = numbers[0] + numbers[1] + ":";
				}
				if(parseInt(numbers[2] + numbers[3]) > 59){
					newTime += "00";
				}else{
					newTime += numbers[2] + numbers[3];
				}
				e.target.value = newTime;
				break;
		}
		return;
	}
	
	//5th condition: if double dot found
	var doubleDotIndex = timeValue.indexOf(":");
	//if user doesnt enter the first part of hours example => :35
	if(doubleDotIndex == 0){
		newTime = "00:";
		splitTime = timeValue.split(':');
		numbers = splitTime[1].split('');
		if(parseInt(numbers[0] + numbers[1]) > 59){
			newTime += "00";
		}else{
			newTime += numbers[0] + numbers[1];
		}
		e.target.value = newTime;
		return;
	}else{
		//if user enter not full time example=> 9:3
		splitTime = timeValue.split(':');
		var partTime1 = splitTime[0].split('');
		if(partTime1.length == 1){
			newTime = "0" + partTime1[0] + ":";
		}else{
			if(parseInt(partTime1[0] + partTime1[1]) > 23){
				newTime = "00:";
			}else{
				newTime = partTime1[0] + partTime1[1] + ":";
			}
		}
		
		var partTime2 = splitTime[1].split('');
		if(partTime2.length == 1){
			newTime += "0" + partTime2[0];
		}else{
			if(parseInt(partTime2[0] + partTime2[1]) > 59){
				newTime += "00";
			}else{
				newTime += partTime2[0] + partTime2[1];
			}
		}
		e.target.value = newTime;
		return;
	}
}
 </script>    


</body>
</html>
