<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

get_header(); ?>

<div class="home-page blog_one">
   	<?php if ( have_posts() ) {
   		//Check Homepage
   		if ( is_home() && ! is_front_page() ) {
 			echo '<h1 class="title">OUR BLOG</h1>';
   		}//end if
		
		// Start the loop.
		while ( have_posts() ) {
		 	the_post() ?>   
		  
	    	<div class="back_box"></div>
	    	<div class="white_box">
	    		<?php
	        	$page_url = get_the_permalink(); 
				$title = get_the_title();
				?>
	        	<a href="<?php echo $page_url ?>"><h2><?php echo $title; ?></h2></a>
	        	<div class="date-time"><?php the_date('M j, Y'); ?></div>
				<div class="post-img-thumnail-home">
			        <?php // Check if the post has a Post Thumbnail assigned to it.
					if ( has_post_thumbnail() ) {
					    the_post_thumbnail('large');
					} ?>
		        </div>
	        	<?php the_excerpt(); ?>
	            <div class="fr share">
	          		<div class="share_social">
		          		<span>SHARE</span>
		             	<ul class="blog-link">
			                <li><a href="https://api.addthis.com/oexchange/0.8/forward/facebook/offer?url=<?php echo $page_url; ?>&title=<?php echo $title; ?>" target="_blank"><img src="<?php bloginfo('template_url') ?>/img/fb.png" title="facebook"></a></li>
	                     	<li><a href="https://api.addthis.com/oexchange/0.8/forward/twitter/offer?url=<?php echo $page_url; ?>&title=<?php echo $title; ?>" target="_blank"><img src="<?php bloginfo('template_url') ?>/img/twit.png" title="twitter"></a></li>
				            <li><a href="https://api.addthis.com/oexchange/0.8/forward/pinterest/offer?url=<?php echo $page_url; ?>&title=<?php echo $title; ?>" target="_blank"><img src="<?php bloginfo('template_url') ?>/img/p.png" title="pinterest"></a></li>			                
				            <li><a href="https://api.addthis.com/oexchange/0.8/forward/google_plusone_share/offer?url=<?php echo $page_url; ?>&title=<?php echo $title; ?>" target="_blank"><img src="<?php bloginfo('template_url') ?>/img/google.png" title="google"></a></li>
		             	</ul>	
	          		</div>
	            </div>
	            <div class="clr"></div>
	   		</div><!--white_box -->
	   		<div class="clr"></div>
   		<?php } //end while ?>
   
    	<div class="top_page padzero" style="cursor: pointer">
       		<img src="<?php bloginfo('template_url')?>/img/arrow.png"> <br />
       		<a href="javascript:void(0)"> TOP OF PAGE</a>
   		</div>
   	<?php } else {
		get_template_part( 'content', 'none' );
	} //end if ?>
</div>

<?php get_footer(); ?>

