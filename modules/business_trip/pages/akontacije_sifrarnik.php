<?php
  _pagePermission(4, false);
  $data = $db->query("Select data from [c0_intranet2_apoteke].[dbo].[akontacija]");
  $data = $data->fetch();

  $sifrarnik_drzave = $db->query("SELECT * from [c0_intranet2_apoteke].[dbo].[countries] ");
  $opcije_drzave= '';
  foreach($sifrarnik_drzave as $one){
    $opcije_drzave.= "<option value='".$one['country_id']."'>".$one['name']."</option> ";
 }
 ?>
<style>

</style>
<!-- START - Main section -->
<section class="full">
<div class="alert alert-info" role="alert" style="margin-top:15px;" id="alert"><b>
Nazivi država se moraju poklapati sa nazivima istih u kartici Države. U protivnom poruka o višem unosu akontacije na nalogu neće biti funkcionalna.
</b></div>

<div class="alert alert-success" role="alert" style="display:none;margin-top:15px;" id="alert">
Uspješno spremljeni podaci!
</div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12 text-center">
        <h2>
          <?php echo __('Šifarnik akontacija'); ?>
        </h2>
      </div>
      <div class="col-sm-4 text-right"><br/>
      </div>
    </div>

  <div class = "row">
      <div style='width:auto;display:inline;'>
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th style="width: 150px;">#</th>
              <th style='text-align:center;'>Akontacija u KM</th>
              <th id="colspan" style='text-align:center;' colspan="1">Trajanje službenog puta</th>
            </tr>
            
          </thead>
        <tbody>
          
        </tbody>
        </table>
    </div>
    <div class="col-sm-12">
      <a href="#" class="btn-sm btn-success" onclick="dodaj_red();">Dodaj red +</a>
      <a href="#" class="btn-sm btn-danger" onclick="obrisi_redove();">Izbriši označene redove</a>
      <a href="#" class="btn-sm btn-info" onclick="spasi();">Spremi podatke!</a>
    </div>
  </div>
</div>
<div class="hidden tabledata"><?php echo json_encode($data, JSON_UNESCAPED_UNICODE); ?></div>
</section>
<!-- END - Main section -->

<?php

  include $_themeRoot.'/footer.php';

 ?>

 <script>

 let php_data = JSON.parse($(".tabledata").text());
 let row_data = JSON.parse(php_data.data);

//svaki red
for(let i = 0; i<=row_data.length -1; i++){
  // console.log(row_data[i]);

  //kolone
  let column_data = row_data[i];
  let dynamic_rows = '';
  
  for(let j=0; j<=column_data.length-1; j++){
    if(i==0 && j == 0){
      dynamic_rows += "<td>Država</td>";
    }else{
      dynamic_rows += "<td contenteditable>" +column_data[j] + "</td>";
    }
  }

  let append_html = "";
  if(i == 0){
    append_html = `
  <tr id="prvi_red">
  <td>Označi za brisanje</td>
  ` + dynamic_rows + `
  </tr>`;
  }else{
    append_html = `
  <tr>
    <td><input type='checkbox' name='record'>
  ` + dynamic_rows + `
  </tr>`;
  }
  

  $("table tbody").append(append_html);
}

$("#colspan").attr('colspan', $("#prvi_red td").length-2);


 function dodaj_red(){
  let column_no = $("#prvi_red td").length;
  let dynamic_rows = '';

  for(i = 3; i<=column_no;i++){
    dynamic_rows += "<td contenteditable></td>";
  }

  let append_html = `
  <tr>
    <td><input type='checkbox' name='record'>
    </td><td contenteditable></td>
  ` + dynamic_rows + `</tr>`;

  $("table tbody").append(append_html);
 }
 
 function obrisi_redove(){
  $("table tbody").find('input[name="record"]').each(function(){
    if($(this).is(":checked")){
          $(this).parents("tr").remove();
      }
  });
 }

 function spasi(){
    let rows = $("tbody tr");
    let i = 0;
    let data = [];
    
    rows.each(function(){
      let j=0;
      let temp = [];
        $(this).find("td").slice(1).each(function(){
              temp.push(this.innerHTML);
          j++;
        });
        data.push(temp);
        i++;
    });

    data = JSON.stringify(data);

    $.ajax({
      type: 'POST',
      url: "<?php echo $url.'/modules/business_trip/myajax.php'; ?>",
      data: {data: data},
      complete: function(r){
        $("#alert-success").show();
      }
    });
 }
 $(document).ready(function () {
  setTimeout(function () {
    $("#alert-success").hide();
  }, 4000);
});
 </script>    


</body>
</html>
