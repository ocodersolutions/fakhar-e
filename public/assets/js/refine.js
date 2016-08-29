// Click like & unline 
$('body').on('click', '.icon_like', function (){
        var myClasses = this.classList;
        id = $(this).attr('id');
        if (myClasses.contains("active")) {
            action = 'unlike';
            ajax_like(id,action);
            myClasses.remove("active");
        } else {
            action = 'like';
            ajax_like(id,action);
            myClasses.add("active");
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
        parent = $(this)[0].attributes[0].nodeValue;
        
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
                        array_category = $('.category .item.selected').toArray();
                        var a2 =  [];
                        for (i = 0; i < array_category.length ; i++) {
                            var a1 = (array_category[i].attributes[1].nodeValue);
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
                            var a1 = (array_category[i].attributes[1].nodeValue);
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
                        var a1 = (array_category[i].attributes[1].nodeValue);
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
                        var a1 = (array_category[i].attributes[1].nodeValue);
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
                    
                   if($(this).hasClass('selected')){
                    $(this).removeClass('selected');
                    }else{
                        $(this).addClass('selected');
                    }
                    array_brand = $('.brands .item.selected').toArray();
                    var a2 =  [];
                    for (i = 0; i < array_brand.length ; i++) {
                        var a1 = (array_brand[i].attributes[1].nodeValue);
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
                    array_color = $('.colors .selected').toArray();
                    var a2 =  [];
                    for (i = 0; i < array_color.length ; i++) {
                        var a1 = (array_color[i].attributes[1].nodeValue);
                        a2.push(a1);
                    };
                    $("input[name='colors']").val(a2);
                    a2 = [];
                break;
            //end - when click item color
            //start - when click item deal
                case "deals":
                    name_deal = $(this)[0].attributes[1].nodeValue;
                    x = $("input[name='"+name_deal+"']").val();

                    if(x == 1){
                        $("input[name='"+name_deal+"']").val('');
                        $(this).removeClass('selected');
                    }else{
                        $('.deals .item.selected').removeClass('selected');
                        a = $('.desktop .deals .item');
                        for (i = 0; i < a.length; i++) {
                            var a1 = a[i].attributes[1].nodeValue;
                            $("input[name='"+a1+"']").val('');
                        };
                        $("input[name='"+name_deal+"']").val('1');
                        $(this).addClass('selected');
                    }
                break;
            //end - when click item deal
            //start - when click sort item
                case "orderBy":
                    
                    val_order = $(this)[0].attributes[1].nodeValue;
                    $('input[name="orderBy"]').val(val_order);
                break;
            //end - when click sort item
            //start - when click typemain
                case "filterTypeMain":
                    
                    console.log($(this));
                    val_order = $(this)[0].attributes[3].nodeValue;
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
                        y = x[i].attributes[2].nodeValue;
                        console.log("input[name="+y+"]");
                        $("input[name="+y+"]").val("");
                    };
                    $("input[name='limit']").val("50");
                break;

        }

    Product.loadProductListAjax(); 
    });
  
    // select childs category
    function check_parent_category(test){
        
        desktop =test.closest('.desktop').length;
        mobile =test.closest('.mobile').length;
        
        if (desktop == 1) {
            
            x = $('.desktop .has-child');
            var x2 =  [];
            for (i = 0; i < x.length ; i++) {
                var x1 = x[i].attributes[2].nodeValue;
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
                var x1 = x[i].attributes[2].nodeValue;
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
        var currentId = $(this)[0].attributes[0].nodeValue;
        var y = $(this)[0].attributes[0].nodeValue;
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
    // $("#min-price").val("$" + $(".slider-3").slider("values", 0));
    // $("#max-price").val("$" + $(".slider-3").slider("values", 1));
});