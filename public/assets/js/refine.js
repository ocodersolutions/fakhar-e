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
    }
    else
    {
        myClasses.add("active");
        $(".icon_arrow").removeClass("fa-chevron-down");
        $(".icon_arrow").addClass("fa-chevron-up");
        $(".sort-item >.uc_line_content").slideDown();
    }        
});

    //when click ".sort-item-select" then assign content of it for ".sort-item-selected" and ".uc_line_content" close slide.
$(".sort-item-select").click(function(){
    var content_sl = $(this).html();
    $(this).parents(".sort-item").find(".sort-item-selected").html(content_sl);
    $(this).parents(".uc_line_content").slideUp();
});

// Click like & unline 
$('body').on('click', '.icon_like', function (){
        var myClasses = this.classList;
        id = $(this).attr('id');
        check_login = $('#status-login').attr('data-login');
        if(check_login == 1){
            //if logged
            if (myClasses.contains("active")) {
                action = 'unlike';
                ajax_like(id,action);
                myClasses.remove("active");
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
                        $('.mobile .'+category_id).find('.item').addClass('selected')
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
                }else if($(this).hasClass('item')){
                    // if click item
                    
                    if($(this).hasClass('selected')){
                        $(this).removeClass('selected');
                    }else{
                        $(this).addClass('selected');
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
                    }else{
                        $(this).addClass('selected');
                        $(this).closest('tr').prependTo('.'+y+' .list-brand table tbody');
                    }
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
                    } else {
                        $(this).removeClass('selected');
                        ($(this).find(".check").remove());
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
                    }else{
                        $('.deals .item.selected').removeClass('selected');
                        a = $('.desktop .deals .item');
                        for (i = 0; i < a.length; i++) {
                            var a1 = $(a[i]).attr('data-deal');
                            $("input[name='"+a1+"']").val('');
                        };
                        $("input[name='"+name_deal+"']").val('1');
                        $(this).addClass('selected');
                    }
                    var discount = '';
                    discount = $('.item.selected[data-deal="discountcode"]').length;
                    if(discount == 1){
                        $('.deals .price').css('display','block');
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
                    val_order = $(this).attr('value');
                    $('input[name="filterTypeMain"]').val(val_order);
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
        // if(discount != 1){
        //     Product.loadProductListAjax(); 
        // }
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
   
    //add element for price filter
    $(document).ready(function() {
        //price
        $(".desktop .slider-3 span:eq(0)").append("<p>0</p>");
        $(".desktop .slider-3 span:eq(1)").append("<p>1000+</p>");
        $(".mobile .slider-3 span:eq(0)").append("<p>0</p>");
        $(".mobile .slider-3 span:eq(1)").append("<p>1000+</p>");
        //deals
        $(".desktop .deals .slider-3 span:eq(0)").append("<p>0%</p>");
        $(".desktop .deals .slider-3 span:eq(1)").append("<p>100%</p>");
        $(".mobile .deals .slider-3 span:eq(0)").append("<p>0%</p>");
        $(".mobile .deals .slider-3 span:eq(1)").append("<p>100%</p>");
    });
    //end-select filter

// select filter price
$(function() {
    $(".prices .slider-3").slider({
        range: true,
        min: 0,
        max: 1000,
        values: [0, 1000],
        change: function(event, ui) {
            minprice = (ui.values[0]);
            maxprice = (ui.values[1]);
            $('.desktop .slider-3 span:eq(0) p').empty().append(minprice);
            $('.desktop .slider-3 span:eq(1) p').empty().append(maxprice == 1000 ? '1000+' : maxprice);
            $('.mobile .slider-3 span:eq(0) p').empty().append(minprice);
            $('.mobile .slider-3 span:eq(1) p').empty().append(maxprice == 1000 ? '1000+' : maxprice);
            $("input[name='minamount']").val(minprice);
            $("input[name='maxamount']").val(maxprice);
            Product.loadProductListAjax();
        }
    });
    // $("#min-price").val("$" + $(".slider-3").slider("values", 0));
    // $("#max-price").val("$" + $(".slider-3").slider("values", 1));
});

$(function() {
    $(".deals .slider-3").slider({
        range: true,
        min: 0,
        max: 100,
        values: [0, 100],
        change: function(event, ui) {
            minprice = (ui.values[0]);
            maxprice = (ui.values[1]);
            $('.desktop .deals .slider-3 span:eq(0) p').empty().append(minprice+'%');
            $('.desktop .deals .slider-3 span:eq(1) p').empty().append(maxprice+'%');
            $('.mobile .deals .slider-3 span:eq(0) p').empty().append(minprice+'%');
            $('.mobile .deals .slider-3 span:eq(1) p').empty().append(maxprice+'%');
           
            $("input[name='minduration']").val(minprice);
            $("input[name='maxduration']").val(maxprice);
            Product.loadProductListAjax();
        }
    });
});

$('form.searchbox, form.form_search').submit(function(event){
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