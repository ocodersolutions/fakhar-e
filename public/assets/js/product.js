var Product = {
    loading     : 0, // 1 is running ajax
    endData     : 0, // 1 can't continue show products
    urlAjaxLoad : "/service/getfeeds/" + Math.random(), // URL load products ajax
    formId      : "#productFilterDetail", // id of form filter

    // load products list by ajax
    loadProductListAjax : function(isLazyLoading){
        $("#loading-div-background").show();
        Product.loading = 1;
        if (typeof isLazyLoading == 'undefined' || isLazyLoading == '') {
            $("#page").val(1);
            Product.endData = 0;
        }

        $.ajax({
            url: this.urlAjaxLoad,
            type: 'POST',
            data: $(this.formId).serialize(),
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
    }
};

$(document).ready(function() {
    Product.loadProductListAjax();
});
        
$(window).scroll(function() {
    var offset = ($(document).height() - $(window).height() )-2200;
    if($(window).scrollTop() >= offset) {
        console.log(Product);
        // ajax call get data from server and append to the div
        if (Product.loading == 0 && Product.endData == 0){
            Product.loadProductListAjax(1);
        }
    }
});