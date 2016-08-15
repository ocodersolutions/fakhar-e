                function resetForm(){
                    $("#minamount").val( $('#slider').slider("option", "min") );
                    $("#maxamount").val( $('#slider').slider("option", "max") );
                    $("#minduration").val(0);
                    $("#maxduration").val(100);
                    $("#filter").val('');
                    $("#keyword").val('');
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

                function uncheckAllCheckbox(){
                    $("#brandautocomplete").val('');
                    $("body").find("input.brandSelection").prop("checked",false).removeClass('activebrand');

                }

                function resetSlider(){
                	
                	$("#slider").slider( "values", 0, $('#slider').slider("option", "min"));
                	$("#slider").slider( "values", 1, $('#slider').slider("option", "max"));
                	update(1,[$('#slider').slider("option", "min"),$('#slider').slider("option", "max")])

					$("#slider2").slider( "values", 0, $('#slider2').slider("option", "min"));
					$("#slider2").slider( "values", 1, $('#slider2').slider("option", "max"));
					update(2,[$('#slider2').slider("option", "min"),$('#slider2').slider("option", "max")])

                    update();
                }
                function resetColor(){
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

                /*clear filter*/
                $("button.deal").click(function(){
                    resetForm();
                    uncheckAllTree();
                    uncheckAllCheckbox();
                    resetSlider();
                    resetColor();
                    resetDeal();
                    loadProductData();
                });
                /*end clear filter*/