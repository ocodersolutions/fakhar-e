<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "site-content" div and all content after.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
?>
	</section>
	</div><!-- .site-content -->

    <footer>
        <div class="container">
            <div class="left-foot">
                <div class="foot_row">
                    <ul class="list">
                        <li><a href="http://<?=LNOVE_SITE_URL?>/info/how-it-works">How it works</a></li>
                        <li><a href="http://<?=LNOVE_SITE_URL?>/info/about-us">About Us</a></li>
                        <li><a href="http://<?=LNOVE_BLOG_URL?>">Blog</a></li>
                        <li><a href="http://<?=LNOVE_SITE_URL?>/info/frequently-asked-questions">FAQ</a></li>
                    </ul>
                </div>

                <div class="foot_row large">
                    <ul class="list">
                        <li><a href="http://<?=LNOVE_SITE_URL?>/info/frequently-asked-questions?tag=shipping">Shipping</a></li>
                        <li><a href="http://<?=LNOVE_SITE_URL?>/info/frequently-asked-questions?tag=pricing">Pricing</a></li> 
                        <li><a href="http://<?=LNOVE_SITE_URL?>/info/privacy-and-policies">Privacy and Policies</a></li>
                        <li><a href="http://<?=LNOVE_SITE_URL?>/info/terms-and-conditions">Terms and Conditions</a></li>
                    </ul>
                </div>
                <div class="foot_row large">
                    <ul class="list">
                        <li><a href="http://<?=LNOVE_SITE_URL?>/info/exchange_and_return">Exchange & Returns</a></li>
                        <li><a href="http://<?=LNOVE_SITE_URL?>/info/contact-us">Contact Us</a></li>
                        <li><a href="http://<?=LNOVE_SITE_URL?>/info/careers">Careers</a></li>
                    </ul>
                </div>
                <div class="input-box">

                    <div style="margin-bottom: 10px;    color: #525058;">
                        <label>To subscribe to our newsletter</label>
                    </div>
                    <input type="text" class="input" id="idNewsLetterInput" name="newsLetterInput" placeholder=" Your email Address">
                    <button class="button" id="idNewsLetter" >  <img src="<?php bloginfo('template_url'); ?>/img/arrow-right.png"></i>

                    </button>
                </div>
            </div>
            <div class="right-foot">
                <div>
                    <h4>CONTACT US</h4>
                    <span>97 Ecclesfield Drive, <br />Toronto, Ontario,</span>
                    <p> M1W 2Y3</p>
                    <p>support@elnove.com</p>
                </div>
            </div>
            <div class="clr"></div>
        </div>
        </div>
        </div>
        <div class="clr"></div>
        </section>
    </footer>
    <div class="last-foot">
        <div class="container">
            <div class="copy">COPYRIGHT 2016 STITCH STYLE SOLUTIONS INC. ALL RIGHTS RESERVED</div>
            <div class="social">
                <ul class="link">
                   <li>
                        <a href="https://facebook.com/ELNove-298882480296894/"> <i class="facebook_icon"></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://twitter.com/elnovethe9s"> <i class="twitter_icon"></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/elnove/"> <i class="instagram_icon"></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.youtube.com/channel/UCpf1DXmAFx_Khiku7dQYQLw"> <i class="youtube_icon"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div><!-- .site -->

<?php wp_footer(); ?>

<script type="text/javascript">
    /* Hide/Show header when user scrolls up and down on mobile */
    $(function () {
        if($(window).width() < 960){
            $('.headroom').each(function () {
                var $win = $(window)
                  , $self = $(this)
                  , isHidden = false
                  , lastScrollTop = 0

                $win.on('scroll', function () {
                  var scrollTop = $win.scrollTop()
                  var offset = scrollTop - lastScrollTop
                  lastScrollTop = scrollTop

                  // min-offset, min-scroll-top
                  if (offset > 10 && scrollTop > 200 ) {
                    if (!isHidden) {
                      $self.addClass('headroom-hidden')
                      isHidden = true
                    }
                  } else if (offset < -10 || scrollTop < 200) {
                    if (isHidden) {
                      $self.removeClass('headroom-hidden')
                      isHidden = false
                    }
                  }
                })
            })
        }
    })

    jQuery('.top_page').click(function(){
        jQuery('html, body').animate({scrollTop : 0},800);
        return false;
    });
</script>
<script type="text/javascript">
    jQuery(".submit_search").click(function(){
        jQuery("#searchform").submit();
    });

    var cross_site = 'elnove.com';
    if($(location).attr('host')=='devblog.elnove.com') {
        cross_site = 'trung.elnove.com';
    } else  if($(location).attr('host')=='s.blog.elnove.com') {
        cross_site = 'staging.elnove.com';
    } else if($(location).attr('host')=='devblog.lnove.local') {
        cross_site = 'lnove.local';
    }
    $('#idNewsLetter').click(function(){ 
        var $email = $('#idNewsLetterInput'); //change form to id or containment selector
        var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
        if ($email.val() == '' || !re.test($email.val())) {
            //myalert('alert_idNewsLetterInput3','type_missing','Ooops!','','OK','Please enter valid email address.',function(){$('#alert_idNewsLetterInput3').foundation('reveal','close'); });
            alert("Ooops!, Please enter valid email address.")
        }
        else {
            $.ajax({
                url: "http://" + cross_site +"/index/newsletter",
                type: 'POST',
                data: {newsLetterInput: $('#idNewsLetterInput').val()},
                dataType: 'json',
                success: function (result) {
                    console.log(result); 
                    console.log(result.status); 
                    if (result.status == 'success') { 
                        $('#idNewsLetterInput').val('');
                        alert("You have been successfully added to our newsletter.");
                        //myalert('alert_idNewsLetterInput1','type_success','Success','','OK','You have been successfully added to our newsletter.',function(){$('#alert_idNewsLetterInput1').foundation('reveal','close'); });
                    } else {
                         //myalert('alert_idNewsLetterInput2', 'type_cancel','Internal Server Error', 'Please contact site administrator.','','',function(){$('#alert_idNewsLetterInput2').foundation('reveal','close'); });
                        alert("Internal Server Error! Please contact site administrator. ")
                    }
                }});                     
           
        }

    });

</script>


<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-5F6KJB"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5F6KJB');</script>
<!-- End Google Tag Manager -->    

</body>
</html>
