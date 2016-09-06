var flag = false;

$(document).ready(function(){
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
    }    
});
    //show or hide '.searchbox-icon' when click "#show-hide-menu" or '.searchbox-icon' (service page)
$(document).on("click", "#show-hide-menu", function(){
    $(this).toggleClass("angleplus");
    if(flag == false){
        $("#search").show();
        $(".searchbox").addClass("searchbox-open").css("z-index", "99");
        // $('.searchbox-icon').click();
        flag = true;
    } else {
        $("#search").hide();
        $(".searchbox").removeClass("searchbox-open");
        flag = false;
    }   
});



    // change attribute of "#header-block" and "#search" when scroll window
$(window).scroll(function(){
    // if($(this).scrollTop() >= $("#header-block").height())
    if($(this).scrollTop() >= 20)
    {
        $("#header-container").css({"position": "fixed", "width": "100%", "top": "0px", "background": "#fff", "z-index": "99"});
        if(window.innerWidth >= 768) 
        {                       
            $("#search").show();
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
            $("#search").hide();
            $(".form_search").show();
            $("#header-container").find(".row").removeClass("wrap-header-top-navbar");
            $(".searchbox").removeClass("searchbox-open");
            $("#show-hide-menu").removeClass("angleplus");
            flag = false;
        }
        // else
        // {
        //     $(".form_search").hide();
        // }
    }
    if($(this).scrollTop() > 0)
    {
        $("#mobile-header").css({"position": "fixed", "width": "100%", "top": "0px", "background": "#fff", "z-index": "99"});
    }
    else
    {
        $("#mobile-header").css("position","static");
    }                
});

$("#m-show-userbox").click(function(event) {
    $("#user-mblock").css('display','block');
});
$("#hid-userbox").click(function(){
    $("#user-mblock").removeAttr("style");
});

