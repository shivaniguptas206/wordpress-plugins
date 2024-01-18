jQuery(document).ready(function ($) {
    $('#car-add-form').submit(function (event) {
        event.preventDefault();

        $('#car-add-form button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        formData.append('action', 'sfp_save_car_data');

        $('#car-add-response').html('<div class="loading">Processing...</div>');

        $.ajax({
            type: 'POST',
            url: car_add_vars.car_add_url,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === 'success') {
                    $('#car-add-response').html('<div class="success">' + response.message + '</div>');
                    window.location.reload(); // Reload the page
                } else {
                    $('#car-add-response').html('<div class="error">' + response.message + '</div>');
                }
            },
            error: function (xhr, status, error) {
                $('#car-add-response').html('<div class="error">AJAX request failed. Please try again.</div>');
            },
            complete: function () {
                $('#car-add-form button[type="submit"]').prop('disabled', false);
            }
        });
    });
});

jQuery(document).ready(function ($) {
    $('#car_model').change(function() {
        if ($(this).val() === 'custom') {
            // If "Custom Model" is selected, show the custom_model_name input
            $('#custom_model_name').show();
        } else {
            // If any other option is selected, hide the custom_model_name input
            $('#custom_model_name').hide();
        }
    });
});
