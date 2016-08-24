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
   $('.deals .item').click(function(){
        name_deal = $(this)[0].attributes[0].nodeValue;
        x = $("input[name='"+name_deal+"']").val();
        if(x == 1){
            $("input[name='"+name_deal+"']").val('');
            $(this).removeClass('selected');
        }else{
            $("input[name='"+name_deal+"']").val('1');
            $(this).addClass('selected');
        }         
    });
    //colors
    $('.colors .list-color .color').click(function(){
        array_color = $('.color.selected').toArray();
        var a2 =  [];
        for (i = 0; i < array_color.length ; i++) {
            var a1 = (array_color[i].attributes[0].nodeValue);
            a2.push(a1);
        };
        $("input[name='colors']").val(a2);
        var a2 = [];
    });
    //brands
    $('.brands .list-brand li').click(function(){
        if($(this).hasClass('selected')){
            $(this).removeClass('selected');
        }else{
            $(this).addClass('selected');
        }
        array_brand = $('.brands .item.selected').toArray();
        var a2 =  [];
        for (i = 0; i < array_brand.length ; i++) {
            var a1 = (array_brand[i].attributes[0].nodeValue);
            a2.push(a1);
        };
        $("input[name='brands']").val(a2);
        var a2 = [];
        Product.loadProductListAjax();
    });
    //category
    $('.category .item').click(function(){
      
        if($(this).hasClass('selected')){
            $(this).removeClass('selected');
        }else{
            $(this).addClass('selected');
        }
        array_category = $('.category .item.selected').toArray();
        var a2 =  [];
        for (i = 0; i < array_category.length ; i++) {
            var a1 = (array_category[i].attributes[0].nodeValue);
            a2.push(a1);
        };
        $("input[name='catids']").val(a2);
        var a2 = [];
        Product.loadProductListAjax();
    });
    //clear filter
     $('.clear-filter').click(function(){
        $('.item.selected').removeClass('selected');
        $('.has-child-selected').removeClass('has-child-selected');
        $('.color.selected').removeClass('selected');
        $('i.check.fa.fa-check').remove();
        x = $('#productFilterDetail input[type="hidden"]');
        for (i = 0; i < x.length ; i++) {
            y = x[i].attributes[3].nodeValue ;
            console.log("input[name="+y+"]");
            $("input[name="+y+"]").val("");
        };
    });
    // select childs category
    $('.desktop  .category .has-child').click(function() {

        id = $(this).attr('id');
        console.log($('.desktop .category .'+id+' .item').length);
        console.log($('.desktop .category .'+id+' .item.selected').length);


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
        array_category = $('.category .item.selected').toArray();
        var a2 =  [];
        for (i = 0; i < array_category.length ; i++) {
            var a1 = (array_category[i].attributes[0].nodeValue);
            a2.push(a1);
        };
        $("input[name='catids']").val(a2);
        var a2 = [];
        Product.loadProductListAjax();
            
    });
   $('.mobile .category .has-child').click(function() {

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
        array_category = $('.category .item.selected').toArray();
        var a2 =  [];
        for (i = 0; i < array_category.length ; i++) {
            var a1 = (array_category[i].attributes[0].nodeValue);
            a2.push(a1);
        };
        $("input[name='catids']").val(a2);
        var a2 = [];
        Product.loadProductListAjax();
    });
    //dropdown effect
    $('.refine .refine-colum ul .fa-angle-down').click(function() {
        x = $(this).parents('.category');
        if(x.length == 0){
            if ($(this).hasClass("change") == false) {
                $(this).addClass('change selected');
            } else {
                $(this).removeClass('change selected');
            }
        }
        var attrContent = getComputedStyle(this, ':after').content;
        var currentId = $(this)[0].attributes[0].nodeValue;
        $('.refine .refine-colum .' + currentId +', .fsp-wrapper .refine-colum .' + currentId + '').animate({
            left: "+=50",
            height: "toggle"
        }, 800, function() {});
    });
    //change icon dropdown
    $('.fa-angle-down').click(function(){
        var x = $(this)[0].attributes[0].nodeValue;
        if ($(this).hasClass("fa-selected") == false) {
            $(this).addClass('fa-selected');
            $('li#'+x).addClass('li-up');
        }else{
            $(this).removeClass('fa-selected');
            $('li#'+x).removeClass('li-up');
        }
    });
    //change icon color
    $('.refine .refine-colum .list-color .color').click(function() {
        if ($(this).hasClass("selected") == false) {
            $(this).addClass('selected').append(" <i class='check fa fa-check' aria-hidden='true'></i>");
        } else {
            $(this).removeClass('selected');
            ($(this).find(".check").remove());
        }
    });
    //add element for price filter
    $(document).ready(function() {
        $("#1 .slider-3 span:eq(0)").append("<p>250</p>");
        $("#1 .slider-3 span:eq(1)").append("<p>1000+</p>");
        $("#2 .slider-3 span:eq(0)").append("<p>250</p>");
        $("#2 .slider-3 span:eq(1)").append("<p>1000+</p>");
    });


//select fileter