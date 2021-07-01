$( document ).ready(function() {
    $( ".search-input" ).keyup(function() {
        let value = $(this).val();
        value = value.toLowerCase();

        // Show or hide wrapper
        if(value === '') $(this).parent().find(".all-elements-wrapper").fadeOut();
        else $(this).parent().find(".all-elements-wrapper").fadeIn();


        $(this).parent().find(".all-elements-wrapper").children(".single-element").each(function () {
            let justValue = $(this).text().toLowerCase();
            let result = justValue.search(value);
            if(result !== -1) $(this).fadeIn(0);
            else $(this).fadeOut(0);
            //console.log($(this).html());
        });
    });


    // Click on single element
    $(".single-element-for-click").click(function () {
        let checkInput = $(this).parent().parent().children("input");
        let ID = $(this).attr('idValue');   // ID of Value
        let value = $(this).text();         // Value of Value

        if(checkInput.length == 1){
            $(this).parent().parent().append(
                '<input type="hidden" name="' + $(this).parent().parent().children("input").attr('name') + '-id" value="' + ID + '">'
            );
        }

        let index = 0;
        $(this).parent().parent().children("input").each(function () {
            if(!index++){
                $(this).val(value);
            }else{
                $(this).val(ID);
            }
        });

        $(this).parent().fadeOut();
    });


    // Check kompetentions
    $(".check-class").click(function () {
        let value = $(this).is(":checked");
        let check_attr_id = $(this).attr('selectedId');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type:'POST',
            url: '?m=performance_management&p=mbo_new',
            data: {check_attr_id : check_attr_id, value : value },
            success:function(data){
                location.reload();
            }
        });
    });

    $(".check-user-list").click(function () {
        let value = $(this).is(":checked");
        let check_attr_id = $(this).attr('selectedId');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type:'POST',
            url: '?m=performance_management&p=mbo_lista_usera_add',
            data: {check_attr_id : check_attr_id, value : value },
            success:function(data){
                // location.reload();
            }
        });
    });
});
