(function ($) {
    'use strict';

    $(document).ready(function ($) {
        var swfwViewWishlistButton = $(".swfw-view-wishlist-button");
        swfwViewWishlistButton.show();
        
        function swfwAddToWishlist(swfwButton, swfwProductID, swfwUserID, swfwWishlistName) {
            // Show loader on the button
            swfwButton.addClass("loading");
        
            // Rest of the code for adding the product to the wishlist
            var swfwUserData = {
                user_id: swfwUserID,
            };
            var swfwProductData = {
                product_id: swfwProductID,
                name: swfwButton.data("product-name"),
                price: swfwButton.data("product-price"),
                // Add any other relevant product information here
            };
        
            // Make an AJAX request to add the product to the wishlist
            $.ajax({
                url: swfw_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'swfw_add_to_wishlist',
                    product_id: swfwProductData.product_id,
                    user_id: swfwUserData.user_id,
                    wishlist_name: swfwWishlistName, // Pass the wishlist name in the AJAX request
                    swfwnonce: swfw_ajax_object.nonce
                },
                beforeSend: function () {
                    // Show loader animation
                    swfwButton.addClass("loading");
                },
                success: function (response) {
                    if (response.success) {
                        // Product added to wishlist
                        swfwButton.addClass("swfw-added");
                        swfwButton.removeClass("loading");
        
                        // Show the "View Wishlist" button immediately
                        swfwViewWishlistButton.show();
        
                        // Show the product added message
                        var swfwProductAddedMessage = $("<div class='swfw-product-added-message'>" + swfw_ajax_object.product_added_message + "</div>");
                        swfwButton.after(swfwProductAddedMessage);
        
                        // Remove the message after a certain duration (e.g., 3 seconds)
                        setTimeout(function () {
                            swfwProductAddedMessage.remove();
                        }, 3000);
        
                        // Reload the page
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        // Handle the error if needed
                        swfwButton.removeClass("loading"); // Remove the loading class in case of an error
                    }
                },
                error: function (error) {
                    // Handle the error if needed
                    alert(swfw_ajax_object.remove_error_wishlist);
                    swfwButton.removeClass("loading"); // Remove the loading class in case of an error
                }
            });
        }
        
        $(document).on("click", ".swfw-wishlistify-button", function (e) {
            e.preventDefault(); // Prevent the default form submission
        
            var swfwButton = $(this);
            var swfwProductID = swfwButton.data("product-id");
            var swfwUserID = swfwButton.data("user-id");
            var swfwWishlistName = '';
        
            // Check if the user is logged in
            if (swfwUserID === 0) {
                var swfwUnloggedMessage = swfwButton.siblings(".swfw-unlogged-message");
                swfwUnloggedMessage.show();
        
                // Hide the message after a certain duration (e.g., 5 seconds)
                setTimeout(function () {
                    swfwUnloggedMessage.hide();
                }, 3000);
        
                return false;
            }
        
            // Check if the button has the "added" class, indicating that the product is already added
            if (swfwButton.hasClass("swfw-added")) {
                // Exit the function to prevent adding the product again
                return false;
            }
        
            // Check if wishlist collection is enabled
            var swfwWishlistCollectionEnabled = true; // Modify this based on your logic
        
            if (swfwWishlistCollectionEnabled) {
                // Get the wishlist name from the input box
                var swfwWishlistNameInput = swfwButton.siblings("input[name='swfw_new_wishlist_name']");
                if (swfwWishlistNameInput.length > 0) {
                    swfwWishlistName = swfwWishlistNameInput.val().trim();
                }
            }
            // Call the swfwAddToWishlist function
            swfwAddToWishlist(swfwButton, swfwProductID, swfwUserID, swfwWishlistName);
        });
        
        // Share wishlist link
        $('.swfw-copy-link-share').on('click', function (event) {
            event.preventDefault();

            var swfwWishlistLink = $(this).data('wishlist-link');

            // Create a temporary input element to copy the link
            var swfwInputElement = $('<input>').val(swfwWishlistLink).appendTo('body');

            // Select the text in the input element
            swfwInputElement.select();

            try {
                // Use the Clipboard API to copy the text to the clipboard
                document.execCommand('copy');

                // Display the success message
                $('#swfw-copy-message').show();

                // Hide the success message after a few seconds (optional)
                setTimeout(function () {
                    $('#swfw-copy-message').hide();
                }, 3000);
            } catch (err) {
                // If copying fails, handle the error
                alert(swfw_ajax_object.copy_error_message + ': ' + err.message);
            } finally {
                // Remove the temporary input element
                swfwInputElement.remove();
            }
        });

        // Function to handle adding selected products to the cart
        function swfwAddSelectedProductsToCart() {
            var swfwSelectedProductIDs = swfwgetSelectedProductIDs();
            if (swfwSelectedProductIDs.length === 0) {
                alert(swfw_ajax_object.no_products_select_add_into_cart);
                return;
            }

            $.ajax({
                url: swfw_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'swfw_add_multiple_to_cart',
                    productIDs: swfwSelectedProductIDs,
                    swfwmultinoncecart: swfw_ajax_object.nonce,
                },
                beforeSend: function () {
                    // Display loading spinner or any UI indication
                },
                success: function (response) {
                    // Handle the success response (updated cart data)
                    if (response && response.cart_count) {
                        // Update the cart count in the frontend (if required)
                        $('.cart-count').html(response.cart_count);
                    }
                    // Display success message
                    if (response && response.message) {
                        alert(response.message);
                    }
                    // Reload the page without visibly showing the refresh
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                },
                error: function (xhr, status, error) {
                  
                },
                complete: function () {
                },
            });
        }

        // Handle "Apply Action" button click
        $('#swfw-apply-button').on('click', function (e) {
            e.preventDefault();
            var action = $('.swfw-action-select').val();
            if (action === 'swfw-multiple-add-to-cart') {
                swfwAddSelectedProductsToCart();
            } else if (action === 'remove') {
            }
        });

        // Function to get selected product IDs
        function swfwgetSelectedProductIDs() {
            var swfwSelectedProductIDs = [];
            $('.swfw-product-checkbox:checked').each(function () {
                var swfwproductID = $(this).data('product-id');
                swfwSelectedProductIDs.push(swfwproductID);
            });
            return swfwSelectedProductIDs;
        }
    });

})(jQuery);
