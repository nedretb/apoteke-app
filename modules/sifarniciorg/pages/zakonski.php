<?php
_pagePermission(4, false);
//$name = $_GET['name'];

$data = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] where Active = 1");
$datao = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] where Active = 0");

?>
<style>
    .button_color{}

    .button_color:hover{
        color: white;
        background-color: #5bc0de;
    }

    .table-wrapper {
        width: 1000px;
        margin: 30px auto;
        background: #fff;
        padding: 20px;
        box-shadow: 0 1px 1px rgba(0,0,0,.05);
    }
    .table-title {
        padding-bottom: 10px;
        margin: 0 0 10px;
    }
    .table-title h2 {
        margin: 6px 0 0;
        font-size: 22px;
    }
    .table-title .add-new {
        float: right;
        height: 30px;
        font-weight: bold;
        font-size: 12px;
        text-shadow: none;
        min-width: 100px;
        border-radius: 50px;
        line-height: 13px;
    }
    .table-title .add-new i {
        margin-right: 4px;
    }
    table.table {
        table-layout: fixed;
    }
    table.table tr th, table.table tr td {
        border-color: #e9e9e9;
    }
    table.table th i {
        font-size: 13px;
        margin: 0 5px;
        cursor: pointer;
    }
    table.table th:last-child {
        width: 100px;
    }
    table.table td a {
        cursor: pointer;
        display: inline-block;
        text-align: center;
        margin: 0 5px;
        min-width: 24px;
    }
    table.table td a.add {
        color: black;
    }
    table.table td a.edit {
        color: #FFC107;
    }
    table.table td a.delete {
        color: #E34724;
    }
    table.table td i {
        font-size: 19px;
    }
    table.table td a.add i {
        font-size: 24px;
        margin-right: -1px;
        position: relative;
        top: 3px;
    }
    table.table .form-control {
        height: 32px;
        line-height: 32px;
        box-shadow: none;
        border-radius: 2px;
    }
    table.table .form-control.error {
        border-color: #f50000;
    }
    table.table td .add {
        display: none;
    }

    .nazad {

    background-color:  #006595 ;
	border-radius:10px;
    border-color: #46b8da;
	display:inline-block;
	color:#ffffff;
	font-family:Arial;
	font-size:12px !important;
    padding: 10px 29px;
    margin: 0px 10px 0px 0px;
	text-decoration:none !important;
    margin-left:10px;
    height: 30px;
    font-weight: bold;
    font-size: 12px;
    text-shadow: none;
    min-width: 90px;
    border-radius: 50px;
    line-height: 13px;
}
.nazad:hover{
    background-color:  #006595 ;
	border-radius:10px;
    border-color: #46b8da;
	display:inline-block;
	color:#ffffff;
	font-family:Arial;
	font-size:12px !important;
    padding: 10px 29px;
    margin: 0px 10px 0px 0px;
	text-decoration:none !important;
    margin-left:10px;
    height: 30px;
    font-weight: bold;
    font-size: 12px;
    text-shadow: none;
    min-width: 90px;
    border-radius: 50px;
    line-height: 13px;
}


       
</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round|Open+Sans">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container" style="width:100%;">
    <h2 style="display:flex; justify-content:center; padding:10px 0px 10px 0px;">Zakonski broj dana godišnjeg odmora</h2>
    <div class="table-wrapper">
        <div class="table-title">
            <div style="display:flex; margin-right:1px; flex-flow: row-reverse;" class="row">

        <a href="/apoteke-app/?m=sifarniciorg&p=all" style="text-decoration:none;"class=" nazad">Nazad</a>   
            <button type="button"  class="btn btn-info add-new ">Dodaj novi</button>

            </div>
        </div>
        <table class="table table-bordered" id="table">

            <thead>
            <tr>
                <th>Entitet</th>
                <th>Broj Dana</th>
                <th>Godina</th>
                <th>Akcije</th>

            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($data as $d){
                echo '<tr>';
                if ($d['region']){
                    echo '<td value="'.$d['id'].'">'.$d['region'].'</td>';
                }
                if ($d['number_of_days']){
                    echo '<td>'.$d['number_of_days'].'</td>';
                }
                if ($d['year']){
                    echo '<td>'.$d['year'].'</td>';
                }
                echo '
                <td style="display: flex;">
                          
                <button type="button" class="table-btn button_color" data-toggle="modal" data-target="#modal1" style="border:none;" onclick="dosomething(this.value)">
  <i class="ion-android-close"></i>
            </button>
            <button  type="button" class="table-btn edit_button button_color" data-toggle="modal" data-target="#myModal" 
            data-contid="'.$d['id'].'"
            data-region="'.$d['region'].'"
            data-days="'.$d['number_of_days'].'"
            data-year="'.$d['year'].'"
            style="border:none;">
  <i class="ion-edit"></i>
            </button>
            
            <!-- Modal for Edit button -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Uredite</h4>
            </div>
            <div id="respons2" class="alert alert-warning">
            Unos već postoji
        </div>
        <div id="respons_valid2" class="alert alert-warning">
            Molimo unesite tačne podatke
        </div>
            <form method="post">
            <input type="hidden" id="contid" value="'.$d['id'].'">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Entitet</label>
                        <input readonly id="region" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="heading">Broj dana</label>
                        <input id="days" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Godina</label>
                        <input readonly id="year" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Zatvori</button>
                    <button type="submit" class="btn btn-primary">Spasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End of Modal for Edit button -->

            <!-- Modal -->
            <div class="modal fade" id="modal1" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                  Da li ste sigurni da želite izbrisati stavku šifarnika?
                  </div>
                  <div class="modal-footer">
                    <button id="btnSubmit" onclick="delAjax('.$d['id'].');"  type="button" class="btn btn-success" data-dismiss="modal"><strong>Da</strong></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><strong>Ne</strong></button>
                  </div>
                </div>
              </div>
            </div>
                </td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
        <div id="respons" class="alert alert-warning">
            Unos već postoji
        </div>
        <div id="respons_valid" class="alert alert-warning">
            Molimo unesite tačne podatke
        </div>

    </div>
</div>


<script>
    $('#respons').hide();
    $('#respons_valid').hide();
    $('#respons2').hide();
    $('#respons_valid2').hide();

    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
        // var actions = $("table td:last-child").html();
        var actions = '<a onclick="addAjax();" class="add button_color" title="" data-toggle="tooltip" data-original-title="Dodajte"><i class="ion-android-add"></i></a>';

        var year = new Date().getFullYear();
        //console.log(year);
        // Append table with add row form on add new button click
        $(".add-new").click(function(){
            $(this).attr("disabled", "disabled");
            var index = $("#table tbody tr:last-child").index();
            var row = '<tr>' +
                '<td><select type="text" class="form-control" name="regija" id="regija">' +
                '<option>FBIH</option>' +
                '<option>BD</option>' +
                '<option>RS</option>' +
                '</select></td>' +
                '<td><input type="text" pattern="\d{1,5}" class="form-control" name="broj_dana" id="broj_dana"></td>' +
                '<td>' +
                '<select type="text" pattern="\d{1,5}" class="form-control" name="godina" id="godina">' +
                '<option>' + year +'</option>' +
                '<option>' + (year+1) + '</option>' +
                '<option>' + (year+2) + '</option>' +
                '<option>' + (year+3) + '</option>' +
                '<option>' + (year+4) + '</option>' +
                '<option>' + (year+5) + '</option>' +
                '<option>' + (year+6) + '</option>' +
                '<option>' + (year+7) + '</option>' +
                '<option>' + (year+8) + '</option>' +
                '<option>' + (year+9) + '</option>' +
                '</select>' +
                '</td>' +
                '<td>' + actions + '</td>' +
                '</tr>';
            $("#table").append(row);
            $("#table tbody tr").eq(index + 1).find(".add, .edit").toggle();
            $('[data-toggle="tooltip"]').tooltip();

        });
        // Add row on add button click
        $(document).on("click", ".add", function(){
            var empty = false;
            var input = $(this).parents("tr").find('input[type="text"]');
            input.each(function(){
                if(!$(this).val()){
                    $(this).addClass("error");
                    empty = true;
                } else{
                    $(this).removeClass("error");
                }
            });

            $(this).parents("tr").find(".error").first().focus();
            if(!empty){
                input.each(function(){
                    $(this).parent("td").html($(this).val());
                });
                $(this).parents("tr").find(".add, .edit").toggle();
                $(".add-new").removeAttr("disabled");
            }
        });
        // Edit row on edit button click
        $(document).on("click", ".edit", function(){
            $(this).parents("tr").find("td:not(:last-child)").each(function(){
                $(this).html('<input type="text" class="form-control" value="' + $(this).text() + '">');
            });
            $(this).parents("tr").find(".add, .edit").toggle();
            $(".add-new").attr("disabled", "disabled");
        });
        // Delete row on delete button click
        $(document).on("click", ".delete", function(){
            $(this).parents("tr").remove();
            $(".add-new").removeAttr("disabled");
        });

        $(document).on('click', '.edit_button', function (){
            $('#respons2').hide();
            $('#respons_valid2').hide();

            let region = $(this).data('region');
            let days = $(this).data('days');
            let year = $(this).data('year');

            $('#region').val(region);
            $('#days').val(days);
            $('#year').val(year);
        });

    });


    function addAjax(){
        $.ajax({
            type: 'POST',
            url: "<?php echo $url . '/modules/sifarniciorg/pages/zakonski_req.php'; ?>",
            data: {type: 'add',
                   region: $('#regija').val(),
                   number_of_days: $('#broj_dana').val(),
                   year: $('#godina').val() },
            success:function (data){
                let response = JSON.parse(data);
                console.log(data);
                if(response == 'duplicate'){
                    $('#table tr:last').remove();
                    $('#respons').show();
                    $('#respons_valid').hide();
                }
                else if (response =='nonnum'){
                    $('#table tr:last').remove();
                    $('#respons_valid').show();
                    $('#respons').hide();
                }
                else{
                    $('#respons').hide();
                    $('#respons_valid').hide();
                    window.location.reload();
                }
            }
        });
    }

    function delAjax(x){
        $.ajax({
            type: 'POST',
            url: "<?php echo $url . '/modules/sifarniciorg/pages/zakonski_req.php'; ?>",
            data: { type: 'del',
                    id: x},
            success:function (data){
                let response = JSON.parse(data);
                console.log(response);
                window.location.reload();
            }
        });
    }

    $('form').on('submit', function (e){
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: "<?php echo $url . '/modules/sifarniciorg/pages/zakonski_req.php'; ?>",
            data: {type: 'edit',
                id: $('#contid').val(),
                region: $('#region').val(),
                number_of_days: $('#days').val(),
                year: $('#year').val() },
            success:function (data){
                let response = JSON.parse(data);
                console.log(data);
                if(response == 'duplicate'){
                    $('#respons2').show();
                    $('#respons_valid2').hide();
                }
                else if (response =='nonnum'){
                    $('#respons_valid2').show();
                    $('#respons2').hide();
                }
                else{
                    $('#respons').hide();
                    $('#respons_valid2').hide();
                    window.location.reload();
                }
            }
        });

    });


</script>
</body>
</html>
