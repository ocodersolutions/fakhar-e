var Product = {
    productId   : null, // id of product
    loading     : 0, // 1 is running ajax
    endData     : 0, // 1 can't continue show products

    // load products list by ajax
    loadProductListAjax : function(isLazyLoading){
        $("#loading-div-background").show();
        Product.loading = 1;
        if (typeof isLazyLoading == 'undefined' || isLazyLoading == '') {
            $("#page").val(1);
            Product.endData = 0;
        }
        console.log($("#productFilterDetail").serialize());
        $.ajax({
            url: "/service/getfeeds/" + Math.random(),
            type: 'POST',
            data: $("#productFilterDetail").serialize(),
            dataType: 'html',
            success: function (result) {
                Product.loading = 0;
                if (typeof isLazyLoading == 'undefined' || isLazyLoading == '') {
                    $("#load-product-list").html(result);
                    $("#page").val(2);
                } else {
                    if (result.indexOf('No Record') > 0) {
                        Product.endData = 1;
                    } else {
                        $("#page").val(parseInt($("#page").val()) + 1); 
                        $("#load-product-list").append(result);
                    }
                }
                $("#loading-div-background").hide();
            }
        });
    },
    // load product detail for alert popup
    loadAjaxForAlert : function(){
        url = '/service/getfeedalert';
        $.ajax({
            url: url,
            type: 'POST',
            data:  { feeddataid: Product.productId },
            dataType:'html',
            beforeSend: function() {
                $("#loading-div-background").show(); 
                $("#alert-content").html();
            },
            success: function (result) {
                $("#alert-content").html(result);
                $("#loading-div-background").hide();
            },
            error: function (request, error) {
                console.log(request);
                console.log(error);
            },
        });
    },
    // load product detail for Quick view popup
    loadAjaxForQuickview : function(){
        $.ajax({
            url: '/service/getfeeddetail',
            type: 'POST',
            data: { feeddataid:Product.productId },
            dataType:'html',
            beforeSend: function() {
                $("#loading-div-background").show(); 
                $("#append_modal").html();
            },
            success: function (result) {
                $("#append_modal").html(result);
                $("#loading-div-background").hide();
            },
            error: function (request, error) {
                console.log(request);
                console.log(error);
            },
        });
    },
};

$(document).ready(function() {
    //Auto load first Page Products when document ready
    Product.loadProductListAjax();

        //check if width of window >=992 then
    if(window.innerWidth >= 992)
    {      
            //when mouse move in '.product_item_popup' then show ".popup_item, .quick_view_v2" and assign height of ".quick_view_v2" and line hieght of tag "span" equal height of ".icon_like"
        $(document).on({
            mouseenter: function () {
                $(this).parent().find(".popup_item, .quick_view_v2").show();
                var height1 = $(this).parent().find(".icon_like").height();
                $(this).parent().find(".quick_view_v2").height(height1).find("span").css("line-height",height1+'px');
            }
            // mouseleave: function () {
            //     $(this).find('.btn-group').fadeOut();
            // }
        }, '.product_item_popup');
            //when mouse leave '.uc_contact' then hide ".popup_item, .quick_view_v2"
        $(document).on({
            mouseleave: function () {
                $(this).parent().find(".popup_item, .quick_view_v2").hide() ;
            }
        }, '.uc_contact');     
    }
});
    //change height of ".share_product" when resize window
$(window).resize(function() {
    $('.share_product').height(function(){
        var height1 = $(this).parents(".product_item").find(".uc_contact").height();
        var height2 = $(this).parents(".product_item").find(".show_item").height();
        return height1 + height2;
    });
});

//when click '.hide_item' then hide ".share_product"
$('body').on('click', '.hide_item', function (){
    $(this).parents(".product_item").find(".share_product").hide();
});

//when click '.show_item' then show and change hieght of '.share_product'
$('body').on('click', '.show_item', function (){
    $(this).parents('.product_item').find('.share_product').show();
    var height1 = $(this).height();
    $(this).parents(".product_item").find(".share_product").height(function(){
        var height2 = $(this).parents(".product_item").find(".uc_contact").height();
        return height1 + height2;
    });
});  

// Show Quick view of Product
$(document).on('click','.quick_view_v2, .uc_avatar',function(){
    Product.productId = $(this).data('article-id');
    Product.loadAjaxForQuickview();
});

// Show Alert Popup of product
$(document).on('click','.icon_alert',function(e){
    login = $('#status-login').attr('data-login');
    active = $(this).hasClass('active');
    feeddataid = $(this).attr('data-alert-id');
    if (login == 1 && active == false ) {
        $(this).attr('data-toggle','modal');
            $.ajax({
                url : "/service/getemailalert",
                type : "post",
                dataType:"text",
                data : {
                    'feeddataid' : feeddataid,
                },
                success:function(result) {}
            });
        $(this).addClass('active');

    }else{
        Product.productId = $(this).data('alert-id');
        Product.loadAjaxForAlert();
    }
    
});

// Show Alert Popup when client click link in Quickview Popup
$('body').on('click', '.modal_body_footer', function (){
    $("#modal_QV").modal("hide");
    $("#modal_alert").modal("show");
    Product.productId = $(this).data('alert-id');
    Product.loadAjaxForAlert();
    $('#modal_alert').css("overflow", "auto");
    $("body").css("overflow", "hidden");
});  

    //change attribute of body when press ESC
$(document).keyup(function(e) {
    if (e.keyCode == 27) 
    { 
        $("body").css("overflow", "auto");
    }
});

    //change attribute of body when click outsite selector
$(document).mouseup(function (e)
{
    var container = $("#modal_QV .modal-content");
    var container2 = $("#modal_alert .modal-content");

    if (!container.is(e.target) && container.has(e.target).length === 0
        && !container2.is(e.target) && container2.has(e.target).length ===0 )
    {
        $("body").css("overflow", "auto");
    }
});
    //change attribute of body when click selector
$("body").on("click", "#modal_alert .close, #modal_QV .close", function () {
    $("body").css("overflow", "auto");
});

// $("body").on("click", "#modal_QV .close", function () {
//     $("body").css("overflow", "auto");
// });

    //change attribute of body when click selector
$("body").on("click", ".uc_avatar, .icon_alert, .quick_view_v2", function () {
    $("body").css("overflow", "hidden");
});

// $("body").on("click", ".icon_alert", function () {
//     $("body").css("overflow", "hidden");
// });

// $("body").on("click", ".quick_view_v2", function () {
//     $("body").css("overflow", "hidden");
// });

//Click button loadmore 
$("#load-more>.btn-st-silver").click(function(){
   if (Product.loading == 0 && Product.endData == 0)
    {
        Product.loadProductListAjax(1);
    }
});

// // Auto load more product when user scroll     
// $(window).scroll(function() {
//     var offset = ($(document).height() - $(window).height() )-2200;
//     if($(window).scrollTop() >= offset) {
//         // ajax call get data from server and append to the div
//         if (Product.loading == 0 && Product.endData == 0){
//             Product.loadProductListAjax(1);
//         }
//     }
// });