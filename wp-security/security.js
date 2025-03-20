jQuery(document).ready(function($) {
    // Check if the token is already valid
    if (wpsec_admin_token.is_token_valid === 'false') {
        // Show token input prompt on page load
        showTokenPrompt();
    }

    // Function to show the token input prompt
    function showTokenPrompt() {
        var tokenPrompt = $('<div id="wpsec-token-prompt" class="wpsec-popup-overlay"> \
                                <div class="wpsec-popup-content"> \
                                    <h2>' + (wpsec_admin_token.is_token_valid === 'false' ? 'Enter Security Token' : 'Access Granted') + '</h2> \
                                    <input type="text" id="wpsec-admin-token" placeholder="Enter security token" /> \
                                    <button id="wpsec-submit-token" class="button button-primary">Submit</button> \
                                    <p id="wpsec-error-message" style="color: red; display: none;">Invalid token. Please try again.</p> \
                                </div> \
                            </div>');
        
        $('body').append(tokenPrompt);

        // Center the popup
        $('#wpsec-token-prompt .wpsec-popup-content').css({
            'position': 'fixed',
            'top': '50%',
            'left': '50%',
            'transform': 'translate(-50%, -50%)',
            'background-color': '#fff',
            'padding': '20px',
            'border-radius': '5px',
            'box-shadow': '0 0 10px rgba(0, 0, 0, 0.1)',
            'width': '300px',
            'text-align': 'center'
        });

        // Submit token via AJAX
        $('#wpsec-submit-token').click(function() {
            var adminToken = $('#wpsec-admin-token').val();

            // Make AJAX request to check token
            $.ajax({
                type: 'POST',
                url: wpsec_admin_token.ajax_url,
                data: {
                    action: 'wpsec_check_admin_token',
                    nonce: wpsec_admin_token.nonce,
                    admin_token: adminToken
                },
                success: function(response) {
                    if (response.success) {
                        // Token is valid
                        $('#wpsec-token-prompt').remove(); // Hide the prompt
                        location.reload(); // Reload page to apply changes
                    } else {
                        // Invalid token
                        $('#wpsec-error-message').show();
                    }
                },
                error: function() {
                    alert('An error occurred while verifying the token.');
                }
            });
        });
    }
});
