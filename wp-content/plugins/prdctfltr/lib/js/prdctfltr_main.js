(function($){
"use strict";

	var curr_data = {};
	var ajaxActive = false;
	var priceRatio = prdctfltr.priceratio;

	$('.prdctfltr_subonly').each( function() {
		prdctfltr_show_sub_cats($(this).closest('.prdctfltr_wc'));
	});

	$.expr[':'].Contains = function(a,i,m){
		return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
	};

	String.prototype.getValueByKey = function (k) {
		var p = new RegExp('\\b' + k + '\\b', 'gi');
		return this.search(p) != -1 ? decodeURIComponent(this.substr(this.search(p) + k.length + 1).substr(0, this.substr(this.search(p) + k.length + 1).search(/(&|;|$)/))) : "";
	};

	function init_ranges() {
		$.each( prdctfltr.rangefilters, function(i, obj3) {
			obj3.onFinish = function (data) {
				if ( $('#'+i).hasClass('pf_rng_price') ) {
					if ( data.min == data.from && data.max == data.to ) {$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_min_"]:first').val( '' );$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_max_"]:first').val( '' ).trigger('change');}else {$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_min_"]:first').val( ( data.from_value == null ? parseInt(data.from)*priceRatio : parseInt($(data.from_value).text())*priceRatio ) );$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_max_"]:first').val( ( data.to_value == null ? parseInt(data.to)*priceRatio : parseInt($(data.to_value).text())*priceRatio ) ).trigger('change');}
				}
				else {
					if ( data.min == data.from && data.max == data.to ) {$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_min_"]:first').val( '' );$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_max_"]:first').val( '' ).trigger('change');} else {$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_min_"]:first').val( ( data.from_value == null ? data.from : $(data.from_value).text() ) );$('#'+i).closest('.prdctfltr_filter').find('input[name^="rng_max_"]:first').val( ( data.to_value == null ? data.to : $(data.to_value).text() ) ).trigger('change');}
				}
			}
			$('#'+i).ionRangeSlider(obj3);
		});
	}
	init_ranges();

	function prdctfltr_sort_classes() {
		if ( prdctfltr.ajax_class == '' ) {
			prdctfltr.ajax_class = '.products';
		}
		if ( prdctfltr.ajax_category_class == '' ) {
			prdctfltr.ajax_category_class = '.product-category';
		}
		if ( prdctfltr.ajax_product_class == '' ) {
			prdctfltr.ajax_product_class = '.type-product';
		}
		if ( prdctfltr.ajax_pagination_class == '' ) {
			prdctfltr.ajax_pagination_class = '.woocommerce-pagination';
		}
		if ( prdctfltr.ajax_count_class == '' ) {
			prdctfltr.ajax_count_class = '.woocommerce-result-count';
		}
		if ( prdctfltr.ajax_orderby_class == '' ) {
			prdctfltr.ajax_orderby_class = '.woocommerce-ordering';
		}
	}
	prdctfltr_sort_classes();

	function reorder_adoptive(curr) {

		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.each( function() {

			var curr_el = $(this);

			curr_el.find('.prdctfltr_adoptive').each( function() {
				var filter = $(this);
				var checkboxes = filter.find('.prdctfltr_checkboxes');
				filter.find('.pf_adoptive_hide').each( function() {
					var addThis = $(this);
					$(this).remove();
					checkboxes.append(addThis);
				});
			});
		});

	}
	reorder_adoptive();

	function reorder_limit(curr) {

		curr = ( typeof curr == 'undefined' ? $('.prdctfltr_wc') : curr );

		curr.each( function() {

			var curr_el = $(this);

			curr_el.find('.prdctfltr_attributes').each( function() {
				var filter = $(this);
				var checkboxes = filter.find('.prdctfltr_checkboxes');
				checkboxes.each(function(){
					var max = parseInt(filter.attr('data-limit'));
					if (max != 0 && $(this).find('> label').length > max+1) {
						$(this).find('> label:gt('+max+')').attr('style', 'display:none !important').end().append($('<div class="pf_more"><span>'+prdctfltr.localization.show_more+'</span></div>'));
					}
				});
			});
		});

	}
	reorder_limit();

	$(document).on('click', '.pf_more:not(.pf_activated)', function() {
		var filter = $(this).closest('.prdctfltr_attributes');
		var checkboxes = filter.find('.prdctfltr_checkboxes');

		var displayType = checkboxes.find('> label:first').css('display');

		checkboxes.find('> label').attr('style', 'display:'+displayType+' !important');
		checkboxes.find('.pf_more').html('<span>'+prdctfltr.localization.show_less+'</span>');
		checkboxes.find('.pf_more').addClass('pf_activated');

		if ( filter.closest('.prdctfltr_wc').hasClass('pf_mod_masonry') ) {
			filter.closest('.prdctfltr_filter_inner').isotope('layout');
		}
	});

	$(document).on('click', '.pf_more.pf_activated', function() {
		var filter = $(this).closest('.prdctfltr_attributes');
		var checkboxes = filter.find('.prdctfltr_checkboxes');
		checkboxes.each(function(){
			var max = parseInt(filter.attr('data-limit'));
			if (max != 0 && $(this).find('> label').length > max+1) {

				$(this).find('> label:gt('+max+')').attr('style', 'display:none !important');
				$(this).find('.pf_more').html('<span>'+prdctfltr.localization.show_more+'</span>').removeClass('pf_activated');

				if ( filter.closest('.prdctfltr_wc').hasClass('pf_mod_masonry') ) {
					filter.closest('.prdctfltr_filter_inner').isotope('layout');
				}
			}
		});
	});

	function set_select_index(curr) {

		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.each( function() {

			var curr_el = $(this);

			var selects = curr_el.find('.pf_select .prdctfltr_filter');
			if ( selects.length > 0 ) {
				var zIndex = selects.length;
				selects.each( function() {
					$(this).css({'z-index':zIndex});
					zIndex--;
				});
			}
		});

	}
	set_select_index();

	function init_search(curr) {

		var curr = $('.prdctfltr_wc');

		curr.each( function() {

			var curr_el = $(this);

			curr_el.find('input.pf_search').each( function() {
				if ( curr_el.hasClass('prdctfltr_click_filter') ) {
					$(this).keyup( function () {
						if ($(this).next().is(':hidden')) {
							$(this).next().show();
						}
						if ($(this).val()==''){
							$(this).next().hide();
						}
					});
				}
			});
		});
	}
	init_search();


	$(document).on( 'keydown', '.pf_search', function() {
		if(event.which==13) {
			$(this).next().trigger('click');
			return false;
		}
	});

	$(document).on( 'click', '.pf_search_trigger', function() {
		var wc = $(this).closest('.prdctfltr_wc');

		if ( !wc.hasClass('prdctfltr_click_filter') ) {
			wc.find('.prdctfltr_woocommerce_filter_submit').trigger('click');
		}
		else {
			var obj = wc.find('.prdctfltr_woocommerce_ordering');
			prdctfltr_respond_550(obj);
		}

		return false;
	});

	function prdctfltr_filter_terms_init(curr) {
		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.each( function() {
			var curr_el = $(this);
			if ( curr_el.hasClass('prdctfltr_search_fields') ) {
				curr_el.find('.prdctfltr_filter.prdctfltr_attributes .prdctfltr_checkboxes').each( function() {
					var curr_list = $(this);
					prdctfltr_filter_terms(curr_list)
				});
			}
		});

	}
	prdctfltr_filter_terms_init();

	function prdctfltr_init_tooltips(curr) {

		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.each( function() {
			var curr_el = $(this);

			var $pf_tooltips = curr_el.find('.prdctfltr_filter.pf_attr_img label, .prdctfltr_terms_customized:not(.prdctfltr_terms_customized_select) label');

			$pf_tooltips
			.on('mouseover', function()
			{
				var $this = $(this);

				if ($this.prop('hoverTimeout'))
				{
					$this.prop('hoverTimeout', clearTimeout($this.prop('hoverTimeout')));
				}

				$this.prop('hoverIntent', setTimeout(function()
				{
					$this.addClass('prdctfltr_hover');
				}, 250));
				})
			.on('mouseleave', function()
				{
				var $this = $(this);

				if ($this.prop('hoverIntent'))
				{
					$this.prop('hoverIntent', clearTimeout($this.prop('hoverIntent')));
				}

				$this.prop('hoverTimeout', setTimeout(function()
				{
					$this.removeClass('prdctfltr_hover');
				}, 250));
			});
		});

	}
	prdctfltr_init_tooltips();

	function prdctfltr_show_opened_widgets() {

		if ( $('.prdctfltr-widget').length > 0 && $('.prdctfltr-widget .prdctfltr_error').length !== 1 ) {
			$('.prdctfltr-widget .prdctfltr_filter').each( function() {

				var curr = $(this);

				if ( curr.find('input[type="checkbox"]:checked').length > 0 ) {

					curr.find('.prdctfltr_widget_title .prdctfltr-down').removeClass('prdctfltr-down').addClass('prdctfltr-up');
					curr.find('.prdctfltr_checkboxes').addClass('prdctfltr_down').css({'display':'block'});

				}
			});
		}

	}
	prdctfltr_show_opened_widgets();

	function prdctfltr_init_scroll(curr) {

		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		if ( curr.hasClass('prdctfltr_scroll_active') ) {

			curr.find('.prdctfltr_filter:not(.prdctfltr_range) .prdctfltr_checkboxes').mCustomScrollbar({
				axis:'y',
				scrollInertia:550,
				autoExpandScrollbar:true,
				advanced:{
					updateOnBrowserResize:true,
					updateOnContentResize:true
				}
			});

			if ( curr.hasClass('pf_mod_row') && ( curr.find('.prdctfltr_checkboxes').length > $('.prdctfltr_filter_wrapper:first').attr('data-columns') ) ) {
				if ( $('.prdctfltr-widget').length == 0 || $('.prdctfltr-widget').length == 1 && $('.prdctfltr-widget .prdctfltr_error').length == 1 ) {

					if ( curr.hasClass('prdctfltr_slide') ) {
						curr.find('.prdctfltr_woocommerce_ordering').show();
					}

					var curr_scroll_column = curr.find('.prdctfltr_filter:first').width();
					var curr_columns = curr.find('.prdctfltr_filter').length;

					curr.find('.prdctfltr_filter_inner').css('width', curr_columns*curr_scroll_column);
					curr.find('.prdctfltr_filter').css('width', curr_scroll_column);
					
					curr.find('.prdctfltr_filter_wrapper').mCustomScrollbar({
						axis:'x',
						scrollInertia:550,
						scrollbarPosition:'outside',
						autoExpandScrollbar:true,
						advanced:{
							updateOnBrowserResize:true,
							updateOnContentResize:false
						}
					});

					if ( curr.hasClass('prdctfltr_slide') ) {
						curr.find('.prdctfltr_woocommerce_ordering').hide();
					}
				}
			}

			if ( $('.prdctfltr-widget').length == 0 || $('.prdctfltr-widget .prdctfltr_error').length == 1 ) {
				curr.find('.prdctfltr_slide .prdctfltr_woocommerce_ordering').hide();
			}

		}
	}

	function prdctfltr_show_sub_cats(curr) {

		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.find('.prdctfltr_subonly label.prdctfltr_active').each( function() {
			var subParent = $(this).closest('.prdctfltr_sub');

			if ( subParent.length > 0 ) {
				var subParentCon = subParent.html();
			}
			else {
				subParent = $(this).next();
				var subParentCon = subParent.html();
			}

			var checkboxesWrap = $(this).closest('.prdctfltr_checkboxes');
			checkboxesWrap.find('label:not(.prdctfltr_ft_none), .prdctfltr_sub').remove();
			if ( checkboxesWrap.find('.mCSB_container').length > 0 ) {
				checkboxesWrap.find('.mCSB_container').append(subParentCon);
			}
			else {
				checkboxesWrap.append(subParentCon);
			}
		});

	}

	function prdctfltr_show_opened_cats(curr) {

		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.find('label.prdctfltr_active').each( function() {
			$(this).next().show();
			$(this).parents('.prdctfltr_sub').each( function() {
				$(this).show();
				if ( !$(this).prev().hasClass('prdctfltr_clicked') ) {
					$(this).prev().addClass('prdctfltr_clicked');
				}
			});
		});

	}

	function prdctfltr_all_cats(curr) {

		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.find('.prdctfltr_filter.prdctfltr_attributes.prdctfltr_expand_parents .prdctfltr_sub').each( function() {
			var curr = $(this);
			if ( !curr.is(':visible') ) {
				curr.show();
				if ( !curr.prev().hasClass('prdctfltr_clicked') ) {
					curr.prev().addClass('prdctfltr_clicked');
				}
			}
		});

	}

	function prdctfltr_submit_form(curr_filter) {

		if ( curr_filter.hasClass('prdctfltr_click_filter') || curr_filter.find('input[name="reset_filter"]:checked').length > 0 ) {

			prdctfltr_respond_550(curr_filter.find('form'));

		}

	}

	$('.prdctfltr_wc').each( function() {

		var curr = $(this);

		prdctfltr_init_scroll(curr);

		if ( curr.find('.prdctfltr_filter.prdctfltr_attributes.prdctfltr_expand_parents').length > 0 ) {
			prdctfltr_all_cats(curr);
		}
		else {
			prdctfltr_show_opened_cats(curr);
		}

		if ( curr.hasClass('pf_mod_masonry') ) {
			curr.find('.prdctfltr_filter_inner').isotope({
				resizable: false,
				masonry: { }
			});
			if ( !curr.hasClass('prdctfltr_always_visible') ) {
				curr.find('.prdctfltr_woocommerce_ordering').hide();
			}
		}

		if ( curr.attr('class').indexOf('pf_sidebar_css') > 0 ) {
			if ( curr.hasClass('pf_sidebar_css_right') ) {
				$('body').css('right', '0px');
			}
			else {
				$('body').css('left', '0px');
			}
			if ( !$('body').hasClass('wc-prdctfltr-active-overlay') ) {
				$('body').addClass('wc-prdctfltr-active-overlay');
			}
		}

	});

	$(document).on( 'change', 'input[name^="rng_"]', function() {
		var curr = $(this).closest('.prdctfltr_woocommerce');

		if ( curr.hasClass('prdctfltr_click_filter') ) {
			prdctfltr_respond_550(curr.find('.prdctfltr_woocommerce_ordering'));
		}
	});

	$(document).on('click', '.prdctfltr_woocommerce_filter_submit', function() {

		var curr = $(this).closest('.prdctfltr_woocommerce_ordering');

		prdctfltr_respond_550(curr);

		return false;

	});

	$(document).on('click', '.prdctfltr_woocommerce_filter', function() {

		var curr_filter = $(this).closest('.prdctfltr_woocommerce');

		if (curr_filter.hasClass('pf_mod_masonry') && curr_filter.find('.prdctfltr_woocommerce_ordering:hidden').length > 0 ) {
			if (curr_filter.hasClass('prdctfltr_active')===false) {
				var curr_check = curr_filter.find('.prdctfltr_woocommerce_ordering');
				curr_check.show().find('.prdctfltr_filter_inner').isotope('layout');
				curr_check.hide();
			}
		}

		if ( !curr_filter.hasClass('prdctfltr_always_visible') ) {
			var curr = $(this).closest('.prdctfltr_woocommerce').find('.prdctfltr_woocommerce_ordering');

			if( $(this).hasClass('prdctfltr_active') ) {
				if ( curr_filter.attr('class').indexOf( 'pf_sidebar' ) == -1 ) {
					if ( curr_filter.hasClass( 'pf_fullscreen' ) ) {
						curr.stop(true,true).fadeOut(200, function() {
							curr.find('.prdctfltr_close_sidebar').remove();
						});
					}
					else {
						curr.stop(true,true).slideUp(200);
					}
				}
				else {
					curr.stop(true,true).fadeOut(200, function() {
						curr.find('.prdctfltr_close_sidebar').remove();
					});
					if ( curr_filter.attr('class').indexOf( 'pf_sidebar_css' ) > 0 ) {
						if ( curr_filter.hasClass('pf_sidebar_css_right') ) {
							$('body').css({'right':'0px','bottom':'auto','top':'auto','left':'auto'});
						}
						else {
							$('body').css({'right':'auto','bottom':'auto','top':'auto','left':'0px'});
						}
						$('.prdctfltr_overlay').remove();
					}
				}
				$(this).removeClass('prdctfltr_active');
				$('body').removeClass('wc-prdctfltr-active');
			}
			else {
				$(this).addClass('prdctfltr_active')
				if ( curr_filter.attr('class').indexOf( 'pf_sidebar' ) == -1 ) {
					$('body').addClass('wc-prdctfltr-active');
					if ( curr_filter.hasClass( 'pf_fullscreen' ) ) {
						curr.prepend('<div class="prdctfltr_close_sidebar"><i class="prdctfltr-delete"></i> '+prdctfltr.localization.close_filter+'</div>');
						curr.stop(true,true).fadeIn(200);

						var curr_height = $(window).height() - curr.find('.prdctfltr_filter_inner').outerHeight() - curr.find('.prdctfltr_close_sidebar').outerHeight() - curr.find('.prdctfltr_buttons').outerHeight();

						if ( curr_height > 128 ) {
							var curr_diff = curr_height/2;
							curr_height = curr.outerHeight();
							curr.css({'padding-top':curr_diff+'px'});
						}
						else {
							curr_height = $(window).height() - curr.find('.prdctfltr_close_sidebar').outerHeight() - curr.find('.prdctfltr_buttons').outerHeight() -128;
						}
						curr_filter.find('.prdctfltr_filter_wrapper').css({'max-height':curr_height});
					}
					else {
						curr.stop(true,true).slideDown(200);
					}
				}
				else {
					curr.prepend('<div class="prdctfltr_close_sidebar"><i class="prdctfltr-delete"></i> '+prdctfltr.localization.close_filter+'</div>');
					curr.stop(true,true).fadeIn(200);
					if ( curr_filter.attr('class').indexOf( 'pf_sidebar_css' ) > 0 ) {
						$('body').append('<div class="prdctfltr_overlay"></div>');
						if ( curr_filter.hasClass('pf_sidebar_css_right') ) {
							$('body').css({'right':'160px','bottom':'auto','top':'auto','left':'auto'});
							$('.prdctfltr_overlay').css({'right':'310px'}).delay(200).animate({'opacity':.33},200,'linear');
						}
						else {
							$('body').css({'right':'auto','bottom':'auto','top':'auto','left':'160px'});
							$('.prdctfltr_overlay').css({'left':'310px'}).delay(200).animate({'opacity':.33},200,'linear');
						}
					}
					$('body').addClass('wc-prdctfltr-active');
				}

			}
		}

		return false;
	});

	$(document).on('click', '.prdctfltr_overlay, .prdctfltr_close_sidebar', function() {

		if ( $(this).closest('.prdctfltr_woocommerce').length > 0 ) {
			$(this).closest('.prdctfltr_woocommerce').find('.prdctfltr_woocommerce_filter.prdctfltr_active').trigger('click');
		}
		else {
			$('.prdctfltr_woocommerce_filter.prdctfltr_active:first').trigger('click');
		}

	});

	$(document).on('click', '.pf_default_select .prdctfltr_widget_title', function() {

		var curr = $(this).closest('.prdctfltr_filter').find('.prdctfltr_checkboxes');

		if ( !curr.hasClass('prdctfltr_down') ) {
			curr.prev().find('.prdctfltr-down').attr('class', 'prdctfltr-up');
			curr.addClass('prdctfltr_down');
			curr.slideDown(100);
		}
		else {
			curr.slideUp(100);
			curr.removeClass('prdctfltr_down');
			curr.prev().find('.prdctfltr-up').attr('class', 'prdctfltr-down');
		}

	});

	var pf_select_opened = false;
	$(document).on('click', '.pf_select .prdctfltr_filter > span, .prdctfltr_terms_customized_select.prdctfltr_filter > span', function() {
		pf_select_opened = true;
		var curr = $(this).next();

		if ( !curr.hasClass('prdctfltr_down') ) {
			curr.prev().find('.prdctfltr-down').attr('class', 'prdctfltr-up');
			curr.addClass('prdctfltr_down');
			curr.slideDown(100, function() {
				pf_select_opened = false;
			});

			if ( !$('body').hasClass('wc-prdctfltr-select') ) {
				$('body').addClass('wc-prdctfltr-select');
			}
		}
		else {
			curr.slideUp(100, function() {
				pf_select_opened = false;

			});
			curr.removeClass('prdctfltr_down');
			curr.prev().find('.prdctfltr-up').attr('class', 'prdctfltr-down');
			if ( curr.closest('.prdctfltr_woocommerce').find('.prdctfltr_down').length == 0 ) {
				$('body').removeClass('wc-prdctfltr-select');
			}
		}

	});

	$(document).on( 'click', 'body.wc-prdctfltr-select', function(e) {

		var curr_target = $(e.target);

		if ( $('.prdctfltr_woocommerce').find('.prdctfltr_down').length > 0 && pf_select_opened === false && !curr_target.is('span, input, i') ) {
			$('.prdctfltr_woocommerce').find('.prdctfltr_down').each( function() {
				var curr = $(this);
				if ( curr.is(':visible') ) {
					curr.slideUp(100);
					curr.removeClass('prdctfltr_down');
					curr.prev().find('.prdctfltr-up').attr('class', 'prdctfltr-down');
				}
			});
			$('body').removeClass('wc-prdctfltr-select');
		}
	});

	$(document).on('click', 'span.prdctfltr_sale input, span.prdctfltr_instock input, span.prdctfltr_reset input', function() {


		var curr_clicked = $(this).attr('name');
		var curr = $(this).parent();

		var curr_filter = curr.closest('.prdctfltr_wc');

		var archiveAjax = false;
		if ( $('body').hasClass('prdctfltr-ajax') ) {
			archiveAjax = true;
		}

		var shortcodeAjax = false;

		if ( archiveAjax === false ) {

			var checkShortcode = $('.prdctfltr_sc_products');
			var checkWidget = $('.prdctfltr_wc_widget');

			if ( checkShortcode.length > 0 ) {
				if ( checkWidget.length > 0 ) {
					checkShortcode = $('.prdctfltr_sc_products:first');
					shortcodeAjax = true;
					var multiAjax = true;
				}
				else {
					checkShortcode = checkShortcode;
					shortcodeAjax = true;
				}

			}
		}

		var ourObj = {};
		if ( archiveAjax===true ) {
			$('.prdctfltr_wc:not([data-id="'+curr_filter.attr('data-id')+'"])').each( function() {
				ourObj[$(this).attr('data-id')] = $(this);
			});
			ourObj[curr_filter.attr('data-id')] = $('.prdctfltr_wc[data-id="'+curr_filter.attr('data-id')+'"]');
		}
		else if ( shortcodeAjax===true ) {
			if ( typeof multiAjax == 'undefined' ) {
				ourObj[curr_filter.attr('data-id')] = curr_filter;
			}
			else {
				$('.prdctfltr_wc:not([data-id="'+curr_filter.attr('data-id')+'"])').each( function() {
					ourObj[$(this).attr('data-id')] = $(this);
				});
				ourObj[curr_filter.attr('data-id')] = $('.prdctfltr_wc[data-id="'+curr_filter.attr('data-id')+'"]');
			}
		}
		else {
			ourObj[curr_filter.attr('data-id')] = curr_filter;
		}

		var pf_length = 0;
		var i;

		for (i in ourObj) {
			if (ourObj.hasOwnProperty(i)) {
				pf_length++;
			}
		}

		$.each( ourObj, function(i, obj) {

			obj = $(obj);

			var curr_obj = obj.find('.prdctfltr_buttons input[name="'+curr_clicked+'"]');

			if ( !curr_obj.parent().hasClass('prdctfltr_active') ) {
				curr_obj.prop('checked', true).change().parent().addClass('prdctfltr_active');
			}
			else {
				curr_obj.removeAttr('checked').parent().removeClass('prdctfltr_active');
			}

			if ( !--pf_length ) {

				prdctfltr_submit_form(curr_filter);

			}

		});

	});

	$(document).on('click', '.prdctfltr_instock:not(span) label, .prdctfltr_orderby:not(span) label, .prdctfltr_per_page:not(span) label', function() {

		var label = $(this);
		var curr_chckbx = label.find('input[type="checkbox"]');
		var curr = curr_chckbx.closest('.prdctfltr_filter');
		var curr_var = curr_chckbx.val();
		var curr_filter = curr_chckbx.closest('.prdctfltr_woocommerce');

		if ( curr_chckbx.closest('label').hasClass('prdctfltr_active') ) {

			curr.children(':first').val('');
			curr.find('input:not([type="hidden"])').prop('checked', false);
			curr.find('label').removeClass('prdctfltr_active');

		}
		else {

			curr.children(':first').val(curr_var);
			curr.find('input:not([type="hidden"])').prop('checked', false);
			curr.find('label').removeClass('prdctfltr_active');
			curr_chckbx.prop('checked', true);
			curr_chckbx.closest('label').addClass('prdctfltr_active')

			if ( curr_chckbx.closest('.prdctfltr_woocommerce').hasClass('pf_select') || curr.hasClass('prdctfltr_terms_customized_select') ) {
				curr_chckbx.closest('.prdctfltr_filter').find('.prdctfltr_checkboxes').slideUp(250).removeClass('prdctfltr_down');
				curr_chckbx.closest('.prdctfltr_filter').find('.prdctfltr_regular_title i').removeClass('prdctfltr-up').addClass('prdctfltr-down');
			}

		}

		prdctfltr_submit_form(curr_filter);
		return false;
	});

	$(document).on('click', '.prdctfltr_byprice label', function() {

		var label = $(this);
		var curr_chckbx = label.find('input[type="checkbox"]');
		var curr = curr_chckbx.closest('.prdctfltr_filter');
		var curr_var = curr_chckbx.val().split('-');
		var curr_filter = curr_chckbx.closest('.prdctfltr_woocommerce');

		if ( curr_chckbx.closest('label').hasClass('prdctfltr_active') ) {

			curr.children(':first').val('');
			curr.children(':first').next().val('');
			curr.find('input:not([type="hidden"])').prop('checked', false);
			curr.find('label').removeClass('prdctfltr_active');

		}
		else {

			curr.children(':first').val(curr_var[0]);
			curr.children(':first').next().val(curr_var[1]);
			curr.find('input:not([type="hidden"])').prop('checked', false);
			curr.find('label').removeClass('prdctfltr_active');
			curr_chckbx.prop('checked', true);
			curr_chckbx.closest('label').addClass('prdctfltr_active');

		}

		prdctfltr_submit_form(curr_filter);
		return false;
	});

	$(document).on('click', '.prdctfltr_attributes input[type="checkbox"]', function() {

		var curr_chckbx = $(this);
		var curr = curr_chckbx.closest('.prdctfltr_filter');
		var curr_var = curr_chckbx.val();
		var curr_filter = curr.closest('.prdctfltr_wc');

		if ( curr_filter.hasClass('pf_adptv_unclick') ) {
			if ( curr_chckbx.parent().hasClass( 'pf_adoptive_hide' ) ) {
				return false;
			}
		}

		prdctfltr_check(curr, curr_chckbx, curr_var);

	});

	function prdctfltr_check(curr, curr_chckbx, curr_var) {

		var curr_filter = curr.closest('.prdctfltr_wc');

		var archiveAjax = false;
		if ( $('body').hasClass('prdctfltr-ajax') ) {
			archiveAjax = true;
		}

		var archivePlain = false;
		if ( $('body').hasClass('prdctfltr-shop') ) {
			archivePlain = true;
		}


		var shortcodeAjax = false;

		if ( archiveAjax === false ) {

			var checkShortcode = $('.prdctfltr_sc_products');
			var checkWidget = $('.prdctfltr_wc_widget');

			if ( checkShortcode.length > 0 ) {
				if ( checkWidget.length > 0 ) {
					checkShortcode = $('.prdctfltr_sc_products:first');
					shortcodeAjax = true;
					var multiAjax = true;
				}
				else {
					checkShortcode = checkShortcode;
					shortcodeAjax = true;
				}

			}
		}

		var ourObj = {};
		if ( archiveAjax===true || archivePlain===true ) {
			$('.prdctfltr_wc:not([data-id="'+curr_filter.attr('data-id')+'"])').each( function() {
				ourObj[$(this).attr('data-id')] = $(this);
			});
			ourObj[curr_filter.attr('data-id')] = $('.prdctfltr_wc[data-id="'+curr_filter.attr('data-id')+'"]');
		}
		else if ( shortcodeAjax===true ) {
			if ( typeof multiAjax == 'undefined' ) {
				ourObj[curr_filter.attr('data-id')] = curr_filter;
			}
			else {
				$('.prdctfltr_wc:not([data-id="'+curr_filter.attr('data-id')+'"])').each( function() {
					ourObj[$(this).attr('data-id')] = $(this);
				});
				ourObj[curr_filter.attr('data-id')] = $('.prdctfltr_wc[data-id="'+curr_filter.attr('data-id')+'"]');
			}
			
		}
		else {
			ourObj[curr_filter.attr('data-id')] = curr_filter;
		}

		var pf_length = 0;
		var i;

		for (i in ourObj) {
			if (ourObj.hasOwnProperty(i)) {
				pf_length++;
			}
		}

		$.each( ourObj, function(i, obj) {

			obj = $(obj);

			if ( curr.hasClass('prdctfltr_multi') ) {

				if ( curr_chckbx.val() !== '' ) {

					if ( curr_chckbx.closest('label').hasClass('prdctfltr_active') ) {

						if ( curr.hasClass('prdctfltr_merge_terms') ) {
							var curr_settings = ( curr.children('input[type="hidden"]:first').val().indexOf('+') > 0 ? curr.children('input[type="hidden"]:first').val().replace('+' + curr_var, '').replace(curr_var + '+', '') : '' );
						}
						else {
							var curr_settings = ( curr.children('input[type="hidden"]:first').val().indexOf(',') > 0 ? curr.children('input[type="hidden"]:first').val().replace(',' + curr_var, '').replace(curr_var + ',', '') : '' );
						}

						var curr_name = curr.children('input[type="hidden"]:first').attr('name');
						var curr_chckbxval = curr_chckbx.attr('value');

						if ( $('body').hasClass('prdctfltr-shop') && !$('body').hasClass('prdctfltr-ajax') ) {
							obj.find('input[name="'+curr_name+'"]').val(curr_settings);
						}
						else {
							obj.find('.prdctfltr_filter input[name="'+curr_name+'"]').val(curr_settings);
						}

						obj.find('.prdctfltr_filter[data-filter="'+curr_name+'"] input[value="'+curr_chckbxval+'"]').prop('checked', false).change().closest('label').removeClass('prdctfltr_active');

					}
					else {

						if ( curr.hasClass('prdctfltr_merge_terms') ) {
							var curr_settings = ( curr.children('input[type="hidden"]:first').val() == '' ? curr_var : curr.children('input[type="hidden"]:first').val() + '+' + curr_var );
						}
						else {
							var curr_settings = ( curr.children('input[type="hidden"]:first').val() == '' ? curr_var : curr.children('input[type="hidden"]:first').val() + ',' + curr_var );
						}

						var curr_name = curr.children('input[type="hidden"]:first').attr('name');
						var curr_chckbxval = curr_chckbx.attr('value');

						if ( $('body').hasClass('prdctfltr-shop') && !$('body').hasClass('prdctfltr-ajax') ) {
							obj.find('input[name="'+curr_name+'"]').val(curr_settings);
						}
						else {
							obj.find('.prdctfltr_filter input[name="'+curr_name+'"]').val(curr_settings);
						}

						obj.find('.prdctfltr_filter[data-filter="'+curr_name+'"] input[value="'+curr_chckbxval+'"]').prop('checked', true).change().closest('label').addClass('prdctfltr_active');

					}
				}
				else {

					var curr_name = curr.children(':first').attr('name');

					if ( $('body').hasClass('prdctfltr-shop') && !$('body').hasClass('prdctfltr-ajax') ) {
						obj.find('.prdctfltr_add_inputs input[name="'+curr_name+'"]').val('');
					}

					obj.find('.prdctfltr_filter[data-filter="'+curr_name+'"] input[name="'+curr_name+'"]').each( function() {
						var curr_field = $(this);

						curr_field.val('');
						curr_field.closest('.prdctfltr_filter').find('input:not([type="hidden"])').prop('checked', false).change().closest('label').removeClass('prdctfltr_active');
					});

				}


			}
			else {

				if ( curr_chckbx.val() == '' ) {

					var curr_name = curr.children(':first').attr('name');

					if ( $('body').hasClass('prdctfltr-shop') && !$('body').hasClass('prdctfltr-ajax') ) {
						obj.find('.prdctfltr_add_inputs input[name="'+curr_name+'"]').val('');
					}

					obj.find('.prdctfltr_filter[data-filter="'+curr_name+'"] input[name="'+curr_name+'"]').each( function() {
						var curr_field = $(this);

						curr_field.val('');
						curr_field.closest('.prdctfltr_filter').find('input:not([type="hidden"]):checked').prop('checked', false).change().closest('label').removeClass('prdctfltr_active');
					});

				}
				else {

					if ( curr_chckbx.closest('label').hasClass('prdctfltr_active') ) {

						var curr_name = curr.children(':first').attr('name');
						var curr_chckbxval = curr_chckbx.attr('value');

						obj.find('.prdctfltr_filter[data-filter="'+curr_name+'"] input[name="'+curr_name+'"]').each( function() {
							var curr_field = $(this);

							curr_field.val('');
							curr_field.closest('.prdctfltr_filter').find('input[value="'+curr_chckbxval+'"]').prop('checked', false).change().closest('label').removeClass('prdctfltr_active');

						});

					}
					else {

						var curr_name = curr.children(':first').attr('name');
						var curr_chckbxval = curr_chckbx.attr('value');

						obj.find('.prdctfltr_filter[data-filter="'+curr_name+'"] input[name="'+curr_name+'"]').each( function() {
							var curr_field = $(this);

							curr_field.val(curr_var);
							curr_field.closest('.prdctfltr_filter').find('input:not([type="hidden"])').prop('checked', false).change().closest('label').removeClass('prdctfltr_active');
							curr_field.closest('.prdctfltr_filter').find('input[value="'+curr_chckbxval+'"]').prop('checked', true).change().closest('label').addClass('prdctfltr_active');

						});

					}

				}
			}

			if ( !--pf_length ) {

				prdctfltr_submit_form(curr_filter);

			}

		});

	}

	$(document).on('click', '.prdctfltr_filter_title a.prdctfltr_title_remove, .prdctfltr_regular_title a, .prdctfltr_widget_title a', function() {

		var curr_deep = false;
		if ( !$(this).hasClass('prdctfltr_title_remove') ) {
			var curr_deep = true;
			var curr = $(this).closest('.prdctfltr_filter');
		}

		var curr_key = $(this).attr('data-key');

		var curr_filter = $(this).closest('.prdctfltr_wc');

		var archiveAjax = false;
		if ( $('body').hasClass('prdctfltr-ajax') ) {
			archiveAjax = true;
		}

		var shortcodeAjax = false;

		if ( archiveAjax === false ) {

			var checkShortcode = $('.prdctfltr_sc_products');
			var checkWidget = $('.prdctfltr_wc_widget');

			if ( checkShortcode.length > 0 ) {
				if ( checkWidget.length > 0 ) {
					checkShortcode = $('.prdctfltr_sc_products:first');
					shortcodeAjax = true;
					var multiAjax = true;
				}
				else {
					checkShortcode = checkShortcode;
					shortcodeAjax = true;
				}

			}
		}

		if ( archiveAjax===true ) {
			var ourObj = $('.prdctfltr_wc');
		}
		else if ( shortcodeAjax===true ) {
			if ( typeof multiAjax == 'undefined' ) {
				var ourObj = $(this).closest('.prdctfltr_wc');
			}
			else {
				var ourObj = $('.prdctfltr_wc');
			}
		}
		else {
			var ourObj = $(this).closest('.prdctfltr_wc');
		}

		var pf_length = ourObj.length;

		ourObj.each( function(i, obj) {

			obj = $(obj);

			if ( curr_key == 's' ) {
				obj.find('.prdctfltr_search input.pf_search').val('').attr('value','');
			}
			else if ( curr_key == 'byprice' ) {
				obj.find('.prdctfltr_byprice input[type="hidden"], .prdctfltr_price input[type="hidden"]').each(function() {
					$(this).remove();
				});
			}
			else if ( curr_key == 'products_per_page' ) {
				obj.find('.prdctfltr_per_page input[type="hidden"]').each(function() {
					$(this).remove();
				});
			}
			else if ( curr_key == 'instock_products' ) {
				obj.find('.prdctfltr_filter.prdctfltr_instock input[type="hidden"], span.prdctfltr_instock input[type="checkbox"]').each(function() {
					$(this).remove();
				});
			}
			else if ( curr_key == 'sale_products' ) {
				obj.find('span.prdctfltr_sale input[type="checkbox"]').each(function() {
					$(this).remove();
				});
			}
			else if ( curr_key.substr(0,4) == 'rng_' ) {
				obj.find('.prdctfltr_range input[type="hidden"][name$="'+curr_key.substr(4, curr_key.length)+'"]').each(function() {
					$(this).remove();
				});
			}
			else if ( curr_key == 'product_cat' ) {

				if ( $('body').hasClass('prdctfltr-shop') && !$('body').hasClass('prdctfltr-ajax') ) {
					var curr_els = obj.find('.prdctfltr_attributes[data-filter="'+curr_key+'"] > input[type="hidden"], .prdctfltr_add_inputs input[name="product_cat"]');
				}
				else {
					var curr_els = obj.find('.prdctfltr_attributes[data-filter="'+curr_key+'"] > input[type="hidden"]');
				}


				if ( curr_deep === true && curr_els.length > 1 ) {

					var cur_vals = obj.find('input[type="checkbox"]:checked');
					cur_vals.each( function() {

						var curr_value = $(this).val();

						curr_els.each( function() {

							var curr_chckd = $(this);
							var curr_chckdval = $(this).val();

							if ( curr_chckdval.indexOf( ',' ) > 0 ) {
								curr_chckd.val(curr_chckdval.replace(',' + curr_value, '').replace(curr_value + ',', ''));
							}
							else if ( curr_chckdval.indexOf( '+' ) > 0 ) {
								curr_chckd.val(curr_chckdval.replace('+' + curr_value, '').replace(curr_value + '+', ''));
							}
							else {
								curr_chckd.val(curr_chckdval.replace(curr_value, '').replace(curr_value, ''));
							}

						});

					});

				}
				else {

					if ( $('body').hasClass('prdctfltr-shop') && !$('body').hasClass('prdctfltr-ajax') ) {
						obj.find('.prdctfltr_attributes[data-filter="'+curr_key+'"] > input[type="hidden"], .prdctfltr_add_inputs input[name="product_cat"]').each(function() {
							$(this).remove();
						});
					}
					else {
						obj.find('.prdctfltr_attributes[data-filter="'+curr_key+'"] > input[type="hidden"]').each(function() {
							$(this).remove();
						});
					}

				}
			}
			else if ( curr_key == 'product_tag' ) {
				var curr_els = obj.find('.prdctfltr_attributes[data-filter="'+curr_key+'"] > input[type="hidden"]');

				if ( curr_deep === true && curr_els.length > 1 ) {

					var cur_vals = obj.find('input[type="checkbox"]:checked');
					cur_vals.each( function() {

						var curr_value = $(this).val();

						curr_els.each( function() {

							var curr_chckd = $(this);
							var curr_chckdval = $(this).val();

							if ( curr_chckdval.indexOf( ',' ) > 0 ) {
								curr_chckd.val(curr_chckdval.replace(',' + curr_value, '').replace(curr_value + ',', ''));
							}
							else if ( curr_chckdval.indexOf( '+' ) > 0 ) {
								curr_chckd.val(curr_chckdval.replace('+' + curr_value, '').replace(curr_value + '+', ''));
							}
							else {
								curr_chckd.val(curr_chckdval.replace(curr_value, '').replace(curr_value, ''));
							}

						});

					});

				}
				else {
					obj.find('.prdctfltr_attributes[data-filter="'+curr_key+'"] > input[type="hidden"]').each(function() {
						$(this).remove();
					});
				}
			}
			else {
				var curr_els = obj.find('.prdctfltr_'+curr_key+' > input[type="hidden"], .prdctfltr_attributes[data-filter="'+curr_key+'"] > input[type="hidden"]');

				if ( curr_deep === true && curr_els.length > 1 ) {

					var cur_vals = obj.find('input[type="checkbox"]:checked');
					cur_vals.each( function() {

						var curr_value = $(this).val();

						curr_els.each( function() {

							var curr_chckd = $(this);
							var curr_chckdval = $(this).val();

							if ( curr_chckdval.indexOf( ',' ) > 0 ) {
								curr_chckd.val(curr_chckdval.replace(',' + curr_value, '').replace(curr_value + ',', ''));
							}
							else if ( curr_chckdval.indexOf( '+' ) > 0 ) {
								curr_chckd.val(curr_chckdval.replace('+' + curr_value, '').replace(curr_value + '+', ''));
							}
							else {
								curr_chckd.val(curr_chckdval.replace(curr_value, '').replace(curr_value, ''));
							}

						});

					});

				}
				else {
					obj.find('.prdctfltr_'+curr_key+' > input[type="hidden"], .prdctfltr_attributes[data-filter="'+curr_key+'"] > input[type="hidden"]').each(function() {
						$(this).remove();
					});
				}
			}
		});

		prdctfltr_respond_550(curr_filter.find('form'));

		return false;
	});

	$(document).on('click', '.prdctfltr_checkboxes label > i', function() {

		var curr = $(this).parent().next();

		$(this).parent().toggleClass('prdctfltr_clicked');

		if ( curr.hasClass('prdctfltr_sub') ) {
			curr.slideToggle(100, function() {
				if ( curr.closest('.prdctfltr_woocommerce').hasClass('pf_mod_masonry') ) {
					curr.closest('.prdctfltr_woocommerce').find('.prdctfltr_filter_inner').isotope('layout');
				}
			});

		}

		return false;

	});

	function prdctfltr_get_loader(curr) {
		var curr_loader = curr.closest('.prdctfltr_woocommerce').attr('data-loader');
		if ( curr.closest('.prdctfltr_woocommerce').find('.prdctfltr_woocommerce_filter i').length > 0 && curr.closest('.prdctfltr_woocommerce').find('.prdctfltr_woocommerce_filter img').length == 0 ) {
			curr.closest('.prdctfltr_woocommerce').find('.prdctfltr_woocommerce_filter').addClass('pf_ajax_loading');
			curr.closest('.prdctfltr_woocommerce').find('.prdctfltr_woocommerce_filter i').replaceWith('<img src="'+prdctfltr.url+'/lib/images/svg-loaders/'+curr_loader+'.svg" class="prdctfltr_reset_this prdctfltr_loader" />');
		}
		else {
			if ( curr.closest('.prdctfltr_woocommerce').hasClass('prdctfltr_wc_widget') ) {
				curr.closest('.prdctfltr_woocommerce').prepend('<img src="'+prdctfltr.url+'/lib/images/svg-loaders/'+curr_loader+'.svg" class="prdctfltr_reset_this prdctfltr_loader" />');
			}
		}
	}

	function prdctfltr_reset_filters_550(obj) {

		if ( obj.closest('.prdctfltr_sc_products').length == 0 && prdctfltr.clearall == 'all' ) {
			obj.find('input[type="hidden"], input[name="sale_products"], input[name="instock_products"]:not([type="hidden"])').remove();
		}
		else {
			obj.find('.prdctfltr_filter input[type="hidden"], input[name="sale_products"], input[name="instock_products"]:not([type="hidden"])').remove();
		}

		obj.find('.prdctfltr_filter input.pf_search').val('').prop('disabled',true).attr('disabled','true');

	}

	function prdctfltr_remove_empty_inputs_550(obj) {

		obj.find('.prdctfltr_filter input[type="hidden"], .prdctfltr_filter input.pf_search, .prdctfltr_add_inputs input[type="hidden"]').each(function() { //, .prdctfltr_add_inputs input[type="hidden"]:not([name="post_type"])

			var curr_val = $(this).val();

			if ( curr_val == '' ) {
				if ( $(this).is(':visible') ) {
					$(this).prop('disabled',true).attr('disabled','true');
				}
				else {
					$(this).remove();
				}
			}

		});

	}

	function prdctfltr_remove_ranges_550(obj) {
		obj.find('.prdctfltr_filter.prdctfltr_range').each( function() {
			var curr_rng = $(this);
			if ( curr_rng.find('[name^="rng_min_"]').val() == undefined || curr_rng.find('[name^="rng_max_"]').val() == undefined ) {
				curr_rng.find('input').remove();
			}
		});
	}

	function prdctfltr_check_display_550(obj) {

		if ( $('body').hasClass('wc-prdctfltr-active') ) {

			if ( obj.attr('class').indexOf( 'pf_sidebar' ) == -1 ) {
				if ( obj.hasClass( 'pf_fullscreen' ) ) {
					obj.find('form').stop(true,true).fadeOut(200, function() {
						obj.find('.prdctfltr_close_sidebar').remove();
					});
				}
				else {
					if ( !obj.hasClass('prdctfltr_wc_widget') ) {
						obj.find('form').stop(true,true).slideUp(200);
					}
				}
			}
			else {
				obj.find('form').fadeOut(200);

				if ( obj.attr('class').indexOf( 'pf_sidebar_css' ) > 0 ) {
					if ( obj.hasClass('pf_sidebar_css_right') ) {
						$('body').css({'right':'0px','bottom':'auto','top':'auto','left':'auto'});
					}
					else {
						$('body').css({'right':'auto','bottom':'auto','top':'auto','left':'0px'});
					}
					$('.prdctfltr_overlay').remove();
				}
				obj.find('form').removeClass('prdctfltr_active');
				$('body').removeClass('wc-prdctfltr-active');

			}

		}

	}

	function prdctfltr_get_fields_550(obj) {

		var curr_fields = {};

		obj.find('.prdctfltr_filter input[type="hidden"], .prdctfltr_filter input.pf_search, .prdctfltr_add_inputs input[name="orderby"]').each( function() { //, .prdctfltr_add_inputs input[type="hidden"]:not([name="post_type"])
			if ( $(this).attr('value') !== '' ) {
				curr_fields[$(this).attr('name')] = $(this).attr('value');
			}
		});
		if ( obj.find('input[name="sale_products"]:checked').length > 0 ) {
			curr_fields['sale_products'] = 'on';
		}
		if ( obj.find('input[name="instock_products"]:checked').length > 0 ) {
			curr_fields['instock_products'] = 'in';
		}

		if ( prdctfltr.analytics == 'yes' ) {

			var analyticsData = {
				action: 'prdctfltr_analytics',
				pf_filters: curr_fields,
				pf_nonce: obj.attr('data-nonce')
				
			}

			$.post(prdctfltr.ajax, analyticsData, function(response) {

			});

		}

		return curr_fields;

	}

	function after_ajax(curr_next) {

		if ( curr_next.find('.prdctfltr_filter.prdctfltr_attributes.prdctfltr_expand_parents').length > 0 ) {
			prdctfltr_all_cats(curr_next);
		}
		else {
			prdctfltr_show_opened_cats(curr_next);
		}
		$('.prdctfltr_subonly').each( function() {
			prdctfltr_show_sub_cats($(this).closest('.prdctfltr_wc'));
		});
		prdctfltr_init_scroll(curr_next);
		prdctfltr_filter_terms_init(curr_next);
		prdctfltr_init_tooltips(curr_next);
		prdctfltr_show_opened_widgets();
		reorder_adoptive(curr_next);
		reorder_limit(curr_next);
		set_select_index(curr_next);
		init_search(curr_next);
		init_ranges();

		if ( curr_next !== undefined ) {
			if ( curr_next.hasClass('pf_mod_masonry') ) {

				curr_next.find('.prdctfltr_woocommerce_ordering').show();
				curr_next.find('.prdctfltr_filter_inner').isotope({
					resizable: false,
					masonry: { }
				});
				if ( !curr_next.hasClass('prdctfltr_always_visible') ) {
					curr_next.find('.prdctfltr_woocommerce_ordering').hide();
				}
			}

		}

		if ( $(prdctfltr.ajax_orderby_class).length<1 ) {
			$('.prdctfltr_add_inputs input[name="orderby"]').remove();
		}

	}

	var pf_paged = 1;
	var pf_offset = 0;
	var pf_restrict = '';

	$(document).on('click', '.prdctfltr_sc_products.prdctfltr_ajax '+prdctfltr.ajax_pagination_class+' a, body.prdctfltr-ajax '+prdctfltr.ajax_pagination_class+' a, .prdctfltr-pagination-default a, .prdctfltr-pagination-load-more a', function() {

		if (ajaxActive===true) {
			return false;
		}

		ajaxActive = true;

		var curr_link = $(this);

		var archiveAjax = false;
		if ( $('body').hasClass('prdctfltr-ajax') ) {
			archiveAjax = true;
		}

		var shortcodeAjax = false;
		var checkShortcode = curr_link.closest('.prdctfltr_sc_products');
		if ( archiveAjax===false && checkShortcode.length > 0 && checkShortcode.hasClass('prdctfltr_ajax') ) {
			shortcodeAjax = true;
			var obj = checkShortcode.find('form');
		}
		else {
			var obj = $('.prdctfltr_wc:first form');
		}

		var curr_href = curr_link.attr('href');

		if ( curr_href.indexOf('paged=') >= 0 ) {
			pf_paged = parseInt( curr_href.getValueByKey('paged'), 10 );
		}
		else {
			if ( shortcodeAjax===false ) {
				pf_offset = parseInt( $(prdctfltr.ajax_class).find(prdctfltr.ajax_product_class).length, 10 );
			}
			else {
				pf_offset = parseInt( checkShortcode.find(prdctfltr.ajax_product_class).length, 10 );
			}
		}

		pf_restrict = 'pagination';

		ajaxActive = false;
		prdctfltr_respond_550(obj);

		return false;

	});

	function prdctfltr_respond_550(curr) {

		if (ajaxActive===true) {
			return false;
		}

		prdctfltr_get_loader(curr);

		ajaxActive = true;
		var archiveAjax = false;
		if ( $('body').hasClass('prdctfltr-ajax') ) {
			archiveAjax = true;
			$(prdctfltr.ajax_class).fadeTo(200,.5).addClass('prdctfltr_faded');
		}

		var archivePlain = false;
		if ( $('body').hasClass('prdctfltr-shop') ) {
			archivePlain = true;
			$(prdctfltr.ajax_class).fadeTo(200,.5).addClass('prdctfltr_faded');
		}

		var shortcodeAjax = false;

		if ( archiveAjax === false ) {

			var checkShortcode = $('.prdctfltr_sc_products.prdctfltr_ajax');
			var checkWidget = $('.prdctfltr_wc_widget');

			if ( checkShortcode.length > 0 ) {
				if ( checkWidget.length > 0 ) {
					checkShortcode = $('.prdctfltr_sc_products.prdctfltr_ajax:first');
					shortcodeAjax = true;
					var multiAjax = true;
				}
				else {
					checkShortcode = checkShortcode;
					shortcodeAjax = true;
				}
				checkShortcode.find(prdctfltr.ajax_class).fadeTo(200,.5).addClass('prdctfltr_faded');
			}
		}

		var curr_fields = {};
		var requested_filters = {};

		if (archiveAjax===true) {
			var ourObj = $('.prdctfltr_wc');
		}
		else if ( shortcodeAjax===true ) {
			if ( typeof multiAjax == "undefined" ) {
				var ourObj = curr.closest('.prdctfltr_wc');
			}
			else {
				var ourObj = $('.prdctfltr_wc');
			}
		}
		else {
			var ourObj = curr.closest('.prdctfltr_wc');
		}

	var pf_length = ourObj.length;

	ourObj.each(function(i, obj) {

		obj=$(obj);

		var pf_id = obj.attr('data-id');

		if ( obj.find('input[name="reset_filter"]:checked').length > 0 ) {
			prdctfltr_reset_filters_550(obj);
		}
		else {
			prdctfltr_remove_empty_inputs_550(obj);
		}

		prdctfltr_remove_ranges_550(obj);

		prdctfltr_check_display_550(obj);

		requested_filters[pf_id] = pf_id;

		curr_fields[pf_id] = prdctfltr_get_fields_550(obj);

		if ( obj.find('input[name="reset_filter"]:checked').length > 0 ) {
			obj.find('input[name="reset_filter"]').remove();
	/*		if ( prdctfltr.clearall == 'category' && typeof prdctfltr.js_filters[pf_id] !== 'undefined' ) {
				if ( typeof prdctfltr.js_filters[pf_id]['adds'] !== 'undefined' ) {
					if ( typeof prdctfltr.js_filters[pf_id]['adds']['product_cat'] !== 'undefined' ) {
						curr_fields['product_cat'] = prdctfltr.js_filters[pf_id]['adds']['product_cat'];
					}
				}
			}*/
		}

		if ( !--pf_length ) {

			if (archiveAjax===true||shortcodeAjax===true) {

				var data = {
					action: 'prdctfltr_respond_550',
					pf_request: prdctfltr.js_filters,
					pf_requested: requested_filters,

					pf_query: prdctfltr.js_filters[pf_id]['args'],
					pf_shortcode: prdctfltr.js_filters[pf_id]['atts'],
					pf_atts: prdctfltr.js_filters[pf_id]['atts_sc'],
					pf_adds: prdctfltr.js_filters[pf_id]['adds'],

					pf_filters: curr_fields,
					pf_mode: 'archive',
					pf_set: (archiveAjax===true?'archive':'shortcode'),
					pf_id: pf_id,
					pf_paged: pf_paged,
					pf_pagefilters: prdctfltr.pagefilters
				}

				if ( $('.prdctfltr_wc_widget').length > 0 ) {

					var widget = $('.prdctfltr_wc_widget:first');

					var rpl = $('<div></div>').append(widget.find('.prdctfltr_filter:first').children(':not(input):first').clone()).html().toString().replace(/\t/g, '');
					var rpl_off = $('<div></div>').append(widget.find('.prdctfltr_filter:first').children(':not(input):first').find('.prdctfltr_widget_title').clone()).html().toString().replace(/\t/g, '');
					
					rpl = rpl.replace(rpl_off, '%%%');

					data.pf_widget_title = $.trim(rpl);

				}

				if ( obj.attr('data-lang') !== undefined ) {
					data.lang = obj.attr('data-lang');
				}

				if ( pf_offset>0 ) {
					data.pf_offset = pf_offset;
				}

				if ( $(prdctfltr.ajax_orderby_class).length>0 ) {
					data.pf_orderby_template = 'set';
				}

				if ( $(prdctfltr.ajax_count_class).length>0 ) {
					data.pf_count_template = 'set';
				}

				$.post(prdctfltr.ajax, data, function(response) {

					if (response) {

						var ajax_length = 0;
						var i;

						for (i in response) {
							if (response.hasOwnProperty(i)) {
								ajax_length++;
							}
						}

						var ajaxRefresh = {};

						$.each(response, function(n,obj2) {
							obj2 = $(obj2);

							if ( obj2.hasClass('prdctfltr_wc') ) {
								if ( pf_offset>0&&$(response['products']).find(prdctfltr.ajax_product_class).length>0 || pf_offset==0 ) {
									if ( $('.prdctfltr_wc[data-id="'+n+'"]').length > 0 ) {
										$('.prdctfltr_wc[data-id="'+n+'"]').replaceWith(obj2);
										ajaxRefresh[n] = n;
									}
								}
								else {
									$('.prdctfltr_wc[data-id="'+n+'"]').find('.prdctfltr_woocommerce_filter').replaceWith(obj2.find('.prdctfltr_woocommerce_filter'));
								}
							}
							else if ( obj2.hasClass('prdctfltr-widget') ) {
								if ( $('.prdctfltr_wc[data-id="'+n+'"]').length > 0 ) {
									$('.prdctfltr_wc[data-id="'+n+'"]').closest('.prdctfltr-widget').replaceWith(obj2);
									ajaxRefresh[n] = n;
								}
							}
							else if ( n == 'products' ) {
								if (archiveAjax===true) {
									var products = $(prdctfltr.ajax_class);
								}
								else if ( shortcodeAjax===true ) {
									var products = checkShortcode.find(prdctfltr.ajax_class);
								}
								else {
									
								}

								if ( obj2.length<1 ) {
									products.empty();
								}
								else {
									if (pf_offset<1) {
										if ( obj2.find(prdctfltr.ajax_product_class).length > 0 ) {
											if ( pf_restrict == 'pagination' ) {
												pf_get_scroll(products, 0);
											}
											pf_animate_products( products, obj2, 'replace' );
										}
										else {
											products.replaceWith(obj2);
										}
									}
									else {
										if ( obj2.find(prdctfltr.ajax_product_class).length > 0 ) {
											pf_animate_products( products, obj2, 'append' );
											$('.prdctfltr_faded').fadeTo(200,1).removeClass('prdctfltr_faded');
											if ( pf_restrict == 'pagination' ) {
												pf_get_scroll(products, pf_offset);
											}
										}
										else {
											$('.prdctfltr_faded').fadeTo(200,1).removeClass('prdctfltr_faded');
										}
									}
								}

							}
							else if ( n == 'pagination' ) {

								if (archiveAjax===true) {
									var pagination = ( prdctfltr.ajax_pagination_type=='default' ? $(prdctfltr.ajax_pagination_class) : $('.'+prdctfltr.ajax_pagination_type) );
								}
								else if ( shortcodeAjax===true ) {
									var pagination = checkShortcode.find(prdctfltr.ajax_pagination_class);
									if ( pagination.length < 1 ) {
										var pagination = checkShortcode.find('.prdctfltr-pagination-default');
									}
									if ( pagination.length < 1 ) {
										var pagination = checkShortcode.find('.prdctfltr-pagination-load-more');
									}
								}
								else {
									
								}

								if ( obj2.length<1 ) {
									pagination.empty();
								}
								else {
									pagination.replaceWith(obj2);
								}

							}
							else if ( n == 'ranges' ) {
								prdctfltr.rangefilters = obj2[0];
							}
							else if ( n == 'orderby' ) {
								$(prdctfltr.ajax_orderby_class).replaceWith(obj2);
								$('body.prdctfltr-ajax '+prdctfltr.ajax_orderby_class+' select').on( 'change', function() {

									var orderVal = this.value;

									var checkFilter = $('.prdctfltr_filter input[value="'+orderVal+'"]');

									if ( checkFilter.length>0 ) {
										checkFilter.trigger('click');
										prdctfltr_respond_550(checkFilter.closest('form'));
									}
									else {
										$('.prdctfltr_wc:first .prdctfltr_add_inputs').append('<input name="orderby" value="'+orderVal+'" />');
										prdctfltr_respond_550($('.prdctfltr_wc:first form'));
									}

								});
							}
							else if ( n == 'count' ) {
								if ( obj2.length<1 ) {
									$(prdctfltr.ajax_count_class).html(prdctfltr.localization.noproducts);
								}
								else {
									$(prdctfltr.ajax_count_class).replaceWith(obj2);
								}
							}


							if ( !--ajax_length ) {

								if ( !$.isEmptyObject( ajaxRefresh ) ) {
									$.each(ajaxRefresh, function(m,obj4) {
										after_ajax($('.prdctfltr_wc[data-id="'+m+'"]'));
									});
								}

								$(document.body).trigger( 'post-load' );
								if ( prdctfltr.js !== '' ) {
									eval(prdctfltr.js);
								}

								ajaxActive = false;
								pf_paged = 1;
								pf_offset = 0;
								pf_restrict = '';

							}

						});


					}

				});



			}
			else {

				obj.find('input[type="hidden"]').each(function () {
					obj.find('input[name="'+this.name+'"]:gt(0)').remove();
				});

				obj.find('.prdctfltr_woocommerce_ordering').submit();

			}

		}

	});


	}

	if ( $('.prdctfltr-widget').length == 0 || $('.prdctfltr-widget .prdctfltr_error').length == 1 ) {

		$(window).on('resize', function() {

			$('.prdctfltr_woocommerce').each( function() {

				var curr = $(this);
		
				if ( curr.hasClass('pf_mod_row') ) {

					if ( window.matchMedia('(max-width: 768px)').matches ) {
						curr.find('.prdctfltr_filter_inner').css('width', 'auto');
					}
					else {
						var curr_columns = curr.find('.prdctfltr_filter_wrapper:first').attr('data-columns');

						var curr_scroll_column = curr.find('.prdctfltr_woocommerce_ordering').width();
						var curr_columns_length = curr.find('.prdctfltr_filter').length;

						curr.find('.prdctfltr_filter_inner').css('width', curr_columns_length*curr_scroll_column/curr_columns);
						curr.find('.prdctfltr_filter').css('width', curr_scroll_column/curr_columns);
					}
				}
			});
		});
	}

	if ((/Trident\/7\./).test(navigator.userAgent)) {
		$(document).on('click', '.prdctfltr_checkboxes label img', function() {
			$(this).parents('label').children('input:first').change().click();
		});
	}

	if ((/Trident\/4\./).test(navigator.userAgent)) {
		$(document).on('click', '.prdctfltr_checkboxes label > span > img, .prdctfltr_checkboxes label > span', function() {
			$(this).parents('label').children('input:first').change().click();
		});
	}

	function prdctfltr_filter_terms(list) {

		var curr_filter = list.closest('.prdctfltr_wc');
		var form = $("<div>").attr({"class":"prdctfltr_search_terms","action":"#"}),
		input = $("<input>").attr({"class":"prdctfltr_search_terms_input prdctfltr_reset_this","type":"text","placeholder":prdctfltr.localization.filter_terms});
		

		if ( curr_filter.hasClass('pf_select') || curr_filter.hasClass('pf_default_select') || list.closest('.prdctfltr_filter').hasClass('prdctfltr_terms_customized_select') ) {
			$(form).append("<i class='prdctfltr-search'></i>").append(input).prependTo(list);
		}
		else{
			$(form).append("<i class='prdctfltr-search'></i>").append(input).insertBefore(list);
		}

		$(input)
		.change( function () {
			var filter = $(this).val();
			if(filter) {
				var curr = $(this).closest('.prdctfltr_filter');
				if ( curr.find('div.prdctfltr_sub').length > 0 ) {
					$(list).find(".prdctfltr_sub:not(:visible)").css({'margin-left':0}).show().prev().addClass('prdctfltr_clicked');
					if ( curr.hasClass('prdctfltr_searching') === false ) {
						curr.addClass('prdctfltr_searching');
					}
				}
				$(list).find("label > span:not(:Contains(" + filter + "))").closest('label').hide();
				$(list).find("label > span:Contains(" + filter + ")").closest('label').show();
			} else {
				var curr = $(this).closest('.prdctfltr_filter');
				if ( curr.find('div.prdctfltr_sub').length > 0 ) {
					$(list).find(".prdctfltr_sub:visible").css({'margin-left':'22px'}).hide().prev().removeClass('prdctfltr_clicked');
				}
				curr.removeClass('prdctfltr_searching');
				$(list).find("label > span").closest('label').show();

				var checkboxes = curr.find('.prdctfltr_checkboxes');

				checkboxes.each(function(){
					var max = parseInt(curr.attr('data-limit'));
					if (max != 0 && $(this).find("label").length > max+1) {
						$(this).find('label:gt('+max+')').attr('style', 'display:none !important');
						$(this).find(".pf_more").html('<span>'+prdctfltr.localization.show_more+'</span>').removeClass('pf_activated');
					}
				});
				
			}

			if ( curr_filter.hasClass('pf_mod_masonry') ) {
				curr_filter.find('.prdctfltr_filter_inner').isotope('layout');
			}

			return false;
		})
		.keyup( function () {
			$(this).change();
		});

	}

	$(document).on('click', '.prdctfltr_sc_products.prdctfltr_ajax '+prdctfltr.ajax_class+' '+prdctfltr.ajax_category_class+' a, .prdctfltr-shop.prdctfltr-ajax '+prdctfltr.ajax_class+' '+prdctfltr.ajax_category_class+' a', function() {

		var curr = $(this).closest(prdctfltr.ajax_category_class);

		var curr_sc = ( curr.closest('.prdctfltr_sc_products').length > 0 ? curr.closest('.prdctfltr_sc_products') : $('.prdctfltr_sc_products:first').length > 0 ? $('.prdctfltr_sc_products:first') : $('.prdctfltr_woocommerce:first').length > 0 ? $('.prdctfltr_woocommerce:first') : 'none' );

		if ( curr_sc == 'none' ) {
			return;
		}

		if ( curr_sc.hasClass('prdctfltr_sc_products') ) {
			var curr_filter = ( curr_sc.find('.prdctfltr_woocommerce').length > 0 ? curr_sc.find('.prdctfltr_woocommerce') : $('.prdctfltr-widget').find('.prdctfltr_woocommerce') );
		}
		else if ( $('.prdctfltr_sc_products').length == 0 ) {
			var curr_filter = curr_sc;
		}
		else {
			return;
		}

		var cat = curr.find('.prdctfltr_cat_support').data('slug');

		var hasFilter = curr_filter.find('.prdctfltr_filter[data-filter="product_cat"] input[value="'+cat+'"]:first');

		if ( hasFilter.length > 0 ) {
			hasFilter.trigger('click');
			if ( !curr_filter.hasClass('prdctfltr_click_filter') ) {
				curr_filter.find('.prdctfltr_woocommerce_filter_submit').trigger('click');
			}
		}
		else {
			var hasField = curr_filter.find('.prdctfltr_filter[data-filter="product_cat"]');

			if ( hasField.length > 0 ) {
				hasField.find('input[name="product_cat"]').val(cat);
			}
			else {
				var append = $('<input name="product_cat" type="hidden" value="'+cat+'" />');
				curr_filter.find('.prdctfltr_add_inputs').append(append);
			}

			if ( !curr_filter.hasClass('prdctfltr_click_filter') ) {
				curr_filter.find('.prdctfltr_woocommerce_filter_submit').trigger('click');
			}
			else {
				prdctfltr_respond_550(curr_filter.find('form'));
			}
		}

		return false;

	});

	$(window).load( function() {
		$('.pf_mod_masonry .prdctfltr_filter_inner').each( function() {
			$(this).isotope('layout');
		});
	});

	if ( $('body').hasClass('prdctfltr-ajax') ) {
		if ( $('body.prdctfltr-ajax '+prdctfltr.ajax_orderby_class).length>0 ) {

			$(document).on('submit', $('body.prdctfltr-ajax '+prdctfltr.ajax_orderby_class), function() {
				return false;
			});

			$('body.prdctfltr-ajax '+prdctfltr.ajax_orderby_class+' select').on( 'change', function() {

				var orderVal = this.value;

				var checkFilter = $('.prdctfltr_filter input[value="'+orderVal+'"]');

				if ( checkFilter.length>0 ) {
					checkFilter.trigger('click');
					prdctfltr_respond_550(checkFilter.closest('form'));
				}
				else {
					$('.prdctfltr_wc:first .prdctfltr_add_inputs').append('<input name="orderby" value="'+orderVal+'" />');
					prdctfltr_respond_550($('.prdctfltr_wc:first form'));
				}

			});

		}

	}

	function pf_get_scroll( products, offset ) {

		if ( prdctfltr.ajax_scroll == 'products' ) {
			if ( offset>0 ) {
				var objOffset = products.find(prdctfltr.ajax_product_class+':gt('+offset+')').offset().top;
			}
			else {
				var objOffset = products.offset().top;
			}
			$('html, body').animate({
				scrollTop: objOffset-100
			}, 500);
		}
		else if ( prdctfltr.ajax_scroll == 'top' ) {
			$('html, body').animate({
				scrollTop: 0
			}, 500);
		}

	}

	function pf_animate_products( products, obj2, type ) {
		if ( type=='append' ) {
			if ( prdctfltr.ajax_animation == 'none' ) {
				products.append(obj2.contents().unwrap());
			}
			else if ( prdctfltr.ajax_animation == 'slide' ) {

				var beforeLength = products.find(prdctfltr.ajax_product_class).length;

				products.append(obj2.contents().unwrap());
				var curr_products = products.find(prdctfltr.ajax_product_class+':gt('+beforeLength+')');

				curr_products.hide();
				if ( typeof curr_products !== 'undefined' ) {
					curr_products.each(function(i) {
						$(this).delay((i++) * 100).slideDown({duration: 200,easing: 'linear'});
					});
				}

			}
			else if ( prdctfltr.ajax_animation == 'random' ) {

				var beforeLength = products.find(prdctfltr.ajax_product_class).length;

				products.append(obj2.contents().unwrap());
				var curr_products = products.find(prdctfltr.ajax_product_class+':gt('+beforeLength+')');

				curr_products.css('visibility', 'hidden');
				if ( typeof curr_products !== 'undefined' ) {
					curr_products.css('visibility', 'hidden');

					var interval = setInterval(function () {
					var $ds = curr_products.not('.pf_faded');
					$ds.eq(Math.floor(Math.random() * $ds.length)).css('visibility','visible').hide().fadeIn(100).addClass('pf_faded');
						if ($ds.length == 1) {
							clearInterval(interval);
						}
					}, 50);
				}

			}
			else {

				var beforeLength = products.find(prdctfltr.ajax_product_class).length;

				products.append(obj2.contents().unwrap());
				var curr_products = products.find(prdctfltr.ajax_product_class+':gt('+beforeLength+')');

				curr_products.hide();
				if ( typeof curr_products !== 'undefined' ) {
					curr_products.each(function(i) {
						$(this).delay((i++) * 100).fadeTo(100, 1);
					});
				}
			}
			
		}
		else {

			if ( prdctfltr.ajax_animation == 'none' ) {
				products.replaceWith(obj2);
			}
			else if ( prdctfltr.ajax_animation == 'slide' ) {
				products.replaceWith(obj2);
				var curr_products = obj2.find(prdctfltr.ajax_product_class);

				curr_products.hide();
				if ( typeof curr_products !== 'undefined' ) {
					curr_products.each(function(i) {
						$(this).delay((i++) * 100).slideDown({duration: 200,easing: 'linear'});
					});
				}
			}
			else if ( prdctfltr.ajax_animation == 'random' ) {
				products.replaceWith(obj2);
				var curr_products = obj2.find(prdctfltr.ajax_product_class);

				curr_products.css('visibility', 'hidden');
				if ( typeof curr_products !== 'undefined' ) {
					curr_products.css('visibility', 'hidden');

					var interval = setInterval(function () {
					var $ds = curr_products.not('.pf_faded');
					$ds.eq(Math.floor(Math.random() * $ds.length)).css('visibility','visible').hide().fadeIn(100).addClass('pf_faded');
						if ($ds.length == 1) {
							clearInterval(interval);
						}
					}, 50);
				}
			}
			else {
				products.replaceWith(obj2);
				var curr_products = obj2.find(prdctfltr.ajax_product_class);

				curr_products.hide();
				if ( typeof curr_products !== 'undefined' ) {
					curr_products.each(function(i) {
						$(this).delay((i++) * 100).fadeTo(100, 1);
					});
				}
			}
		}
	}

	if ( $(prdctfltr.ajax_orderby_class).length<1 ) {
		$('.prdctfltr_add_inputs input[name="orderby"]').remove();
	}


})(jQuery);