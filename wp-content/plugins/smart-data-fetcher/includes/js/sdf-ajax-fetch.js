jQuery(document).ready(function($) {
    // Trigger the AJAX request on button click
    $('.sdf-ajax-call-button').click(function(e) {
        e.preventDefault();

        var button = $(this);
        var action = button.data('action');  // Get action from data-action attribute
        var nonce = button.data('nonce');    // Get nonce from data-nonce attribute
        var spinner = $('#' + action + '_spinner');  // Dynamically find the corresponding spinner
        var status = $('#' + action + '_status');  // Dynamically find the corresponding status message
        
        // Disable the button and show the spinner
        button.prop('disabled', true);
        spinner.show();  // Show the spinner
        spinner.find('.spinner').addClass('is-active'); // Activate the spinner

        // Show loading message
        status.html('Processing...');

        // Perform AJAX request
        $.ajax({
            type: 'POST',
            url: sdf_ajax_obj.ajax_url,
            data: {
                action: action,   // Action hook for PHP
                nonce: nonce,     // Security nonce
            },
            success: function(response) {
                if (response.success) {
                    status.html(response.data.message);
                    console.log(response.data)
                } else {
                    status.html('Error: ' + response.data.message);
                }
            },
            error: function(response) {
                status.html('Error: ' + response.message);
            },
            complete: function() {
                // Re-enable the button and hide the spinner after the request completes
                button.prop('disabled', false);
                spinner.find('.spinner').removeClass('is-active'); // Deactivate the spinner
                spinner.hide(); // Hide the spinner
            }
        });
    });
});

jQuery(document).ready(function($) {
    $('.sdf-reprocess-button').click(function(e) {
        e.preventDefault();

        var button = $(this);
        var frn = button.data('frn');
        var nonce = button.data('nonce');
        var statusCell = $('#frn_' + frn);
        var postIdCell = $('#post_' + frn);

        // Disable button and show processing text
        button.prop('disabled', true).text('Processing...').css('color', 'BlueViolet');

        $.ajax({
            type: 'POST',
            url: sdf_ajax_obj.ajax_url,
            data: {
                action: 'sdf_reprocess_specific_firm',
                frn: frn,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update status in UI
                    statusCell.text('completed').css('color', 'blue');
                    postIdCell.text(response.data.post_id).css('color', 'gray');
                    // Update button text and color
                    button.text('Completed').css('color', 'blue').prop('disabled', true);
                } else {
                    alert(response.data.message);
                    button.text('Reprocess').prop('disabled', false);
                }
            },
            error: function(response) {
                console.log(response.data.message);
                alert('An error occurred.');
                button.text('Reprocess').prop('disabled', false);
            }
        });
    });
});


