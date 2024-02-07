(function ($) {
    'use strict';

    $(document).ready(function ($) {
        // Handle click event on remove product button
        $(document).on("click", ".swfw-remove-product", function () {
            var swfwProductID = $(this).data("product-id");
            var $swfwRow = $(this).closest("tr"); // Reference to the table row

            // Add loader class to the remove button
            $(this).addClass("loading");

            // Perform AJAX request to remove the product
            $.ajax({
                url: swfw_ajax_object.ajax_url,
                type: "POST",
                data: {
                    action: "swfw_remove_product_from_wishlist",
                    productID: swfwProductID,
                    swfwremovenonce: swfw_ajax_object.nonce
                },
                success: function (response) {
                    if (response.success) {
                        // Remove the table row from the wishlist table
                        $swfwRow.remove();

                        // Check if there are any remaining products
                        if ($(".swfw-wishlist tr").length === 0) {
                            // Display the empty wishlist message
                            $(".swfw-wishlist").html("<p>" + swfw_ajax_object.empty_wishlist_message + "</p>");
                        }
                    } else {
                        // Display the error message
                        alert(swfw_ajax_object.remove_error);
                    }
                },
                complete: function () {
                    // Remove the loader class from the remove button
                    $(".swfw-remove-product").removeClass("loading");
                }
            });
        });
    });

    //multiple product remove from table

    $(document).on("click", "#swfw-apply-button", function () {
        var swfw_action = $(".swfw-action-select").val();

        if (swfw_action === "remove") {
            // Array to store the product IDs to be removed
            var swfw_productsToRemove = [];

            // Find all the checkboxes that are checked
            $(".swfw-product-checkbox:checked").each(function () {
                swfw_productsToRemove.push($(this).data("product-id"));
            });

            if (swfw_productsToRemove.length > 0) {
                // Add loader class to the "Apply Action" button
                $(this).addClass("loading");

                // Perform AJAX request to remove the products
                $.ajax({
                    url: swfw_ajax_object.ajax_url,
                    type: "POST",
                    data: {
                        action: "swfw_remove_multiple_products_from_wishlist",
                        productIDs: swfw_productsToRemove,
                        swfwmultiremovenonce: swfw_ajax_object.nonce
                    },
                    success: function (response) {
                        if (response.success) {
                            // Loop through the product IDs and remove the corresponding rows/cards
                            swfw_productsToRemove.forEach(function (swfw_productID) {
                                // Remove the table row from the wishlist table
                                $(".swfw-wishlist").find("tr[data-product-id='" + swfw_productID + "']").remove();
                                });

                            // Check if there are any remaining products
                            if ($(".swfw-wishlist tr").length === 0) {
                                // Display the empty wishlist message
                                $(".swfw-wishlist").html("<p>" + swfw_ajax_object.empty_wishlist_message + "</p>");
                            }

                            // Reload the page after a delay of 1 second (1000 milliseconds)
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Display the error message
                            alert(swfw_ajax_object.remove_error);
                        }
                    },
                    complete: function () {
                        // Remove the loader class from the "Apply Action" button
                        $("#swfw-apply-button").removeClass("loading");
                    }
                });
            } else {
                // No products selected, show a message or take appropriate action
                alert(swfw_ajax_object.no_products_selected);
            }
        }
    });
    
})(jQuery);
