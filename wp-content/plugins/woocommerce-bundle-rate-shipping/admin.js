// Javascript for WooCommerce Bundle Rate Shipping plugin
var ENDA_BUNDLERATE = {};

( function( $ ){    

    // Updates layer input names so that indeces remain true
    ENDA_BUNDLERATE.updateLayerNames = function(table) {
        var i = 0,
            rows = table.find('tr.rate_row');

        rows.each(function() {                
            $(this).find('input').each(function() {                
                var new_name = $(this).attr('name').replace(/rates\]\[\d\]/, 'rates]['+i+']');                
                $(this).attr('name', new_name);
            });                
            i += 1;
        });
        return;
    };

    // Updates start counts
    ENDA_BUNDLERATE.updateStartCount = function(el) {
        var start_count, 
            this_row = el.parents('tr.rate_row').first(),
            next_row = this_row.next();
            
        if (el.hasClass('remove_bundle_layer')) {                
            start_count = parseInt( this_row.prev().find('input').first().val() ) + 1;                
        }
        else {
            start_count = parseInt( el.val() ) + 1;
        }
        
        next_row.find('span.start_count').first().text(start_count);        
    }; 

    // Toggle specific countries field
    ENDA_BUNDLERATE.toggleSpecificCountries = function(el) {
        if ( el.val() === 'specific' ) {
            el.parent().next('div').show();
        }
        else {
            el.parent().next('div').hide();
        }
    };

    // Add a new layer of bundle rate
    ENDA_BUNDLERATE.addLayer = function(el) {
        if ( el.attr('disabled') === 'disabled' ) {
            return false;
        }        

        var self = this,
            category_block = el.parents('table').first(),
            last_row = category_block.find('tbody tr.rate_row').last(),
            data = {
                action:         'get_new_layer',
                start_count:    parseInt( last_row.prev().find('input').first().val() ) + 1,
                cost_input:     last_row.find('input').last().attr('name'),
                products_input: last_row.find('input').first().attr('name')
            };
            
        $.post(ajaxurl, data, function(response) {
            $(response).insertBefore(last_row);
            self.updateLayerNames(category_block);
        });  
                        
        return false;          
    };

    // Remove a layer
    ENDA_BUNDLERATE.removeLayer = function(el) {
        var table = el.parents('table').first();
            
        this.updateStartCount(el);
        el.parents('tr').first().remove();
        this.updateLayerNames(table);
        return false;
    };

    // Disable Add Layer button
    ENDA_BUNDLERATE.disableAddLayerButton = function (el) {
        var first_layer = el.find('tr.rate_row input').first().val();                        

        if ( !first_layer || '0' === first_layer || '' === first_layer) {
            el.find('.add_layer').first().attr('disabled', true);
        }
    };

    // Add a new layer of configuration
    ENDA_BUNDLERATE.addConfiguration = function(el) {
        var rows = $('#bundle_rate_configuration > tbody > tr'),
            data = {
                action:         'get_new_configuration_layer',
                index:          rows.length
            };
            
        $.post(ajaxurl, data, function(response) {
            $(response).insertAfter(rows.last());

            // Run init
            ENDA_BUNDLERATE.init();
        });                  
                        
        return false;          
    };

    // Function to run on page load and when a new configuration is added
    ENDA_BUNDLERATE.init = function() {
        var self = this;

        // Toggle specific countries display        
        $('.shipping_destination .destination').on( 'change', function() {
            self.toggleSpecificCountries( $(this) );
        }).change();        

        $("select.chosen_select").chosen();
        
        // Disable "add layer" button while first layer's products count is not set
        $('.shipping_rates table').each(function() {
            self.disableAddLayerButton( $(this) );
        });
    };

    $(document).ready(function(){   
        var methods = ENDA_BUNDLERATE;
        
        methods.init();

        $('#bundle_rate_configuration')
        // Add an extra layer to a bundle rate configuration
        .on('click', '.add_layer', function() {
            return methods.addLayer( $(this) );
        })
        // Remove a bundle layer from the DOM
        .on('click', '.remove_bundle_layer', function() {
            return methods.removeLayer( $(this) );
        })
        // Update start_count when products count changes
        .on('change', 'input.bundle_rate_products', function() {
            methods.updateStartCount( $(this) );            

            if ( !$(this).val() || '0' === $(this).val() || '' === $(this).val()) {
                $(this).parents('table').find('.add_layer').first().attr('disabled', true);
            }
            else { 
                $(this).parents('table').find('.add_layer').first().attr('disabled', false);
            }
        })
        .on('click', '.add_configuration', function() {
            return methods.addConfiguration( $(this) );
        });
    });
})(jQuery);