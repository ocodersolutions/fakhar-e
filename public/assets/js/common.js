$(document).ready(function() {

$('.chosen_select_left').each(function(i, obj) {
    var attrName = $(obj).find(":selected").text();
    var number = $(obj).data('number');
    selected = $.trim(attrName);
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
                var className = '#select-right-' + number;
                $(className).append( "<option>"+employeeData[i]+"</option>" ).trigger("chosen:updated");
            }
        }
    });
});
//lay ve het cac select hien tai
//for cac select -> lay ve option dang duoc chon -> 

$('.chosen_select_left').change(function(){
    var number = $(this).data('number');
    
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
            var className = '#select-right-' + number;
            for(i = 0; employeeData.length >i; i++){
                $(className).append( "<option>"+employeeData[i]+"</option>" ).trigger("chosen:updated");
            }
        }

    });
});
//form add new 
    $(function () {
        $('form#add-new').bind('submit', function () {
          $.ajax({
            type: 'post',
            url: '/style/styledefination',
            data: $('form').serialize(),
            success: function (result) {

              if(result == 1){
                alert('Created');
                location.reload();
              }else{
                alert('has error');
              }
            }
          });
          return false;
        });
    });

//form update 
$(function () {
    $('form.style-update').bind('submit', function () {
        $.ajax({
            type: 'post',
            url: '/style/updatestyledefination',
            data: $(this).serialize(),
            success: function (result) {
               if(result == 1){
                alert('Update Success');
                location.reload();
              }else{
                alert('has error');
              }
          
            }
          });
          return false;
        });
    });



// function myFunction(){
//     alert(12111);
// }

// $(".add_attr").click(function() {
    


// var y = document.getElementById('form-defination');
// console.log(y.innerHTML);
// $('#kaka').append(y.innerHTML);
   
//       return false; 

//          });

  

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

    // $(".my_delete_yes").click(function(){
    //     $del_style = $(this).attr("data-delete");
    //     alert($del_style);
    //     $.ajax({
    //         url: "style/deletestyle",
    //         type: "POST",
    //         data: { del_style : $del_style },
    //         dataType: "html",
    //         success: function ()
    //         {
    //               // alert(result);
    //         }
    //     });
    // });

    $(".sb_create_style").click(function(){
        $(this).parents(".style_page").find("#top_left").animate({ left: "+=50", height: "toggle" }, 1000, function() {});
    });

});

$(document).on("click", ".my_delete_yes", function(){
    del_style = $(this).attr("data-delete2");
    $('[data-delete='+del_style+']').parents('tr').remove();
    // alert(del_style);
    $.ajax({
        url: "style/deletestyle",
        type: "POST",
        data: { del_style : del_style },
        // dataType: "html",
        success: function (html)
        {
              // alert(result);
            // $('[data-delete='+del_style+']').parents('tr').remove();
        }
    });
    location.reload();
}); 

$(document).on("click",".btn_delete_style", function(e){
    e.preventDefault();
    $(".my_cancel, .my_btn").css("display", "none");
    $(".my_delete_yes, .my_delete_no").css("display", "block");
    var x = $(this).attr("data-delete");
    $('.my_delete_yes').attr("data-delete2",x);
    // var that = $(this),
    // tableRow = that.parents('.test_id');
    // alert(tableRow.index());
    // $(".my_delete_yes").attr("data-index",tableRow.index());
    myalert('alert_idNewsLetterInput3','type_remove','Ooops!','','OK','Do you want to delete or not?',function(){$('#alert_idNewsLetterInput3').foundation('reveal','close'); });
});
$(document).on( 'click', '.delete_style', function () {
        table.row( $(".btn_delete_style").parents('tr') ).remove().draw();
    } );