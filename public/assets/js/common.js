$(document).ready(function() {

$('.chosen_select_left').change(function(){
    $('#loading').css('display','block');
    $(".chosen_select_right option").remove();
    selected = $(this).val();
    $.ajax({
        url : "/style/getAttributeValue",
        type : "post",
        dataType:"text",
        data : {
            'selected' : selected,
        },
        success: function (result){
            $('#loading').css('display','none');
            var employeeData = JSON.parse(result);
            for(i = 0; employeeData.length >i; i++){
                $('.chosen_select_right').append( "<option>"+employeeData[i]+"</option>" ).trigger("chosen:updated");
            }
        }
    });
});

    $(function () {
        $('form#form-defination').bind('submit', function () {
          $.ajax({
            type: 'post',
            url: '/style/styledefination',
            data: $('form#form-defination').serialize(),
            success: function (result) {
              if(result == 1){
                console.log($('#form-defination').context);
                // $('#top_right .row').append($('#form-defination').html());
              }
            }
          });
          return false;
        });
    });





    $(".chosen_select_left").chosen();
    $(".chosen_select_right").chosen(); 

    $('#idNewsLetter').click(function() { 
        
    	var $email = $('#idNewsLetterInput'); //change form to id or containment selector
    	var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
    	if ($email.val() == '' || !re.test($email.val())) 
        {
    		myalert('alert_idNewsLetterInput3','type_missing','Ooops!','','OK','Please enter valid email address.',function(){$('#alert_idNewsLetterInput3').foundation('reveal','close'); });
        }
        else 
        {
            $("#loading-div-background").show();
            $.ajax({
                url: "/index/newsletter",
                type: 'POST',
                data: { newsLetterInput: $('#idNewsLetterInput').val()  },
                dataType: 'json',
                success: function (result) 
                { 
                    // alert(result.status);
                    if (result.status == 'success') 
                    { 
                    	$('#idNewsLetterInput').val('');
                    	myalert('alert_idNewsLetterInput1','type_success','Success','','OK','You have been successfully added to our newsletter.',function(){$('#alert_idNewsLetterInput1').foundation('reveal','close'); });
                    } 
                    else if (result.status == 'success1') 
                    {
                        myalert('alert_idNewsLetterInput3','type_missing','Ooops!','','OK','This email is already registered.',function(){$('#alert_idNewsLetterInput3').foundation('reveal','close'); });
                    }
                    else 
                    {
                    	 myalert('alert_idNewsLetterInput2', 'type_cancel','Internal Server Error', 'Please contact site administrator.','','',function(){$('#alert_idNewsLetterInput2').foundation('reveal','close'); });
                    }
                     $("#loading-div-background").hide();
                }
            });                       	   
        }

    });   

    $(".no_delete_style").click(function(){
        $(".close").click();
    }); 

    $(".delete_style").click(function(){
        $del_style = $(this).attr("data-delete");
        $.ajax({
            url: "style/deletestyle",
            type: "POST",
            data: { del_style : $del_style },
            dataType: "html",
            success: function ()
            {
                  // alert(result);
            }
        });
    });

    $(".sb_create_style").click(function(){
        $(this).parents(".style_page").find("#top_left").animate({ left: "+=50", height: "toggle" }, 1000, function() {});
    });

});

$(document).on("click",".btn_delete_style", function(){
    var x = $(this).attr("data-delete");
    $('.delete_style').attr("data-delete",x);
});

// $(document).on( 'click', '.delete_style', function () {
//         table.row( $(".btn_delete_style").parents('tr') ).remove().draw();
//     } );