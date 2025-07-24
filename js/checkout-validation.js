jQuery(function($) {
    // Ensure our field is included in the checkout update
    $(document.body).on('checkout_error', function() {
        var $radioGroup = $('input[name="sample_collection_method"]');
        if (!$radioGroup.is(':checked')) {
            $('.sample-collection-group').addClass('woocommerce-invalid woocommerce-invalid-required-field');
        }
    });
    
    // Clear validation when a radio is selected
    $(document.body).on('change', 'input[name="sample_collection_method"]', function() {
        $('.sample-collection-group').removeClass('woocommerce-invalid woocommerce-invalid-required-field');
        var collection_method = $(this).val();
        if (collection_method === 'home') {
            $('.sample-collection-home-address').show();
            $('.sample-collection-clinic-address').hide();
        }else if (collection_method === 'center') {
            $('.sample-collection-clinic-address').show();
            $('.sample-collection-home-address').hide();
        }else {
            $('.sample-collection-home-address').hide();
            $('.sample-collection-clinic-address').hide();
        }
    });
    
    // Add our field to the AJAX update
    $(document.body).on('updated_checkout', function() {
        $('input[name="sample_collection_method"]').on('change', function() {
            $(document.body).trigger('update_checkout');
        });
    });
});