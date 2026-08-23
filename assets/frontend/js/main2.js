;
(function($) {
    "use strict";

    $(document).ready(function() {

        var rtlEnable = $('html').attr('dir');
        var sliderRtlValue = !(typeof rtlEnable === 'undefined' || rtlEnable === 'ltr');
        if ($(window).width() < 992) {
            $(document).on('click', '.navbar-area .navbar-nav li.menu-item-has-mega-menu>a', function (e) {
                e.preventDefault();
            });
            $(document).on('click', '.navbar-area .navbar-nav li.menu-item-has-children>a', function (e) {
                e.preventDefault();
            });
        }

        /*-------------------------------
            Navbar Fix
        ------------------------------*/
        $(document).on('click', '.donate-lists li', function(event) {
            var el = $(this);
            var value = el.data('value');
            var parentWrap = el.parent().parent();
            el.addClass('active');
            el.siblings().removeClass('active');
            parentWrap.find('.donation-amount').val(value);

        });

        /*-------------------------------
            Donation Single
        ------------------------------*/

        $(document).on('click','#mobile_btn',function(e){
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('#donate_box_wrapper').offset().top - 200
            }, 2000);
            $(this).hide(1000);
        });


        /*-------------------------------
            Navbar Fix
        ------------------------------*/
        if ($(window).width() < 991) {
            navbarFix()
        }


        /*--------------------
            wow js init
        ---------------------*/
        new WOW().init();

        /*-------------------------
            magnific popup activation
        -------------------------*/
        $('.video-play,.video-popup,.small-vide-play-btn').magnificPopup({
            type: 'video'
        });

        /*------------------
            back to top
        ------------------*/
        $(document).on('click', '.back-to-top', function() {
            $("html,body").animate({
                scrollTop: 0
            }, 2000);
        });
        /*------------------------------
            donate activation
        -------------------------------*/
        var donate_link = '';
        $(document).on('change input', '.donation_wrapper > .amount_wrapper > input', function() {
            $(this).val(parseFloat($(this).val()).toFixed(2));
            $(this).parent().parent().find('> a').attr('href', donate_link.replace('{amount}', parseFloat($(this).val()).toFixed(2)));
        });
        $(document).on('click', '.donation_wrapper > .single_amount_wrapper > .single_amount', function() {
            $('.donation_wrapper > .amount_wrapper > input').val(parseFloat($(this).attr('data-value')).toFixed(2)).trigger('change');
        });

        /*---------------------------
            Testimonial carousel
        ---------------------------*/
        $('.testimonial-carousel').slick({
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: false,
            autoplay: true,
            centerMode: true,
            speed: 1000,
            autoplaySpeed: 5000,
            // fade: true,
            prevArrow: '<span class="slick-prev"><i class="fas fa-chevron-left"></i></span>',
            nextArrow: '<span class="slick-next"><i class="fas fa-chevron-right"></i></span>',
            appendArrows: $('.banner-slider-controls .slider-nav'),
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
        $('.content-carousel').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            asNavFor: '.testimonial-carousel',
            dots: false,
            centerMode: true,
            arrows: false,
            focusOnSelect: true
        });
        /*---------------------------
            BLog Grid carousel
        ---------------------------*/
        var $TestimonialCarousel = $('.blog-grid-carousel');
        if ($TestimonialCarousel.length > 0) {
            $TestimonialCarousel.owlCarousel({
                loop: true,
                autoplay: true, //true if you want enable autoplay
                autoPlayTimeout: 1000,
                margin: 30,
                dots: true,
                nav: true,
                navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                animateOut: 'fadeOut',
                animateIn: 'fadeIn',
                responsive: {
                    0: {
                        items: 1
                    },
                    460: {
                        items: 1
                    },
                    599: {
                        items: 1
                    },
                    768: {
                        items: 2
                    },
                    960: {
                        items: 2
                    },
                    1200: {
                        items: 3
                    },
                    1920: {
                        items: 3
                    }
                }
            });
        }
        /*---------------------------
            header carousel
        ---------------------------*/
        var $headerCarousel = $('.header-slider-one');
        if ($headerCarousel.length > 0) {
            $headerCarousel.owlCarousel({
                loop: true,
                autoplay: true, //true if you want enable autoplay
                autoPlayTimeout: 1000,
                margin: 30,
                dots: true,
                nav: true,
                rtl: sliderRtlValue,
                navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
                animateOut: 'fadeOut',
                animateIn: 'fadeIn',
                responsive: {
                    0: {
                        items: 1,
                        nav: false,
                    },
                    460: {
                        items: 1,
                        nav: false,
                    },
                    599: {
                        items: 1
                    },
                    768: {
                        items: 1
                    },
                    960: {
                        items: 1
                    },
                    1200: {
                        items: 1
                    },
                    1920: {
                        items: 1
                    }
                }
            });
        }

        /*----------------------
            Category Slider
        -----------------------*/

        $('.category-slider').slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: true,
            pauseOnHover: true,
            swipeToSlide: true,
            rtl: sliderRtlValue,
            responsive: [{
                    breakpoint: 991,
                    settings: {
                        arrows: false,
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        slidesToShow: 2,
                    }
                }
            ]
        });


        /*----------------------
            Featured Slider
        -----------------------*/

        $('.featured-slider').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: true,
            pauseOnHover: true,
            rtl: sliderRtlValue,
            swipeToSlide: true,
            responsive: [{
                    breakpoint: 991,
                    settings: {
                        arrows: false,
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        slidesToShow: 1,
                    }
                }
            ]
        });

        /*----------------------
            Success Slider
        -----------------------*/

        $('.success-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            dots: true,
            autoplay: true,
            pauseOnHover: true,
            rtl: sliderRtlValue,
        });

        /*----------------------
            Blog Slider
        -----------------------*/

        $('.blog-slider').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: true,
            pauseOnHover: true,
            rtl: sliderRtlValue,
            swipeToSlide: true,
            responsive: [{
                    breakpoint: 991,
                    settings: {
                        arrows: false,
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        slidesToShow: 1,
                    }
                }
            ]
        });



        /*----------------------
            Banner Mask Slider
        -----------------------*/

        $('.header-slider-new').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            // asNavFor: '.banner-mask-slider',
            dots: false,
            autoplay: true,
            pauseOnHover: true,
            rtl: sliderRtlValue,
        });

        /*----------------------
            Client Slider
        -----------------------*/

        $('.client-slider').slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            dots: false,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: true,
            draggable: true,
            pauseOnHover: true,
            swipeToSlide: true,
            adaptiveHeight: false,
            rtl: sliderRtlValue,
            responsive: [{
                    breakpoint: 991,
                    settings: {
                        arrows: false,
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        slidesToShow: 2,
                    }
                }
            ]
        });


        /*----------------------
            Search Popup
        -----------------------*/
        var bodyOvrelay = $('#body-overlay');
        var searchPopup = $('#search-popup');

        $(document).on('click', '#body-overlay', function(e) {
            e.preventDefault();
            bodyOvrelay.removeClass('active');
            searchPopup.removeClass('active');
        });
        $(document).on('click', '.border-none', function(e) {
            e.preventDefault();
            bodyOvrelay.removeClass('active');
            searchPopup.removeClass('active');
        });
        $(document).on('click', '#search', function(e) {
            e.preventDefault();
            searchPopup.addClass('active');
            bodyOvrelay.addClass('active');
        });

        /*-----------------
                Nice Select
            ------------------*/

            $('select').niceSelect();


    });


    //define variable for store last scrolltop
    var lastScrollTop = '';

    $(window).on('scroll', function() {

        //back to top show/hide
        var ScrollTop = $('.back-to-top');
        if ($(window).scrollTop() > 1000) {
            ScrollTop.fadeIn(1000);
        } else {
            ScrollTop.fadeOut(1000);
        }

        // Sticky-Memu
        if ($(window).width() > 991) {
            StickyMenu();
        }



    });


    $(window).on('resize', function() {
        /*-------------------------------
            Navbar Fix
        ------------------------------*/
        if ($(window).width() < 991) {
            navbarFix()
        }
    });


    $(window).on('load', function() {

        /*-----------------
            preloader
        ------------------*/
        var preLoder = $("#preloader");
        preLoder.fadeOut(1000);

        /*-----------------
            back to top
        ------------------*/
        var backtoTop = $('.back-to-top')
        backtoTop.fadeOut();

    });

    function navbarFix() {
        $(document).on('click', '.navbar-area .navbar-nav li.menu-item-has-children>a, .navbar-area .navbar-nav li.appside-megamenu>a', function(e) {
            e.preventDefault();
        })
    }

    function StickyMenu() {
        /*--------------------------
                 sticky menu activation
        ---------------------------*/
        var st = $(this).scrollTop();
        var mainMenuTop = $('.navbar-area');
        if ($(window).scrollTop() > 1000) {
            mainMenuTop.addClass('nav-fixed');
        } else {
            mainMenuTop.removeClass('nav-fixed ');
        }
        lastScrollTop = st;
    }

    /*--------------------------
             Tab js
    ---------------------------*/

    $('ul.tabs li').click(function() {
        var tab_id = $(this).attr('data-tab');
        $('ul.tabs li').removeClass('active');
        $('.tab-content').removeClass('active');
        $(this).addClass('active');
        $("#" + tab_id).addClass('active');
    });


})(jQuery);