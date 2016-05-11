/*global jQuery,Vivus,ajaxurl */

(function(){

	// USE STRICT
	"use strict";
	
	var SPB = SPB || {};

	////////////////////////////////////////////
	// GENERAL
	/////////////////////////////////////////////
 	SPB.general = {
 		init: function() {

 		},
 		load: function() {
 			// OFFSET CALC
			SPB.general.offsetCalc();
			SPB.var.window.smartresize( function() {
				SPB.general.offsetCalc();
			});
 		},
 		offsetCalc: function() {
 			var adjustment = 0;
			
			if (jQuery('#wpadminbar').length > 0) {
				adjustment = jQuery('#wpadminbar').height();
				SPB.var.wpadminbarheight = jQuery('#wpadminbar').height();
			}
			
			if (SPB.var.body.hasClass('sticky-header-enabled') && !SPB.var.body.hasClass('sticky-header-transparent') ) {
				adjustment += jQuery('.sticky-header').height() > 0 ? jQuery('.sticky-header').height() : jQuery('#header-section').height();
			}

			if (jQuery('.sticky-top-bar').length > 0) {
				adjustment += jQuery('.sticky-top-bar').height() > 0 ? jQuery('.sticky-top-bar').height() : jQuery('#top-bar').height();
			}
			
			SPB.var.offset = adjustment;
 		}
 	};

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

			if ( !SPB.var.body.hasClass('sticky-header-transparent') ) {
				return;
			}
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
	// FAQs
	/////////////////////////////////////////////
 	SPB.faqs = {
		init: function () {
			jQuery('.faq-item').on('click', function() {
				var faqItem = jQuery(this);
				faqItem.toggleClass('closed');
				faqItem.find('.faq-text').slideToggle(400);
			});
		}
	};


	/////////////////////////////////////////////
	// ICON BOX GRID
	/////////////////////////////////////////////
 	SPB.iconBoxGrid = {
		init: function () {
			jQuery(document).on('click', '.spb_icon_box_grid a.box-link', function(e) {
				var linkHref = jQuery(this).attr('href'),
					linkOffset = jQuery(this).data('offset') ? jQuery(this).data('offset') : 0;

				if (linkHref && linkHref.indexOf('#') === 0) {
					var headerHeight = SPB.var.offset;

					SPB.var.isScrolling = true;

					jQuery('html, body').stop().animate({
						scrollTop: jQuery(linkHref).offset().top - headerHeight + linkOffset
					}, 1000, 'easeInOutExpo', function() {
						SPB.var.isScrolling = false;
					});

					e.preventDefault();

				} else {
					return e;
				}
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
							selfDestroy: true,
							onReady: function(svg) {
								svg.finish();
							}
						});
				} else {
					new Vivus(svg_id, {
						duration: 200,
						file: file_url,
						type: anim_type,
						pathTimingFunction: Vivus.EASE_IN,
						animTimingFunction: Vivus.EASE_OUT,
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
	SPB.var.isScrolling = false;
	SPB.var.offset = 0;
	SPB.var.wpadminbarheight = 32;

	/////////////////////////////////////////////
	// DOCUMENT READY
	/////////////////////////////////////////////
	SPB.onReady = {
		init: function() {

			SPB.general.init();

			// FAQs
			if ( jQuery('.spb_faqs_element').length > 0 ) {
				//SPB.faqs.init();
			}

			// ICON GRID
			if ( jQuery('.spb_icon_box_grid').length > 0 ) {
				SPB.iconBoxGrid.init();
			}

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

			SPB.general.load();

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


	/* http://prismjs.com/download.html?themes=prism&languages=markup+css+clike+javascript+php */
	var _self="undefined"!=typeof window?window:"undefined"!=typeof WorkerGlobalScope&&self instanceof WorkerGlobalScope?self:{},Prism=function(){var e=/\blang(?:uage)?-(\w+)\b/i,t=0,n=_self.Prism={util:{encode:function(e){return e instanceof a?new a(e.type,n.util.encode(e.content),e.alias):"Array"===n.util.type(e)?e.map(n.util.encode):e.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/\u00a0/g," ")},type:function(e){return Object.prototype.toString.call(e).match(/\[object (\w+)\]/)[1]},objId:function(e){return e.__id||Object.defineProperty(e,"__id",{value:++t}),e.__id},clone:function(e){var t=n.util.type(e);switch(t){case"Object":var a={};for(var r in e)e.hasOwnProperty(r)&&(a[r]=n.util.clone(e[r]));return a;case"Array":return e.map&&e.map(function(e){return n.util.clone(e)})}return e}},languages:{extend:function(e,t){var a=n.util.clone(n.languages[e]);for(var r in t)a[r]=t[r];return a},insertBefore:function(e,t,a,r){r=r||n.languages;var l=r[e];if(2==arguments.length){a=arguments[1];for(var i in a)a.hasOwnProperty(i)&&(l[i]=a[i]);return l}var o={};for(var s in l)if(l.hasOwnProperty(s)){if(s==t)for(var i in a)a.hasOwnProperty(i)&&(o[i]=a[i]);o[s]=l[s]}return n.languages.DFS(n.languages,function(t,n){n===r[e]&&t!=e&&(this[t]=o)}),r[e]=o},DFS:function(e,t,a,r){r=r||{};for(var l in e)e.hasOwnProperty(l)&&(t.call(e,l,e[l],a||l),"Object"!==n.util.type(e[l])||r[n.util.objId(e[l])]?"Array"!==n.util.type(e[l])||r[n.util.objId(e[l])]||(r[n.util.objId(e[l])]=!0,n.languages.DFS(e[l],t,l,r)):(r[n.util.objId(e[l])]=!0,n.languages.DFS(e[l],t,null,r)))}},plugins:{},highlightAll:function(e,t){var a={callback:t,selector:'code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code'};n.hooks.run("before-highlightall",a);for(var r,l=a.elements||document.querySelectorAll(a.selector),i=0;r=l[i++];)n.highlightElement(r,e===!0,a.callback)},highlightElement:function(t,a,r){for(var l,i,o=t;o&&!e.test(o.className);)o=o.parentNode;o&&(l=(o.className.match(e)||[,""])[1],i=n.languages[l]),t.className=t.className.replace(e,"").replace(/\s+/g," ")+" language-"+l,o=t.parentNode,/pre/i.test(o.nodeName)&&(o.className=o.className.replace(e,"").replace(/\s+/g," ")+" language-"+l);var s=t.textContent,u={element:t,language:l,grammar:i,code:s};if(!s||!i)return n.hooks.run("complete",u),void 0;if(n.hooks.run("before-highlight",u),a&&_self.Worker){var c=new Worker(n.filename);c.onmessage=function(e){u.highlightedCode=e.data,n.hooks.run("before-insert",u),u.element.innerHTML=u.highlightedCode,r&&r.call(u.element),n.hooks.run("after-highlight",u),n.hooks.run("complete",u)},c.postMessage(JSON.stringify({language:u.language,code:u.code,immediateClose:!0}))}else u.highlightedCode=n.highlight(u.code,u.grammar,u.language),n.hooks.run("before-insert",u),u.element.innerHTML=u.highlightedCode,r&&r.call(t),n.hooks.run("after-highlight",u),n.hooks.run("complete",u)},highlight:function(e,t,r){var l=n.tokenize(e,t);return a.stringify(n.util.encode(l),r)},tokenize:function(e,t){var a=n.Token,r=[e],l=t.rest;if(l){for(var i in l)t[i]=l[i];delete t.rest}e:for(var i in t)if(t.hasOwnProperty(i)&&t[i]){var o=t[i];o="Array"===n.util.type(o)?o:[o];for(var s=0;s<o.length;++s){var u=o[s],c=u.inside,g=!!u.lookbehind,h=!!u.greedy,f=0,d=u.alias;u=u.pattern||u;for(var p=0;p<r.length;p++){var m=r[p];if(r.length>e.length)break e;if(!(m instanceof a)){u.lastIndex=0;var y=u.exec(m),v=1;if(!y&&h&&p!=r.length-1){var b=r[p+1].matchedStr||r[p+1],k=m+b;if(p<r.length-2&&(k+=r[p+2].matchedStr||r[p+2]),u.lastIndex=0,y=u.exec(k),!y)continue;var w=y.index+(g?y[1].length:0);if(w>=m.length)continue;var _=y.index+y[0].length,P=m.length+b.length;if(v=3,P>=_){if(r[p+1].greedy)continue;v=2,k=k.slice(0,P)}m=k}if(y){g&&(f=y[1].length);var w=y.index+f,y=y[0].slice(f),_=w+y.length,S=m.slice(0,w),O=m.slice(_),j=[p,v];S&&j.push(S);var A=new a(i,c?n.tokenize(y,c):y,d,y,h);j.push(A),O&&j.push(O),Array.prototype.splice.apply(r,j)}}}}}return r},hooks:{all:{},add:function(e,t){var a=n.hooks.all;a[e]=a[e]||[],a[e].push(t)},run:function(e,t){var a=n.hooks.all[e];if(a&&a.length)for(var r,l=0;r=a[l++];)r(t)}}},a=n.Token=function(e,t,n,a,r){this.type=e,this.content=t,this.alias=n,this.matchedStr=a||null,this.greedy=!!r};if(a.stringify=function(e,t,r){if("string"==typeof e)return e;if("Array"===n.util.type(e))return e.map(function(n){return a.stringify(n,t,e)}).join("");var l={type:e.type,content:a.stringify(e.content,t,r),tag:"span",classes:["token",e.type],attributes:{},language:t,parent:r};if("comment"==l.type&&(l.attributes.spellcheck="true"),e.alias){var i="Array"===n.util.type(e.alias)?e.alias:[e.alias];Array.prototype.push.apply(l.classes,i)}n.hooks.run("wrap",l);var o="";for(var s in l.attributes)o+=(o?" ":"")+s+'="'+(l.attributes[s]||"")+'"';return"<"+l.tag+' class="'+l.classes.join(" ")+'" '+o+">"+l.content+"</"+l.tag+">"},!_self.document)return _self.addEventListener?(_self.addEventListener("message",function(e){var t=JSON.parse(e.data),a=t.language,r=t.code,l=t.immediateClose;_self.postMessage(n.highlight(r,n.languages[a],a)),l&&_self.close()},!1),_self.Prism):_self.Prism;var r=document.currentScript||[].slice.call(document.getElementsByTagName("script")).pop();return r&&(n.filename=r.src,document.addEventListener&&!r.hasAttribute("data-manual")&&document.addEventListener("DOMContentLoaded",n.highlightAll)),_self.Prism}();"undefined"!=typeof module&&module.exports&&(module.exports=Prism),"undefined"!=typeof global&&(global.Prism=Prism);
	Prism.languages.markup={comment:/<!--[\w\W]*?-->/,prolog:/<\?[\w\W]+?\?>/,doctype:/<!DOCTYPE[\w\W]+?>/,cdata:/<!\[CDATA\[[\w\W]*?]]>/i,tag:{pattern:/<\/?(?!\d)[^\s>\/=.$<]+(?:\s+[^\s>\/=]+(?:=(?:("|')(?:\\\1|\\?(?!\1)[\w\W])*\1|[^\s'">=]+))?)*\s*\/?>/i,inside:{tag:{pattern:/^<\/?[^\s>\/]+/i,inside:{punctuation:/^<\/?/,namespace:/^[^\s>\/:]+:/}},"attr-value":{pattern:/=(?:('|")[\w\W]*?(\1)|[^\s>]+)/i,inside:{punctuation:/[=>"']/}},punctuation:/\/?>/,"attr-name":{pattern:/[^\s>\/]+/,inside:{namespace:/^[^\s>\/:]+:/}}}},entity:/&#?[\da-z]{1,8};/i},Prism.hooks.add("wrap",function(a){"entity"===a.type&&(a.attributes.title=a.content.replace(/&amp;/,"&"))}),Prism.languages.xml=Prism.languages.markup,Prism.languages.html=Prism.languages.markup,Prism.languages.mathml=Prism.languages.markup,Prism.languages.svg=Prism.languages.markup;
	Prism.languages.css={comment:/\/\*[\w\W]*?\*\//,atrule:{pattern:/@[\w-]+?.*?(;|(?=\s*\{))/i,inside:{rule:/@[\w-]+/}},url:/url\((?:(["'])(\\(?:\r\n|[\w\W])|(?!\1)[^\\\r\n])*\1|.*?)\)/i,selector:/[^\{\}\s][^\{\};]*?(?=\s*\{)/,string:/("|')(\\(?:\r\n|[\w\W])|(?!\1)[^\\\r\n])*\1/,property:/(\b|\B)[\w-]+(?=\s*:)/i,important:/\B!important\b/i,"function":/[-a-z0-9]+(?=\()/i,punctuation:/[(){};:]/},Prism.languages.css.atrule.inside.rest=Prism.util.clone(Prism.languages.css),Prism.languages.markup&&(Prism.languages.insertBefore("markup","tag",{style:{pattern:/(<style[\w\W]*?>)[\w\W]*?(?=<\/style>)/i,lookbehind:!0,inside:Prism.languages.css,alias:"language-css"}}),Prism.languages.insertBefore("inside","attr-value",{"style-attr":{pattern:/\s*style=("|').*?\1/i,inside:{"attr-name":{pattern:/^\s*style/i,inside:Prism.languages.markup.tag.inside},punctuation:/^\s*=\s*['"]|['"]\s*$/,"attr-value":{pattern:/.+/i,inside:Prism.languages.css}},alias:"language-css"}},Prism.languages.markup.tag));
	Prism.languages.clike={comment:[{pattern:/(^|[^\\])\/\*[\w\W]*?\*\//,lookbehind:!0},{pattern:/(^|[^\\:])\/\/.*/,lookbehind:!0}],string:{pattern:/(["'])(\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/,greedy:!0},"class-name":{pattern:/((?:\b(?:class|interface|extends|implements|trait|instanceof|new)\s+)|(?:catch\s+\())[a-z0-9_\.\\]+/i,lookbehind:!0,inside:{punctuation:/(\.|\\)/}},keyword:/\b(if|else|while|do|for|return|in|instanceof|function|new|try|throw|catch|finally|null|break|continue)\b/,"boolean":/\b(true|false)\b/,"function":/[a-z0-9_]+(?=\()/i,number:/\b-?(?:0x[\da-f]+|\d*\.?\d+(?:e[+-]?\d+)?)\b/i,operator:/--?|\+\+?|!=?=?|<=?|>=?|==?=?|&&?|\|\|?|\?|\*|\/|~|\^|%/,punctuation:/[{}[\];(),.:]/};
	Prism.languages.javascript=Prism.languages.extend("clike",{keyword:/\b(as|async|await|break|case|catch|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally|for|from|function|get|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|set|static|super|switch|this|throw|try|typeof|var|void|while|with|yield)\b/,number:/\b-?(0x[\dA-Fa-f]+|0b[01]+|0o[0-7]+|\d*\.?\d+([Ee][+-]?\d+)?|NaN|Infinity)\b/,"function":/[_$a-zA-Z\xA0-\uFFFF][_$a-zA-Z0-9\xA0-\uFFFF]*(?=\()/i}),Prism.languages.insertBefore("javascript","keyword",{regex:{pattern:/(^|[^\/])\/(?!\/)(\[.+?]|\\.|[^\/\\\r\n])+\/[gimyu]{0,5}(?=\s*($|[\r\n,.;})]))/,lookbehind:!0,greedy:!0}}),Prism.languages.insertBefore("javascript","class-name",{"template-string":{pattern:/`(?:\\\\|\\?[^\\])*?`/,greedy:!0,inside:{interpolation:{pattern:/\$\{[^}]+\}/,inside:{"interpolation-punctuation":{pattern:/^\$\{|\}$/,alias:"punctuation"},rest:Prism.languages.javascript}},string:/[\s\S]+/}}}),Prism.languages.markup&&Prism.languages.insertBefore("markup","tag",{script:{pattern:/(<script[\w\W]*?>)[\w\W]*?(?=<\/script>)/i,lookbehind:!0,inside:Prism.languages.javascript,alias:"language-javascript"}}),Prism.languages.js=Prism.languages.javascript;
	Prism.languages.php=Prism.languages.extend("clike",{keyword:/\b(and|or|xor|array|as|break|case|cfunction|class|const|continue|declare|default|die|do|else|elseif|enddeclare|endfor|endforeach|endif|endswitch|endwhile|extends|for|foreach|function|include|include_once|global|if|new|return|static|switch|use|require|require_once|var|while|abstract|interface|public|implements|private|protected|parent|throw|null|echo|print|trait|namespace|final|yield|goto|instanceof|finally|try|catch)\b/i,constant:/\b[A-Z0-9_]{2,}\b/,comment:{pattern:/(^|[^\\])(?:\/\*[\w\W]*?\*\/|\/\/.*)/,lookbehind:!0}}),Prism.languages.insertBefore("php","class-name",{"shell-comment":{pattern:/(^|[^\\])#.*/,lookbehind:!0,alias:"comment"}}),Prism.languages.insertBefore("php","keyword",{delimiter:/\?>|<\?(?:php)?/i,variable:/\$\w+\b/i,"package":{pattern:/(\\|namespace\s+|use\s+)[\w\\]+/,lookbehind:!0,inside:{punctuation:/\\/}}}),Prism.languages.insertBefore("php","operator",{property:{pattern:/(->)[\w]+/,lookbehind:!0}}),Prism.languages.markup&&(Prism.hooks.add("before-highlight",function(e){"php"===e.language&&(e.tokenStack=[],e.backupCode=e.code,e.code=e.code.replace(/(?:<\?php|<\?)[\w\W]*?(?:\?>)/gi,function(a){return e.tokenStack.push(a),"{{{PHP"+e.tokenStack.length+"}}}"}))}),Prism.hooks.add("before-insert",function(e){"php"===e.language&&(e.code=e.backupCode,delete e.backupCode)}),Prism.hooks.add("after-highlight",function(e){if("php"===e.language){for(var a,n=0;a=e.tokenStack[n];n++)e.highlightedCode=e.highlightedCode.replace("{{{PHP"+(n+1)+"}}}",Prism.highlight(a,e.grammar,"php").replace(/\$/g,"$$$$"));e.element.innerHTML=e.highlightedCode}}),Prism.hooks.add("wrap",function(e){"php"===e.language&&"markup"===e.type&&(e.content=e.content.replace(/(\{\{\{PHP[0-9]+\}\}\})/g,'<span class="token php">$1</span>'))}),Prism.languages.insertBefore("php","comment",{markup:{pattern:/<[^?]\/?(.*?)>/,inside:Prism.languages.markup},php:/\{\{\{PHP[0-9]+\}\}\}/}));

})(jQuery);