(function ($) {
    "use strict";


    /*---------- 01. On Load Function ----------*/
    $(window).on("load", function () {
        $(".preloader").fadeOut();
    });

    $(window).on('resize', function () {
        $(".slick-slider").slick("refresh");
    });
    // $('select').niceSelect(); 
    if ($('.nice-select').length) {
        $('.nice-select').niceSelect();
    }

    /*---------- 02. Preloader ----------*/
    if ($(".preloader").length > 0) {
        $(".preloaderCls").each(function () {
            $(this).on("click", function (e) {
                e.preventDefault();
                $(".preloader").css("display", "none");
            });
        });
    }

    /*---------- 03. Mobile Menu ----------*/
    $.fn.thmobilemenu = function (options) {
        var opt = $.extend(
            {
                menuToggleBtn: ".th-menu-toggle",
                bodyToggleClass: "th-body-visible",
                subMenuClass: "th-submenu",
                subMenuParent: "menu-item-has-children",
                thSubMenuParent: "th-item-has-children",
                subMenuParentToggle: "th-active",
                meanExpandClass: "th-mean-expand",
                subMenuToggleClass: "th-open",
                toggleSpeed: 400,
            },
            options
        );

        return this.each(function () {
            var menu = $(this); // Select menu

            // Menu Show & Hide
            function menuToggle() {
                menu.toggleClass(opt.bodyToggleClass);

                // collapse submenu on menu hide or show
                var subMenu = "." + opt.subMenuClass;
                $(subMenu).each(function () {
                    if ($(this).hasClass(opt.subMenuToggleClass)) {
                        $(this).removeClass(opt.subMenuToggleClass);
                        $(this).css("display", "none");
                        $(this).parent().removeClass(opt.subMenuParentToggle);
                    }
                });
            }

            // Class Set Up for every submenu
            menu.find("." + opt.subMenuParent).each(function () {
                var submenu = $(this).find("ul");
                submenu.addClass(opt.subMenuClass);
                submenu.css("display", "none");
                $(this).addClass(opt.subMenuParent);
                $(this).addClass(opt.thSubMenuParent); // Add th-item-has-children class
                $(this).children("a").append(opt.appendElement);
            });

            // Toggle Submenu
            function toggleDropDown($element) {
                var submenu = $element.children("ul");
                if (submenu.length > 0) {
                    $element.toggleClass(opt.subMenuParentToggle);
                    submenu.slideToggle(opt.toggleSpeed);
                    submenu.toggleClass(opt.subMenuToggleClass);
                }
            }

            // Submenu toggle Button
            var itemHasChildren = "." + opt.thSubMenuParent + " > a";
            $(itemHasChildren).each(function () {
                $(this).on("click", function (e) {
                    e.preventDefault();
                    toggleDropDown($(this).parent());
                });
            });

            // Menu Show & Hide On Toggle Btn click
            $(opt.menuToggleBtn).each(function () {
                $(this).on("click", function () {
                    menuToggle();
                });
            });

            // Hide Menu On outside click
            menu.on("click", function (e) {
                e.stopPropagation();
                menuToggle();
            });

            // Stop Hide full menu on menu click
            menu.find("div").on("click", function (e) {
                e.stopPropagation();
            });
        });
    };


    $(".th-menu-wrapper").thmobilemenu();

    /*---------- 04. Sticky fix ----------*/
    $(window).on("scroll", function () {
        var topPos = $(this).scrollTop();
        sticky()
        if (topPos > 150) {
            $('.sticky-wrapper').addClass('will-sticky')
            sticky()
        } else {
            $('.sticky-wrapper').removeClass('sticky')
            $('.sticky-wrapper').removeClass('will-sticky')
        }

        function sticky() {
            if (topPos > 400) {
                $('.sticky-wrapper').addClass('sticky')
                $('.sticky-wrapper').removeClass('will-sticky')
            }
        }
    })

    /*----------- 04.1.  One Page Nav ----------*/
    function onePageNav(element) {
        if ($(element).length > 0) {
            $(element).each(function () {
                var link = $(this).find('a');
                $(this).find(link).each(function () {
                    $(this).on('click', function () {
                        var target = $(this.getAttribute('href'));
                        if (target.length) {
                            event.preventDefault();
                            $('html, body').stop().animate({
                                scrollTop: target.offset().top - 10
                            }, 1000);
                        };

                    });
                });
            })
        }
    };
    onePageNav('.onepage-nav');
    onePageNav('.scroll-down');

    /*---------- 05. Scroll To Top ----------*/
    if ($('.scroll-top').length > 0) {

        var scrollTopbtn = document.querySelector('.scroll-top');
        var progressPath = document.querySelector('.scroll-top path');
        var pathLength = progressPath.getTotalLength();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
        progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
        progressPath.style.strokeDashoffset = pathLength;
        progressPath.getBoundingClientRect();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
        var updateProgress = function () {
            var scroll = $(window).scrollTop();
            var height = $(document).height() - $(window).height();
            var progress = pathLength - (scroll * pathLength / height);
            progressPath.style.strokeDashoffset = progress;
        }
        updateProgress();
        $(window).scroll(updateProgress);
        var offset = 50;
        var duration = 750;
        jQuery(window).on('scroll', function () {
            if (jQuery(this).scrollTop() > offset) {
                jQuery(scrollTopbtn).addClass('show');
            } else {
                jQuery(scrollTopbtn).removeClass('show');
            }
        });
        jQuery(scrollTopbtn).on('click', function (event) {
            event.preventDefault();
            jQuery('html, body').animate({
                scrollTop: 0
            }, duration);
            return false;
        })
    }

    /*---------- 06. Set Background Image ----------*/
    if ($("[data-bg-src]").length > 0) {
        $("[data-bg-src]").each(function () {
            var src = $(this).attr("data-bg-src");
            $(this).css("background-image", "url(" + src + ")");
            $(this).removeAttr("data-bg-src").addClass("background-image");
        });
    }

    /*----------- 07. Global Slider ----------*/
    $(".th-carousel").each(function () {
        var asSlide = $(this);

        // Collect Data
        function d(data) {
            return asSlide.data(data);
        }

        // Custom Arrow Button
        var prevButton =
            '<button type="button" class="slick-prev"><i class="' +
            d("prev-arrow") +
            '"></i></button>',
            nextButton =
                '<button type="button" class="slick-next"><i class="' +
                d("next-arrow") +
                '"></i></button>';

        // Function For Custom Arrow Btn
        $("[data-slick-next]").each(function () {
            $(this).on("click", function (e) {
                e.preventDefault();
                $($(this).data("slick-next")).slick("slickNext");
            });
        });

        $("[data-slick-prev]").each(function () {
            $(this).on("click", function (e) {
                e.preventDefault();
                $($(this).data("slick-prev")).slick("slickPrev");
            });
        });

        // Check for arrow wrapper
        if (d("arrows") == true) {
            if (!asSlide.closest(".arrow-wrap").length) {
                asSlide.closest(".container").parent().addClass("arrow-wrap");
            }
        }

        asSlide.slick({
            dots: d("dots") ? true : false,
            fade: d("fade") ? true : false,
            arrows: d("arrows") ? true : false,
            speed: d("speed") ? d("speed") : 1000,
            asNavFor: d("asnavfor") ? d("asnavfor") : false,
            autoplay: d("autoplay") == false ? false : true,
            infinite: d("infinite") == false ? false : true,
            slidesToShow: d("slide-show") ? d("slide-show") : 1,
            adaptiveHeight: d("adaptive-height") ? true : false,
            centerMode: d("center-mode") ? true : false,
            autoplaySpeed: d("autoplay-speed") ? d("autoplay-speed") : 8000,
            centerPadding: d("center-padding") ? d("center-padding") : "0",
            focusOnSelect: d("focuson-select") == false ? false : true,
            pauseOnFocus: d("pauseon-focus") ? true : false,
            pauseOnHover: d("pauseon-hover") ? true : false,
            variableWidth: d("variable-width") ? true : false,
            vertical: d("vertical") ? true : false,
            verticalSwiping: d("vertical") ? true : false,
            prevArrow: d("prev-arrow") ?
                prevButton : '<button type="button" class="slick-prev"><i class="far fa-arrow-left"></i></button>',
            nextArrow: d("next-arrow") ?
                nextButton : '<button type="button" class="slick-next"><i class="far fa-arrow-right"></i></button>',
            rtl: $("html").attr("dir") == "rtl" ? true : false,
            responsive: [{
                breakpoint: 1600,
                settings: {
                    arrows: d("xl-arrows") ? true : false,
                    dots: d("xl-dots") ? true : false,
                    slidesToShow: d("xl-slide-show") ?
                        d("xl-slide-show") : d("slide-show"),
                    centerMode: d("xl-center-mode") ? true : false,
                    centerPadding: "0",
                },
            },
            {
                breakpoint: 1400,
                settings: {
                    arrows: d("ml-arrows") ? true : false,
                    dots: d("ml-dots") ? true : false,
                    slidesToShow: d("ml-slide-show") ?
                        d("ml-slide-show") : d("slide-show"),
                    centerMode: d("ml-center-mode") ? true : false,
                    centerPadding: 0,
                },
            },
            {
                breakpoint: 1200,
                settings: {
                    arrows: d("lg-arrows") ? true : false,
                    dots: d("lg-dots") ? true : false,
                    slidesToShow: d("lg-slide-show") ?
                        d("lg-slide-show") : d("slide-show"),
                    centerMode: d("lg-center-mode") ?
                        d("lg-center-mode") : false,
                    centerPadding: 0,
                },
            },
            {
                breakpoint: 992,
                settings: {
                    arrows: d("md-arrows") ? true : false,
                    dots: d("md-dots") ? true : false,
                    slidesToShow: d("md-slide-show") ?
                        d("md-slide-show") : 1,
                    centerMode: d("md-center-mode") ?
                        d("md-center-mode") : false,
                    centerPadding: 0,
                },
            },
            {
                breakpoint: 768,
                settings: {
                    arrows: d("sm-arrows") ? true : false,
                    dots: d("sm-dots") ? true : false,
                    slidesToShow: d("sm-slide-show") ?
                        d("sm-slide-show") : 1,
                    centerMode: d("sm-center-mode") ?
                        d("sm-center-mode") : false,
                    centerPadding: 0,
                },
            },
            {
                breakpoint: 576,
                settings: {
                    arrows: d("xs-arrows") ? true : false,
                    dots: d("xs-dots") ? true : false,
                    slidesToShow: d("xs-slide-show") ?
                        d("xs-slide-show") : 1,
                    centerMode: d("xs-center-mode") ?
                        d("xs-center-mode") : false,
                    centerPadding: 0,
                },
            },
                // You can unslick at a given breakpoint now by adding:
                // settings: "unslick"
                // instead of a settings object
            ],
        });
    });

    // hero slider 1
    var $slider = $('.hero-slider-6');
    var $progressBar = $('.progress');
    var $status1 = $('.pagingInfo1');
    var $progressBarLabel = $('.slider__label');
    var $status2 = $('.pagingInfo2');

    $slider.on('init  afterChange', function (event, slick, currentSlide, nextSlide) {
        //currentSlide is undefined on init -- set it to 0 in this case (currentSlide is 0 based)
        if (!slick.$dots) {
            return;
        }



        var i = (currentSlide ? currentSlide : 0) + 0;
        $status1.text(i + 1);
        $status2.text(slick.slideCount);
    });

    //   Progress Bar
    $slider.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
        var calc = ((nextSlide) / (slick.slideCount - 1)) * 100;

        $progressBar
            .css('background-size', calc + '% 100%')
            .attr('aria-valuenow', calc);


        $progressBarLabel.text(calc + '% completed');
    });



    // Testimonail slider ----------------------

    var slick3d = $('.slick-3d-active');
    slick3d.on('init', function (event, slick, currentSlide) {
        var cur = $(slick.$slides[slick.currentSlide]),
            next = cur.next(),
            next2 = cur.next().next(),
            prev = cur.prev(),
            prev2 = cur.prev().prev();
        prev.addClass('slick-3d-prev');
        next.addClass('slick-3d-next');
        prev2.addClass('slick-3d-prev2');
        next2.addClass('slick-3d-next2');
        cur.removeClass('slick-3d-next')
            .removeClass('slick-3d-prev')
            .removeClass('slick-3d-next2')
            .removeClass('slick-3d-prev2');
        slick.$prev = prev;
        slick.$next = next;
    }).on('beforeChange', function (event, slick, currentSlide, nextSlide) {
        var cur = $(slick.$slides[nextSlide]);
        slick.$prev.removeClass('slick-3d-prev');
        slick.$next.removeClass('slick-3d-next');
        slick.$prev.prev().removeClass('slick-3d-prev2');
        slick.$next.next().removeClass('slick-3d-next2');
        var next = cur.next(),
            prev = cur.prev();
        prev.addClass('slick-3d-prev');
        next.addClass('slick-3d-next');
        prev.prev().addClass('slick-3d-prev2');
        next.next().addClass('slick-3d-next2');
        slick.$prev = prev;
        slick.$next = next;
        cur.removeClass('slick-next')
            .removeClass('slick-3d-prev')
            .removeClass('slick-next2')
            .removeClass('slick-3d-prev2');
    });

    slick3d.slick({
        speed: 1000,
        arrows: false,
        dots: false,
        focusOnSelect: true,
        prevArrow: '<button type="button" class="slick-prev"><i class="fal fa-arrow-left"></i></button>',
        nextArrow: '<button type="button" class="slick-next"><i class="fal fa-arrow-right"></i></button>',
        infinite: true,
        centerMode: true,
        slidesPerRow: 1,
        slidesToShow: 1,
        slidesToScroll: 1,
        centerPadding: '0',
        swipe: true,
        customPaging: function (slider, i) {
            return '';
        },
        responsive: [{
            breakpoint: 1024,
            settings: {
                arrows: false,
            }
        }]
    });



    // hero slider 
    var $slider = $('.heroSlider');
    var $progressBar = $('.progress');
    var $status1 = $('.pagingInfo1');
    var $progressBarLabel = $('.slider__label');
    var $status2 = $('.pagingInfo2');

    $slider.on('init reInit afterChange', function (event, slick, currentSlide, nextSlide) {
        //currentSlide is undefined on init -- set it to 0 in this case (currentSlide is 0 based)
        if (!slick.$dots) {
            return;
        }



        var i = (currentSlide ? currentSlide : 0) + 0;
        $status1.text(i + 1);
        $status2.text(slick.slideCount);
    });

    //   Progress Bar
    $slider.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
        var calc = ((nextSlide) / (slick.slideCount - 1)) * 100;

        $progressBar
            .css('background-size', calc + '% 100%')
            .attr('aria-valuenow', calc);


        $progressBarLabel.text(calc + '% completed');
    });

    $('.heroSlide').slick({
        arrows: false,
        autoplay: true,
        dots: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        prevArrow: "<button class='arrowLeft'><i class='fa-solid fa-arrow-left'></i></button>",
        nextArrow: "<button class='arrowRight'><i class='fa-solid fa-arrow-right ms-2'></i></button>",
    });

    /*----------- 08. Custom Animaiton For Slider ----------*/
    $('[data-ani-duration]').each(function () {
        var durationTime = $(this).data('ani-duration');
        $(this).css('animation-duration', durationTime);
    });

    $('[data-ani-delay]').each(function () {
        var delayTime = $(this).data('ani-delay');
        $(this).css('animation-delay', delayTime);
    });

    $('[data-ani]').each(function () {
        var animaionName = $(this).data('ani');
        $(this).addClass(animaionName);
        $('.slick-current [data-ani]').addClass('th-animated');
    });

    $('.th-carousel').on('afterChange', function (event, slick, currentSlide, nextSlide) {
        $(slick.$slides).find('[data-ani]').removeClass('th-animated');
        $(slick.$slides[currentSlide]).find('[data-ani]').addClass('th-animated');
    })

    /*----------- 09. Flip Slider ----------*/
    if ($(".flip-gallery").length > 0) {
        $(".flip-gallery").flipster({
            style: 'carousel',
            spacing: -0.5,
            nav: true,
            buttons: true,
            loop: true,
            scrollwheel: false,
        });
    }

    /*----------- 10. Ajax Contact Form ----------*/
    var form = ".ajax-contact";
    var invalidCls = "is-invalid";
    var $email = '[name="email"]';
    var $validation =
        '[name="name"],[name="email"],[name="subject"],[name="number"],[name="message"]'; // Must be use (,) without any space
    var formMessages = $(".form-messages");

    function sendContact() {
        var formData = $(form).serialize();
        var valid;
        valid = validateContact();
        if (valid) {
            jQuery
                .ajax({
                    url: $(form).attr("action"),
                    data: formData,
                    type: "POST",
                })
                .done(function (response) {
                    // Make sure that the formMessages div has the 'success' class.
                    formMessages.removeClass("error");
                    formMessages.addClass("success");
                    // Set the message text.
                    formMessages.text(response);
                    // Clear the form.
                    $(
                        form +
                        ' input:not([type="submit"]),' +
                        form +
                        " textarea"
                    ).val("");
                })
                .fail(function (data) {
                    // Make sure that the formMessages div has the 'error' class.
                    formMessages.removeClass("success");
                    formMessages.addClass("error");
                    // Set the message text.
                    if (data.responseText !== "") {
                        formMessages.html(data.responseText);
                    } else {
                        formMessages.html(
                            "Oops! An error occured and your message could not be sent."
                        );
                    }
                });
        }
    }

    function validateContact() {
        var valid = true;
        var formInput;

        function unvalid($validation) {
            $validation = $validation.split(",");
            for (var i = 0; i < $validation.length; i++) {
                formInput = form + " " + $validation[i];
                if (!$(formInput).val()) {
                    $(formInput).addClass(invalidCls);
                    valid = false;
                } else {
                    $(formInput).removeClass(invalidCls);
                    valid = true;
                }
            }
        }
        unvalid($validation);

        if (
            !$($email).val() ||
            !$($email)
                .val()
                .match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)
        ) {
            $($email).addClass(invalidCls);
            valid = false;
        } else {
            $($email).removeClass(invalidCls);
            valid = true;
        }
        return valid;
    }

    $(form).on("submit", function (element) {
        element.preventDefault();
        sendContact();
    });

    /*---------- 11. Search Box Popup ----------*/
    function popupSarchBox($searchBox, $searchOpen, $searchCls, $toggleCls) {
        $($searchOpen).on("click", function (e) {
            e.preventDefault();
            $($searchBox).addClass($toggleCls);
        });
        $($searchBox).on("click", function (e) {
            e.stopPropagation();
            $($searchBox).removeClass($toggleCls);
        });
        $($searchBox)
            .find("form")
            .on("click", function (e) {
                e.stopPropagation();
                $($searchBox).addClass($toggleCls);
            });
        $($searchCls).on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            $($searchBox).removeClass($toggleCls);
        });
    }
    popupSarchBox(
        ".popup-search-box",
        ".searchBoxToggler",
        ".searchClose",
        "show"
    );

    /*---------- 12. Popup Sidemenu ----------*/
    function popupSideMenu($sideMenu, $sideMunuOpen, $sideMenuCls, $toggleCls) {
        // Sidebar Popup
        $($sideMunuOpen).on('click', function (e) {
            e.preventDefault();
            $($sideMenu).addClass($toggleCls);
        });
        $($sideMenu).on('click', function (e) {
            e.stopPropagation();
            $($sideMenu).removeClass($toggleCls)
        });
        var sideMenuChild = $sideMenu + ' > div';
        $(sideMenuChild).on('click', function (e) {
            e.stopPropagation();
            $($sideMenu).addClass($toggleCls)
        });
        $($sideMenuCls).on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $($sideMenu).removeClass($toggleCls);
        });
    };
    popupSideMenu('.sidemenu-wrapper', '.sideMenuToggler', '.sideMenuCls', 'show');

    /*---------- 12. Popup Sidemenu ----------*/
    function popupSideMenu($sideMenu2, $sideMunuOpen2, $sideMenuCls2, $toggleCls2) {
        // Sidebar Popup
        $($sideMunuOpen2).on('click', function (e) {
            e.preventDefault();
            $($sideMenu2).addClass($toggleCls2);
        });
        $($sideMenu2).on('click', function (e) {
            e.stopPropagation();
            $($sideMenu2).removeClass($toggleCls2)
        });
        var sideMenuChild = $sideMenu2 + ' > div';
        $(sideMenuChild).on('click', function (e) {
            e.stopPropagation();
            $($sideMenu2).addClass($toggleCls2)
        });
        $($sideMenuCls2).on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $($sideMenu2).removeClass($toggleCls2);
        });
    };
    popupSideMenu('.shopping-cart', '.sideMenuToggler2', '.sideMenuCls', 'show');




    /*----------- 13. Magnific Popup ----------*/
    /* magnificPopup img view */
    $(".popup-image").magnificPopup({
        type: "image",
        gallery: {
            enabled: true,
        },
    });

    /* magnificPopup video view */
    $(".popup-video").magnificPopup({
        type: "iframe",
    });

    /* magnificPopup video view */
    $(".popup-content").magnificPopup({
        type: "inline",
        midClick: true,
    });

    $(".popup-content").on("click", function () {
        $(".slick-slider").slick("refresh");
    });

    /*----------- 09. tilt ----------*/
    $('.tilt-active').tilt({
        maxTilt: 7,
        perspective: 1000,
    })


    /************lettering js***********/
    function injector(t, splitter, klass, after) {
        var a = t.text().split(splitter), inject = '';
        if (a.length) {
            $(a).each(function (i, item) {
                inject += '<span class="' + klass + (i + 1) + '">' + item + '</span>' + after;
            });
            t.empty().append(inject);
        }
    }

    var methods = {
        init: function () {

            return this.each(function () {
                injector($(this), '', 'char', '');
            });

        },

        words: function () {

            return this.each(function () {
                injector($(this), ' ', 'word', ' ');
            });

        },

        lines: function () {

            return this.each(function () {
                var r = "eefec303079ad17405c889e092e105b0";
                // Because it's hard to split a <br/> tag consistently across browsers,
                // (*ahem* IE *ahem*), we replaces all <br/> instances with an md5 hash 
                // (of the word "split").  If you're trying to use this plugin on that 
                // md5 hash string, it will fail because you're being ridiculous.
                injector($(this).children("br").replaceWith(r).end(), r, 'line', '');
            });

        }
    };

    $.fn.lettering = function (method) {
        // Method calling logic
        if (method && methods[method]) {
            return methods[method].apply(this, [].slice.call(arguments, 1));
        } else if (method === 'letters' || !method) {
            return methods.init.apply(this, [].slice.call(arguments, 0)); // always pass an array
        }
        $.error('Method ' + method + ' does not exist on jQuery.lettering');
        return this;
    };

    $(".about-anime").lettering();
    /*---------- 14. Section Position ----------*/
    // Interger Converter
    function convertInteger(str) {
        return parseInt(str, 10);
    }

    $.fn.sectionPosition = function (mainAttr, posAttr) {
        $(this).each(function () {
            var section = $(this);

            function setPosition() {
                var sectionHeight = Math.floor(section.height() / 2), // Main Height of section
                    posData = section.attr(mainAttr), // where to position
                    posFor = section.attr(posAttr), // On Which section is for positioning
                    topMark = "top-half", // Pos top
                    bottomMark = "bottom-half", // Pos Bottom
                    parentPT = convertInteger($(posFor).css("padding-top")), // Default Padding of  parent
                    parentPB = convertInteger($(posFor).css("padding-bottom")); // Default Padding of  parent

                if (posData === topMark) {
                    $(posFor).css(
                        "padding-bottom",
                        parentPB + sectionHeight + "px"
                    );
                    section.css("margin-top", "-" + sectionHeight + "px");
                } else if (posData === bottomMark) {
                    $(posFor).css(
                        "padding-top",
                        parentPT + sectionHeight + "px"
                    );
                    section.css("margin-bottom", "-" + sectionHeight + "px");
                }
            }
            setPosition(); // Set Padding On Load
        });
    };

    var postionHandler = "[data-sec-pos]";
    if ($(postionHandler).length) {
        $(postionHandler).imagesLoaded(function () {
            $(postionHandler).sectionPosition("data-sec-pos", "data-pos-for");
        });
    }

    /*----------- 15. Filter ----------*/
    $(".filter-active").imagesLoaded(function () {
        var $filter = ".filter-active",
            $filterItem = ".filter-item",
            $filterMenu = ".filter-menu-active";

        if ($($filter).length > 0) {
            var $grid = $($filter).isotope({
                itemSelector: $filterItem,
                filter: "*",
                masonry: {
                    // use outer width of grid-sizer for columnWidth
                    columnWidth: 1,
                },
            });

            // filter items on button click
            $($filterMenu).on("click", "button", function () {
                var filterValue = $(this).attr("data-filter");
                $grid.isotope({
                    filter: filterValue,
                });
            });

            // Menu Active Class
            $($filterMenu).on("click", "button", function (event) {
                event.preventDefault();
                $(this).addClass("active");
                $(this).siblings(".active").removeClass("active");
            });
        }
    });

    $(".masonary-active").imagesLoaded(function () {
        var $filter = ".masonary-active",
            $filterItem = ".filter-item";

        if ($($filter).length > 0) {
            $($filter).isotope({
                itemSelector: $filterItem,
                filter: "*",
                masonry: {
                    // use outer width of grid-sizer for columnWidth
                    columnWidth: 1,
                },
            });
        }
    });



    if ($(".odometer").length) {
        $(".odometer").appear();
        $(document.body).on("appear", ".odometer", function (e) {
            var odo = $(".odometer");
            odo.each(function () {
                var countNumber = $(this).attr("data-count");
                $(this).html(countNumber);
            });
            window.odometerOptions = {
                format: "d",
            };
        });
    }

    /*----------- 14. Date Time Picker ----------*/
    // Only Date Picker
    $('.date-pick').datetimepicker({
        timepicker: false,
        datepicker: true,
        format: 'd-m-y',
        step: 10
    });

    // Only Time Picker
    $('.time-pick').datetimepicker({
        datepicker: false,
        format: 'H:i',
        step: 30
    });

    /* ==================================================
            # Wow Init
         ===============================================*/
    var wow = new WOW({
        boxClass: 'wow',
        animateClass: 'animated',
        offset: 0,
        mobile: true,
        live: true
    });
    new WOW().init();

    /*---------- 16. AS Tab ----------*/
    $.fn.asTab = function (options) {
        var opt = $.extend(
            {
                sliderTab: false,
                tabButton: "button",
            },
            options
        );

        $(this).each(function () {
            var $menu = $(this);
            var $button = $menu.find(opt.tabButton);

            // Append indicator
            $menu.append('<span class="indicator"></span>');
            var $line = $menu.find(".indicator");

            // On Click Button Class Remove and indecator postion set
            $button.on("click", function (e) {
                e.preventDefault();
                var cBtn = $(this);
                cBtn.addClass("active").siblings().removeClass("active");
                if (opt.sliderTab) {
                    $(slider).slick("slickGoTo", cBtn.data("slide-go-to"));
                } else {
                    linePos();
                }
            });

            // Work With slider
            if (opt.sliderTab) {
                var slider = $menu.data("asnavfor"); // select slider

                // Select All button and set attribute
                var i = 0;
                $button.each(function () {
                    var slideBtn = $(this);
                    slideBtn.attr("data-slide-go-to", i);
                    i++;

                    // Active Slide On load > Actived Button
                    if (slideBtn.hasClass("active")) {
                        $(slider).slick(
                            "slickGoTo",
                            slideBtn.data("slide-go-to")
                        );
                    }

                    // Change Indicator On slide Change
                    $(slider).on(
                        "beforeChange",
                        function (event, slick, currentSlide, nextSlide) {
                            $menu
                                .find(
                                    opt.tabButton +
                                    '[data-slide-go-to="' +
                                    nextSlide +
                                    '"]'
                                )
                                .addClass("active")
                                .siblings()
                                .removeClass("active");
                            linePos();
                        }
                    );
                });
            }

            // Indicator Position
            function linePos() {
                var $btnActive = $menu.find(opt.tabButton + ".active"),
                    $height = $btnActive.css("height"),
                    $width = $btnActive.css("width"),
                    $top = $btnActive.position().top + "px",
                    $left = $btnActive.position().left + "px";

                $line.get(0).style.setProperty("--height-set", $height);
                $line.get(0).style.setProperty("--width-set", $width);
                $line.get(0).style.setProperty("--pos-y", $top);
                $line.get(0).style.setProperty("--pos-x", $left);

                if (
                    $($button).first().position().left ==
                    $btnActive.position().left
                ) {
                    $line
                        .addClass("start")
                        .removeClass("center")
                        .removeClass("end");
                } else if (
                    $($button).last().position().left ==
                    $btnActive.position().left
                ) {
                    $line
                        .addClass("end")
                        .removeClass("center")
                        .removeClass("start");
                } else {
                    $line
                        .addClass("center")
                        .removeClass("start")
                        .removeClass("end");
                }
            }
            linePos();
        });
    };



    // Call On Load
    if ($('.testi-box-tab').length) {
        $('.testi-box-tab').asTab({
            sliderTab: true,
            tabButton: '.tab-btn'
        });
    }

    if ($('.testi-block-tab').length) {
        $('.testi-block-tab').asTab({
            sliderTab: true,
            tabButton: '.tab-btn'
        });
    }


    if ($(".hero-indicator").length) {
        $(".hero-indicator").asTab({
            sliderTab: true,
            tabButton: ".indicatior-btn",
        });
    }

    if ($(".hero-indicator3").length) {
        $(".hero-indicator3").asTab({
            sliderTab: true,
            tabButton: ".indicatior-btn",
        });
    }
    if ($(".hero-indicator.style2").length) {
        $(".hero-indicator.style2").asTab({
            sliderTab: true,
            tabButton: ".indicatior-btn",
        });
    }


    ($.fn.vsTab = function (t) {
        var a = $.extend({
            sliderTab: !1,
            tabButton: "button",
            indicator: !1
        }, t);
        $(this).each(function () {
            var t = $(this),
                s = t.find(a.tabButton);
            if (
                (s.on("click", function (t) {
                    t.preventDefault();
                    var s = $(this);
                    s.addClass("active").siblings().removeClass("active"), a.sliderTab && $(o).slick("slickGoTo", s.data("slide-go-to"));
                }),
                    a.sliderTab)
            ) {
                var o = t.data("asnavfor"),
                    n = 0;
                s.each(function () {
                    var s = $(this);
                    s.attr("data-slide-go-to", n),
                        n++,
                        s.hasClass("active") && $(o).slick("slickGoTo", s.data("slide-go-to")),
                        $(o).on("beforeChange", function (e, s, o, n) {
                            t.find(a.tabButton + '[data-slide-go-to="' + n + '"]')
                                .addClass("active")
                                .siblings()
                                .removeClass("active");
                        });
                });
            }
        });
    }),
        $(".th-custom-dots").length && $(".th-custom-dots").vsTab({
            sliderTab: !0,
            tabButton: ".dot-btn"
        }),
        /*----------- 18. Shape Mockup ----------*/
        $.fn.shapeMockup = function () {
            var $shape = $(this);
            $shape.each(function () {
                var $currentShape = $(this),
                    shapeTop = $currentShape.data("top"),
                    shapeRight = $currentShape.data("right"),
                    shapeBottom = $currentShape.data("bottom"),
                    shapeLeft = $currentShape.data("left");
                $currentShape
                    .css({
                        top: shapeTop,
                        right: shapeRight,
                        bottom: shapeBottom,
                        left: shapeLeft,
                    })
                    .removeAttr("data-top")
                    .removeAttr("data-right")
                    .removeAttr("data-bottom")
                    .removeAttr("data-left")
                    .parent()
                    .addClass("shape-mockup-wrap");
            });
        };

    if ($(".shape-mockup")) {
        $(".shape-mockup").shapeMockup();
    }

    /*----------- 19. Countdown ----------*/

    $.fn.countdown = function () {
        $(this).each(function () {
            var $counter = $(this),
                countDownDate = new Date($counter.data("offer-date")).getTime(), // Set the date we're counting down toz
                exprireCls = "expired";

            // Finding Function
            function s$(element) {
                return $counter.find(element);
            }

            // Update the count down every 1 second
            var counter = setInterval(function () {
                // Get today's date and time
                var now = new Date().getTime();

                // Find the distance between now and the count down date
                var distance = countDownDate - now;

                // Time calculations for days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor(
                    (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
                );
                var minutes = Math.floor(
                    (distance % (1000 * 60 * 60)) / (1000 * 60)
                );
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Check If value is lower than ten, so add zero before number
                days < 10 ? (days = "0" + days) : null;
                hours < 10 ? (hours = "0" + hours) : null;
                minutes < 10 ? (minutes = "0" + minutes) : null;
                seconds < 10 ? (seconds = "0" + seconds) : null;

                // If the count down is over, write some text
                if (distance < 0) {
                    clearInterval(counter);
                    $counter.addClass(exprireCls);
                    $counter.find(".message").css("display", "block");
                } else {
                    // Output the result in elements
                    s$(".day").html(days);
                    s$(".hour").html(hours);
                    s$(".minute").html(minutes);
                    s$(".seconds").html(seconds);
                }
            }, 1000);
        });
    };

    if ($(".counter-list").length) {
        $(".counter-list").countdown();
    }

    $('#priceSlide').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
        vertical: true,
        verticalSwiping: true,
        autoplay: true,
        autoplaySpeed: 1500, // Adjust the speed as needed (in milliseconds)
    });
    


    /*----------- 21. Price Slider ----------*/
    $(".price_slider").slider({
        range: true,
        min: 10,
        max: 100,
        values: [10, 75],
        slide: function (event, ui) {
            $(".from").text("$" + ui.values[0]);
            $(".to").text("$" + ui.values[1]);
        }
    });
    $(".from").text("$" + $(".price_slider").slider("values", 0));
    $(".to").text("$" + $(".price_slider").slider("values", 1));




    /*----------- 21. image Slider ----------*/
    $("#slider").on("input change", (e) => {
        const sliderPos = e.target.value;
        // Update the width of the foreground image
        $('.foreground-img').css('width', `${sliderPos}%`)
        // Update the position of the slider button
        $('.slider-button').css('left', `calc(${sliderPos}% - 18px)`)
    });

    /* ----------------------------  */

    $('#productSlide').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
        vertical: true,
        verticalSwiping: true,

    });

    $('#productSlide2').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
        vertical: true,
        verticalSwiping: true,

    });




    $(function () {

        $('.progress-bar').each(function () {
            $(this).find('.progress-content').animate({
                width: $(this).attr('data-percentage')
            }, 2000);

            $(this).find('.progress-number-mark').animate({
                left: $(this).attr('data-percentage')
            }, {
                duration: 2000,
                step: function (now, fx) {
                    var data = Math.round(now);
                    $(this).find('.percent').html(data + '%');
                }
            });
        });
    });

    //one page sticky menu  
    $(window).on('scroll', function () {
        if ($('.onepage').length > 0) {
            if ($(window).scrollTop() > 0) {
                $('.th-header .sticky-active').addClass('sticky');
            } else {
                $('.th-header .sticky-active').removeClass('sticky');
            }
        };
    });

    /*----------- 23. Indicator ----------*/
    // Indicator
    $.fn.indicator = function () {
        var $menu = $(this),
            $linkBtn = $menu.find("a"),
            $btn = $menu.find("button");
        // Append indicator
        $menu.append('<span class="indicator"></span>');
        var $line = $menu.find(".indicator");
        // Check which type button is Available
        if ($linkBtn.length) {
            var $currentBtn = $linkBtn;
        } else if ($btn.length) {
            var $currentBtn = $btn;
        }
        // On Click Button Class Remove
        $currentBtn.on("click", function (e) {
            e.preventDefault();
            $(this).addClass("active");
            $(this).siblings(".active").removeClass("active");
            linePos();
        });

        function linePos() {
            var $btnActive = $menu.find(".active"),
                $height = $btnActive.css("height"),
                $width = $btnActive.css("width"),
                $top = $btnActive.position().top + "px",
                $left = $btnActive.position().left + "px";

            $(window).on('resize', function () {
                $top = $btnActive.position().top + "px",
                    $left = $btnActive.position().left + "px";
            });

            $line.get(0).style.setProperty("--height-set", $height);
            $line.get(0).style.setProperty("--width-set", $width);
            $line.get(0).style.setProperty("--pos-y", $top);
            $line.get(0).style.setProperty("--pos-x", $left);

        }
        linePos();
    };

    // Call On Load
    if ($(".indicator-active").length) {
        $(".indicator-active").indicator();
    }


    /* ==================================================
#  Load More 
===============================================*/

    $(function () {
        $(".project-sec").slice(0, 4).show();
        $("#loadMore").on("click", function (e) {
            e.preventDefault();
            $(".loadcontent:hidden").slice(0, 3).slideDown();
            if ($(".loadcontent:hidden").length == 0) {
                $("#loadMore").text("No Content").addClass("noContent");
            }
        });

    })

    /*----------- 00. Woocommerce Toggle ----------*/
    // Ship To Different Address
    $("#ship-to-different-address-checkbox").on("change", function () {
        if ($(this).is(":checked")) {
            $("#ship-to-different-address")
                .next(".shipping_address")
                .slideDown();
        } else {
            $("#ship-to-different-address").next(".shipping_address").slideUp();
        }
    });

    // Login Toggle
    $(".woocommerce-form-login-toggle a").on("click", function (e) {
        e.preventDefault();
        $(".woocommerce-form-login").slideToggle();
    });

    // Coupon Toggle
    $(".woocommerce-form-coupon-toggle a").on("click", function (e) {
        e.preventDefault();
        $(".woocommerce-form-coupon").slideToggle();
    });

    // Woocommerce Shipping Method
    $(".shipping-calculator-button").on("click", function (e) {
        e.preventDefault();
        $(this).next(".shipping-calculator-form").slideToggle();
    });

    // Woocommerce Payment Toggle
    $('.wc_payment_methods input[type="radio"]:checked')
        .siblings(".payment_box")
        .show();
    $('.wc_payment_methods input[type="radio"]').each(function () {
        $(this).on("change", function () {
            $(".payment_box").slideUp();
            $(this).siblings(".payment_box").slideDown();
        });
    });

    // Woocommerce Rating Toggle
    $(".rating-select .stars a").each(function () {
        $(this).on("click", function (e) {
            e.preventDefault();
            $(this).siblings().removeClass("active");
            $(this).parent().parent().addClass("selected");
            $(this).addClass("active");
        });
    });

    // Quantity Plus Minus ---------------------------

    $(".quantity-plus").each(function () {
        $(this).on("click", function (e) {
            e.preventDefault();
            var $qty = $(this).siblings(".qty-input");
            var currentVal = parseInt($qty.val(), 10);
            if (!isNaN(currentVal)) {
                $qty.val(currentVal + 1);
            }
        });
    });

    $(".quantity-minus").each(function () {
        $(this).on("click", function (e) {
            e.preventDefault();
            var $qty = $(this).siblings(".qty-input");
            var currentVal = parseInt($qty.val(), 10);
            if (!isNaN(currentVal) && currentVal > 1) {
                $qty.val(currentVal - 1);
            }
        });
    });





})(jQuery);



function DisplayReviewPopup() {
    var reviewPopup = document.querySelector('.Review_popup');
    var popupButton = document.querySelector('.add_review_button');
    if (reviewPopup) {
        reviewPopup.style.display = 'block';
    }
    popupButton.style.display = 'none'
}

function CloseReviewPopup() {
    var reviewPopup = document.querySelector('.Review_popup');
    var popupButton = document.querySelector('.add_review_button');
    if (reviewPopup) {
        reviewPopup.style.display = 'none';
    }
    popupButton.style.display = 'block'
}











// ********************************************************************************************
// Function to create the pending reviews table
// ********************************************************************************************
document.addEventListener("DOMContentLoaded", function () {
    // Function to create the pending reviews table
    function createPendingReviewsTable(snapshot) {
        var tableDiv = document.querySelector(".pending_reviews_table_area");
        tableDiv.innerHTML = "";

        var table = document.createElement("table");
        table.classList.add("pending_reviews_table");

        var thead = document.createElement("thead");
        var headerRow = document.createElement("tr");
        headerRow.innerHTML = "<th>Name</th><th>Email</th><th>Message</th><th>Action</th>";
        thead.appendChild(headerRow);
        table.appendChild(thead);

        var tbody = document.createElement("tbody");

        snapshot.forEach(function (childSnapshot) {
            var reviewData = childSnapshot.val();
            var reviewKey = childSnapshot.key; // Get the key of the current pending review
            var row = document.createElement("tr");
            row.innerHTML = "<td>" + reviewData.name + "</td>" +
                "<td>" + reviewData.email + "</td>" +
                "<td>" + reviewData.message + "</td>" +
                "<td class='actionColumn'><button class='approve_btn'><i class='fa-solid fa-square-check'>&nbsp;</i>Approve</button>" +
                "<button class='delete_btn'><i class='fa-solid fa-trash-can'></i>&nbsp; Delete</button></td>";

            // Add event listener to the approve button
            var approveBtn = row.querySelector(".approve_btn");
            approveBtn.addEventListener("click", function () {
                approveReview(reviewKey, reviewData);
            });

            // Add event listener to the delete button
            var deleteBtn = row.querySelector(".delete_btn");
            deleteBtn.addEventListener("click", function () {
                deleteReview(reviewKey);
            });

            tbody.appendChild(row);
        });

        table.appendChild(tbody);
        tableDiv.appendChild(table);
    }

    // Function to refresh pending reviews table data
    function refreshPendingReviewsTable() {
        var pendingReviewsRef = firebase.database().ref("PendingReviews");
        pendingReviewsRef.once("value", function (snapshot) {
            createPendingReviewsTable(snapshot);
        });
    }

    // Function to approve a review
    function approveReview(reviewKey, reviewData) {
        // Get a reference to the Firebase database
        var database = firebase.database();

        // Get a reference to the "ApprovedReviews" collection
        var approvedReviewsRef = database.ref("approvedReviews");

        // Set the review data with the "name" key's value as the key for the object
        var updatedReviewData = {};
        updatedReviewData[reviewData.name] = reviewData;

        // Add the updated review data to the "ApprovedReviews" collection
        approvedReviewsRef.update(updatedReviewData)
            .then(function () {
                // If adding to "ApprovedReviews" succeeds, remove the review from "PendingReviews"
                var pendingReviewsRef = database.ref("PendingReviews/" + reviewKey);
                pendingReviewsRef.remove()
                    .then(function () {
                        // If removal from "PendingReviews" succeeds, refresh the pending reviews table
                        refreshPendingReviewsTable();
                    })
                    .catch(function (error) {
                        console.error("Error removing review from PendingReviews:", error);
                    });
            })
            .catch(function (error) {
                console.error("Error adding review to ApprovedReviews:", error);
            });
    }

    // Function to delete a review
    function deleteReview(reviewKey) {
        // Get a reference to the Firebase database
        var database = firebase.database();

        // Get a reference to the "PendingReviews" collection
        var pendingReviewsRef = database.ref("PendingReviews/" + reviewKey);

        // Remove the review from the "PendingReviews" collection
        pendingReviewsRef.remove()
            .then(function () {
                // If removal succeeds, refresh the pending reviews table
                refreshPendingReviewsTable();
            })
            .catch(function (error) {
                console.error("Error removing review from PendingReviews:", error);
            });
    }

    // Call the function to display pending reviews when the page is loaded
    refreshPendingReviewsTable();
});




// Call the function to display pending reviews when the page is loaded
document.addEventListener("DOMContentLoaded", function () {
    refreshPendingReviewsTable();
});








//************************************************* */
// Function to create the approved reviews table
//************************************************* */

// Function to create the approved reviews table
function createApprovedReviewsTable(snapshot) {
    var tableDiv = document.querySelector(".approved_reviews_table_area");
    tableDiv.innerHTML = "";

    var table = document.createElement("table");
    table.classList.add("approved_reviews_table");

    var thead = document.createElement("thead");
    var headerRow = document.createElement("tr");
    headerRow.innerHTML = "<th>Name</th><th>Email</th><th>Message</th><th>Action</th>";
    thead.appendChild(headerRow);
    table.appendChild(thead);

    var tbody = document.createElement("tbody");

    snapshot.forEach(function (childSnapshot) {
        var reviewData = childSnapshot.val();
        var reviewKey = childSnapshot.key; // Get the key of the current approved review
        var row = document.createElement("tr");
        row.innerHTML = "<td>" + reviewData.name + "</td>" +
            "<td>" + reviewData.email + "</td>" +
            "<td>" + reviewData.message + "</td>" +
            "<td class='actionColumn'><button class='delete_btn'><i class='fa-solid fa-trash-can'></i>&nbsp; Delete</button></td>";

        // Add event listener to the delete button
        var deleteBtn = row.querySelector(".delete_btn");
        deleteBtn.addEventListener("click", function () {
            deleteReview(reviewKey);
        });

        tbody.appendChild(row);
    });

    table.appendChild(tbody);
    tableDiv.appendChild(table);
}

// Function to refresh approved reviews table data
function refreshApprovedReviewsTable() {
    var approvedReviewsRef = firebase.database().ref("approvedReviews");
    approvedReviewsRef.once("value", function (snapshot) {
        createApprovedReviewsTable(snapshot);
    });
}

// Function to delete a review
function deleteReview(reviewKey) {
    // Get a reference to the Firebase database
    var database = firebase.database();

    // Get a reference to the "ApprovedReviews" collection
    var approvedReviewsRef = database.ref("approvedReviews/" + reviewKey);

    // Remove the review from the "ApprovedReviews" collection
    approvedReviewsRef.remove()
        .then(function () {
            // If removal succeeds, refresh the approved reviews table
            refreshApprovedReviewsTable();
        })
        .catch(function (error) {
            console.error("Error removing review from ApprovedReviews:", error);
        });
}

// Call the function to display approved reviews when the page is loaded
document.addEventListener("DOMContentLoaded", function () {
    refreshApprovedReviewsTable();
});


// ********************************************************************************************
// testimonials toggle switch
// ********************************************************************************************
document.querySelector('.reviewTogglerPending').style.backgroundColor = '#4CBB17';
document.querySelector('.reviewTogglerApproved').style.backgroundColor = '#4bbb175d';
document.querySelector('.approved_reviews_table_area').style.display = 'none';


function showPendingReviews() {
    document.querySelector('.pending_reviews_table_area').style.display = 'block';
    document.querySelector('.approved_reviews_table_area').style.display = 'none';

    document.querySelector('.reviewTogglerPending').style.backgroundColor = '#4CBB17';
    document.querySelector('.reviewTogglerApproved').style.backgroundColor = '#4bbb175d';
}

function showApprovedReviews() {
    document.querySelector('.pending_reviews_table_area').style.display = 'none';
    document.querySelector('.approved_reviews_table_area').style.display = 'block';

    document.querySelector('.reviewTogglerPending').style.backgroundColor = '#4bbb175d';
    document.querySelector('.reviewTogglerApproved').style.backgroundColor = '#4CBB17';
}













// ********************************************************************************************
// Function to logout user after 1 hour of inactivity since last user activity
// ********************************************************************************************

window.onload = function () {
    // Function to handle user logout
    function logoutUser() {
        console.log("Logging out user due to inactivity or tab/window close");
        auth.signOut().then(() => {
            // Clear any user session data
            localStorage.clear();
            sessionStorage.clear();

            // Redirect to login page after logout
            window.location.href = 'index.html';
        });
    }

    // Function to set login time
    function setLoginTime() {
        var loginTime = new Date().getTime();
        localStorage.setItem('loginTime', loginTime);
        console.log("Login time set at: " + new Date(loginTime));
    }

    // Function to check inactivity based on login time
    function checkInactivity() {
        var loginTime = localStorage.getItem('loginTime');
        if (loginTime) {
            var currentTime = new Date().getTime();
            var timeSinceLogin = currentTime - loginTime;

            if (timeSinceLogin > 60 * 60 * 1000) { // 1 hour
                logoutUser();
            } else {
                // Reset the inactivity timeout to check again after the remaining time
                var remainingTime = 60 * 60 * 1000 - timeSinceLogin;
                clearTimeout(inactivityTimeout);
                inactivityTimeout = setTimeout(logoutUser, remainingTime);
            }
        }
    }

    // Set login time on load if not already set
    if (!localStorage.getItem('loginTime')) {
        setLoginTime();
    }

    // Check inactivity on page load/reload
    checkInactivity();

    // Event listeners for user activity to reset login time
    document.addEventListener("mousemove", function () {
        setLoginTime();
        checkInactivity();
    });

    document.addEventListener("keypress", function () {
        setLoginTime();
        checkInactivity();
    });

    // Variable to store the inactivity timeout
    var inactivityTimeout;

    // Log out the user when closing the tab or browser
    // window.addEventListener("beforeunload", function () {
    //     logoutUser();
    // });


    window.addEventListener('unload', function () {
        logoutUser();
    });

};





