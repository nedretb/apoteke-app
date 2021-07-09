$( document ).ready(function() {
    $('#respons').hide();
    $('#respons2').hide();
    $('.js-example-basic-multiple2').select2();
    $('.js-example-basic-multiple').select2();

    let pp = window.location.href;
    console.log(pp.substr(pp.length-1));

    if (pp.substr(pp.length-1) == 1 ){
        console.log('yes');
        $('#tabs-1').removeClass('ht-active');
        $('#tabs-2').addClass('ht-active');
    }
    else {
        //console.log('no');
    }
});


$('#emp_no').on('change', function (){
    //console.log($('#emp_no').val());

    $.ajax({
        type: 'POST',
        url: "/apoteke-app/modules/work_booklet/pages/save_current_exp.php",
        data: { type: 'POST',
            tip: 'get_name',
            employee_no: $('#emp_no').val()
        },
        success: function (r) {
            //let response = JSON.parse(r);
            //console.log(r);
            //console.log($('#emp_name').html(r));
            $('#emp_name').val(r);
            $('#emp_name').select2();
        }
    })
});

$('#emp_name').on('change', function (){
    //console.log($('#emp_name').val());

    $.ajax({
        type: 'POST',
        url: "/apoteke-app/modules/work_booklet/pages/save_current_exp.php",
        data: { type: 'POST',
            tip: 'get_emp_no',
            employee_no: $('#emp_name').val()
        },
        success: function (r) {
            let response = JSON.parse(r);
            //console.log(response);
            //console.log($('#emp_name').html(r));
            $('#emp_no').val(response);
            $('#emp_no').select2();
        }
    })
});

$('form').on('submit', function (e){
    e.preventDefault();

    var today = new Date();
    if (Date.parse($('#dateFrom').val()) > Date.parse($('#dateTo').val()) || Date.parse($('#dateTo').val()) > Date.parse(today) || Date.parse($('#dateFrom').val()) > Date.parse(today)){
        //console.log('nemoze');
        $('#respons').show();
    }
    else{

        //$('#respons').hide();
        $.ajax({
            type: 'POST',
            url: "/apoteke-app/modules/work_booklet/pages/save_current_exp.php",
            data: { type: 'POST',
                tip: 'save',
                id: $('#data_id').val(),
                employee_no: $('#emp_no').val(),
                employee_name: $('#emp_name').val(),
                employer: $('#employer').val(),
                dateFrom: $('#dateFrom').val(),
                dateTo: $('#dateTo').val(),
                coefficient: $('#coefficient').val(),
                dc: $('#dc').val(),
                invalid: $('#invalid_select').val(),
                invalidity_category: $('#invalid_category').val()
            },
            success: function (r) {
                //window.location.replace('?m=work_booklet&p=add-new&edit=' + $('#emp_no').val());

                let response = JSON.parse(r);
                if (response == 'double_date'){
                    console.log('wdadd');
                    $('#respons').show();
                }
                else{
                    //$("#alert-success").show();
                    $('#respons').hide();
                    // window.location.replace('?m=work_booklet&p=add-new&edit=' + $('#emp_no').val());
                }


            }
        })
    }


});

$('#archive').on('click', function (){
    $.ajax({
        type: 'POST',
        url: "/apoteke-app/modules/work_booklet/pages/save_current_exp.php",
        data: { type: 'POST',
            tip: 'archive',
            id: $('#data_id').val(),
            employee_no: $('#emp_no').val(),
            employee_name: $('#emp_name').val(),
            employer: $('#employer').val(),
            dateFrom: $('#dateFrom').val(),
            dateTo: $('#dateTo').val(),
            coefficient: $('#coefficient').val(),
            dc: $('#dc').val(),
            invalid: $('#invalid_select').val(),
            invalidity_category: $('#invalid_category').val()
        },
        success: function (r) {
            let response = JSON.parse(r);
            if (response == 'duplicate'){
                console.log('wdadd');
                $('#respons2').show();
            }
            else{
                $("#alert-success").show();
                window.location.replace('?m=work_booklet&p=all');
            }


        }
    })

});

if($('#emp_no').val()){
    $("#add_new").attr("href", "?m=work_booklet&p=add_work_experience&new="+$('#emp_no').val());
}

$('#emp_no').on('change', function (){
    $("#add_new").attr("href", "?m=work_booklet&p=add_work_experience&new="+$('#emp_no').val());
});

$('#dateFrom').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    //startDate: startDate,
    //endDate: new Date(year + '/12/31')
});

$('#dateTo').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    //startDate: $("#dateFrom").val(),
    //endDate: new Date(year + '/12/31')
});


$('#invalidity_category').hide();

$('#invalid').on('change', function (){

    console.log($('#invalid_select').val());
    if ($('#invalid_select').val() =="DA"){
        $('#invalidity_category').show();
    }
    else{
        $('#invalidity_category').hide();
    }
});

$(".sh-click").click(function () {
    let id = $(this).attr('val');
    $(".hidden-tabs").removeClass('ht-active');

    $("#"+id).addClass('ht-active');
});

// $( function() {
//     $( "#tabs" ).tabs();
// } );
