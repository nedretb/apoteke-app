<?php
  _pagePermission(4, false);

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
          <?php echo __('Šifrarnici'); ?>
         
        </h2>
      </div>
      <div class="col-sm-4 text-right"><br/>

      </div>

    </div>


    <div class="row">

        <?php

       

        ?>
    </div>
  <div class = "row">
      <div class='col-6' style='width:auto;display:inline;'>
        <table class="table table-hover">
        <thead>
        <th>Naziv šifrarnika</th>
        <th style='text-align:center;'>Akcije</th>
        </thead>
        <tbody>
        <?php 
          foreach($sifrarnici as $key=>$value){
            ?>
            <tr>
            <td>
            <?php echo $value; ?>
            </td>
            <td style='text-align:center;'>
            <a class='table-btn' onclick="window.location.href ='/apoteke-app/?m=business_trip&p=sifrarnik&name=<?php echo $key;?>'"><i class='ion-android-list'></i></a>
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

 
 </script>    


</body>
</html>
