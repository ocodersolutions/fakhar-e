<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
get_header(); ?>
	
<div class="single_blog_box">
    <div class="single_blog_one">
        <?php custom_breadcrumbs() ?>
        <?php while ( have_posts() ) : the_post(); ?>
        <div class="title_box">
            <h2 class="main_title"><?php the_title(); ?></h2>
            <div class="date-time"><?php the_date('M j, Y'); ?></div>
            <div><?php the_content(); ?></div>        
            <div class="fl"></div>  
            <div class="fr share">
                <span>SHARE</span>
                <?php 
                $page_url = get_the_permalink(); 
                $title = get_the_title();
                ?>
                <ul class="blog-link">
                    <li><a href="https://api.addthis.com/oexchange/0.8/forward/facebook/offer?url=<?php echo $page_url; ?>&title=<?php echo $title; ?>" target="_blank"><img src="<?php bloginfo('template_url') ?>/img/fb.png" title="facebook" ></a></li>
                     <li><a href="https://api.addthis.com/oexchange/0.8/forward/twitter/offer?url=<?php echo $page_url; ?>&title=<?php echo $title; ?>" target="_blank"><img src="<?php bloginfo('template_url') ?>/img/twit.png" title="twitter"></a></li>
			        <li><a href="https://api.addthis.com/oexchange/0.8/forward/pinterest/offer?url=<?php echo $page_url; ?>&title=<?php echo $title; ?>" target="_blank"><img src="<?php bloginfo('template_url') ?>/img/p.png" title="pinterest"></a></li>
			        <li><a href="https://api.addthis.com/oexchange/0.8/forward/google_plusone_share/offer?url=<?php echo $page_url; ?>&title=<?php echo $title; ?>" target="_blank"><img src="<?php bloginfo('template_url') ?>/img/google.png" title="google"></a></li>
                </ul>
            </div>
            <div class="clr"></div>
        </div><!--title_box -->
                
        <div class="message-box">
			<?php if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif; ?>                   
        </div>
                   
        <div class="top_page martop ">
            <img src="<?php bloginfo('template_url') ?>/img/arrow.png"> <br />
            <a href=""> TOP OF PAGE</a>
        </div>
        <?php endwhile; ?>
    </div><!-- single_blog_one -->                
                            
    <?php get_sidebar(); ?>            
    <div class="clr"></div> 
</div>

<?php get_footer(); ?>
