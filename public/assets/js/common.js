$(document).ready(function() {

    var table = $('#example').DataTable();

    $('.chosen_select_left').each(function(i, obj) {
        var attrName = $(obj).find(":selected").text();
        var number = $(obj).data('number');
        var value = $(obj).data('value-'+number);
        var temp = new Array();
        if (typeof(value) != "undefined"){ temp = value.split(","); }
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
                    var optionString = '';
                    for(i = 0; employeeData.length >i; i++){
              
                        $.each(temp, function(key, value) { 
                            
                            if(value == employeeData[i]){
                                selected = 'selected';
                                return false;
                            }else{
                                selected = '';
                            } 
                        });
                            optionString += "<option "+selected+" >"+employeeData[i]+"</option>";
                    
                    }
                    var className = '#select-right-' + number;
                    $(className).append( optionString).trigger("chosen:updated");
                }
            });
       
    });
//lay ve het cac select hien tai
//for cac select -> lay ve option dang duoc chon -> 


 
    $('.chosen_select_right').on('change', function(evt, params) {
        
         var number = $(this).closest('form').find('.top_right_attName .chosen_select_left').attr('data-number');
         if(params.selected == 'all'){
            $('.chosen_select_right#select-right-'+number+' option').removeAttr('selected');
            $(this).val('all');
            $('.chosen_select_right#select-right-'+number).trigger('chosen:updated');
         }else{
            $('.chosen_select_right#select-right-'+number+' option.all').removeAttr('selected');
            $('.chosen_select_right#select-right-'+number).trigger('chosen:updated');
         }
     

    });

    $('.chosen_select_left').change(function(){

        x = $('.style-update .chosen_select_left option:selected');
        var number = $(this).data('number');

        $('#loading').css('display','block');
        $(".chosen_select_right#select-right-"+number+" option").remove();
        selected = $(this).val();
        form_action = $(this).closest('.style-update').length;
        exist = 0;

        if (form_action == 1 ) {
            // console.log(selected);
            arr_left = [];
             for(i = 0; x.length >i; i++){

                if($.inArray($(x[i]).val(), arr_left) != -1){
                    exist = 1;
                    break;
                }
                arr_left.push($(x[i]).val());
               
            }
        }else{

            for(i = 0; x.length >i; i++){
                if(selected == $(x[i]).val()){
                    exist = 1;
                    break;
                }
            }
        }
        if (exist == 1) {
            alert ('Style already exists ');
            $('.chosen_select_right#select-right-'+number+' option.all').removeAttr('selected');
            $('.chosen_select_right#select-right-'+number).trigger('chosen:updated');
        }else{
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
                    var optionString = '<option class="all" value="all">All</option>';

                    for(i = 0; employeeData.length >i; i++){
                        optionString += "<option>"+employeeData[i]+"</option>";
                    }
                    $(className).append( optionString ).trigger("chosen:updated");
                }
            }); 
        }
    });

 $(function () {
        $('form#update-style').bind('submit', function () {
            form = $(this).serialize();
            // action = $('input[name="update-style"]').val();
            $.ajax({
            type: 'post',
            url: '/style/defination',
            data : {
                'form' : form,
                // 'action' : action
            },
            success: function (result) {
                $('#df-notice').fadeIn(200).fadeOut(3000);
                console.log(result);
              // if(result == 1){
              //   // alert('Created');
              //   location.reload();
              // }else{
              //   alert('has error');
              // }
              
            }
          });
            return false;
        });
    });




    //form add new 
    $(function () {
        $('form#add-new').bind('submit', function () {
            form = $(this).serialize();
            $.ajax({
            type: 'post',
            url: '/style/styledefination',
            data : {
                'form' : form,
            },
            success: function (result) {
              if(result == 1){
                // alert('Created');
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
        $('form.style-update').bind('submit', function (event) {
            var number = $(this).closest('form').find('.top_right_attName .chosen_select_left').attr('data-number');
            var action = $(this).find("input[type=submit]:focus").attr('data-action');
            var form = $(this).serialize();
            if(action == 'update'){
                $.ajax({
                    type: 'post',
                    url: '/style/updatestyledefination',
                    data : {
                        'form' : form,
                    },
                    success: function (result) {
                        if(result == 1){
                            $('#style-update-'+number+' .df-item-update-success').fadeIn(200).fadeOut(3000);
                        }else{
                            $('#style-update-'+number+' .df-item-update-error').fadeIn(200).fadeOut(3000);
                        }
                        
                        console.log(result);
                    // if(result == 1){
                    //     // alert('Update Success');
                    //     location.reload();
                    // }else{
                    //     alert('has error');
                    // }
                    
                    }
                  });
            }else if(action == 'delete'){
               
                 $.ajax({
                    type: 'post',
                    url: '/style/deletestyledefination',
                    data : {
                        'form' : form,
                    },
                    success: function (result) {
                        if (result == 1) {
                            $('#style-update-'+number).fadeOut(2000);
                        }
                    }
                  });


            }
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
    // $('[data-delete='+del_style+']').parents('tr').remove();
    table.row( $('[data-delete='+del_style+']').parents('tr')).remove().draw();
    // alert(del_style);
    $.ajax({
        url: "style/deletestyle",
        type: "POST",
        data: { del_style : del_style },
        dataType: "test",
        success: function ()
        {
              // alert(result);
            // $('[data-delete='+del_style+']').parents('tr').remove();
        }
    });
    // location.reload();
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



