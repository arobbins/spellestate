/**
 * WooChimp Plugin JavaScript
 */

/**
 * Based on jQuery
 */
jQuery(document).ready(function() {

    /**
     * Show or hide webhook url
     */
    jQuery('#woochimp_webhook_url').prop('disabled', true);

    jQuery('#woochimp_enable_webhooks').each(function() {
        if (!jQuery(this).is(':checked')) {
            jQuery('#woochimp_webhook_url').parent().parent().hide();
        }
    });

    jQuery('#woochimp_enable_webhooks').change(function() {
        if (jQuery(this).is(':checked')) {
            jQuery('#woochimp_webhook_url').parent().parent().show();
        }
        else {
            jQuery('#woochimp_webhook_url').parent().parent().hide();
        }
    });

    /**
     * Admin hints
     */
    jQuery('form').each(function(){
        jQuery(this).find(':input').each(function(){
            if (typeof woochimp_hints !== 'undefined' && typeof woochimp_hints[this.id] !== 'undefined') {
                jQuery(this).parent().parent().find('th').append('<div class="woochimp_tip" title="'+woochimp_hints[this.id]+'"><i class="fa fa-question"></div>');
            }
        });
    });
    jQuery.widget('ui.tooltip', jQuery.ui.tooltip, {
        options: {
            content: function() {
                return jQuery(this).prop('title');
            }
        }
    });
    jQuery('.woochimp_tip').tooltip();

    /**
     * Make condition multiselects "chosen"
     */
    jQuery('.set_condition_value_products').each(function() {
        jQuery(this).chosen({
            no_results_text: woochimp_label_no_results_match_products,
            placeholder_text_multiple: woochimp_label_select_some_products,
            width: '400px'
        });
    });
    jQuery('.set_condition_value_categories').each(function() {
        jQuery(this).chosen({
            no_results_text: woochimp_label_no_results_match_categories,
            placeholder_text_multiple: woochimp_label_select_some_categories,
            width: '400px'
        });
    });

    /**
     * Hide unused condition fields
     */
    function woochimp_hide_unused_condition_fields() {
        jQuery('.set_condition_key').each(function() {
            jQuery(this).children().each(function() {
                if (!jQuery(this).is(':selected')) {
                    var real_this = jQuery(this);
                    jQuery.each(['custom_key', 'operator', 'value'], function(index, value) {
                        real_this.parent().parent().parent().parent().find('.set_condition_' + value + '_' + real_this.val()).each(function() {
                            jQuery(this).parent().parent().hide();
                        });
                    });
                }
            });
        });
        jQuery('.set_condition_key').change(function() {
            var new_form_condition_key = jQuery(this).children().filter(':selected').val();

            var real_this = jQuery(this);

            jQuery.each(['custom_key', 'operator', 'value'], function(index, value) {
                real_this.parent().parent().parent().find('.set_condition_'+value).each(function() {
                    jQuery(this).val('');

                    if (!jQuery(this).hasClass('set_condition_' + value + '_' + new_form_condition_key)) {
                        jQuery(this).parent().parent().hide();
                    }
                    else {
                        jQuery(this).parent().parent().show();
                        if (value === 'operator') {
                            jQuery(this).val(jQuery(this).find('option').first().val());
                        }
                    }
                });
            });
        });
    }

    woochimp_hide_unused_condition_fields();

    /**
     * Apply accordion to list/group selection on checkout settings page
     */
    jQuery('#woochimp_list_groups_list').accordion({
        header: '> div > h4',
        heightStyle: 'content'
    });

    /**
     * Load service status
     */
    jQuery('#woochimp-status').each(function() {
        jQuery.post(
            ajaxurl,
            {
                'action': 'woochimp_mailchimp_status'
            },
            function(response) {

                try {
                    var result = jQuery.parseJSON(response);
                }
                catch (err) {
                    jQuery('#woochimp-status').html(woochimp_label_bad_ajax_response);
                }

                if (result) {
                    jQuery('#woochimp-status').html(result['message']);
                }
            }
        );
    });

    /**
     * Load mailing lists, groups and merge fields (widget and shortcode pages)
     */
    if (jQuery('#woochimp_list_widget').length || jQuery('#woochimp_list_shortcode').length) {

        var object_type = jQuery('#woochimp_list_widget').length ? 'widget' : 'shortcode';

        // Disable submit button until lists are loaded
        jQuery('#submit').prop('disabled', true);
        jQuery('#submit').prop('title', woochimp_label_still_connecting_to_mailchimp);

        jQuery.post(
            ajaxurl,
            {
                'action': 'woochimp_get_lists',
                'data': {'page': object_type}
            },
            function(response) {

                try {
                    var result = jQuery.parseJSON(response);
                }
                catch (err) {
                    jQuery('#woochimp_list_'+object_type).replaceWith(woochimp_label_bad_ajax_response);
                    jQuery('#woochimp_groups_'+object_type).replaceWith(woochimp_label_bad_ajax_response);
                    jQuery('#woochimp_'+object_type+'_fields').replaceWith(woochimp_label_bad_ajax_response);
                }

                if (result && typeof result['message'] === 'object') {

                    /**
                     * Update lists
                     */
                    if (typeof result['message']['lists'] === 'object') {
                        var fields = {'checkout': '', 'widget': '', 'shortcode': ''};

                        for (var field in fields) {
                            for (var prop in result['message']['lists']) {
                                fields[field] += '<option value="'+prop+'" '+((typeof woochimp_selected_list === 'object' && typeof woochimp_selected_list[field] === 'string' && woochimp_selected_list[field] == prop) ? 'selected="selected"' : '')+'>'+result['message']['lists'][prop]+'</option>';
                            }
                        }

                        // Update DOM
                        jQuery('#woochimp_list_'+object_type).replaceWith('<select id="woochimp_list_'+object_type+'" name="woochimp_options[woochimp_list_'+object_type+']" class="woochimp-field">'+fields[object_type]+'</select>');

                        // Make it chosen!
                        jQuery('#woochimp_list_'+object_type).chosen({
                            no_results_text: woochimp_label_no_results_match_list,
                            placeholder_text_single: woochimp_label_select_mailing_list,
                            width: '400px'
                        }).change( function(evt, params) {
                            woochimp_update_groups_and_tags(object_type, params['selected']);
                        });
                    }

                    /**
                     * Update groups
                     */
                    if (typeof result['message']['groups'] === 'object') {
                        var fields = {'checkout': '', 'widget': '', 'shortcode': ''};

                        for (var field in fields) {
                            for (var prop in result['message']['groups']) {
                                fields[field] += '<option value="'+prop+'" '+((typeof woochimp_selected_groups === 'object' && typeof woochimp_selected_groups[object_type] === 'object' && woochimp_selected_groups[object_type].indexOf(prop) !== -1) ? 'selected="selected"' : '')+'>'+result['message']['groups'][prop]+'</option>';
                            }
                        }

                        // Update DOM
                        jQuery('#woochimp_groups_'+object_type).replaceWith('<select multiple id="woochimp_groups_'+object_type+'" name="woochimp_options[woochimp_groups_'+object_type+'][]" class="woochimp-field">'+fields[object_type]+'</select>');

                        // Make it chosen!
                        jQuery('#woochimp_groups_'+object_type).chosen({
                            no_results_text: woochimp_label_no_results_match_groups,
                            placeholder_text_multiple: woochimp_label_select_some_groups,
                            width: '400px'
                        });
                    }

                    /**
                     * Update merge fields
                     */
                    if (typeof result['message']['merge'] === 'object') {

                        var selected_list_id = '';

                        // Find selected list id
                        jQuery('#woochimp_list_'+object_type).find('option:selected').each( function() {
                            selected_list_id = jQuery(this).val();
                        });

                        var checkout_properties = null;

                        // Check if checkout properties are set
                        if (object_type === 'checkout' && typeof result['message']['checkout_properties'] === 'object') {
                            checkout_properties = result['message']['checkout_properties'];
                        }

                        // Render fields table
                        if (typeof result['message']['merge'] === 'object' && typeof result['message']['selected_merge'] === 'object') {
                            render_merge_fields_table(result['message']['merge'], result['message']['selected_merge'], object_type, selected_list_id, checkout_properties);
                        }
                    }
                }

                /**
                 * Enable submit button
                 */
                jQuery('#submit').prop('disabled', false);
                jQuery('#submit').prop('title', '');
            }
        );

    }

    /**
     * Handle list change
     */
    function woochimp_update_groups_and_tags(page, list_id) {

        // Replace groups field with loading animation
        var preloader = '<p id="woochimp_groups_'+page+'" class="woochimp_loading"><span class="woochimp_loading_icon"></span>'+woochimp_label_connecting_to_mailchimp+'</p>';
        jQuery('#woochimp_groups_'+page).parent().html(preloader);

        // Replace fields section with loading animation
        var preloader = '<div class="woochimp-status" id="woochimp_'+page+'_fields"><p class="woochimp_loading"><span class="woochimp_loading_icon"></span>'+woochimp_label_connecting_to_mailchimp+'</p></div>';
        jQuery('#woochimp_fields_table').html(preloader);

        // Disable submit button until groups are updated
        jQuery('#submit').prop('disabled', true);
        jQuery('#submit').prop('title', woochimp_label_still_connecting_to_mailchimp);

        jQuery.post(
            ajaxurl,
            {
                'action': 'woochimp_update_groups_and_tags',
                'data': {'page': page, 'list': list_id}
            },
            function(response) {

                try {
                    var result = jQuery.parseJSON(response);
                }
                catch (err) {
                    jQuery('#woochimp_groups_'+page).replaceWith(woochimp_label_bad_ajax_response);
                    jQuery('#woochimp_'+page+'_fields').replaceWith(woochimp_label_bad_ajax_response);
                }

                if (result && typeof result['message'] === 'object') {

                    // Render groups field
                    if (typeof result['message']['groups'] === 'object') {
                        var fields = {'checkout': '', 'widget': '', 'shortcode': ''};

                        for (var field in fields) {
                            for (var prop in result['message']['groups']) {
                                fields[field] += '<option value="'+prop+'" '+((typeof woochimp_selected_groups === 'object' && typeof woochimp_selected_groups[object_type] === 'object' && woochimp_selected_groups[object_type].indexOf(prop) !== -1) ? 'selected="selected"' : '')+'>'+result['message']['groups'][prop]+'</option>';
                            }
                        }

                        // Update DOM
                        jQuery('#woochimp_groups_'+page).replaceWith('<select multiple id="woochimp_groups_'+page+'" name="woochimp_options[woochimp_groups_'+page+'][]" class="woochimp-field">'+fields[page]+'</select>');

                        // Make it chosen!
                        jQuery('#woochimp_groups_'+page).chosen({
                            no_results_text: woochimp_label_no_results_match_groups,
                            placeholder_text_multiple: woochimp_label_select_some_groups,
                            width: '400px',
                        });
                    }

                    var checkout_properties = null;

                    // Check if checkout properties are set
                    if (page === 'checkout' && typeof result['message']['checkout_properties'] === 'object') {
                        checkout_properties = result['message']['checkout_properties'];
                    }

                    // Render merge fields
                    if (typeof result['message']['merge'] === 'object' && typeof result['message']['selected_merge'] === 'object') {
                        render_merge_fields_table(result['message']['merge'], result['message']['selected_merge'], page, list_id, checkout_properties);
                    }
                }

                /**
                 * Enable submit button
                 */
                jQuery('#submit').prop('disabled', false);
                jQuery('#submit').prop('title', '');
            }
        );
    }

    /**
     * Render merge fields table
     */
    function render_merge_fields_table(available_fields, selected_fields, page, list_id, checkout_properties) {

        if (list_id !== '') {
            available_fields = available_fields[list_id];
        }
        else {
            available_fields = [];
        }

        // Generate options
        var field_options = '<option value></option>';

        if (typeof available_fields === 'object') {
            for (var prop in available_fields) {
                if (available_fields.hasOwnProperty(prop)) {
                    field_options += '<option value="'+prop+'">'+available_fields[prop]+' ('+prop+')</option>';
                }
            }
        }

        // Set up name field depending on page type
        if (page === 'checkout') {
            var checkout_field_options = '<option value></option>';

            if (typeof checkout_properties === 'object') {
                for (var prop in checkout_properties) {
                    if (checkout_properties.hasOwnProperty(prop)) {
                        checkout_field_options += '<option value="'+prop+'">'+checkout_properties[prop]+'</option>';
                    }
                }
            }
            var input_field = '<select class="woochimp_name_select" name="woochimp_options[field_names]['+page+'][%%%id%%%][name]" id="woochimp_field_name_%%%id%%%">'+checkout_field_options+'</select>';
        }
        else {
            var input_field = '<input type="text" class="woochimp_name_input" name="woochimp_options[field_names]['+page+'][%%%id%%%][name]" id="woochimp_field_name_%%%id%%%" value="%%%value%%%" />';
        }

        // Begin table
        var fields_table = '<table id="woochimp_fields_table"><thead><tr><th>'+woochimp_label_fields_field+'</th><th>'+woochimp_label_fields_tag+'</th><th></th></tr></thead><tbody>';

        // Table content with preselected options
        if (typeof selected_fields === 'object' && Object.keys(selected_fields).length > 0) {
            for (var prop in selected_fields) {
                if (selected_fields.hasOwnProperty(prop)) {
                var this_field = input_field.replace('%%%id%%%', prop);
                this_field = this_field.replace('%%%id%%%', prop);
                this_field = this_field.replace('%%%value%%%', selected_fields[prop]['name']);
                fields_table += '<tr class="woochimp_field_row" id="woochimp_field_'+prop+'"><td>'+this_field+'</td><td><select class="woochimp_tag_select" name="woochimp_options[field_names]['+page+']['+prop+'][tag]" id="woochimp_field_tag_'+prop+'">'+field_options+'</select></td><td><button type="button" class="woochimp_remove_field"><i class="fa fa-times"></i></button></td></tr>';
                }
            }
        }

        // Table content with no preselected options
        else {
            var this_field = input_field.replace('%%%id%%%', '1');
            this_field = this_field.replace('%%%id%%%', '1');
            this_field = this_field.replace('%%%value%%%', '');
            fields_table += '<tr class="woochimp_field_row" id="woochimp_field_1"><td>'+this_field+'</td><td><select class="woochimp_tag_select" name="woochimp_options[field_names]['+page+'][1][tag]" id="woochimp_field_tag_1">'+field_options+'</select></td><td><button type="button" class="woochimp_remove_field"><i class="fa fa-times"></i></button></td></tr>';
        }

        // End table
        fields_table += '</tbody><tfoot><tr><td><button type="button" name="woochimp_add_field" id="woochimp_add_field" class="button button-primary" value="'+woochimp_label_add_new+'"><i class="fa fa-plus">&nbsp;&nbsp;'+woochimp_label_add_new+'</i></button></td><td></td><td></td></tr></tfoot></table>';

        jQuery('#woochimp_'+page+'_fields').replaceWith(fields_table);

        // Select preselected options
        if (typeof selected_fields === 'object' && Object.keys(selected_fields).length > 0) {
            for (var prop in selected_fields) {
                if (selected_fields.hasOwnProperty(prop)) {
                    jQuery('#woochimp_field_tag_'+prop).find('option[value="'+selected_fields[prop]['tag']+'"]').prop('selected', true);
                }
            }
        }

        // Select preselected field names for checkout
        if (page === 'checkout') {
            if (typeof selected_fields === 'object' && Object.keys(selected_fields).length > 0) {
                for (var prop in selected_fields) {
                    if (selected_fields.hasOwnProperty(prop)) {
                        jQuery('#woochimp_field_name_'+prop).find('option[value="'+selected_fields[prop]['name']+'"]').prop('selected', true);
                    }
                }
            }
        }

        // Make all select fields chosen
        jQuery('.woochimp_tag_select').each( function() {
            jQuery(this).chosen({
                no_results_text: woochimp_label_no_results_match_tags,
                placeholder_text_single: woochimp_label_select_tag,
                width: '300px'
            }).change( function(evt, params) {
                regenerate_tag_chosen();
            });
        });
        jQuery('.woochimp_name_select').each( function() {
            jQuery(this).chosen({
                no_results_text: woochimp_label_no_results_match_checkout_fields,
                placeholder_text_single: woochimp_label_select_checkout_field,
                width: '300px'
            });
        });

        // Regenerate fields (so we make selected fields disabled on other fields)
        regenerate_tag_chosen();

        /**
         * Handle new fields
         */
        jQuery('#woochimp_add_field').click( function() {

            // Get last field id
            var current_id = (jQuery('#woochimp_fields_table tbody>tr:last').attr('id').replace('woochimp_field_', ''));

            // Remove chosen from last element
            jQuery('#woochimp_field_tag_'+current_id).chosen('destroy');

            // Remove chosen from checkout field name
            if (page === 'checkout') {
                jQuery('#woochimp_field_name_'+current_id).chosen('destroy');
            }

            // Clone it and insert
            jQuery('#woochimp_fields_table tbody>tr:last').clone(true).insertAfter('#woochimp_fields_table tbody>tr:last');

            jQuery('#woochimp_fields_table tbody>tr:last').each( function() {

                // Change ids
                var next_id = parseInt(current_id, 10) + 1;
                jQuery(this).attr('id', 'woochimp_field_'+next_id);
                jQuery(this).find(':input').each( function() {
                    if (jQuery(this).is('input')) {
                        jQuery(this).attr('id', 'woochimp_field_name_'+next_id);
                        jQuery(this).attr('name', 'woochimp_options[field_names]['+page+']['+next_id+'][name]');
                        jQuery(this).val('');
                    }
                    else if (jQuery(this).is('select')) {
                        if (jQuery(this).hasClass('woochimp_name_select')) {
                            jQuery(this).attr('id', 'woochimp_field_name_'+next_id);
                            jQuery(this).attr('name', 'woochimp_options[field_names]['+page+']['+next_id+'][name]');
                            jQuery(this).val('');
                        }
                        else if (jQuery(this).hasClass('woochimp_tag_select')) {
                            jQuery(this).attr('id', 'woochimp_field_tag_'+next_id);
                            jQuery(this).attr('name', 'woochimp_options[field_names]['+page+']['+next_id+'][tag]');
                            jQuery(this).val('');
                        }
                    }
                });

                // Make both tag fields chosen
                jQuery('#woochimp_field_tag_'+current_id).chosen({
                    no_results_text: woochimp_label_no_results_match_tags,
                    placeholder_text_single: woochimp_label_select_tag,
                    width: '300px'
                });
                jQuery('#woochimp_field_tag_'+next_id).chosen({
                    no_results_text: woochimp_label_no_results_match_tags,
                    placeholder_text_single: woochimp_label_select_tag,
                    width: '300px'
                });

                // Make both checkout field name fields chosen
                if (page === 'checkout') {
                    jQuery('#woochimp_field_name_'+current_id).chosen({
                        no_results_text: woochimp_label_no_results_match_checkout_fields,
                        placeholder_text_single: woochimp_label_select_checkout_field,
                        width: '300px'
                    });
                    jQuery('#woochimp_field_name_'+next_id).chosen({
                        no_results_text: woochimp_label_no_results_match_checkout_fields,
                        placeholder_text_single: woochimp_label_select_checkout_field,
                        width: '300px'
                    });
                }

            });

            regenerate_tag_chosen();

            return false;
        });

        /**
         * Handle field removal
         */
        jQuery('.woochimp_remove_field').each( function() {
            jQuery(this).click( function() {
                // Do not remove the last set - reset field values instead
                if (jQuery(this).parent().parent().parent().children().length === 1) {
                    jQuery(this).parent().parent().find(':input').each( function() {
                        jQuery(this).val('');
                    });
                }
                else {
                    jQuery(this).parent().parent().remove();
                }

                
                jQuery('.woochimp_name_select').each( function() {
                    jQuery(this).trigger('chosen:updated');
                });

                regenerate_tag_chosen();
            });
        });

    }

    /**
     * Regenerate all chosen fields
     */
    function regenerate_tag_chosen() {
        var all_selected = {};

        // Get all selected fields
        jQuery('.woochimp_tag_select').each( function() {
            if (jQuery(this).find(':selected').length > 0 && jQuery(this).find(':selected').val() !== '') {
                all_selected[jQuery(this).prop('id')] = jQuery(this).find(':selected').val();
            }
        });

        // Regenerate chosen fields
        jQuery('.woochimp_tag_select').each( function() {

            if (Object.keys(all_selected).length !== 0) {

                for (var prop in all_selected) {

                    if (prop !== jQuery(this).prop('id')) {

                        // Disable
                        jQuery(this).find('option[value="'+all_selected[prop]+'"]').prop('disabled', true);
                    }


                        // Enable previously disabled values if they are available now
                        jQuery(this).find(':disabled').each( function() {

                            // Check if such disabled property exists within selected properties
                            var option_value = jQuery(this).val();
                            var exists = false;

                            for (var proper in all_selected) {
                                if (all_selected[proper] === option_value) {
                                    exists = true;
                                    break;
                                }
                            }

                            // Remove if it does not exist
                            if (!exists) {
                                jQuery(this).removeAttr('disabled');
                            }

                        });

                }
            }
            else {
                // Enable all properties on all fields if there's only one left
                jQuery(this).find(':disabled').each( function() {
                    jQuery(this).removeAttr('disabled');
                });
            }

            jQuery(this).trigger('chosen:updated');
        });
    }

    /**
     * Integration - additional fields
     */
    if (typeof woochimp_enabled !== 'undefined' && woochimp_enabled == '0') {
        jQuery('#woochimp_api_key').parent().parent().hide();
    }

    // Handle show/hide api key field
    jQuery('#woochimp_enabled').change(function() {
        if (jQuery(this).prop('checked')) {
            jQuery('#woochimp_api_key').parent().parent().fadeIn();
        }
        else {
            jQuery('#woochimp_api_key').parent().parent().fadeOut();
        }
    });

    /**
     * Subscription on checkout - additional fields
     */
    if (typeof woochimp_webhook_enabled === 'undefined' || !woochimp_webhook_enabled) {
        jQuery(this).find('#woochimp_do_not_resubscribe').parent().parent().remove();
    }

    if (typeof woochimp_enabled_checkout !== 'undefined' && woochimp_enabled_checkout === '1') {
        jQuery('#woochimp_enabled_checkout').parent().parent().parent().children().each(function() {
            if (jQuery(this).find('#woochimp_enabled_checkout').length === 0) {
                jQuery(this).hide();
            }
        });
    }
    else if (typeof woochimp_enabled_checkout !== 'undefined' && woochimp_enabled_checkout === '2') {
        jQuery('#woochimp_text_checkout').parent().parent().hide();
        jQuery('#woochimp_checkbox_position').parent().parent().hide();
        jQuery('#woochimp_default_state').parent().parent().hide();
    }
    else if (typeof woochimp_enabled_checkout !== 'undefined' && woochimp_enabled_checkout === '3') {
        jQuery('#woochimp_do_not_resubscribe').parent().parent().hide();
    }

    jQuery('#woochimp_enabled_checkout').change(function() {
        if (jQuery(this).val() === '2') {
            jQuery('#woochimp_text_checkout').parent().parent().fadeOut();
            jQuery('#woochimp_checkbox_position').parent().parent().fadeOut();
            jQuery('#woochimp_default_state').parent().parent().fadeOut();
            jQuery(this).parent().parent().parent().children().each(function() {
                if (jQuery(this).find('#woochimp_enabled_checkout').length === 0 && jQuery(this).find('#woochimp_text_checkout').length === 0 && jQuery(this).find('#woochimp_checkbox_position').length === 0 && jQuery(this).find('#woochimp_default_state').length === 0) {
                    jQuery(this).fadeIn();
                }
            });
        }
        else if (jQuery(this).val() === '3') {
            jQuery(this).parent().parent().parent().children().each(function() {
                jQuery(this).find('#woochimp_do_not_resubscribe').parent().parent().fadeOut();
                if (jQuery(this).find('#woochimp_enabled_checkout').length === 0 && jQuery(this).find('#woochimp_do_not_resubscribe').length === 0) {
                    jQuery(this).fadeIn();
                }
            });
        }
        else {
            jQuery(this).parent().parent().parent().children().each(function() {
                if (jQuery(this).find('#woochimp_enabled_checkout').length === 0) {
                    jQuery(this).fadeOut();
                }
            });
        }
    });
    
    /**
     * Subscription widget - additional fields
     */
    if (typeof woochimp_enabled_widget !== 'undefined' && woochimp_enabled_widget === '0') {
        jQuery('#woochimp_enabled_widget').parent().parent().parent().children().each(function() {
            if (jQuery(this).find('#woochimp_enabled_widget').length === 0) {
                jQuery(this).hide();
            }
        });
    }

    jQuery('#woochimp_enabled_widget').change(function() {
        if (jQuery(this).attr('checked')) {
            jQuery(this).parent().parent().parent().children().each(function() {
                if (jQuery(this).find('#woochimp_enabled_widget').length === 0) {
                    jQuery(this).fadeIn();
                }
            });
        }
        else {
            jQuery(this).parent().parent().parent().children().each(function() {
                if (jQuery(this).find('#woochimp_enabled_widget').length === 0) {
                    jQuery(this).fadeOut();
                }
            });
        }
    });
    
    /**
     * Subscription form shortcode - additional fields
     */
    if (typeof woochimp_enabled_shortcode !== 'undefined' && woochimp_enabled_shortcode === '0') {
        jQuery('#woochimp_enabled_shortcode').parent().parent().parent().children().each(function() {
            if (jQuery(this).find('#woochimp_enabled_shortcode').length === 0) {
                jQuery(this).hide();
            }
        });
    }

    jQuery('#woochimp_enabled_shortcode').change(function() {
        if (jQuery(this).attr('checked')) {
            jQuery(this).parent().parent().parent().children().each(function() {
                if (jQuery(this).find('#woochimp_enabled_shortcode').length === 0) {
                    jQuery(this).fadeIn();
                }
            });
        }
        else {
            jQuery(this).parent().parent().parent().children().each(function() {
                if (jQuery(this).find('#woochimp_enabled_shortcode').length === 0) {
                    jQuery(this).fadeOut();
                }
            });
        }
    });

    /**
     * Checkout page - lists and groups
     */
    if (jQuery('.woochimp_list_checkout').length) {

        // Disable submit button until lists are loaded
        jQuery('#submit').prop('disabled', true);
        jQuery('#submit').prop('title', woochimp_label_still_connecting_to_mailchimp);

        jQuery.post(
            ajaxurl,
            {
                'action': 'woochimp_get_lists_with_multiple_groups_and_fields',
                'data': woochimp_checkout_sets
            },
            function(response) {

                try {
                    var result = jQuery.parseJSON(response);
                }
                catch (err) {
                    jQuery('.woochimp_list_checkout').each(function () {
                        jQuery(this).replaceWith(woochimp_label_bad_ajax_response);
                    });
                    jQuery('.woochimp_fields_checkout').each(function () {
                        jQuery(this).replaceWith(woochimp_label_bad_ajax_response);
                    });
                }

                if (result && typeof result['message'] === 'object') {

                    /**
                     * Render lists and groups selection
                     */
                    var current_field_id = 0;

                    jQuery('.woochimp_list_checkout').each(function () {

                        current_field_id++;

                        var current_selected_list = (typeof woochimp_checkout_sets !== 'undefined' && typeof woochimp_checkout_sets[current_field_id] !== 'undefined' && typeof woochimp_checkout_sets[current_field_id]['list'] !== 'undefined' ? woochimp_checkout_sets[current_field_id]['list'] : null);

                        // List selection
                        if (typeof result['message']['lists'] === 'object') {
                            var fields = '';

                            for (var prop in result['message']['lists']) {
                                fields += '<option value="'+prop+'" '+ (current_selected_list !== null && current_selected_list === prop ? 'selected="selected"' : '') +'>'+result['message']['lists'][prop]+'</option>';
                            }

                            var field_field = '<select id="woochimp_list_checkout_'+ current_field_id +'" name="woochimp_options[sets]['+ current_field_id +'][list]" class="woochimp-field">'+fields+'</select>';
                            var field_html = '<table class="form-table"><tbody><tr valign="top"><th scope="row">'+ woochimp_label_mailing_list +'</th><td>'+ field_field +'</td></tr></tbody></table>';

                            jQuery(this).replaceWith(field_html);

                            // Make it chosen!
                            jQuery('#woochimp_list_checkout_'+ current_field_id).chosen({
                                no_results_text: woochimp_label_no_results_match_list,
                                placeholder_text_single: woochimp_label_select_mailing_list,
                                width: '400px'
                            }).change(function(evt, params) {
                                var current_field_id = jQuery(this).prop('id').replace('woochimp_list_checkout_', '');
                                woochimp_update_checkout_groups_and_tags(current_field_id, params['selected']);
                            });

                            // Groups selection
                            if (typeof result['message']['groups'] === 'object') {
                                var fields = '';

                                var current_selected_groups = (typeof woochimp_checkout_sets !== 'undefined' && typeof woochimp_checkout_sets[current_field_id] !== 'undefined' && typeof woochimp_checkout_sets[current_field_id]['groups'] === 'object' ? woochimp_checkout_sets[current_field_id]['groups'] : null);

                                // Check if list is selected
                                if (current_selected_list !== null && typeof result['message']['groups'][current_selected_list] === 'object') {
                                    for (var prop in result['message']['groups'][current_selected_list]) {
                                        fields += '<option value="'+prop+'" '+ (current_selected_groups !== null && current_selected_groups.indexOf(prop) !== -1 ? 'selected="selected"' : '') +'>'+result['message']['groups'][current_selected_list][prop]+'</option>';
                                    }
                                }
                                else {
                                    fields += '<option value=""></option>';
                                }

                                var field_field = '<select multiple id="woochimp_groups_checkout_'+ current_field_id +'" name="woochimp_options[sets]['+ current_field_id +'][groups][]" class="woochimp-field">'+ fields +'</select>';
                                var field_html = '<tr valign="top"><th scope="row">'+ woochimp_label_groups +'</th><td>'+ field_field +'</td></tr>';

                                jQuery('#woochimp_list_checkout_'+ current_field_id).parent().parent().after(field_html);

                                // Make it chosen!
                                jQuery('#woochimp_groups_checkout_'+ current_field_id).chosen({
                                    no_results_text: woochimp_label_no_results_match_groups,
                                    placeholder_text_multiple: woochimp_label_select_some_groups,
                                    width: '400px'
                                });

                            }
                        }

                    });

                    /**
                     * Render merge fiels selection
                     */
                    var current_field_id = 0;

                    if (typeof result['message']['merge'] === 'object' && typeof result['message']['checkout_properties'] === 'object') {

                        jQuery('.woochimp_fields_checkout').each(function() {

                           current_field_id++;

                           var current_selected_list = (typeof woochimp_checkout_sets !== 'undefined' && typeof woochimp_checkout_sets[current_field_id] !== 'undefined' && typeof woochimp_checkout_sets[current_field_id]['list'] !== 'undefined' ? woochimp_checkout_sets[current_field_id]['list'] : null);
                           var current_selected_merge = (typeof woochimp_checkout_sets !== 'undefined' && typeof woochimp_checkout_sets[current_field_id] !== 'undefined' && typeof woochimp_checkout_sets[current_field_id]['merge'] !== 'undefined' ? woochimp_checkout_sets[current_field_id]['merge'] : null);

                           render_checkout_merge_fields_table(current_field_id, current_selected_list, current_selected_merge, result['message']['merge'], result['message']['checkout_properties']);
                        });

                    }

                    /**
                     * Update accordion height
                     */
                    jQuery('#woochimp_list_groups_list').accordion('refresh');

                    /**
                     * Enable add set button
                     */
                    jQuery('#woochimp_add_set').prop('disabled', false);
                    jQuery('#woochimp_add_set').prop('title', '');

                    /**
                     * Enable submit button
                     */
                    jQuery('#submit').prop('disabled', false);
                    jQuery('#submit').prop('title', '');

                }

        });

        /**
         * Render checkout merge fields table
         */
        function render_checkout_merge_fields_table(current_field_id, current_selected_list, current_selected_merge, merge_fields, checkout_properties) {

            if (current_selected_list !== null) {
                merge_fields = merge_fields[current_selected_list];
            }
            else {
                merge_fields = [];
            }

            // Generate options
            var field_options = '<option value></option>';

            if (typeof merge_fields === 'object') {
                for (var prop in merge_fields) {
                    if (merge_fields.hasOwnProperty(prop)) {
                        field_options += '<option value="'+prop+'">'+merge_fields[prop]+' ('+prop+')</option>';
                    }
                }
            }

            // Set up checkout field names
            var checkout_field_options = '<option value></option>';

            for (var prop in checkout_properties) {
                if (checkout_properties.hasOwnProperty(prop)) {
                    checkout_field_options += '<option value="'+prop+'">'+checkout_properties[prop]+'</option>';
                }
            }

            var input_field = '<select class="woochimp_name_select" name="woochimp_options[sets]['+ current_field_id +'][field_names][%%%id%%%][name]" id="woochimp_field_name_'+ current_field_id +'_%%%id%%%">'+checkout_field_options+'</select>';

            // Begin table
            var fields_table = '<div class="woochimp_fields_table_container"><table id="woochimp_fields_table_'+ current_field_id +'"><thead><tr><th style="text-align:left;font-weight:normal;">'+woochimp_label_fields_field+'</th><th style="text-align:left;font-weight:normal;">'+woochimp_label_fields_tag+'</th><th></th></tr></thead><tbody>';

            // Table content with preselected options
            if (typeof current_selected_merge === 'object' && current_selected_merge !== null && Object.keys(current_selected_merge).length > 0) {
                for (var prop in current_selected_merge) {
                    if (current_selected_merge.hasOwnProperty(prop)) {
                    var this_field = input_field.replace('%%%id%%%', prop);
                    this_field = this_field.replace('%%%id%%%', prop);
                    this_field = this_field.replace('%%%value%%%', current_selected_merge[prop]['name']);
                    fields_table += '<tr class="woochimp_field_row" id="woochimp_field_'+current_field_id+'_'+prop+'"><td>'+this_field+'</td><td><select class="woochimp_tag_select" name="woochimp_options[sets]['+ current_field_id +'][field_names]['+prop+'][tag]" id="woochimp_field_tag_'+current_field_id+'_'+prop+'">'+field_options+'</select></td><td><button type="button" class="woochimp_remove_field"><i class="fa fa-times"></i></button></td></tr>';
                    }
                }
            }

            // Table content with no preselected options
            else {
                var this_field = input_field.replace('%%%id%%%', '1');
                this_field = this_field.replace('%%%id%%%', '1');
                this_field = this_field.replace('%%%value%%%', '');
                fields_table += '<tr class="woochimp_field_row" id="woochimp_field_'+current_field_id+'_1"><td>'+this_field+'</td><td><select class="woochimp_tag_select" name="woochimp_options[sets]['+ current_field_id +'][field_names][1][tag]" id="woochimp_field_tag_'+current_field_id+'_1">'+field_options+'</select></td><td><button type="button" class="woochimp_remove_field"><i class="fa fa-times"></i></button></td></tr>';
            }

            // End table
            fields_table += '</tbody><tfoot><tr><td><button type="button" name="woochimp_add_field" id="woochimp_add_field" class="button button-primary" value="'+woochimp_label_add_new+'"><i class="fa fa-plus">&nbsp;&nbsp;'+woochimp_label_add_new+'</i></button></td><td></td><td></td></tr></tfoot></table></div>';

            // Render table
            jQuery('#woochimp_fields_table_'+current_field_id).replaceWith(fields_table);

            // Select preselected options
            if (typeof current_selected_merge === 'object' && current_selected_merge !== null && Object.keys(current_selected_merge).length > 0) {
                for (var prop in current_selected_merge) {
                    if (current_selected_merge.hasOwnProperty(prop)) {
                        jQuery('#woochimp_field_tag_'+current_field_id+'_'+prop).find('option[value="'+current_selected_merge[prop]['tag']+'"]').prop('selected', true);
                    }
                }
            }

            // Select preselected checkout field names
            if (typeof current_selected_merge === 'object' && current_selected_merge !== null && Object.keys(current_selected_merge).length > 0) {
                for (var prop in current_selected_merge) {
                    if (current_selected_merge.hasOwnProperty(prop)) {
                        jQuery('#woochimp_field_name_'+current_field_id+'_'+prop).find('option[value="'+current_selected_merge[prop]['name']+'"]').prop('selected', true);
                    }
                }
            }

            // Make all select fields chosen
            jQuery('.woochimp_tag_select').each( function() {
                jQuery(this).chosen({
                    no_results_text: woochimp_label_no_results_match_tags,
                    placeholder_text_single: woochimp_label_select_tag,
                    width: '300px'
                }).change( function(evt, params) {
                    regenerate_checkout_tag_chosen(current_field_id);
                });
            });
            jQuery('.woochimp_name_select').each( function() {
                jQuery(this).chosen({
                    no_results_text: woochimp_label_no_results_match_checkout_fields,
                    placeholder_text_single: woochimp_label_select_checkout_field,
                    width: '300px'
                });
            });

            // Regenerate fields (so we make selected fields disabled on other fields)
            regenerate_checkout_tag_chosen(current_field_id);

            /**
             * Handle new fields
             */
            jQuery('#woochimp_fields_table_'+current_field_id).find('#woochimp_add_field').each(function() {
                jQuery(this).click( function() {

                    var $table = jQuery(this).parent().parent().parent().parent();

                    // Get set id and last field id
                    var table_last_tr_id = jQuery($table).find('tbody>tr:last').attr('id');
                    table_last_tr_id = table_last_tr_id.replace('woochimp_field_', '');
                    table_last_tr_id = table_last_tr_id.split('_');

                    var current_field_id = table_last_tr_id[0];
                    var current_id = table_last_tr_id[1];

                    // Remove chosen from last element
                    jQuery($table).find('#woochimp_field_tag_'+current_field_id+'_'+current_id).chosen('destroy');

                    // Remove chosen from checkout field name
                    jQuery($table).find('#woochimp_field_name_'+current_field_id+'_'+current_id).chosen('destroy');

                    // Clone row and insert after the last one
                    var new_fields_row = jQuery($table).find('tbody>tr:last').clone(true);
                    jQuery($table).find('tbody>tr:last').after(new_fields_row);

                    jQuery($table).find('tbody>tr:last').each( function() {

                        // Change ids
                        var next_id = parseInt(current_id, 10) + 1;
                        jQuery(this).attr('id', 'woochimp_field_'+current_field_id+'_'+next_id);
                        jQuery(this).find(':input').each( function() {
                            if (jQuery(this).is('input')) {
                                jQuery(this).attr('id', 'woochimp_field_name_'+current_field_id+'_'+next_id);
                                jQuery(this).attr('name', 'woochimp_options[sets]['+current_field_id+'][field_names]['+next_id+'][name]');
                                jQuery(this).val('');
                            }
                            else if (jQuery(this).is('select')) {
                                if (jQuery(this).hasClass('woochimp_name_select')) {
                                    jQuery(this).attr('id', 'woochimp_field_name_'+current_field_id+'_'+next_id);
                                    jQuery(this).attr('name', 'woochimp_options[sets]['+current_field_id+'][field_names]['+next_id+'][name]');
                                    jQuery(this).val('');
                                }
                                else if (jQuery(this).hasClass('woochimp_tag_select')) {
                                    jQuery(this).attr('id', 'woochimp_field_tag_'+current_field_id+'_'+next_id);
                                    jQuery(this).attr('name', 'woochimp_options[sets]['+current_field_id+'][field_names]['+next_id+'][tag]');
                                    jQuery(this).val('');
                                }
                            }
                        });

                        // Make both tag fields chosen
                        jQuery('#woochimp_field_tag_'+current_field_id+'_'+current_id).chosen({
                            no_results_text: woochimp_label_no_results_match_tags,
                            placeholder_text_single: woochimp_label_select_tag,
                            width: '300px'
                        });
                        jQuery('#woochimp_field_tag_'+current_field_id+'_'+next_id).chosen({
                            no_results_text: woochimp_label_no_results_match_tags,
                            placeholder_text_single: woochimp_label_select_tag,
                            width: '300px'
                        });

                        // Make both checkout field name fields chosen
                        jQuery('#woochimp_field_name_'+current_field_id+'_'+current_id).chosen({
                            no_results_text: woochimp_label_no_results_match_checkout_fields,
                            placeholder_text_single: woochimp_label_select_checkout_field,
                            width: '300px'
                        });
                        jQuery('#woochimp_field_name_'+current_field_id+'_'+next_id).chosen({
                            no_results_text: woochimp_label_no_results_match_checkout_fields,
                            placeholder_text_single: woochimp_label_select_checkout_field,
                            width: '300px'
                        });

                    });

                    regenerate_checkout_tag_chosen(current_field_id);

                    return false;

                });
            });

            /**
             * Handle field removal
             */
            jQuery('.woochimp_remove_field').each( function() {
                jQuery(this).click( function() {
                    // Do not remove the last set - reset field values instead
                    if (jQuery(this).parent().parent().parent().children().length === 1) {
                        jQuery(this).parent().parent().find(':input').each( function() {
                            jQuery(this).val('');
                        });
                    }
                    else {
                        jQuery(this).parent().parent().remove();
                    }

                    jQuery('.woochimp_name_select').each( function() {
                        jQuery(this).trigger('chosen:updated');
                    });

                    regenerate_checkout_tag_chosen(current_field_id);
                });
            });

        }


    }

    /**
     * Checkout - regenerate all chosen fields
     */
    function regenerate_checkout_tag_chosen(current_field_id) {
        var all_selected = {};

        // Get all selected fields
        jQuery('#woochimp_fields_table_'+current_field_id).find('.woochimp_tag_select').each( function() {
            if (jQuery(this).find(':selected').length > 0 && jQuery(this).find(':selected').val() !== '') {
                all_selected[jQuery(this).prop('id')] = jQuery(this).find(':selected').val();
            }
        });

        // Regenerate chosen fields
        jQuery('#woochimp_fields_table_'+current_field_id).find('.woochimp_tag_select').each( function() {

            if (Object.keys(all_selected).length !== 0) {

                for (var prop in all_selected) {

                    if (prop !== jQuery(this).prop('id')) {

                        // Disable
                        jQuery(this).find('option[value="'+all_selected[prop]+'"]').prop('disabled', true);
                    }

                        // Enable previously disabled values if they are available now
                        jQuery(this).find(':disabled').each( function() {

                            // Check if such disabled property exists within selected properties
                            var option_value = jQuery(this).val();
                            var exists = false;

                            for (var proper in all_selected) {
                                if (all_selected[proper] === option_value) {
                                    exists = true;
                                    break;
                                }
                            }

                            // Remove if it does not exist
                            if (!exists) {
                                jQuery(this).removeAttr('disabled');
                            }

                        });

                }
            }
            else {
                // Enable all properties on all fields if there's only one left
                jQuery(this).find(':disabled').each( function() {
                    jQuery(this).removeAttr('disabled');
                });
            }

            jQuery(this).trigger('chosen:updated');
        });
    }

    /**
     * Checkout - handle list change
     */
    function woochimp_update_checkout_groups_and_tags(current_field_id, list_id) {

        // Replace groups field with loading animation
        var preloader = '<p id="woochimp_groups_checkout_'+current_field_id+'" class="woochimp_loading"><span class="woochimp_loading_icon"></span>'+woochimp_label_connecting_to_mailchimp+'</p>';
        jQuery('#woochimp_groups_checkout_'+current_field_id).parent().html(preloader);

        // Replace fields section with loading animation
        var preloader = '<div class="woochimp-status" id="woochimp_fields_table_'+current_field_id+'"><p class="woochimp_loading"><span class="woochimp_loading_icon"></span>'+woochimp_label_connecting_to_mailchimp+'</p></div>';
        jQuery('#woochimp_fields_table_'+current_field_id).parent().replaceWith(preloader);

        // Disable add set button until groups and fields are updated
        jQuery('#woochimp_add_set').prop('disabled', true);
        jQuery('#woochimp_add_set').prop('title', woochimp_label_still_connecting_to_mailchimp);

        // Disable submit button until groups and fields are updated
        jQuery('#submit').prop('disabled', true);
        jQuery('#submit').prop('title', woochimp_label_still_connecting_to_mailchimp);

        // Get data
        jQuery.post(
            ajaxurl,
            {
                'action': 'woochimp_update_checkout_groups_and_tags',
                'data': {'list': list_id}
            },
            function(response) {
                var result = jQuery.parseJSON(response);

                if (result && typeof result['message'] === 'object') {

                    // Render groups field
                    if (typeof result['message']['groups'] === 'object') {
                        var fields = '';

                        for (var prop in result['message']['groups']) {
                            fields += '<option value="'+prop+'">'+result['message']['groups'][prop]+'</option>';
                        }

                        // Update DOM
                        jQuery('#woochimp_groups_checkout_'+current_field_id).replaceWith('<select multiple id="woochimp_groups_checkout_'+current_field_id+'" name="woochimp_options[sets]['+current_field_id+'][groups][]" class="woochimp-field">'+fields+'</select>');

                        // Make it chosen!
                        jQuery('#woochimp_groups_checkout_'+current_field_id).chosen({
                            no_results_text: woochimp_label_no_results_match_groups,
                            placeholder_text_multiple: woochimp_label_select_some_groups,
                            width: '400px',
                        });
                    }

                    // Render merge fields table
                    render_checkout_merge_fields_table(current_field_id, list_id, null, result['message']['merge'], result['message']['checkout_properties']);

                    /**
                     * Enable add set button
                     */
                    jQuery('#woochimp_add_set').prop('disabled', false);
                    jQuery('#woochimp_add_set').prop('title', '');

                    /**
                     * Enable submit button
                     */
                    jQuery('#submit').prop('disabled', false);
                    jQuery('#submit').prop('title', '');

                }
            }
        );

    }

    /**
     * Checkout - add new set
     */
    jQuery('#woochimp_add_set').click(function() {

        // Get last field id
        var current_id = (jQuery('#woochimp_list_groups_list>div:last-child').attr('id').replace('woochimp_list_groups_list_', ''));

        // Remove chosen from all fields that have one
        var chosen_removed_from = [];

        jQuery('#woochimp_list_groups_list>div:last-child').find('select').each(function() {
            if (['woochimp_sets_condition_'+current_id,
                 'woochimp_sets_condition_operator_products_'+current_id,
                 'woochimp_sets_condition_operator_categories_'+current_id,
                 'woochimp_sets_condition_operator_amount_'+current_id,
                 'woochimp_sets_condition_operator_custom_'+current_id].indexOf(jQuery(this).prop('id')) === -1) {
                chosen_removed_from.push(jQuery(this).prop('id'));
                jQuery(this).chosen('destroy');
            }
        });

        // Clone element and insert after the last one
        jQuery('#woochimp_list_groups_list>div:last-child').clone(true).insertAfter('#woochimp_list_groups_list>div:last-child');

        // Regenerate chosen on previous fields
        for (var i=0, len=chosen_removed_from.length; i<len; i++) {
            if (chosen_removed_from[i].search('woochimp_list_checkout_') !== -1) {
                jQuery('#'+chosen_removed_from[i]).chosen({
                    no_results_text: woochimp_label_no_results_match_list,
                    placeholder_text_single: woochimp_label_select_mailing_list,
                    width: '400px'
                });
            }
            else if (chosen_removed_from[i].search('woochimp_groups_checkout_') !== -1) {
                jQuery('#'+chosen_removed_from[i]).chosen({
                    no_results_text: woochimp_label_no_results_match_groups,
                    placeholder_text_multiple: woochimp_label_select_some_groups,
                    width: '400px'
                });
            }
            else if (chosen_removed_from[i].search('woochimp_field_tag_') !== -1) {
                jQuery('#'+chosen_removed_from[i]).chosen({
                    no_results_text: woochimp_label_no_results_match_tags,
                    placeholder_text_single: woochimp_label_select_tag,
                    width: '300px'
                });
            }
            else if (chosen_removed_from[i].search('woochimp_field_name_') !== -1) {
                jQuery('#'+chosen_removed_from[i]).chosen({
                    no_results_text: woochimp_label_no_results_match_checkout_fields,
                    placeholder_text_single: woochimp_label_select_checkout_field,
                    width: '300px'
                });
            }
            else if (chosen_removed_from[i].search('woochimp_sets_condition_products_') !== -1) {
                jQuery('#'+chosen_removed_from[i]).chosen({
                    no_results_text: woochimp_label_no_results_match_products,
                    placeholder_text_multiple: woochimp_label_select_some_products,
                    width: '400px'
                });
            }
            else if (chosen_removed_from[i].search('woochimp_sets_condition_categories_') !== -1) {
                jQuery('#'+chosen_removed_from[i]).chosen({
                    no_results_text: woochimp_label_no_results_match_categories,
                    placeholder_text_multiple: woochimp_label_select_some_categories,
                    width: '400px'
                });
            }
        }

        /**
         * Fix new elements
         */
        jQuery('#woochimp_list_groups_list>div:last-child').each(function() {

            // Get next id (well.. it's current already)
            var next_id = parseInt(current_id, 10) + 1;

            // Change main div id
            jQuery(this).attr('id', 'woochimp_list_groups_list_'+next_id);

            // Change ids and names of mailing list and groups fields
            jQuery(this).find('#woochimp_list_checkout_'+current_id).attr('id', 'woochimp_list_checkout_'+next_id);
            jQuery('#woochimp_list_checkout_'+next_id).attr('name', 'woochimp_options[sets]['+next_id+'][list]');
            jQuery(this).find('#woochimp_groups_checkout_'+current_id).attr('id', 'woochimp_groups_checkout_'+next_id);
            jQuery('#woochimp_groups_checkout_'+next_id).attr('name', 'woochimp_options[sets]['+next_id+'][groups][]');

            // Remove selected options from mailing list
            jQuery('#woochimp_list_checkout_'+next_id).find('option:selected').prop('selected', false);

            // Remove all options from groups
            jQuery('#woochimp_groups_checkout_'+next_id).html('<option value=""></option>');

            // Change id of fields table
            jQuery(this).find('#woochimp_fields_table_'+current_id).attr('id', 'woochimp_fields_table_'+next_id);

            // Remove all field table rows except of first one
            jQuery('#woochimp_fields_table_'+next_id+' > tbody').find('tr:gt(0)').remove();

            // Change id of the first fields table row
            jQuery('#woochimp_fields_table_'+next_id+' > tbody').find('tr').attr('id', 'woochimp_field_'+next_id+'_1');

            // Change id and name of first field name field and reset selection
            jQuery('#woochimp_fields_table_'+next_id+' > tbody').find('.woochimp_name_select').attr('id', 'woochimp_field_name_'+next_id+'_1');
            jQuery('#woochimp_field_name_'+next_id+'_1').attr('name', 'woochimp_options[sets]['+next_id+'][field_names][1][name]');
            jQuery('#woochimp_field_name_'+next_id+'_1').find('option:selected').prop('selected', false);

            // Change id and name of first field tag field and remove all options
            jQuery('#woochimp_fields_table_'+next_id+' > tbody').find('.woochimp_tag_select').attr('id', 'woochimp_field_tag_'+next_id+'_1');
            jQuery('#woochimp_field_tag_'+next_id+'_1').attr('name', 'woochimp_options[sets]['+next_id+'][field_names][1][tag]');
            jQuery('#woochimp_field_tag_'+next_id+'_1').html('<option value=""></option>');

            // Change id and name of condition field
            jQuery(this).find('#woochimp_sets_condition_'+current_id).attr('id', 'woochimp_sets_condition_'+next_id);
            jQuery('#woochimp_sets_condition_'+next_id).attr('name', 'woochimp_options[sets]['+next_id+'][condition]');
            jQuery('#woochimp_sets_condition_'+next_id).find('option:selected').prop('selected', false);

            // Condition - operator - products
            jQuery(this).find('#woochimp_sets_condition_operator_products_'+current_id).attr('id', 'woochimp_sets_condition_operator_products_'+next_id);
            jQuery('#woochimp_sets_condition_operator_products_'+next_id).attr('name', 'woochimp_options[sets]['+next_id+'][operator_products]');
            jQuery('#woochimp_sets_condition_operator_products_'+next_id).find('option:selected').prop('selected', false);

            // Condition - operator - categories
            jQuery(this).find('#woochimp_sets_condition_operator_categories_'+current_id).attr('id', 'woochimp_sets_condition_operator_categories_'+next_id);
            jQuery('#woochimp_sets_condition_operator_categories_'+next_id).attr('name', 'woochimp_options[sets]['+next_id+'][operator_categories]');
            jQuery('#woochimp_sets_condition_operator_categories_'+next_id).find('option:selected').prop('selected', false);

            // Condition - operator - amount
            jQuery(this).find('#woochimp_sets_condition_operator_amount_'+current_id).attr('id', 'woochimp_sets_condition_operator_amount_'+next_id);
            jQuery('#woochimp_sets_condition_operator_amount_'+next_id).attr('name', 'woochimp_options[sets]['+next_id+'][operator_amount]');
            jQuery('#woochimp_sets_condition_operator_amount_'+next_id).find('option:selected').prop('selected', false);

            // Condition - operator - custom
            jQuery(this).find('#woochimp_sets_condition_operator_custom_'+current_id).attr('id', 'woochimp_sets_condition_operator_custom_'+next_id);
            jQuery('#woochimp_sets_condition_operator_custom_'+next_id).attr('name', 'woochimp_options[sets]['+next_id+'][operator_custom]');
            jQuery('#woochimp_sets_condition_operator_custom_'+next_id).find('option:selected').prop('selected', false);

            // Products multi select
            jQuery(this).find('#woochimp_sets_condition_products_'+current_id).attr('id', 'woochimp_sets_condition_products_'+next_id);
            jQuery('#woochimp_sets_condition_products_'+next_id).attr('name', 'woochimp_options[sets]['+next_id+'][condition_products][]');
            jQuery('#woochimp_sets_condition_products_'+next_id).find('option:selected').prop('selected', false);

            // Categories multi select
            jQuery(this).find('#woochimp_sets_condition_categories_'+current_id).attr('id', 'woochimp_sets_condition_categories_'+next_id);
            jQuery('#woochimp_sets_condition_categories_'+next_id).attr('name', 'woochimp_options[sets]['+next_id+'][condition_categories][]');
            jQuery('#woochimp_sets_condition_categories_'+next_id).find('option:selected').prop('selected', false);

            // Order total value
            jQuery(this).find('#woochimp_sets_condition_amount_'+current_id).attr('id', 'woochimp_sets_condition_amount_'+next_id);
            jQuery('#woochimp_sets_condition_amount_'+next_id).attr('name', 'woochimp_options[sets]['+next_id+'][condition_amount]');
            jQuery('#woochimp_sets_condition_amount_'+next_id).val('');

            // Custom field key
            jQuery(this).find('#woochimp_sets_condition_key_custom_'+current_id).attr('id', 'woochimp_sets_condition_key_custom_'+next_id);
            jQuery('#woochimp_sets_condition_key_custom_'+next_id).attr('name', 'woochimp_options[sets]['+next_id+'][condition_key_custom]');
            jQuery('#woochimp_sets_condition_key_custom_'+next_id).val('');
            jQuery('#woochimp_sets_condition_amount_'+next_id).val('');

            // Custom field value
            jQuery(this).find('#woochimp_sets_condition_custom_value_'+current_id).attr('id', 'woochimp_sets_condition_custom_value_'+next_id);
            jQuery('#woochimp_sets_condition_custom_value_'+next_id).attr('name', 'woochimp_options[sets]['+next_id+'][condition_custom_value]');
            jQuery('#woochimp_sets_condition_custom_value_'+next_id).val('');
        });

        /**
         * Make new select fields chosen
         */
        jQuery('#woochimp_list_groups_list>div:last-child').find('select').each(function() {
            var current_select_id = jQuery(this).prop('id');

            if (current_select_id.search('woochimp_list_checkout_') !== -1) {
                jQuery('#'+current_select_id).chosen({
                    no_results_text: woochimp_label_no_results_match_list,
                    placeholder_text_single: woochimp_label_select_mailing_list,
                    width: '400px'
                });
            }
            else if (current_select_id.search('woochimp_groups_checkout_') !== -1) {
                jQuery('#'+current_select_id).chosen({
                    no_results_text: woochimp_label_no_results_match_groups,
                    placeholder_text_multiple: woochimp_label_select_some_groups,
                    width: '400px'
                });
            }
            else if (current_select_id.search('woochimp_field_tag_') !== -1) {
                jQuery('#'+current_select_id).chosen({
                    no_results_text: woochimp_label_no_results_match_tags,
                    placeholder_text_single: woochimp_label_select_tag,
                    width: '300px'
                });
            }
            else if (current_select_id.search('woochimp_field_name_') !== -1) {
                jQuery('#'+current_select_id).chosen({
                    no_results_text: woochimp_label_no_results_match_checkout_fields,
                    placeholder_text_single: woochimp_label_select_checkout_field,
                    width: '300px'
                });
            }
            else if (current_select_id.search('woochimp_sets_condition_products_') !== -1) {
                jQuery('#'+current_select_id).chosen({
                    no_results_text: woochimp_label_no_results_match_products,
                    placeholder_text_multiple: woochimp_label_select_some_products,
                    width: '400px'
                });
            }
            else if (current_select_id.search('woochimp_sets_condition_categories_') !== -1) {
                jQuery('#'+current_select_id).chosen({
                    no_results_text: woochimp_label_no_results_match_categories,
                    placeholder_text_multiple: woochimp_label_select_some_categories,
                    width: '400px'
                });
            }
        });

        woochimp_hide_unused_condition_fields();
        regenerate_checkout_tag_chosen(current_id);

        /**
         * Update accordion
         */
        jQuery('#woochimp_list_groups_list').accordion('refresh');
        var $accordion = jQuery("#woochimp_list_groups_list").accordion();
        var last_accordion_element = $accordion.find('h4').length;
        $accordion.accordion('option', 'active', (last_accordion_element - 1));
        regenerate_carousel_handle_titles();

        return false;
    });

    /**
     * Checkout - remove set
     */
    jQuery('.woochimp_list_groups_remove').each(function() {
        jQuery(this).click(function() {

            // Remove set if it's not the last one
            if (jQuery(this).parent().parent().parent().children().length !== 1) {
                jQuery(this).parent().parent().remove();
            }

            /**
             * Update accordion
             */
            jQuery('#woochimp_list_groups_list').accordion('refresh');
            regenerate_carousel_handle_titles();

        });
    });

    /**
     * Regenerate carousel handle titles
     */
    function regenerate_carousel_handle_titles()
    {
        var fake_id = 1;

        jQuery('#woochimp_list_groups_list').children().each(function() {
            jQuery(this).find('.woochimp_list_groups_title').html(woochimp_label_set_no + '' + fake_id);
            fake_id++;
        });
    }

});
