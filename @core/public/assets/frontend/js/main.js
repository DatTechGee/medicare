(function ($) {
    "use strict";

    $(document).ready(function () {
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
        /*--------------------
        *  language change
        * ------------------*/

        $(document).on('click', '.selected-language', function (e) {
            e.preventDefault();
            $(this).next('ul').toggleClass('show');

        });
        var o = $(".global-carousel-init");
        o.length > 0 && $.each(o, function () {
            var t = $(this), n = t.children("div"), i = !!t.data("loop") && t.data("loop"),
                o = !!t.data("center") && t.data("center"), d = t.data("desktopitem") ? t.data("desktopitem") : 1,
                s = t.data("mobileitem") ? t.data("mobileitem") : 1,
                l = t.data("tabletitem") ? t.data("tabletitem") : 1, c = !!t.data("nav") && t.data("nav"),
                r = !!t.data("dots") && t.data("dots"), u = !!t.data("autoplay") && t.data("autoplay"),
                m = t.data("navcontainer") ? t.data("navcontainer") : "",
                v = t.data("stagepadding") ? t.data("stagepadding") : 0, p = t.data("margin") ? t.data("margin") : 0;
            n.length < 2 || t.owlCarousel({
                loop: i,
                autoplay: u,
                autoPlayTimeout: 5000,
                smartSpeed: 2000,
                autoplayHoverPause: true,
                margin: p,
                dots: r,
                center: o,
                nav: c,
                rtl: sliderRtlValue,
                navContainer: m,
                stagePadding: v,
                navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>'],
                responsive: {
                    0: {items: 1, nav: !1, stagePadding: 0},
                    460: {items: s, nav: !1, stagePadding: 0},
                    599: {items: s, nav: !1, stagePadding: 0},
                    768: {items: l, nav: !1, stagePadding: 0},
                    960: {items: l, nav: !1, stagePadding: 0},
                    1200: {items: d},
                    1920: {items: d}
                }
            })
        });
        var progressBar = $('.donation-progress');

        if (progressBar.length){
            $.each(progressBar,function (value){
                var el = $(this);
                progressBarInit(el,el.data('percentage'));
            })
        }
        function progressBarInit(selector,percentage){
            selector.rProgressbar({
                percentage: percentage,
                fillBackgroundColor: '#f1ae44'
            });
        }

        /*--------------------
           wow js init
       ---------------------*/
        new WOW().init();

        /*-------------------------
            magnific popup activation
        -------------------------*/
        $('.video-play-btn,.video-popup,.small-vide-play-btn,.video-play').magnificPopup({
            type: 'video'
        });
        $('.image-popup,.medical-image-popup').magnificPopup({
            type: 'image',
            gallery: {
                // options for gallery
                enabled: true
            },
        });

        /*------------------
           back to top
       ------------------*/
        $(document).on('click', '.back-to-top', function () {
            $("html,body").animate({
                scrollTop: 0
            }, 2000);
        });
        /*------------------------------
           counter section activation
       -------------------------------*/
        var counternumber = $('.count-num');
        if (counternumber.length > 1) {
            counternumber.rCounter();
        }

        /*-------------------------------
            case study filter
        ---------------------------------*/
        var $caseStudyThreeContainer = $('.case-studies-masonry');
        if ($caseStudyThreeContainer.length > 0) {
            $('.case-studies-masonry').imagesLoaded(function () {
                var caseMasonry = $caseStudyThreeContainer.isotope({
                    itemSelector: '.masonry-item', // use a separate class for itemSelector, other than .col-
                    isOriginLeft : $('html').attr('dir') === 'rtl' ? false : true,
                    masonry: {
                        gutter: 0
                    }
                });
                $(document).on('click', '.case-studies-menu li', function () {
                    var filterValue = $(this).attr('data-filter');
                    caseMasonry.isotope({
                        filter: filterValue
                    });
                });
            });
            $(document).on('click', '.case-studies-menu li', function () {
                $(this).siblings().removeClass('active');
                $(this).addClass('active');
            });
        }

        /*----------------------
            Search Popup
        -----------------------*/
        var bodyOvrelay = $('#body-overlay');
        var searchPopup = $('#search-popup');

        $(document).on('click', '#body-overlay,.search-popup-close-btn', function (e) {
            e.preventDefault();
            bodyOvrelay.removeClass('active');
            searchPopup.removeClass('show');
        });
        $(document).on('click', '#search', function (e) {
            e.preventDefault();
            searchPopup.addClass('show');
            bodyOvrelay.addClass('active');
        });

    });

    $(window).on('scroll', function () {

        //back to top show/hide
        var ScrollTop = $('.back-to-top');
        if ($(window).scrollTop() > 1000) {
            ScrollTop.fadeIn(1000);
        } else {
            ScrollTop.fadeOut(1000);
        }

    });


    $(window).on('load', function () {

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

    /*----------------------------------------
           SearchBar
      ----------------------------------------*/

        $('.search-close').on('click', function () {
            $('.search-bar').removeClass('active');
        });
        $('.search-open').on('click', function () {
            $('.search-bar').toggleClass('active');
        });


})(jQuery);
