$( document ).ready(function() {
	
var getfeeddetailEvent = function(){
	 
    feeddataid = $(this).data('value');
    url = '/service/getfeeddetail';
      $.ajax({
        url: url,
        type: 'POST',
        data:  {
            feeddataid:feeddataid
        },
        dataType:'html',
          beforeSend: function() {
              $("#loading-div-background").show(); 
           },
        success: function (result) {
           //console.log(result);
           $("#myModal .append").html(result);
           $("#loading-div-background").hide();
           $('#myModal').modal('show');
           // console.log( $(".modal").width() );
           // console.log( $(".container").width() );
           $('.continueFixAtBottom').css({
        	      top: $(".modal").scrollTop()+$(".modal").height()-170,
        	        //left:  ($(".modal").width()-$('.container').width()-18)/2,
        	        left: 0,
        	  });
           $('.my_tooltip').tooltipster({
                position: 'top',
                theme: 'tooltipster-shadow',
            });
        },
        error:function(){
            alert("Have some error");
        }
    });
      
    
}


 $(".discover").on('click', getfeeddetailEvent);
 $(document).on('click','.discover', getfeeddetailEvent)
 
 
 var backButtonHideModelEvent = function(event){
	 event.preventDefault();
	 $('.outerBoxContainer').show();
	 $('#myModal').modal('hide');
 }
 $(".backButtonHideModel").on('click', backButtonHideModelEvent);
 $(document).on('click','.backButtonHideModel', backButtonHideModelEvent)
 
 
 
 
	 
	var gotourlEvent = function (event) {
		 console.log('OK');
		 event.preventDefault();
		 window.open($(this).attr('href'), '_blank');                	
	}  
	 var gotourlEvent2 = function (event) {
		 console.log('OK2');
		 event.preventDefault();
		 window.open($(this).attr('href'), '_blank');                	
	}    
	$('.gotourl').on('click', gotourlEvent2); 
	$(document).on('click','.gotourl', gotourlEvent);  
		
	
	
	$( ".modal" ).scroll(function() {
		$('.continueFixAtBottom').css({
		      top: $(".modal").scrollTop()+$(".modal").height()-170,
		        //left:  ($(".modal").width()-$('.container').width()-18)/2,
		      left: 0,
		  })
	});
 
	function sharethisFunc(event){ 
		event.preventDefault();
		
//		if( $(this).attr('href').indexOf("facebook") >= 0 ) {
//			that = this
//			$.post("https://graph.facebook.com/?id="+encodeURIComponent($(this).attr('href').replace('https://api.addthis.com/oexchange/0.8/forward/facebook/offer?url=',''))+"&scrape=true",
//				    function(contents){
//			});
//			$.post("https://graph.facebook.com/?id="+encodeURIComponent($(this).attr('href').replace('https://api.addthis.com/oexchange/0.8/forward/facebook/offer?url=',''))+"&scrape=true",
//				    function(contents){
//			});			
//		}
		window.open ($(this).attr('href'), "popup", "width="+$('.container').width()+", height=600, left="+($(".modal").width()-$('.container').width()-18)/2);
		
	}
	$('.sharethis').click('click', sharethisFunc);
	$(document).on('click', '.sharethis', sharethisFunc);	

});






