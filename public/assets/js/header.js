var flag = false;
// get url of page
var pathname = $(location).attr('href');
var pathurl = window.location.pathname;   
var windowHeight = $(window).height();
var bodyHeight = $('body').height();    
$(document).ready(function(){
    $('html, body').animate({scrollTop : 0},800);
    if (pathname.indexOf("/profile") >0){
            $("#show-hide-menu").addClass('angleplus');
            flag = true;
        }
    // hide "#search" when document ready
    $("#search").hide();

    var submitIcon = $('.searchbox-icon');
    var inputBox = $('.searchbox-input');
    var searchBox = $('.searchbox');
    var isOpen = false;
    // when click '.searchbox-icon' then check if '.searchbox-input' is empty then event default, if "isOpen" equal "false" then '.searchbox' add class 'searchbox-open' and focus '.searchbox-input' and value of "isOpen" is true and change attribute of "#search", else then counterwork
    submitIcon.click(function(e){
        if(inputBox.val()=="")
        {
            e.preventDefault();
        }
        if(isOpen == false)
        {
            searchBox.addClass('searchbox-open');
            inputBox.focus();
            isOpen = true;
             $("#search").css("z-index","99")            
        } else 
        {
            searchBox.removeClass('searchbox-open');
            inputBox.focusout();
            isOpen = false;
            $("#search").removeAttr("style");
            //$("#show-hide-menu").click();
        }         
    });  
    submitIcon.mouseup(function(){
        return false;
    });
    searchBox.mouseup(function(){
        return false;
    });
    $(document).mouseup(function(){
        if(isOpen == true)
        {                
            submitIcon.click();
        }
    }); 

    //show popup full screen when click in object
    $("#pop-menu").fullScreenPopup({
        bgColor: '#fff'
    });
    $("#refine-search-popup").fullScreenPopup({
        bgColor: '#fff'
    });
    $("#sort-item-popup").fullScreenPopup({
        bgColor: '#fff'
    });
    // before popup set width fore tittle tag and set margin
    $("#refine-search-popup,#pop-menu,#sort-item-popup").click(function(){
        
        $("#popup ul,#sis-content-popup ul").css("margin-top","70px");
        var w = jQuery(".fsp-content").width();
        $("p.m-title").css('width',w);

    });

    var icons = {
    header: "ui-icon-circle-arrow-e",
    activeHeader: "ui-icon-circle-arrow-s"
    };
    // show menu khi click "#accordion", "#accordion-m"
    $( "#accordion" ).accordion({
        icons: icons,
        heightStyle:"content",
        collapsible: true,
        active: false
    });
    $( "#accordion-m" ).accordion({
        icons: icons,
        heightStyle:"content",
        collapsible: true,
        active: false
    });
    // back to top
    $('#click_back_to_top').on('click',function(){
        $('html, body').animate({scrollTop : 0},800);
        return false;
    });

   // hide menu and footer in 3 page style , styleDefination and style
    
    //pathArr = ("/style","/style/defination","/venue");
   //alert(pathname);
   if ((pathname.indexOf("/venue") > 0) ||(pathname.indexOf("/style") > 0)) {
    $("nav#menu").hide();
    $("footer").hide();
   }
});

function buttonUp(){
    var inputVal = $('.searchbox-input').val();
    inputVal = $.trim(inputVal).length;
    if( inputVal == 0){
         $('.searchbox-input').val('');
         
    } 
}


    //show or hide '.searchbox-icon' when click "#show-hide-menu" or '.searchbox-icon' (sub-page)
$("#show-hide-menu").click(function(){
    $("#show-hide-menu").toggleClass("angleplus");
    if(flag == false){
        $("#search").show();
        $(".searchbox").addClass("searchbox-open").css("z-index", "99");
        // $('.searchbox-icon').click();
        flag = true;
    } else {
        $("#search").hide();
        flag = false;
        /*** check input search if have content do search ***/
        if ($('.searchbox-input').val() != ""){
            $('.searchbox-icon').click();
        }
    }    
});
    //show or hide '.searchbox-icon' when click "#show-hide-menu" or '.searchbox-icon' (service page)
$(document).on("click", "#show-hide-menu", function(){
    $(this).toggleClass("angleplus");
    if(flag == false){
        $("#top-nav").removeAttr("style");
        $("#search").show();
        $(".searchbox").addClass("searchbox-open").css("z-index", "99");
         $('#row-top-navbar').removeAttr('style');
         $("#top-nav").css("opacity","0");
        // $('.searchbox-icon').click();
        flag = true;
    } else {
        $("#search").hide();
        $(".searchbox").removeClass("searchbox-open");
        $("#top-nav").removeAttr("style");
        $('#row-top-navbar').css("display","block");
        flag = false;
        /*** check input search if have content do search ***/
        if ($('.searchbox-input').val() != ""){
            $('.searchbox-icon').click();
        }
    }   
});



    // change attribute of "#header-block" and "#search" when scroll window

$(window).scroll(function(){
    // if($(this).scrollTop() >= $("#header-block").height())

    if($(this).scrollTop() >= 20)
    {
        if (pathname.indexOf("/profile") > 0){
                
                if (flag == true){
                    $('#row-top-navbar').removeAttr('style');
                    $("#show-hide-menu").addClass("angleplus");
                    flag == false;
                }
             }
        $("#header-container").css({"position": "fixed", "width": "100%", "top": "0px", "background": "#fff", "z-index": "99"});
        if(window.innerWidth >= 768) 
        {                       
            
                 $("#search").show();
                $("#top-nav").css("opacity","0");
                $(".form_search").hide();
                $("#header-container").find(".row").addClass("wrap-header-top-navbar");
                $(".searchbox").addClass("searchbox-open").css("z-index", "99");
                $("#show-hide-menu").addClass("angleplus");
                 flag = true;
             
        }
    }
    else 
    {
        $("#header-container").css("position","static");
        
        if(window.innerWidth >= 768) 
        {                       
            
            if (pathname.indexOf("/profile") < 0){
              $("#show-hide-menu").removeClass("angleplus");
              flag = false;
              $("#search").hide();
                $(".form_search").show();
                $("#header-container").find(".row").removeClass("wrap-header-top-navbar");
                $(".searchbox").removeClass("searchbox-open");
                $("#top-nav").removeAttr("style");
            }else{
                flag = true;
            }
        }
        // else
        // {
        //     $(".form_search").hide();
        // }
    }
    if($(this).scrollTop() > 0)
    {
        //alert("");
        //$("#mobile-header").css({"position": "fixed", "width": "100%", "top": "0px", "background": "#fff", "z-index": "99"});
        $("#mtop-bar").hide();
        $("#menu-logo").hide();
        $("#search-bar").css({"position": "fixed", "width": "100%", "top": "0px", "background": "#fff", "z-index": "99"});
    }
    else
    {
        $("#mtop-bar").show();
        $("#menu-logo").show();
        //$("#mobile-header").css("position","static");
        $("#search-bar").css({"position":"static","width": "auto"});
    }                
});

    // show/hide userbox (service page)
$("#m-show-userbox").click(function(event) {
    $("#user-mblock").css('display','block');
});
$("#hid-userbox").click(function(){
    $("#user-mblock").removeAttr("style");
});

    // show/hide userbox (sub-page)
$(document).on ("click","#m-show-userbox", function(){
    $("#user-mblock").css('display','block');
});
$(document).on ("click","#hid-userbox", function(){
    $("#user-mblock").removeAttr("style");
});
//scroll to show back to top buttom
$(window).scroll(function() {
    /* Act on the event */
    if($(this).scrollTop() >= 700){
        $('#click_back_to_top').css("opacity","1");
    }else{
        $('#click_back_to_top').css("opacity","0");
    }
});
// autocomple search box

$( function() {
    
    $( "input[name='search2']" ).autocomplete({
      source: function(request, response) {
        $.ajax({
            dataType: "json",
            data:
            {
                term: request.term,
            },
            type: 'POST',
            url: "/venue/venueautocomple",
            success: function(data) {
                response(data);
                
            },
            error: function (request, error) {
                console.log(request);
                //console.log(error);
            },
        });
      },
      
      select: function(event, ui) {
        $(this).val(ui.item.value);
        $(this). parents("form").submit();
        // $('form.searchbox, form.form_search, form.searchbox-mobile').submit();
      },
      open: function(event, ui) {
            w = $(this).width() + 30;
            $(".ui-autocomplete").css("width", w);
        }
    });
});
    
 //********************* popup when search venue**************************//   
 $( function() {
        bodyH = $('body').height();
        winH = $(window).height();
        winW = $(window).width();
        if (winW >769){
            if(bodyH < winH){
            $('footer').css({"position": "fixed", "bottom": "0" ,"width" : "100%" , "z-index" : "9999"});
            $('footer').find('.copyright').css("background","#fff");
        }
        }
        
    });