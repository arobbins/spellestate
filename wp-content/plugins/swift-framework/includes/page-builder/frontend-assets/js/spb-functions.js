/*global jQuery,Vivus,ajaxurl */

(function(){

	// USE STRICT
	"use strict";
	
	var SPB = SPB || {};

	/////////////////////////////////////////////
	// ANIMATED HEADLINE
	/////////////////////////////////////////////
 	SPB.animatedHeadline = {
		init: function () {
			var animatedHeadlines = jQuery('.spb-animated-headline'),
				animationDelay = 2500;

			animatedHeadlines.each( function() {
				var headline = jQuery(this).find('.sf-headline');

				setTimeout( function() {
					SPB.animatedHeadline.animateHeadline( headline );
				}, animationDelay);
			});

			// Single letter animation
			SPB.animatedHeadline.singleLetters( jQuery('.sf-headline.letters').find('b') );
		},
		singleLetters: function ( $words ) {
			$words.each( function() {
				var word = jQuery(this),
					letters = word.text().split(''),
					selected = word.hasClass('is-visible');

				for ( var i in letters ) {
					if ( word.parents('.rotate-2').length > 0 ) letters[i] = '<em>' + letters[i] + '</em>';
					letters[i] = ( selected ) ? '<i class="in">' + letters[i] + '</i>': '<i>' + letters[i] + '</i>';
				}

			    var newLetters = letters.join('');
			    word.html( newLetters ).css( 'opacity', 1 );
			});
		},
		animateHeadline: function ( $headlines ) {
			var duration = 2500;

			$headlines.each( function() {
				var headline = jQuery(this);
				
				if ( headline.hasClass('loading-bar') ) {
					duration = 3800;
					var barAnimationDelay = 3800,
						barWaiting = barAnimationDelay - 3000;
					setTimeout( function() {
						headline.find('.sf-words-wrapper').addClass('is-loading');
					}, barWaiting);
				} else if ( headline.hasClass('clip') ) {
					var spanWrapper = headline.find('.sf-words-wrapper'),
						newWidth = spanWrapper.width() + 10;
					spanWrapper.css('width', newWidth);
				} else if ( !headline.hasClass('type') ) {
					//assign to .sf-words-wrapper the width of its longest word
					var words = headline.find('.sf-words-wrapper b'),
						width = 0;
					words.each( function() {
						var wordWidth = jQuery(this).width();
					    if (wordWidth > width) width = wordWidth;
					});
					width = width > 0 ? width : '';
					headline.find('.sf-words-wrapper').css('width', width);
				}

				//trigger animation
				setTimeout( function() {
					SPB.animatedHeadline.hideWord( headline.find('.is-visible').eq(0) );
				}, duration);
			});
		},
		hideWord: function ( $word ) {
			var nextWord = SPB.animatedHeadline.takeNext( $word ),
				animationDelay = 2500,
				lettersDelay = 50,
				typeLettersDelay = 150,
				selectionDuration = 500,
				typeAnimationDelay = selectionDuration + 800,
				revealDuration = 600,
				barAnimationDelay = 3800,
				barWaiting = barAnimationDelay - 3000;

			if ( $word.parents('.sf-headline').hasClass('type') ) {
				var parentSpan = $word.parent('.sf-words-wrapper');
				parentSpan.addClass('selected').removeClass('waiting');	
				setTimeout( function() { 
					parentSpan.removeClass('selected'); 
					$word.removeClass('is-visible').addClass('is-hidden').children('i').removeClass('in').addClass('out');
				}, selectionDuration);
				setTimeout( function() {
					SPB.animatedHeadline.showWord( nextWord, typeLettersDelay );
				}, typeAnimationDelay);
			} else if ( $word.parents('.sf-headline').hasClass('letters') ) {
				var bool = ( $word.children('i').length >= nextWord.children('i').length ) ? true : false;
				SPB.animatedHeadline.hideLetter( $word.find('i').eq(0), $word, bool, lettersDelay );
				SPB.animatedHeadline.showLetter( nextWord.find('i').eq(0), nextWord, bool, lettersDelay );
			}  else if ( $word.parents('.sf-headline').hasClass('clip') ) {
				$word.parents('.sf-words-wrapper').animate({ width : '2px' }, revealDuration, function(){
					SPB.animatedHeadline.switchWord( $word, nextWord );
					SPB.animatedHeadline.showWord( nextWord );
				});
			} else if ( $word.parents('.sf-headline').hasClass('loading-bar') ) {
				$word.parents('.sf-words-wrapper').removeClass('is-loading');
				SPB.animatedHeadline.switchWord($word, nextWord);
				setTimeout( function() {
					SPB.animatedHeadline.hideWord( nextWord );
				}, barAnimationDelay);
				setTimeout( function() {
					$word.parents('.sf-words-wrapper').addClass('is-loading');
				}, barWaiting);
			} else {
				SPB.animatedHeadline.switchWord( $word, nextWord );
				setTimeout( function() {
					SPB.animatedHeadline.hideWord( nextWord );
				}, animationDelay);
			}
		},
		showWord: function ( $word, $duration ) {
			var revealDuration = 600,
				revealAnimationDelay = 1500;

			if ( $word.parents('.sf-headline').hasClass('type') ) {
				SPB.animatedHeadline.showLetter( $word.find('i').eq(0), $word, false, $duration );
				$word.addClass('is-visible').removeClass('is-hidden');
			} else if ( $word.parents('.sf-headline').hasClass('clip') ) {
				$word.parents('.sf-words-wrapper').animate({
					'width' : $word.width() + 10
				}, revealDuration, function() { 
					setTimeout( function() {
						SPB.animatedHeadline.hideWord( $word );
					}, revealAnimationDelay); 
				});
			}
		},
		hideLetter: function ( $letter, $word, $bool, $duration ) {
			var animationDelay = 2500;

			$letter.removeClass('in').addClass('out');
			
			if ( !$letter.is(':last-child') ) {
			 	setTimeout( function() {
			 		SPB.animatedHeadline.hideLetter( $letter.next(), $word, $bool, $duration );
			 	}, $duration);  
			} else if ( $bool ) { 
			 	setTimeout( function() {
			 		SPB.animatedHeadline.hideWord( SPB.animatedHeadline.takeNext( $word ) );
			 	}, animationDelay);
			}

			if ( $letter.is(':last-child') && jQuery('html').hasClass('no-csstransitions') ) {
				var nextWord = SPB.animatedHeadline.takeNext( $word );
				SPB.animatedHeadline.switchWord( $word, nextWord );
			} 
		},
		showLetter: function ( $letter, $word, $bool, $duration ) {
			var animationDelay = 2500;

			$letter.addClass('in').removeClass('out');
			
			if ( !$letter.is(':last-child') ) { 
				setTimeout( function() {
					SPB.animatedHeadline.showLetter( $letter.next(), $word, $bool, $duration );
				}, $duration ); 
			} else { 
				if ( $word.parents('.sf-headline').hasClass('type') ) {
					setTimeout( function() {
						$word.parents('.sf-words-wrapper').addClass('waiting');
					}, 200);
				}
				if ( !$bool ) {
					setTimeout( function() {
						SPB.animatedHeadline.hideWord( $word );
					}, animationDelay);
				}
			}
		},
		takeNext: function ( $word ) {
			return ( !$word.is(':last-child') ) ? $word.next() : $word.parent().children().eq(0);
		},
		takePrev: function ( $word ) {
			return ( !$word.is(':first-child') ) ? $word.prev() : $word.parent().children().last();
		},
		switchWord: function ( $oldWord, $newWord ) {
			$oldWord.removeClass('is-visible').addClass('is-hidden');
			$newWord.removeClass('is-hidden').addClass('is-visible');
		}
	};


	/////////////////////////////////////////////
	// DYNAMIC HEADER
	/////////////////////////////////////////////
	SPB.dynamicHeader = {
		init: function () {
			var headerHeight = jQuery('.header-wrap').height();
			SPB.var.window.scroll(function() {
				var inview = jQuery('.dynamic-header-change:in-viewport');
				var scrollTop = SPB.var.window.scrollTop() + headerHeight;

				if ( inview.length > 0 ) {
					inview.each(function() {
						var thisSection = jQuery(this),
							thisStart = thisSection.offset().top,
							thisEnd = thisStart + thisSection.outerHeight(),
							headerStyle = thisSection.data('header-style');

						//console.log('scrollTop: '+scrollTop+', start: '+thisStart+', end: '+thisEnd+', style:'+headerStyle)
						
						if ( scrollTop < thisStart || scrollTop > thisEnd ) {
							return;
						}

						if ( headerStyle === "" && SPB.var.defaultHeaderStyle !== "" ) {
							jQuery('.header-wrap').attr('data-style', SPB.var.defaultHeaderStyle);
						}

						if ( scrollTop > thisStart && scrollTop < thisEnd ) {
							jQuery('.header-wrap').attr('data-style', headerStyle);
						}
					});
				}
			});
		}
	};


	/////////////////////////////////////////////
	// ISOTOPE ASSET
	/////////////////////////////////////////////
 	SPB.isotopeAsset = {
		init: function () {
			jQuery('.spb-isotope').each(function() {
				var isotopeInstance = jQuery(this),
					layoutMode = isotopeInstance.data('layout-mode');

				isotopeInstance.isotope({
					resizable: true,
					layoutMode: layoutMode,
					isOriginLeft: !SPB.var.isRTL
				});
				setTimeout(function() {
					isotopeInstance.isotope('layout');
				}, 500);
			});	
		}
	};


	/////////////////////////////////////////////
	// SVG ICON ANIMATE
	/////////////////////////////////////////////
 	SPB.svgIconAnimate = {
		init: function () {
			jQuery('.sf-svg-icon-animate').each(function() {
				var thisSVG = jQuery(this),
					svg_id = thisSVG.attr('id'),
					file_url = thisSVG.data('svg-src'),
					anim_type = thisSVG.data('anim-type');
					//path_timing = thisSVG.data('path-timing'),
					//anim_timing = thisSVG.data('anim-timing');

				if ( thisSVG.hasClass('animation-disabled') ) {
					new Vivus(svg_id, {
							duration: 1,
							file: file_url,
							type: anim_type,
							onReady: function(svg) {
								svg.reset().play();
								setTimeout(function() {
									thisSVG.css('opacity', 1);
								}, 50);
							}
						});
				} else {
					new Vivus(svg_id, {
						duration: 200,
						file: file_url,
						type: anim_type,
						pathTimingFunction: Vivus.EASE_IN,
						animTimingFunction: Vivus.EASE_OUT,
						onReady: function(svg) {
							thisSVG.appear(function() {
								setTimeout(function(){
									svg.reset().play();
								}, 200);
							});
						}
					});
					setTimeout(function() {
						thisSVG.css('opacity', 1);
					}, 50);
				}
			});
		}
	};


	/////////////////////////////////////////////
	// TEAM MEMBER AJAX
	/////////////////////////////////////////////
 	SPB.teamMemberAjax = {
		init: function () {
			
			jQuery(document).on( 'click', '.team-member-ajax', function(e) {

				if ( SPB.var.isMobile || SPB.var.window.width() < 1000 ) {
					return e;
				}

				e.preventDefault();

				// Add body classes
			    SPB.var.body.addClass( 'sf-team-ajax-will-open' );
			    SPB.var.body.addClass( 'sf-container-block sf-ajax-loading' );

			    // Fade in overlay
			    jQuery('.sf-container-overlay').animate({
			    	'opacity' : 1
			    }, 300);

				// Run ajax post
				var postID = jQuery(this).data('id');
				jQuery.post( ajaxurl, {
			        action: 'spb_team_member_ajax',            
			        post_id: postID // << should grab this from input...
			    }, function(data) {
			        var response   =  jQuery(data);
			        var postdata   =  response.filter('#postdata').html();
			        
			        SPB.var.body.append( '<div class="sf-team-ajax-container"></div>' );
			        jQuery( '.sf-team-ajax-container' ).html(postdata);

			        setTimeout(function() {
			        	jQuery( '.sf-container-overlay' ).addClass('loading-done');
			        	SPB.var.body.addClass( 'sf-team-ajax-open' );
			        	jQuery('.sf-container-overlay').on( 'click touchstart', SPB.teamMemberAjax.closeOverlay );
			        }, 300);
			    });
			});

			jQuery(document).on( 'click', '.team-ajax-close', function(e) {
				e.preventDefault();
				SPB.teamMemberAjax.closeOverlay();
			});
		},
		closeOverlay: function() {
			SPB.var.body.removeClass( 'sf-team-ajax-open' );
			jQuery( '.sf-container-overlay' ).off( 'click touchstart' ).animate({
				'opacity' : 0
			}, 500, function() {
				SPB.var.body.removeClass( 'sf-container-block' );
				SPB.var.body.removeClass( 'sf-team-ajax-will-open' );
				jQuery( '.sf-team-ajax-container' ).remove();
	        	jQuery( '.sf-container-overlay' ).removeClass('loading-done');
			});
		}
	};


	/////////////////////////////////////////////
	// GLOBAL VARIABLES
	/////////////////////////////////////////////
	SPB.var = {};
	SPB.var.window = jQuery(window);
	SPB.var.body = jQuery('body');
	SPB.var.isRTL = SPB.var.body.hasClass('rtl') ? true : false;
	SPB.var.deviceAgent = navigator.userAgent.toLowerCase();
	SPB.var.isMobile = SPB.var.deviceAgent.match(/(iphone|ipod|ipad|android|iemobile)/);
	SPB.var.isIEMobile = SPB.var.deviceAgent.match(/(iemobile)/);
	SPB.var.isSafari = navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1 &&  navigator.userAgent.indexOf('Android') == -1;
	SPB.var.isFirefox = navigator.userAgent.indexOf('Firefox') > -1;
	SPB.var.defaultHeaderStyle = jQuery('.header-wrap').data('default-style');

	/////////////////////////////////////////////
	// DOCUMENT READY
	/////////////////////////////////////////////
	SPB.onReady = {
		init: function() {

			// SVG ICON ANIMATE
			if ( jQuery('.sf-svg-icon-animate').length > 0 ) {
				SPB.svgIconAnimate.init();
			}

			// DYNAMIC HEADER
			if ( SPB.var.body.hasClass('sticky-header-transparent') ) {
				SPB.dynamicHeader.init();
			}

			// ISOTOPE ASSETS
			if ( jQuery('.spb-isotope').length > 0 ) {
				SPB.isotopeAsset.init();
			}
		}
	};


	/////////////////////////////////////////////
	// DOCUMENT LOAD
	/////////////////////////////////////////////
	SPB.onLoad = {
		init: function() {
			if ( jQuery('.spb-animated-headline').length > 0 ) {
				SPB.animatedHeadline.init();
			}

			if ( jQuery('.team-member-ajax').length > 0 ) {
				SPB.teamMemberAjax.init();
			}
		}
	};


	/////////////////////////////////////////////
	// HOOKS
	/////////////////////////////////////////////
	jQuery(document).ready(SPB.onReady.init);
	jQuery(window).load(SPB.onLoad.init);

})(jQuery);