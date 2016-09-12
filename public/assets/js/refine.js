var ishidden = true;
// $demso = 0;
$(document).ready(function() {
    // apply filterTable to all tables on this page but don't filter content with the no-filter class
    $('table').filterTable({ignoreClass: 'no-filter'});
    $('.desktop p.filter-table').prependTo('.desktop .brands .brand');
    $('.mobile p.filter-table').prependTo('.mobile .brands .brand');

    // set css default of ".sort-list" when document ready
    $(".sort-list").css({"opacity": "0.3", "cursor": "default"});
});

//when click ".sort-list" then remove class "col-xs-6" of ".product.col-lg-3.col-sm-4", change attribute css of its and ".sort-list"
$(".sort-grid").click(function(){
    $(this).parents("body").find("#load-product-list").addClass("grid-view");
    $(this).css({"opacity": "0.3", "cursor": "default"});
    $(this).parents(".row").find(".sort-list").css({"opacity": "1", "cursor": "pointer"});

});

//when click ".sort-list" then remove class "col-xs-6" of ".product.col-lg-3.col-sm-4", change attribute css of its and ".sort-grid"
$(".sort-list").click(function(){
    $(this).parents("body").find("#load-product-list").removeClass("grid-view");
    $(this).css({"opacity": "0.3", "cursor": "default"});
    $(this).parents(".row").find(".sort-grid").css({"opacity": "1", "cursor": "pointer"});

});

//when click ".sort-item" then check if its exist class "active" then remove class "active", ".icon_arrow" add class "fa-chevron-down" and remove class "fa-chevron-up", ".sort-item >.uc_line_content" close slide; else add class "active", ".icon_arrow" remove class "fa-chevron-down" and add class "fa-chevron-up", ".sort-item >.uc_line_content" open slide.
$(".sort-item").click(function(){
    
    var myClasses = this.classList;
    if(myClasses.contains("active"))
    {
        myClasses.remove("active");
        $(".icon_arrow").removeClass("fa-chevron-up");
        $(".icon_arrow").addClass("fa-chevron-down");
        $(".sort-item >.uc_line_content").slideUp();
         //$(this).find(".uc_des").prepend('<li data-parent="orderBy" value="" class="sort-item-select" >Sort By</li>');
    }
    else
    {
        myClasses.add("active");
        $(".icon_arrow").removeClass("fa-chevron-down");
        $(".icon_arrow").addClass("fa-chevron-up");
        $(".sort-item >.uc_line_content").slideDown();
        if (ishidden){
            $(this).find(".uc_des li:first-child").hide();
        }else{
            $(this).find(".uc_des li:first-child").show();
        }
    }        
});

    //when click ".sort-item-select" then assign content of it for ".sort-item-selected" and ".uc_line_content" close slide.
$(".sort-item-select").click(function(){
    var content_sl = $(this).html();
    $(this).parents(".sort-item").find(".sort-item-selected").html(content_sl);
    $(this).parents(".uc_line_content").slideUp();
    if($(this).text()== "Sort By"){
        ishidden = true;
    }else{
        ishidden = false;
    }
});
 //when click ".sort-item-select"inmobile
 $("#sis-content-popup .sort-item-select").click(function(){
    $('a.fsp-close').trigger('click') ; 
 });


$('body').on('click', '.qv-like', function (){
    id = $(this).attr('id');
    if($(this).hasClass('liked')){
        $(this).removeClass('liked');
        $('span[id="'+id+'"]').removeClass('active');
        $('span[id="'+id+'"]').closest('.product').fadeOut();
        action = 'unlike';
    }else{
        $(this).addClass('liked');
        $('span[id="'+id+'"]').addClass('active');
        $('span[id="'+id+'"]').closest('.product').fadeIn();
        action = 'like';
    }
    ajax_like(id,action);
});


    // Click like & unline 
    $('body').on('click', '.icon_like', function (){
        var myClasses = this.classList;
        id = $(this).attr('id');
        check_login = $('#status-login').attr('data-login');
        saveitem = $('input[name="filterTypeMain"]').val();
        if(check_login == 1){
            //if logged
            if (myClasses.contains("active")) {
                action = 'unlike';
                ajax_like(id,action);
                myClasses.remove("active");
                if(saveitem == 'saveitem'){ $(this).closest('.product').fadeOut(); }
            } else {
                action = 'like';
                ajax_like(id,action);
                myClasses.add("active");
            }
        }else if(check_login == 0){
            //if not signed

             myalert('myalertid2','type_missing','Oops','You are not logged in to use this feature, please login link bellow to continue.','Login');
            $(document).on('opened.fndtn.reveal', '[data-reveal]', function () {
                $("button.my_btn_ok").click(function(){ // click button
                   $('a.close-reveal-modal').trigger('click'); // will close modal
                   window.location.replace("/auth/login");
                });
            });
        }
    });

    function ajax_like(id,action){
        if(action == 'like'){
            url_like = "/service/likeclosset";
        }else{
            url_like = "/service/unlikeclosset";
        }
        $.ajax({
            url : url_like,
            type : "post",
            dataType:"text",
            data : {
                'closesetId' : id,
            },
        });
    }
// Click like & unline 

//select fileter    
    //deal
   $('.deals .item, .colors .list-color .color, .brands .list-brand li, .category .item, .category .has-child, .clear-filter, .orderBy li, .filterTypeMain input[name="filter"]').click(function(){
        notload = 0;
        mobile = $(this).closest('.mobile').length;
        desktop = $(this).closest('.desktop').length;
        parent = $(this).attr("data-parent");
        
        // parent_category = $(this).closest('div').attr('class');
        // id = $(this).attr('id');
        test = $(this);
        
        if (desktop == 1){
            
            switch (parent){
                // when click item category
                case "category":
                    
                    // if click category parent
                    if($(this).hasClass('has-child')){
                        
                        if ($(this).hasClass("has-child-selected") == false) {
                            $(this).addClass('has-child-selected');
                            category_id = $(this).attr('id');
                            $('.desktop .'+category_id+' .has-child').addClass('has-child-selected');
                            $('.desktop .'+category_id).find('.item').addClass('selected')
                        } else {
                            $(this).removeClass('has-child-selected');
                            category_id = $(this).attr('id');
                            $('.desktop .'+category_id+' .has-child').removeClass('has-child-selected');
                            $('.desktop .'+category_id).find('.item').removeClass('selected')
                        }
                        check_parent_category(test);
                        array_category = $('.category .item.selected');
                        var a2 =  [];
                        for (i = 0; i < array_category.length ; i++) {
                            var a1 = $(array_category[i]).attr('data-catids');
                            a2.push(a1);
                        };
                        $("input[name='catids']").val(a2);
                        var a2 = [];
                       
                    }else if($(this).hasClass('item')){
                    // if click item
                        
                        if($(this).hasClass('selected')){
                            $(this).removeClass('selected');
                        }else{
                            $(this).addClass('selected');
                        }
                        check_parent_category(test);
                        array_category = $('.category .item.selected');
                        var a2 =  [];
                        for (i = 0; i < array_category.length ; i++) {
                            var a1 = $(array_category[i]).attr('data-catids');
                            a2.push(a1);
                        };
                        $("input[name='catids']").val(a2);
                        var a2 = [];
                    }
                break;

            }
        }else if(mobile == 1){
            
            
            switch(parent){
                case "category":
                if($(this).hasClass('has-child')){
                    // if click category parent
                    
                    if ($(this).hasClass("has-child-selected") == false) {
                        $(this).addClass('has-child-selected');
                        category_id = $(this).attr('id');
                        $('.mobile .'+category_id+' .has-child').addClass('has-child-selected');
                        $('.mobile .'+category_id).find('.item').addClass('selected');
                        console.log($(this));
                    } else {
                        $(this).removeClass('has-child-selected');
                        category_id = $(this).attr('id');
                        $('.mobile .'+category_id+' .has-child').removeClass('has-child-selected');
                        $('.mobile .'+category_id).find('.item').removeClass('selected')
                    }
                    check_parent_category(test);
                    array_category = $('.category .item.selected').toArray();
                    var a2 =  [];
                    for (i = 0; i < array_category.length ; i++) {
                        var a1 = $(array_category[i]).attr('data-catids');
                        a2.push(a1);
                    };
                    $("input[name='catids']").val(a2);
                    var a2 = [];
                }
                else if($(this).hasClass('item')){
                    // if click item
                    // var attofthis = $(this).data('parent');
                    
                    if($(this).hasClass('selected')){
                        $(this).removeClass('selected');
                        // $demso -= 1;
                        // alert($demso);
                        // parentTag = $( this ).closest('ul.1').addClass('class_name');
                        // alert(parentTag);
                        //$(this).closest('.mobile').find('li[data-parent="'+attofthis+'"]').removeClass('has-child-selected');
                        
                    }
                    else {
                        $(this).addClass('selected');
                        // $demso += 1;
                        // alert($demso);
                        //$(this).closest('ul').closest('ul').find('li').addClass('has-child-selected');
                        // parentTag = $( this ).parent(".1").addClass('class_name');
                        // alert(parentTag);
                        // $(this).parent().closest('.mobile').find('m-title').addClass('has-child-selected');
                    }
                    // alert($demso);
                    // if($demso > 0) {
                    //     // console.log($(this));
                    //     if($(this).parents(".accessories")) {
                    //         // console.log($(this));
                    //         $(this).parents(".mobile").find("#category").addClass('has-child-selected');
                    //         console.log($(this).parents(".mobile").find("#accessories").addClass('has-child-selected'));
                    //         // $(this).parents("#accessories").addClass('has-child-selected');
                    //     }
                    //     else { alert("sai");    }

                    // }
                    // else {
                    //     alert("error");
                    // }
                    check_parent_category(test);
                    array_category = $('.category .item.selected').toArray();
                    var a2 =  [];
                    for (i = 0; i < array_category.length ; i++) {
                        var a1 = $(array_category[i]).attr('data-catids');
                        a2.push(a1);
                    };
                    $("input[name='catids']").val(a2);
                    var a2 = [];
                }
                break;
            }
        }

        //              PUBLIC
        //  filer public for desktop & mobile

        switch(parent){
                
            //star - when click item brand
                case "brands":
                    if(mobile == 1){
                        y = 'mobile';
                    }else if(desktop == 1){
                        y = 'desktop';
                    }
                    if($(this).hasClass('selected')){
                        $(this).removeClass('selected');
                        $(this).closest('tr').appendTo('.'+y+' .list-brand table tbody');
                        $(this).closest('.mobile > ul').find("#brands").removeClass('has-child-selected');
                    }else{
                        $(this).addClass('selected');
                        $(this).closest('tr').prependTo('.'+y+' .list-brand table tbody');
                        $(this).closest('.mobile > ul').find("#brands").addClass('has-child-selected');
                    }
                    $('.list-brand').animate({scrollTop: 0}, 800); 
                    array_brand = $('.brands .item.selected');
                    var a2 =  [];
                    for (i = 0; i < array_brand.length ; i++) {
                        var a1 = $(array_brand[i]).attr('data-brand');
                        a2.push(a1);
                    };
                    $("input[name='brands']").val(a2);
                    var a2 = [];
                break;
            //end -  when click item brand
            //start - when click item color
                case "colors":
                
                    if ($(this).hasClass("selected") == false) {
                        $(this).addClass('selected').append(" <i class='check fa fa-check' aria-hidden='true'></i>");
                        $(this).closest('ul').find('#colors').addClass('has-child-selected');
                    } else {
                        $(this).removeClass('selected');
                        ($(this).find(".check").remove());
                        $(this).closest('ul').find('#colors').removeClass('has-child-selected');
                    }
                    array_color = $('.colors .selected');
                    var a2 =  [];
                    for (i = 0; i < array_color.length ; i++) {
                        var a1 = $(array_color[i]).attr('data-color');
                        a2.push(a1);
                    };
                    $("input[name='colors']").val(a2);
                    a2 = [];
                break;
            //end - when click item color
            //start - when click item deal
                case "deals":
                    name_deal = $(this).attr('data-deal');
                    x = $("input[name='"+name_deal+"']").val();
                    if('.item[data-deal="discountcode"]')
                    if(x == 1){
                        $("input[name='"+name_deal+"']").val('');
                        $(this).removeClass('selected');
                        $(this).closest('.mobile').find('#deals').removeClass('has-child-selected');
                    }else{
                        $('.deals .item.selected').removeClass('selected');
                        a = $('.desktop .deals .item');
                        for (i = 0; i < a.length; i++) {
                            var a1 = $(a[i]).attr('data-deal');
                            $("input[name='"+a1+"']").val('');
                        };
                        $("input[name='"+name_deal+"']").val('1');
                        $(this).addClass('selected');
                        $(this).closest('.mobile').find('#deals').addClass('has-child-selected');

                    }
                    var discount = '';
                    discount = $('.item.selected[data-deal="discountcode"]').length;
                    if(discount == 1){
                        $('.deals .price').css('display','block');
                        notload = 1;
                    }else{
                        $('.deals .price').css('display','none');
                    }
                    
                break;
            //end - when click item deal
            //start - when click sort item
                case "orderBy":
                    
                    val_order = $(this).attr('value');
                    $('input[name="orderBy"]').val(val_order);
                break;
            //end - when click sort item
            //start - when click typemain
                case "filterTypeMain":
                    z = $(this).attr('value');
                    if( z == 'all'){
                        $('input[name="filterTypeMain"]').val(z);
                    }else{
                        check_login = $('#status-login').attr('data-login');
                        if(check_login != 1){
                            myalert('myalertid2','type_missing','Oops','You are not logged in to use this feature, please login link bellow to continue.','Login');
                            $(document).on('opened.fndtn.reveal', '[data-reveal]', function () {
                                $("button.my_btn_ok").click(function(){ // click button
                                   $('a.close-reveal-modal').trigger('click'); // will close modal
                                   window.location.replace("/auth/login");
                                });
                            });
                            notload = 1;
                        }else{
                            $('input[name="filterTypeMain"]').val(z);
                        }
                        
                    }
                    
                break;
            //end - when click typemain


                case "clear":
                    
                    $('.item.selected').removeClass('selected');
                    $('.has-child-selected').removeClass('has-child-selected');
                    $('.color.selected').removeClass('selected');
                    $('i.check.fa.fa-check').remove();
                    x = $('#productFilterDetail input[type="hidden"]');
                    for (i = 0; i < x.length ; i++) {
                        y = $(x[i]).attr('name');
                        $("input[name="+y+"]").val("");
                    };
                    $("input[name='limit']").val("50");
                break;

        }
        if(notload != 1){
            Product.loadProductListAjax(); 
        }
        
    });
   
    // select childs category
    function check_parent_category(test){
        
        desktop =test.closest('.desktop').length;
        mobile =test.closest('.mobile').length;
        if (desktop == 1) {
            var x = $('.desktop .has-child');
            var x2 =  []; 
            for (i = 0; i < x.length ; i++) {
                var x1 = $(x[i]).attr('id');
                total = $('.desktop .'+x1+' .item').length;
                current = $('.desktop .'+x1+' .item.selected').length;
                if(total - current == 0){
                    $('.desktop #'+x1).addClass('has-child-selected');
                }else if(total - current > 0){
                    $('.desktop #'+x1).removeClass('has-child-selected');
                }
            };
            x2 =  [];
        }else if (mobile == 1) {
            
            x = $('.mobile .has-child');
            var x2 =  [];
            for (i = 0; i < x.length ; i++) {
                var x1 = $(x[i]).attr('id');;
                total = $('.mobile .'+x1+' .item').length;
                current = $('.mobile .'+x1+' .item.selected').length;
                if(total - current == 0){
                    $('.mobile #'+x1).addClass('has-child-selected');
                }else if(total - current > 0){
                    $('.mobile #'+x1).removeClass('has-child-selected');
                }
            };
            x2 =  [];
        }
    }
    //clear filter
 
    //dropdown effect
    $('.refine .refine-colum ul .fa-angle-down').click(function() {
        z = $('#prices.li-up').length;
        p = $(this).attr('data-class');
        if(p == 'prices'){
            if (z > 0) {
                $('.show-price').css('display','none');
            }else{
                $('.show-price').css('display','block');
            }
        }

        x = $(this).parents('.category');
        var attrContent = getComputedStyle(this, ':after').content;
        var currentId = $(this).attr("data-class");
        var y = $(this).attr('data-class');
        if(x.length == 0){
            if ($(this).hasClass("change") == false) {
                $(this).addClass('change selected');
            } else {
                $(this).removeClass('change selected');
            }
        }
        $('.refine .refine-colum .' + currentId +', .fsp-wrapper .refine-colum .' + currentId + '').animate({
            left: "+=50",
            height: "toggle"
        }, 800, function() {});
        if ($(this).hasClass("fa-selected") == false) {
            $(this).addClass('fa-selected');
            $('li#'+y).addClass('li-up');
        }else{
            $(this).removeClass('fa-selected');
            $('li#'+y).removeClass('li-up');
        }
    });
   $(".refine .refine-colum > ul > li, #1").click(function () {
        var tog = $(this).attr("data-class");
        $("." + tog).slideToggle(800, function () {});
        var ifa = $(this).closest('ul').find("i" + "[data-class='" + tog +"']");
            ifa.toggleClass('fa-selected');
            //$(this).toggleClass('li-up');
    });
    //add element for price filter
    $(document).ready(function() {
        //price
        //$(".desktop .ranger span.irs-slider .from").append("<p>0</p>");
        //$(".desktop .slider-3 span:eq(1)").append("<p>1000+</p>");
        //$(".mobile .slider-3 span:eq(0)").append("<p>0</p>");
        //$(".mobile .slider-3 span:eq(1)").append("<p>1000+</p>");
        //deals
        // $(".desktop .deals .slider-3 span:eq(0)").append("<p>0%</p>");
        // $(".desktop .deals .slider-3 span:eq(1)").append("<p>100%</p>");
        // $(".mobile .deals .slider-3 span:eq(0)").append("<p>0%</p>");
        // $(".mobile .deals .slider-3 span:eq(1)").append("<p>100%</p>");
    });
    //end-select filter

// select filter price
// $(function() {
//     $(".prices .slider-3").slider({
//         range: true,
//         min: 0,
//         max: 1000,
//         values: [0, 1000],
//         change: function(event, ui) {
//             minprice = (ui.values[0]);
//             maxprice = (ui.values[1]);
//             $('.desktop .slider-3 span:eq(0) p').empty().append(minprice);
//             $('.desktop .slider-3 span:eq(1) p').empty().append(maxprice == 1000 ? '1000+' : maxprice);
//             $('.mobile .slider-3 span:eq(0) p').empty().append(minprice);
//             $('.mobile .slider-3 span:eq(1) p').empty().append(maxprice == 1000 ? '1000+' : maxprice);
//             $("input[name='minamount']").val(minprice);
//             $("input[name='maxamount']").val(maxprice);
//             //Product.loadProductListAjax();
//         }
//     });
// });

// $(function() {
//     $(".deals .slider-3").slider({
//         range: true,
//         min: 0,
//         max: 100,
//         values: [0, 100],
//         change: function(event, ui) {
//             minprice = (ui.values[0]);
//             maxprice = (ui.values[1]);
//             $('.desktop .deals .slider-3 span:eq(0) p').empty().append(minprice+'%');
//             $('.desktop .deals .slider-3 span:eq(1) p').empty().append(maxprice+'%');
//             $('.mobile .deals .slider-3 span:eq(0) p').empty().append(minprice+'%');
//             $('.mobile .deals .slider-3 span:eq(1) p').empty().append(maxprice+'%');
           
//             $("input[name='minduration']").val(minprice);
//             $("input[name='maxduration']").val(maxprice);
//             //Product.loadProductListAjax();
//         }
//     });
// });

$('form.searchbox, form.form_search, form.searchbox-mobile').submit(function(event){
    event.preventDefault(); 
    y = '';
    x = '';
    x = $('form.searchbox input[name="search1"]').val();
    if (x == "") {
        x = $('form.searchbox input[name="search2"]').val();
        (x != '' ) ? y = 1 : y = 0;
    }
    if (x == "") {
        x = $('form.form_search input[name="search1"]').val();
    } 
    if (x == ""){
        x = $('form.form_search input[name="search2"]').val();
        (x != '' ) ? y = 1 : y = 0;
    }


    if (y == 1) {
        y = 'venue';
    }else{
        y = 'normal';
    }

    check = $('span[data-search="'+x+'"]').length;
   

    if ( check == 0 && x != '') {
        $('.option-selected').append('<div class="item '+y+'" data-search="'+x+'"><span data-search="'+x+'">'+x+'</span><i data-search="'+x+'" class="fa fa-times" aria-hidden="true"></i></div>');
        $('i.fa-times').click(function(){
            x = $(this).attr('data-search');
            $('.item[data-search="'+x+'"]').remove();
            check_search_tag();
        });
    }
    check_search_tag();
   
});

function check_search_tag(){
    array_search_normal = $('.option-selected .normal span');
    array_search_venue = $('.option-selected .venue span');
    var y2 =  [];
    for (i = 0; i < array_search_normal.length ; i++) {
        var y1 = $(array_search_normal[i]).attr('data-search');
        if(y2.indexOf(y1)==-1){
            y2.push(y1);
        }
    };
    $('#productFilterDetail input[name="searchArticle"]').val(y2);

    var y2 =  [];
    for (i = 0; i < array_search_venue.length ; i++) {
        var y1 = $(array_search_venue[i]).attr('data-search');
        if(y2.indexOf(y1)==-1){
            y2.push(y1);
        }
    };
    $('#productFilterDetail input[name="searchVenue"]').val(y2);

    $('input[name="search1"]').val('');
    $('input[name="search2"]').val('');
    Product.loadProductListAjax();
}

check_price_checked()
$('input#check-price').click(function(){
    $(this).toggleClass('allow_slider');
    // if ($(this).hasClass("allow_slider") == true) {
    //     $(this).addClass('allow_slider');
    //     $('input[name="profileBasePrices"]').val('0');
    //     $('.show-price').css('display','block');
    //     $('.ranger').css('opacity','0.3');
        
    // } else {
    //     $(this).removeClass('allow_slider');
    //     $('input[name="profileBasePrices"]').val('1');
    //     $('.show-price').css('display','none');
    //     $('.ranger').css('opacity','1');
    // }
    check_allow_slide();
    //Product.loadProductListAjax(); 
});
 function check_allow_slide(show){
    x = $('input#check-price').hasClass("allow_slider");
    if (x == false){
        $('.show-price').css('display','block');
        $('.ranger').css('opacity','0.3');
        $('input[name="profileBasePrices"]').val('0');
    }else{
        $('.show-price').css('display','none');
        $('.ranger').css('opacity','1');
         $('input[name="profileBasePrices"]').val('1');
    }
 }
function check_price_checked(){
    x = $('input#check-price').hasClass("allow_slider");
    if(x == false){
        $('.show-price').css('display','none');
        $('.ranger').css('opacity','0.3');
    }
}
// function click button apply refine search in mobile
$('button#apply-submit').click(function(){
    $("a.fsp-close").trigger('click');
})
// function click label radios form
// $("#refine-search-popup").on("click",function(){
//     var thischecked = $('#rfs-content-popup .filterTypeMain input[type=radio]')
//     if (thischecked.is(":checked")){
//         thischecked.closest(".option-filter-row").css("background","blue");
//     }
// })
$(function() {
    //test ionslider 
    var from = 0;
    var to = 1000;

    $(".priceSlider").ionRangeSlider({
            hide_min_max: true,
            type: "double",
            min: 0,
            max: 1000,
            from: from,
            to: to,
            keyboard: true,
            step: 1,
            hide_from_to:false,
            onChange:function(data){
                data = $("#priceSlider").data("ionRangeSlider");
                mdata= $ ("#mPriceSlider").data("ionRangeSlider");
               
                 maxprice = data.old_to;
               if (maxprice == 1000){
                   $(".irs-to").html('1000+');
                }
            },
            onFinish: function (data) {
                data = $("#priceSlider").data("ionRangeSlider");
                mdata= $ ("#mPriceSlider").data("ionRangeSlider");
                minprice = data.old_from;
                maxprice = data.old_to;
                mMinprice = mdata.old_from;
                mMaxprice = mdata.old_to;
                winsdows_size = $(window).width();
                if ( winsdows_size > 768){
                    $("input[name='minamount']").val(minprice);
                    $("input[name='maxamount']").val(maxprice);
                }else{
                    $("input[name='minamount']").val(mMinprice);
                $("input[name='maxamount']").val(mMaxprice);
                }
                
                $("input[name='minamount']").val(mMinprice);
                $("input[name='maxamount']").val(mMaxprice);
                //Product.loadProductListAjax();
            }
            
        });
    var dealfrom = 0;
    var dealto = 100;
    //test ion slider
    $(".dealsSlider").ionRangeSlider({
            hide_min_max: true,
            type: "double",
            min: 0,
            max: 100,
            from: dealfrom,
            to: dealto,
            keyboard: true,
            postfix: "%",
            step: 1,
            hide_from_to:false,
            onChange:function(data){
                data = $("#dealsSlider").data("ionRangeSlider");
                mdata= $ ("#mdealsSlider").data("ionRangeSlider");
            },
            onFinish: function (data) {
                data = $("#dealsSlider").data("ionRangeSlider");
                mdata= $ ("#mdealsSlider").data("ionRangeSlider");
                console.log(data);
                //console.log(mdata);
                mindeal = data.old_from;
                maxdeal = data.old_to;
                mMindeal = mdata.old_from;
                mMaxdeal = mdata.old_to;
                if ( winsdows_size > 768){
                $("input[name='minduration']").val(mindeal);
                $("input[name='maxduration']").val(maxdeal);
                }else{
                   $("input[name='minduration']").val(mMindeal);
                    $("input[name='maxduration']").val(mMaxdeal);
                }
                
                Product.loadProductListAjax();
            }
            
        });
});