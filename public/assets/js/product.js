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
});

// Show Quick view of Product
$(document).on('click','.quick_view_v2, .uc_avatar',function(){
    Product.productId = $(this).data('article-id');
    Product.loadAjaxForQuickview();
});

// Show Alert Popup of product
$(document).on('click','.icon_alert',function(){
    Product.productId = $(this).data('alert-id');
    Product.loadAjaxForAlert();
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

$(document).keyup(function(e) {
    if (e.keyCode == 27) 
    { 
        $("body").css("overflow", "auto");
    }
});

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

$("body").on("click", "#modal_alert .close, #modal_QV .close", function () {
    $("body").css("overflow", "auto");
});

// $("body").on("click", "#modal_QV .close", function () {
//     $("body").css("overflow", "auto");
// });

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