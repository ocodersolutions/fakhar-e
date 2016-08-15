<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
    <link rel="icon" href="<?php bloginfo('template_url'); ?>/favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/favicon.ico" type="image/x-icon" />
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/html5.js"></script>
	<![endif]-->
	<script>(function(){document.documentElement.className='js'})();</script>
	
    <?php wp_head(); ?>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/style.css">
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/fa/css/font-awesome.css">
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/owl.carousel.css">
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/normalize.css"> 
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/responsive.css">
    <script src="<?php bloginfo('template_url'); ?>/js/jquery.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/js/jquery.gray.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/js/headroom.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script type="text/javascript">
    jQuery(document).mouseup(function (e) {
        var container = jQuery('#menuicon');

        if (!container.is(e.target) // if the target of the click isn't the container...
            && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            if(jQuery('.mainmenu').hasClass('showmenu'))
                jQuery('.mainmenu').removeClass('showmenu').addClass('hidemenu');

            if(jQuery('.mainmenu').hasClass('showmenu1'))
                jQuery('.mainmenu').removeClass('showmenu1').addClass('hidemenu1');
        }
        else
        {
            if(jQuery('.mainmenu').hasClass('hidemenu'))
                jQuery('.mainmenu').removeClass('hidemenu').addClass('showmenu');

                jQuery('.mainmenu').removeClass('hidemenu1').addClass('showmenu1');
        }
    });
    </script>
</head>

<body <?php body_class(); ?> >
<div id="page" class="hfeed site">
	<header class="headroom">
   	    <div class="container">
            <nav>
                <ul id="navbar">
                    <li><a href="javascript:void(0);" id="menuicon"><img src="<?php bloginfo('template_url'); ?>/img/menu_icon.png" alt="menu" /></a>
                        <ul class="mainmenu hidemenu1">
                            <li><a href="http://<?=LNOVE_SITE_URL?>/info/how-it-works">How it works</a></li>
                            <li><a href="http://<?=LNOVE_SITE_URL?>/info/about-us">About Us</a></li>
                            <li><a href="http://<?=LNOVE_BLOG_URL?>">Blog</a></li>
                            <li><a href="http://<?=LNOVE_SITE_URL?>/info/frequently-asked-questions">FAQ</a></li>
                            <li><a href="http://<?=LNOVE_SITE_URL?>/info/frequently-asked-questions?tag=shipping">Shipping</a></li>
                            <li><a href="http://<?=LNOVE_SITE_URL?>/info/frequently-asked-questions?tag=pricing">Pricing</a></li> 
                            <li><a href="http://<?=LNOVE_SITE_URL?>/info/privacy-and-policies">Privacy and Policies</a></li>
                            <li><a href="http://<?=LNOVE_SITE_URL?>/info/exchange_and_return">Exchange & Returns</a></li>
                            <li><a href="http://<?=LNOVE_SITE_URL?>/info/careers">Careers</a></li>
                            <li><a href="http://<?=LNOVE_SITE_URL?>/info/contact-us">Contact Us</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <!-- <div class="header_right">
                <a  href="/auth/login" class="link_new">SIGN IN</a>
                <a  href="/profile" class="green_btn">GET STARTED</a>
            </div>-->

           <div class="logo2">
               <a href="http://<?=LNOVE_SITE_URL?>"><img src="<?php bloginfo('template_url'); ?>/img/lnove_logo.png" alt="lnove" /></a>
           </div>
		</div>
   	    <div class="clr"></div>
	</header>
	<div class="clr"></div>
    <div class="banner_bg"></div>
	<div id="content" class="site-content container">
		<section>