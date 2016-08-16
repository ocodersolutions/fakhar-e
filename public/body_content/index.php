<!--  -->
<!DOCTYPE html>
<html class="no-js">
    <head>
		<meta charset="utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	    <title>header</title>
	    <link rel="stylesheet/less" type="text/css" href="body1.less" />
	    <link rel="stylesheet/less" type="text/css" href="../assets/css/custom_product_list.less" />
	   
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">  
	    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700,900,bold' rel='stylesheet' type='text/css'>
	    <script   src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
	    <script src="http://cdnjs.cloudflare.com/ajax/libs/less.js/2.6.1/less.min.js"></script>
	    
    </head>
    <body>
	<div id="body-main-sideright" class="col-lg-12">
		<div class="row top-option">    
		   	<form action="" id="check-option" class="col-lg-3"><input type="radio" name="radio-1" id="radio-1">
			    <label for="radio-1"><span class="rd-check"></span>ALL</label>
			    <input type="radio" name="radio-1" id="radio-2">
			    <label for="radio-2"><span class="rd-check"></span>SALE</label>
			    <input type="radio" name="radio-1" id="radio-3">
			    <label for="radio-3"><span class="rd-check"></span>SAVED ITEMS</label>
	   		</form>
	   		<div class="items-tag"><span>PUMA<i class="fa fa-times" aria-hidden="true"></i></span></div>
	   		<div class="items-tag"><span>BLUE<i class="fa fa-times" aria-hidden="true"></i></span></div>
	   		<div class="items-tag"><span>SPECIAL OFFERS<i class="fa fa-times" aria-hidden="true"></i></span></div>
		</div>
        <div class="row ">
         	<div class="col-lg-3" id="product">
				
				<!-- start of team member -->
				<div class="product_item">
					<div class="uc_contact">
						<img class="uc_avatar" src="product_item.jpg">
						<div class="uc_link">
							<a href="#" id="active1" class="uc_icon" onclick="toggleColor('active1')" >
								<i class="fa fa-heart-o" aria-hidden="true"></i>
							</a>

							<a href="#" class="uc_icon" onclick="display_mode('object_share','show');">
								<i class="fa fa-share-alt" aria-hidden="true"></i>
							</a>

							<a href="#" id="active2" class="uc_icon" onclick="toggleColor('active2')" >
								<i class="fa fa-bell-o" aria-hidden="true"></i>
							</a>

							<a href="#" class="buy_product"><span>BUY</span></a>
						</div>				
					</div>		<!-- End .product_item -->

					<div class="clear_float"></div>

					<div class="share_product" id="object_share" >
						<i class="fa fa-times " aria-hidden="true" onclick="display_mode('object_share','hide');"></i>
						<h2>SHARE VIA</h2>
						<div class="share_page">
							<a href="#">
								<i class="fa fa-facebook fa-2x " aria-hidden="true" ></i> 
								<span>Facebook</span>
							</a>
						</div>
						<div class="clear_float"></div>
						<div class="share_page">
							<a href="#">
								<i class="fa fa-pinterest-p fa-2x " aria-hidden="true"></i> 
								<span>Pinterest</span>
							</a>
						</div>
						<div class="clear_float"></div>
						<div class="share_page">
							<a href="#">
								<i class="fa fa-tumblr fa-2x " aria-hidden="true"></i> 
								<span>Twitter</span>
							</a>
						</div>
						<div class="clear_float"></div>
						<div class="share_page">
							<a href="#">
								<i class="fa fa-google-plus fa-2x " aria-hidden="true"></i> 
								<span>Google +</span>
							</a>
						</div>
						<div class="clear_float"></div>
						<div class="share_page">
							<a href="#">
								<i class="fa fa-envelope-o fa-2x " aria-hidden="true"></i> 
								<span>Email</span>
							</a>
						</div>					
					</div>		<!-- End .share_product -->

					<div class="clear_float"></div>

					<div class="uc_info">
						<p class="uc_job"><span>65.00 CAD</span>&nbsp;&nbsp;48.00 CAD</p>
						<p>Puma Football Windbreaker</p>
					</div>
				</div>
				<!-- end of team member -->
		
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 2</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 3</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 4</div>
         	</div>
         	<div class="col-lg-3" id="product">
				<div class="product">product 5</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 6</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 7</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 8</div>
         	</div>
         	<div class="col-lg-3" id="product">
				<div class="product">product 9</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 10</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 11</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 12</div>
         	</div>
         	<div class="col-lg-3" id="product">
				<div class="product">product 13</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 14</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 15</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 16</div>
         	</div>
         	<div class="col-lg-3" id="product">
				<div class="product">product 17</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 18</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 19</div>
         	</div>
         	<div class="col-lg-3" id="product">
         		<div class="product">product 12</div>
         	</div>
         	<!-- <div class="row" id="load-more">
         		
         	</div> -->
        </div>
        <div id="load-more" class="row text-center">
			<button class="btn-st-silver">Load more</button>
		</div>
	</div>
		<script type="text/javascript">

			function display_mode(id, mode) {
				var e = document.getElementById(id);
				if(mode == 'show') e.style.display = 'block';
				else e.style.display = 'none';
	    	}

	    	function toggleColor(id) { 
				var myClasses = document.getElementById(id).classList;
				if (myClasses.contains("active")) {
				myClasses.remove("active");
				} else {
				myClasses.add("active");
				}
				
			}
	    	
		</script>
    </body>
</html>