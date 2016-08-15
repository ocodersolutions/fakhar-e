function resetForm(){
    $("#minamount").val(0);
    $("#maxamount").val(1000);
    $("#minduration").val(0);
    $("#maxduration").val(100);
    // $("#filter").val('');
    // $("#keyword").val('');
    var item = ['catids','brands','colors','stores','deals'];
    jQuery.each( item, function(index,value){
        $("#"+value).val('');
         //alert(value);
    });

}

function uncheckAllTree(){
    var api = $('#tree').aciTree('api');
    var leaves = $("#tree *");
    var checkboxes = api.checkboxes(leaves);

    checkboxes.each(function (index, item) {
        var $item = $(item);
        api.uncheck($item);
    });
}

function resetBrand(){
    $("#brandautocomplete").val('');
    $("body").find("input.brandSelection").prop("checked",false).removeClass('activebrand');

}

function resetSlider(){

    // Save slider instance to var
    var slider_price = $("#rangeprice").data("ionRangeSlider");
     slider_price.reset();
     var slider = $("#range").data("ionRangeSlider");
     slider.reset();
    // Call sliders reset method
    
    //Show my prices only

    $('#priceboxglobal').css('opacity', 0.2);
    $('#profileBasePrices').val(1); 
    $("#priceboxglobalOverlay").show();
    $("#profilePrices").prop('checked',true)
}
function resetColor(){
    $(".colors_box").find("input[type=checkbox]").prop("checked",false);
    $(".colorselection").removeClass('activecolor');
}

function resetDeal(){
    $(".deals_box").find("input[type=radio]").prop("checked",false);
    $('#onsale').val('');
    $('#todaynew').val('');
    $('#newthisweek').val('');
    $('#freeshipping').val('');
    $('#specialoffer').val('');
    $('#discountcode').val('');                    
}
function resetStores(){
    $("#storeautocomplete").val('');
    $("body").find("input.storeSelection").prop("checked",false).removeClass('activebrand');
}

/*clear filter*/
$("#clearFilter").click(function(){
    resetForm();
    uncheckAllTree();
    resetBrand();
    resetSlider();
    resetColor();
    resetDeal();
    resetStores();
    loadProductData();
});
/*end clear filter*/