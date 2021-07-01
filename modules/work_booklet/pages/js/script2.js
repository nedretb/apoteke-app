
$( document ).ready(function() {
    let enddate = $('#endate').val();

    $('#dateFrom').datepicker({
        todayBtn: "linked",
        format: 'dd.mm.yyyy',
        language: 'bs',
        //endDate: enddate
    });
    $('#dateToto').datepicker({
        todayBtn: "linked",
        format: 'dd.mm.yyyy',
        language: 'bs',
        //endDate: enddate
    });
});

$('#respons').hide();

if($('#emp_no').val()){
    $("#add_new").attr("href", "?m=work_booklet&p=add_work_experience&new="+$('#emp_no').val());
}

$('#emp_no').on('change', function (){
    $("#add_new").attr("href", "?m=work_booklet&p=add_work_experience&new="+$('#emp_no').val());
    console.log("?m=work_booklet&p=add_work_experience&new="+$('#emp_no').val());
});

$('form').on('submit', function (e){
    e.preventDefault();

    document.addEventListener("DOMContentLoaded", function() {
        var elements = document.getElementsByTagName("INPUT");
        for (var i = 0; i < elements.length; i++) {
            elements[i].oninvalid = function(e) {
                e.target.setCustomValidity("");
                if (!e.target.validity.valid) {
                    e.target.setCustomValidity("Unesite tačne podatke");
                }
            };
            elements[i].oninput = function(e) {
                e.target.setCustomValidity("");
            };
        }
    })

    var today = new Date();
    if (Date.parse($('#dateFrom').val()) > Date.parse($('#dateToto').val()) || Date.parse($('#dateTo').val()) > Date.parse(today) || Date.parse($('#dateFrom').val()) > Date.parse(today)){
        console.log('nemoze');
        $('#respons').show();
    }
    else{
        let edit_save = "";
        if($('#edit').val() !=0){
            edit_save = 'edit';
        }
        else {
            edit_save = 'save';
        }
        $('#respons').hide();
        $.ajax({
            type: 'POST',
            url: "/apoteke-app/modules/work_booklet/pages/save_work_experience.php",
            data: { type: 'POST',
                tip: edit_save,
                id: $('#edit').val(),
                employee_no: $('#emp_no').val(),
                employee_name: $('#emp_name').val(),
                employer: $('#employer').val(),
                dateFrom: $('#dateFrom').val(),
                dateTo: $('#dateToto').val(),
                coefficient: $('#coefficient').val(),
                exp_y: $('#exp_y').val(),
                exp_m: $('#exp_m').val(),
                exp_d: $('#exp_d').val(),
            },
            success: function (r) {
                $("#alert-success").show();
                let response = JSON.parse(r);
                console.log(JSON.parse(r));
                if(response == "double_date"){
                    $('#respons').show();
                    console.log($('#employer').val());
                }
                else{
                    $('#respons').hide();
                    //console.log('?m=work_booklet&p=add-new&edit=' + $('#emp_no').val());
                    // window.location.replace('?m=work_booklet&p=add-new&edit=' + $('#emp_no').val()+'&pp=1');
                    //window.location.reload('Location: ?m=work_booklet&p=count_go3');
                    console.log($('#employer').val());
                    if($('#employer').val() == 'MKT'){
                        window.location.replace('?m=work_booklet&p=add-new&edit=' + $('#emp_no').val()+'&pp=0');
                    }else {
                        window.location.replace('?m=work_booklet&p=add-new&edit=' + $('#emp_no').val()+'&pp=1');
                    }
                }
            }
        })
    }


});


