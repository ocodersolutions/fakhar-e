// var button;
// var userInfo;
window.fbAsyncInit = function() {
	
	var 													FBappId = '678546158918666'; // main domain - elnove.com / m.
	if($(location).attr('host')=='dev.elnove.com') 			FBappId = '921413221278386';
	else if($(location).attr('host')=='staging.elnove.com') 	FBappId = '1430879530553959';
	//else if($(location).attr('host')=='trung.elnove.com') 	FBappId = '678551988918083';
	else if($(location).attr('host')=='trung.elnove.com') 	FBappId = '197600357248915';
	else if($(location).attr('host')=='devmobile.elnove.com') 	FBappId = '1700806690132207';
	else if($(location).attr('host')=='m.lnove.local') 	FBappId = '649739978501638';
	else if($(location).attr('host')=='lnove.local') 	FBappId = '1238074509543418';
	else if($(location).attr('host')=='s.m.elnove.com') 	FBappId = '420904578119853';
	console.log(FBappId)
    FB.init({ 
        appId: FBappId,
        status: true, 
        cookie: true,
        xfbml: true,
        oauth: true,
        version    : 'v2.5'});
    // run once with current status and whenever the status changes
};

 $('.fb-login').click(function(event) {
 	event.preventDefault();
 	//console.log('ok');
     FB.getLoginStatus(updateButton);
     //FB.Event.subscribe('auth.statusChange', updateButton);
 });
	
  function updateButton(response) { console.log(response)
     button       =   document.getElementById('fb-auth');
     //userInfo     =   document.getElementById('user-info');
     if (response.authResponse) 
     {
         //user is already logged in and connected
         FB.api('/me', function(info) {
             login(response, info);
         });
         FB.logout(function(response) {
             logout(response);
         });
     } 
     else 
     {
         //user is not connected to your app or logged out
         button.innerHTML = 'Login';
         FB.login(function(response) {
             if (response.authResponse) {
                 FB.api('/me', function(info) {
                     login(response, info);
                 });	   
             } else {
                 //user cancelled login or did not grant authorization
             }
         }, {scope:'public_profile,email,user_birthday,read_stream'});
     }
 }

 (function() {
     var e = document.createElement('script'); e.async = true;
     e.src = document.location.protocol 
         + '//connect.facebook.net/en_US/all.js';
     document.getElementById('fb-root').appendChild(e);
 }());

 function login(response, info)
 {
	 console.log(response);
 	console.log(info); 
// 	if( 
// 			typeof info.email == undefined || !info.email || info.email == '' || 
// 			typeof info.birthday == undefined || !info.birthday || info.birthday == ''
// 	) {
// 		$('#firstName').val( info.first_name );
// 		$('#lastName').val( info.last_name );
// 		if( !(typeof info.email == undefined || !info.email || info.email == '') ) {
// 			$('#email').val( info.email );
// 		}
// 		if( !(typeof info.birthday == undefined || !info.birthday || info.birthday == '') ) {
// 			var dob = info.birthday.split("/");
// 			$('#suMonth').val( dob[0] );
// 			$('#suDay').val( dob[1] );
// 			$('#suYear').val( dob[2] );
// 		}
//		
//         myalert('myalertSignup1', 'type_missing','Oops', 'We were not able to pull all the required information, please provide the needed information and submit.','');
//         $(document).on('opened.fndtn.reveal', '[data-reveal]', function () {
//                $("button.my_btn_ok").click(function(){
//                    $('a.close-reveal-modal').trigger('click');
//                    });
//                }); 		
//		
// 		return false;
// 	}
//	
 	console.log('here 1'); 
     if (response.authResponse) 
     {
         var accessToken = response.authResponse.accessToken;
         var action_name = $('.fb-login').attr('class');
         action_name = action_name.split(' ');
         action_name = action_name[1];
        	var url='/'; 
        	console.log('here 2');
         $.ajax({
             url: url+'auth/fb-login',
             type: 'post',
             dataType: 'json',
             data: {action: action_name, email : info.email, firstName: info.first_name, lastName: info.last_name, dob: info.birthday},
             success: function(msg) {
                 if(msg.status == 'fail')
                 {
                     alert('You have not registerd. Please register first.');
                     window.location.href=url+"profile/";
                     return false;
                 }
                 else if(msg.status == 'success')
                 {
                     window.location.href=url+"service/";
                     return false;
                 }
             } 
         });
     }
 }

 function logout(response)
 {
     //userInfo.innerHTML                             =   "";
     //document.getElementById('debug').innerHTML     =   "";
     //document.getElementById('other').style.display =   "none";
 }
