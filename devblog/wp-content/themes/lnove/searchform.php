<form method="get" id="searchform" action="<?php echo home_url() ; ?>/">
	<input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" class="my_search" >
	<div class="search"> 
	  <img class="submit_search" src="<?php bloginfo('template_url') ?>/img/search.png"> 
	</div>
</form>
