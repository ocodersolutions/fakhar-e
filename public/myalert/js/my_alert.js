   //custom show alert
    var myalert = function(modalID, type,title,paragraph,btn_text,sub_title,callback){
    	


    	if( $('#myModalContainer #'+modalID).length == 0 )  {
    		
//	    	$('#myModalContainer').append(
//	    			$($('#myModalTemplate').html()).attr('id', modalID)
//	    	);

    		$($('#myModalTemplate').html()).attr('id', modalID).appendTo( $('#myModalContainer') );
    		
    		
    	}

        getstyle(modalID,type,title,paragraph,btn_text,sub_title,callback);
       
    }; 

    function getstyle(modalID,type,title,paragraph,btn_text,sub_title,callback){
        $("#"+modalID).removeClass('type_remove type_success type_missing type_cancel type_thankyou');
        $("#"+modalID).addClass(type);
        $(".my_title").text(title);

        if(sub_title !=null ){
            $("#"+modalID + " .sub_title").text(sub_title);
        }

        $("#"+modalID + " .paragraph").text(paragraph);
        if(btn_text == '' || btn_text==null) {
            btn_text = 'OK';
        }
        $("#"+modalID + " .my_btn_type").text(btn_text);
        
        if(type == 'type_remove') {
            if ($("#"+modalID + " .my_cancel").hasClass('my_visibility')) {
                $("#"+modalID + " .my_cancel").removeClass('my_visibility');
            }
        }
        else {
            $("#"+modalID + " .my_cancel").addClass('my_visibility');
        }
        

        $("#"+modalID).foundation('reveal','open',{});

           
           
          if( typeof callback === 'function'  ) {
        	  
              $(document).on('opened.fndtn.reveal', '#'+modalID, function () {
            	  //alert('OK2')
            	  $('#'+modalID + " button.my_btn_type").unbind( "click" );
                  $('#'+modalID + " button.my_btn_type").click(function(){
                    $(".my_venue_alert").css("display", "none");
                  });
              	  $('#'+modalID + " button.my_btn_type").click(callback);
              	
                  $('#'+modalID + " .my_cancel").click(function(){
                	  $('#'+modalID).foundation('reveal','close'); 
                  });   
                  $('#'+modalID + " .my_delete_yes").click(function(){
                    $(".my_cancel, .my_btn").css("display", "block");
                    $(".my_delete_yes, .my_delete_no").css("display", "none");
                    $('#'+modalID).foundation('reveal','close'); 
                  });
                  $('#'+modalID + " .my_delete_no").click(function(){
                    $(".my_cancel, .my_btn").css("display", "block");
                    $(".my_delete_yes, .my_delete_no").css("display", "none");
                    $('#'+modalID).foundation('reveal','close'); 
                  });    

                  $(document).keyup(function(e) {
                    if (e.keyCode == 27) 
                    { 
                        $(".my_venue_alert").css("display", "none");
                    }
                  }); 

                  $(".my_wrap_close").click(function(){
                    $(".my_venue_alert").css("display", "none");
                  });            	
              });        	  
        	  
          }          
          
          $('.receive_alert_bx_lbl').unbind( "click" );
          $('.receive_alert_bx_lbl').click(function(){ 
          	$('.receive_alert_bx').prop( "checked", !$('.receive_alert_bx').prop("checked") )
          	
          });          
         
        //  jQ6('#myModal').reveal({
        //      animation: 'fadeAndPop',                   //fade, fadeAndPop, none
        //      animationspeed: 300,                       //how fast animtions are
        //      closeonbackgroundclick: true,              //if you click background will modal close?
        //      dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
        // });
         return false;
    }