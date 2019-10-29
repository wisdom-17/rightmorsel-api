
// jQuery for adding classes onto the modal open and removing on close. 
// This adds a class to HTML which then removes overflow disabling double scrolling.

window.addEventListener("load", function(){
	window.cookieconsent.initialise({
		"palette": {
			"popup": {
				"background": "#000"
			},
			"button": {
				"background": "#a12023"
			}
		},
		"content": {
			"message": "This website uses cookies to ensure you get the best experience on our website. By continuing, you agree to our use of cookies. ",
			"href": "https://halalhmc.org/privacy-policy",
		}
    })
});

jQuery(document).ready(function(){
    jQuery(".supplier-modal").on("shown.bs.modal", function () {
        // This adds a class to the HTML of modal open
        jQuery("html").addClass("modal-open");
    });

    jQuery(".supplier-modal").on("hidden.bs.modal", function () {
        jQuery("html").removeClass("modal-open");
    });
});


// Initialise BS tooltip
jQuery(document).ready(function(){
    jQuery('[data-toggle="tooltip"]').tooltip(); 
});


// Owl initialise slider
jQuery(document).ready(function($){

    $(function() {
        $('.lazy').lazy(
            {
                effect: "fadeIn",
                effectTime: 1000,
                threshold: 0
            }
        );

        $('.lazy-no-anim').lazy();
    });

    jQuery("#campaigns-slider").owlCarousel({
        loop:false,
        nav:true,
        itemElement: 'div.slider-item',
        items:1,
        navText:[
            "<i class='fa fa-chevron-left'></i>",
            "<i class='fa fa-chevron-right'></i>"
        ]
    });

    jQuery("#meta-slider").owlCarousel({
        loop:false,
        nav:true,
        itemElement: 'div.slider-item',
        responsive:{
            0:{
                items:2,
            },
            450:{
                items:3,
            },
            767:{
                items:3,
            },
            991:{
                items:4,
            }
        },
        navText:[
            "<i class='fa fa-chevron-left'></i>",
            "<i class='fa fa-chevron-right'></i>"
        ]
    });

    // Rewards slider
    jQuery("#steps-slider").owlCarousel({
        loop:false,
        nav:true,
        itemElement: 'div.slider-item',
        responsive:{
            0:{
                items:1,
            },
            767:{
                items:2,
            },
            991:{
                items:3,
            }
        },
        navText:[
            "<i class='fa fa-chevron-left'></i>",
            "<i class='fa fa-chevron-right'></i>"
        ]
    });

    // WPSMS slider (twitter)
    jQuery(".wpsms").owlCarousel({
        items:1,
        loop:true,
        autoplay:true,
        autoplayTimeout:3000,
        autoplayHoverPause:true,
    });

    // Homepage logos slider
    jQuery("#promoted-slider").owlCarousel({
    	lazyLoad:true,
        loop:true,
        nav:false,
        dots:true,
        itemElement: 'div.slider-item',
        autoplay:true,
        autoplayTimeout:1500,
        autoplayHoverPause:true,
        responsive:{
            0:{
                items:3,
            },
            375:{
                items:4,
            },
            550:{
                items:5,
            },
            767:{
                items:6,
            },
            991:{
                items:9,
            }
        },
        navText:[
            "<i class='fa fa-chevron-left'></i>",
            "<i class='fa fa-chevron-right'></i>"
        ]
    });

    // Social wall news slider
    jQuery("#news-slider").owlCarousel({
        loop:true,
        nav:false,
        dots:true,
        itemElement: 'div.post-item',
        items:1,
        autoplay:true,
        autoplayTimeout:3000,
        autoplayHoverPause:true,
    });

    //  Count of posts slider
    jQuery("#count-slider").owlCarousel({
        loop:true,
        nav:false,
        dots:true,
        itemElement: 'div.post-item',
        autoplay:true,
        autoplayTimeout:3000,
        autoplayHoverPause:true,
        responsive:{
            0:{
                items:2,
            },
            500:{
                items:3,
            },
            767:{
                items:5,
            },
            991:{
                items:6,
            }
        },
    });

     // Latest outlets slider
    jQuery("#latest-outlets").owlCarousel({
        loop:false,
        nav:true,
        dots:false,
        itemElement: 'div.post-item',
        responsive:{
            0:{
                items:1,
            },
            650:{
                items:2,
            },
            991:{
                items:3,
            }
        },
        navText:[
            "<i class='fa fa-chevron-left'></i>",
            "<i class='fa fa-chevron-right'></i>"
        ]
    });

    jQuery("#latest-vacancies").owlCarousel({
        loop:false,
        nav:true,
        dots:false,
        itemElement: 'div.post-item',
        responsive:{
            0:{
                items:1,
            },
            650:{
                items:2,
            },
            991:{
                items:3,
            }
        },
        navText:[
            "<i class='fa fa-chevron-left'></i>",
            "<i class='fa fa-chevron-right'></i>"
        ]
    });


  // Sort Featured posts	
    jQuery("#outlet_items div.mapodia_featured").sort(function(a, b) {
    	return parseFloat(a.id) - parseFloat(b.id);
    	}).each(function() {
    	var elem = jQuery(this);
    	elem.remove();
    	jQuery(elem).appendTo("#outlet_items");
    });

    // Sort normal posts	
    jQuery("#outlet_items div.mapodia").sort(function(a, b) {
    	return parseFloat(a.id) - parseFloat(b.id);
    	}).each(function() {
    	var elem = jQuery(this);
    	elem.remove();
    	jQuery(elem).appendTo("#outlet_items");
    });
	
	

    // masonry grid styles
    if (jQuery('.grid').length){
        var $masonry = jQuery('.grid').isotope({
            itemSelector: '.grid-item',
            columnWidth: '.grid-item',
            transitionDuration: 0,
			sortBy : 'original-order'
        });
        // added styles to make masonry behave inside bootstrap
        jQuery('[data-toggle="tab"]').each(function () {
            var $this = jQuery(this);
            $this.on('shown.bs.tab', function () {
                $masonry.isotope({
                    columnWidth: '.grid-item',
                    itemSelector: '.grid-item',
                    transitionDuration: 0
                });
            });
        });		
    }
});

// Gallery Light gallery function
jQuery(document).ready(function(){
  if (jQuery('#lightgallery').length){
    jQuery('#lightgallery').lightGallery({
        mode: 'lg-fade',
        cssEasing: 'cubic-bezier(0.25, 0, 0.25, 1)',
        selector: '.lightgallery-item',
        hash: false,
    });
  }
});

// MENU Light gallery function
jQuery(document).ready(function(){
  if (jQuery('#menu-lightbox').length){
    jQuery('#menu-lightbox').lightGallery({
        mode: 'lg-fade',
        cssEasing: 'cubic-bezier(0.25, 0, 0.25, 1)',
        selector: '.menu-lightbox',
        hash: false,
    });
  }
});

// Overlay menu scripts
jQuery(function() {
    jQuery("button.hamburger-menu").click(function() {
        jQuery('html').addClass('showMenu');
        jQuery('body').addClass('showMenu');
    });
    jQuery("button.overlay-menu-close").click(function() {
        jQuery('html').removeClass('showMenu');
        jQuery('body').removeClass('showMenu');
    });
});


// mobile menu and submenu scripts with back button text morph
jQuery(function() {
    var linkText = "";
    jQuery("#menu-main-menu-1 li.menu-item-has-children > a").click(function(e) {
        e.preventDefault();
        if(jQuery(window).outerWidth() <= 991) {
            if(!jQuery(this).parent("li").hasClass("showing")) {
                linkText = jQuery(this).text();
                jQuery("#menu-main-menu-1 > li").hide();
                jQuery(this).parent("li").addClass("showing").show();
                jQuery(this).next(".sub-menu").fadeIn();
                jQuery(this).text("BACK");
            }
            else {
                jQuery(this).text(linkText);
                jQuery(this).next(".sub-menu").hide();
                jQuery(this).parent("li").removeClass("showing");
                jQuery("#menu-main-menu-1 > li").show();
            }
        }
    });
});

// Floating labels for contact page.
jQuery(window).bind('load', function() {
    jQuery('.wpcf7-form-control').parents('p.form-row').addClass('text-label');
    jQuery('.wpcf7-form-control').focus(function() {
        jQuery(this).parents('p.form-row').addClass('focused');
    });
    jQuery('.wpcf7-form-control').blur(function() {
        if(jQuery(this)[0].value.length < 1) {
            jQuery(this).parents('p.form-row').removeClass('focused');
        }
    });
    jQuery('.wpcf7-form-control').each(function() {
        if(jQuery(this)[0].value.length > 0) {
            jQuery(this).parents('p.form-row').addClass('focused');
        }
    });
});

// Floating labels for general usage
jQuery(window).bind('load', function() {
    jQuery('.form-control').parents('p.form-row').addClass('text-label');
    jQuery('.form-control').focus(function() {
        jQuery(this).parents('p.form-row').addClass('focused');
    });
    jQuery('.form-control').blur(function() {
        if(jQuery(this)[0].value.length < 1) {
            jQuery(this).parents('p.form-row').removeClass('focused');
        }
    });
    jQuery('.form-control').each(function() {
        if(jQuery(this)[0].value.length > 0) {
            jQuery(this).parents('p.form-row').addClass('focused');
        }
    });
});

// JS morph text
jQuery(document).ready(function(){
jQuery("#js-rotating").Morphext({
    // The [in] animation type. Refer to Animate.css for a list of available animations.
    animation: "fadeInUp",
    separator: ",",
    speed: 2000,
    complete: function () {
        // Called after the entrance animation is executed.
    }
});
});

// Add class onto the parent of the floating getLocation button on click
jQuery(function() {
    jQuery("a.search-by-postcode").click(function() {
        jQuery('.findAnOutlet').addClass('changeSearchType');
        jQuery(".auto-search").hide();
        jQuery(".manual-search").fadeIn();
    });
});

// Number counter
jQuery(document).ready(function(){
jQuery('.count').each(function () {
    jQuery(this).prop('Counter',0).animate({
        Counter: jQuery(this).text()
    }, {
        duration: 4000,
        easing: 'linear',
        step: function (now) {
            jQuery(this).text(Math.ceil(now));
        }
    });
});
});



// // Register interest button
// jQuery(function() {
//     // Adds a class to open the overlay
//     jQuery(".register-interest button").click(function() {
//         jQuery('.register-interest-overlay').addClass('show');
//     });
//     // Removes the added class to close the overlay
//     jQuery(".register-interest-overlay button.closeOverlay").click(function() {
//         jQuery('.register-interest-overlay').removeClass('show');
//     });
// });


// Adds a class to open the outlets single post directions overlay

jQuery(function() {
    jQuery(".single-outlet-post button.open-overlay").click(function() {
        jQuery(this).parents('div.single-post').toggleClass('show');
        jQuery(this).toggleClass('active');
    });
});
