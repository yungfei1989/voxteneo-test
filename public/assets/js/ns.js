jQuery(function (jQuery) {

'use strict';

    var jQuery = jQuery.noConflict();

    /**
    * ==============================
    * Page Loader 
    * ==============================
    */
    function addCSS(css){

        var head    = document.getElementsByTagName('head')[0],
            stylez       = document.createElement('style');

        stylez.setAttribute('type', 'text/css');

        if (stylez.styleSheet) {
            stylez.styleSheet.cssText = css;
        } else {                        
            stylez.appendChild(document.createTextNode(css));
        }

        head.appendChild(stylez);
    }

    addCSS('<style>#full-site-wrapper { display: none; opacity: 0; }</style>')


    jQuery(window).load(function () {
        
        setTimeout(function() {
            jQuery("#loading").fadeOut(100);
            jQuery("#full-site-wrapper").show().animate({
                opacity: 1
            }, 150)
        }, 500);

    /**
    * ==============================
    * Custom Scroll
    * ==============================
    */


        if (jQuery(window).width() < 767) {
            if (jQuery(".top-bar .navigation").length) {
                jQuery(".top-bar .navigation").mCustomScrollbar({
                    theme: "dark-2",
                    scrollButtons: {
                        enable: false
                    }
                });
            }
        }
    /**
    * ==============================
    *  ISOTOPE
    * ==============================
    */

        if (jQuery().isotope) {
            var jQuerycontainer = jQuery('.isotope'); // cache container
            jQuerycontainer.isotope({
                itemSelector: '.isotope-item'
            });
            jQuery('.filtrable a').click(function () {
                var selector = jQuery(this).attr('data-filter');
                jQuery('.filtrable li').removeClass('current');
                jQuery(this).parent().addClass('current');
                jQuerycontainer.isotope({filter: selector});
                return false;
            });
            jQuerycontainer.isotope('layout'); // layout/layout
        }

        jQuery(window).resize(function () {
            if (jQuery().isotope) {
                jQuery('.row.isotope').isotope('layout'); // layout/relayout on window resize
            }
        });

        jQuery('#product-filter').isotope({filter: '.tab-1'});

    });


    jQuery(window).scroll(function () {

    /**
    * ==============================
    * Scroll To Top 
    * ==============================
    */
        if (jQuery(this).scrollTop() > 100) {
            jQuery('.to-top').css({bottom: '55px'});
        } else {
            jQuery('.to-top').css({bottom: '-150px'});
        }

    });


    jQuery(function (jQuery) {


    /**
    * ==============================
    * Remove Active Class 
    * ==============================
    */

        jQuery(document).click(function (e) {
            var active = e.target ? jQuery(e.target).closest('.active').get(0) : null;
            jQuery("body").removeClass('off-canvas-body');            
        });

    /**
    * ==============================
    * Header Off Canvas Add
    * ==============================
    */

        jQuery(".nav-trigger").on("click", function (e) {
            e.stopPropagation();
            jQuery(".header-wrap .navigation").toggleClass("off-canvas");
            jQuery("body").toggleClass("off-canvas-body");
        });


    /**
    * ==============================
    * Scroll To Top Animate
    * ==============================
    */

        jQuery('.to-top').click(function () {
            jQuery('html, body').animate({scrollTop: 0}, 800);
            return false;
        });


    /**
    * ==============================
    * Product Thumbnails Hover 
    * ==============================
    */

        jQuery('.product-thumbnails').on('click', 'li', function () {
            jQuery('.product-thumbnails li.active').removeClass('active');
            jQuery(this).addClass('active');
        });


    /**
    * ==============================
    * Header PopUps
    * ==============================
    */

        jQuery(".search-hover, .toggle-hover, .cart-hover").each(function () {
            var $toggle = jQuery(this);
            $toggle.children('a').click(function (e) {
                e.preventDefault();
                $toggle.toggleClass("active");
            });
        });


    /**
    * ==============================
    * Subscribe Popup
    * ==============================
    */

    jQuery(".sb-close-btn").on('click',function(){
      sessionStorage.setItem("subcriber_popup", 0);      
    })
    
//    if(sessionStorage.getItem("subcriber_popup") != 0){
//        jQuery(".subscribe-me").subscribeBetter({
//            trigger: "onidle", // You can choose which kind of trigger you want for the subscription modal to appear. Available triggers are "atendpage" which will display when the user scrolls to the bottom of the page, "onload" which will display once the page is loaded, and "onidle" which will display after you've scrolled.
//            animation: "flyInDown", // You can set the entrance animation here. Available options are "fade", "flyInRight", "flyInLeft", "flyInUp", and "flyInDown". The default value is "fade".
//            delay: 0, // You can set the delay between the trigger and the appearance of the modal window. This works on all triggers. The value should be in milliseconds. The default value is 0.
//            showOnce: true, // Toggle this to false if you hate your users. :)
//            autoClose: false, // Toggle this to true to automatically close the modal window when the user continue to scroll to make it less intrusive. The default value is false.
//            scrollableModal: false      //  If the modal window is long and you need the ability for the form to be scrollable, toggle this to true. The default value is false.
//        });
//        jQuery("#adsModal").modal('toggle');
//      }
//      else{
//        jQuery(".subscribe-me").hide();
//      }
      
      window.onscroll = function() {showPopup()};
      function showPopup() {
        if(jQuery("#adsModal").hasClass('inactive')){
            return false;
        }
        if ((document.body.scrollTop > 550 || document.documentElement.scrollTop > 550) && sessionStorage.getItem("subcriber_popup") != 0) {
          jQuery("#adsModal").modal('show');
          jQuery("#adsModal").addClass('inactive');
        }
      }
      
    /**
    * ==============================
    * Page Loader Text 
    * ==============================
    */

        var words = ["SUNGLASSES", "FASHION", "INTERIOR", "COSMETIC", "JEWELRY", "BAKERY", "BIKE", "ACCESSORIES", "COLLECTIONS", "SUGGETIONS"];

        window.addEventListener("load", function () {
            var randoms = window.document.getElementsByClassName("randoms");
            for (var i = 0; i < randoms.length; i++)
                changeWord(randoms[i]);
        }, false);

        function changeWord(a) {
            a.style.opacity = '0.1';
            a.innerHTML = words[getRandomInt(0, words.length - 1)];
            setTimeout(function () {
                a.style.opacity = '1';
            }, 825);
            setTimeout(function () {
                changeWord(a);
            }, getRandomInt(500, 1600));
        }

        function getRandomInt(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }
 


    /**
    * ==============================
    * Revolution Slider 
    * ==============================
    */

        if (jQuery('.slider-section').length > 0) {
            jQuery('.tp-banner').revolution({
                delay: 15000,
                startwidth: 300,
                startheight: 420,
                hideThumbs: 10,
                fullWidth: "off",
                forceFullWidth: "off",
                onHoverStop: "off",
                navigationStyle: "square",
                spinner: "spinner2",
                hideTimerBar: "on"
            });
        }

    /**
    * ==============================
    * Sticky Header
    * ==============================
    */

        if (jQuery(window).width() > 767) {
                
            jQuery(window).scroll(function(e) {

                var scrollTopDistance = jQuery(document).scrollTop();

                if( scrollTopDistance > 200 ) {
                    jQuery(".main-header").addClass("navbar-fixed-top");
                } else {
                    jQuery(".main-header").removeClass("navbar-fixed-top");
                }
            });
        } 


        

        if (jQuery(window).width() < 767) {
                
        }
            

        jQuery('.cate-toggle').click(function () {
            jQuery('.cate-wrap').slideToggle();
            
            return false;
        });

    /**
    * ==============================
    * Best Seller Slider 
    * ==============================
    */

        if (jQuery('.deal-slider').length > 0) {
            jQuery(".deal-slider").owlCarousel({
                dots: false,
                loop: false,
                autoplay: false,
                nav: true,
                autoplayHoverPause: true,
                smartSpeed: 100,
                margin: 30,
                responsive: {
                    0: {items: 1},
                    1200: {items: 4},
                    990: {items: 3},
                    600: {items: 2},
                    480: {items: 1}
                },
                navText: [
                    "<i class='arrow_carrot-left'></i>",
                    "<i class='arrow_carrot-right'></i>"
                ]
            });
        }


        if (jQuery('.deal-cntdwn').length > 0) {
            jQuery('#cntdwn-1').countdown({since: new Date(2019, 12 - 1, 59)});
            jQuery('#cntdwn-2').countdown({since: new Date(2019, 9 - 1, 54)});
            jQuery('#cntdwn-3').countdown({since: new Date(2015, 12 - 1, 58)});
            jQuery('#cntdwn-4').countdown({since: new Date(2015, 12 - 1, 47)});
            jQuery('#cntdwn-5').countdown({since: new Date(2015, 12 - 1, 54)});
            jQuery('#cntdwn-6').countdown({since: new Date(2015, 12 - 1, 58)});
        }

    /**
    * ==============================
    * Featured Product Slider
    * ==============================
    */

        if (jQuery('.featured-slider').length > 0) {
            jQuery(".featured-slider").owlCarousel({
                dots: false,
                loop: true,
                autoplay: true,
                nav: true,
                autoplayHoverPause: true,
                smartSpeed: 100,
                margin: 30,
                responsive: {
                    0: {items: 2},
                    1200: {items: 4},
                    990: {items: 3},
                    600: {items: 2},
                    480: {items: 2}
                },
                navText: [
                    "<i class='arrow_carrot-left'></i>",
                    "<i class='arrow_carrot-right'></i>"
                ]
            });
        }


    /**
    * ==============================
    * Best Seller Slide
    * ==============================
    */

        if (jQuery('.best-seller').length > 0) {
            jQuery(".best-seller").owlCarousel({
                dots: false,
                loop: true,
                autoplay: true,
                nav: true,
                autoplayHoverPause: true,
                smartSpeed: 100,
                margin: 30,
                responsive: {
                    0: {items: 2},
                    1200: {items: 4},
                    990: {items: 3},
                    600: {items: 2},
                    480: {items: 2}
                },
                navText: [
                    "<i class='arrow_carrot-left'></i>",
                    "<i class='arrow_carrot-right'></i>"
                ]
            });
        }
        
    /**
    * ==============================
    * Related Product Slide
    * ==============================
    */

        if (jQuery('.related-prod-slider').length > 0) {
            jQuery(".related-prod-slider").owlCarousel({
                dots: false,
                loop: true,
                autoplay: true,
                nav: true,
                autoplayHoverPause: true,
                smartSpeed: 100,
                margin: 30,
                responsive: {
                    0: {items: 2},
                    1200: {items: 4},
                    990: {items: 3},
                    600: {items: 2},
                    480: {items: 2}
                },
                navText: [
                    "<i class='arrow_carrot-left'></i>",
                    "<i class='arrow_carrot-right'></i>"
                ]
            });
        }

    /**
    * ==============================
    * Brand Slider Slider
    * ==============================
    */

        if (jQuery('.brand-slider').length > 0) {
            jQuery(".brand-slider").owlCarousel({
                dots: false,
                loop: true,
                autoplay: true,
                nav: true,
                autoplayHoverPause: true,
                smartSpeed: 100,
                stagePadding: 100,
                responsive: {
                    0: {items: 2},
                    1200: {items: 3},
                    990: {items: 3},
                    600: {items: 2},
                    480: {items: 2}
                },
                navText: [
                    "<i class='arrow_carrot-left'></i>",
                    "<i class='arrow_carrot-right'></i>"
                ]
            });
        }


        if (jQuery('.store-slider').length > 0) {
            jQuery(".store-slider").owlCarousel({
                dots: false,
                loop: true,
                autoplay: true,
                nav: true,
                autoplayHoverPause: true,
                smartSpeed: 100,
                responsive: {
                    0: {items: 2},
                    1200: {items: 8},
                    990: {items: 3},
                    600: {items: 2},
                    480: {items: 2}
                },
                navText: [
                    "<i class='arrow_carrot-left'></i>",
                    "<i class='arrow_carrot-right'></i>"
                ]
            });
        }
        
    /**
    * ==============================
    * FAQS Slider
    * ==============================
    */

        if (jQuery('.faqs-slider').length > 0) {
            jQuery(".faqs-slider").owlCarousel({
                dots: false,
                loop: false,
                autoplay: true,
                nav: true,
                autoplayHoverPause: true,
                responsive: {
                    0: {items: 1},
                    1200: {items: 1}
                },
                navText: [
                    "<i class='arrow_carrot-left'></i>",
                    "<i class='arrow_carrot-right'></i>"
                ]
            });
        }

    });


});


function sendWA(phone){
  var url ="https://web.whatsapp.com/send?phone="+ phone;
  window.open(url); 
}

jQuery('#search-btn').on('click',function (e) {
  e.preventDefault();
  window.location = '/search?keyword='+ jQuery('#search_keyword').val();
});