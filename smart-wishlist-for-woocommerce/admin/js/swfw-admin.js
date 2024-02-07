(function ($) {
    'use strict';

    $(document).ready(function () {
        $('.swfw-toggle-input').on('change', function () {
            $('#swfw-plugin-container').toggleClass('swfw-dark-mode');
            swfwUpdateTitleColor();
        });

        $('.swfw-click').on('click', function () {
            $('#swfw-plugin-container').toggleClass('swfw-dark-mode');
            swfwUpdateTitleColor();
        });

        // Update the add_settings_field title color based on dark mode
        function swfwUpdateTitleColor() {
            if ($('#swfw-plugin-container').hasClass('swfw-dark-mode')) {
                $('.wp-core-ui .form-table th').css('color', 'red');
                $('.wp-core-ui .form-table td').css('color', 'white');
                $('#swfw-plugin-container').css('background-color', 'black');
            } else {
                $('.wp-core-ui .form-table th').css('color', 'initial');
                $('.wp-core-ui .form-table td').css('color', 'initial');
                $('#swfw-plugin-container').css('background-color', 'white');
            }
        }

        // Check the user's preference and set the initial state accordingly
        if (localStorage.getItem('swfwDarkModeEnabled') === 'true') {
            $('.swfw-toggle-input').prop('checked', true);
            $('#swfw-plugin-container').addClass('swfw-dark-mode');
        } else {
            $('.swfw-toggle-input').prop('checked', false);
            $('#swfw-plugin-container').removeClass('swfw-dark-mode');
        }

        // Store user's preference on toggle change
        $('.swfw-toggle-input').on('change', function () {
            if ($(this).is(':checked')) {
                localStorage.setItem('swfwDarkModeEnabled', 'true');
            } else {
                localStorage.setItem('swfwDarkModeEnabled', 'false');
            }
        });

        // Initial check for dark mode and update the title color accordingly
        swfwUpdateTitleColor();
    });
    jQuery(document).ready(function ($) {
        // Get the checkbox elements and the color picker elements
        var swfwButtonCheckbox = $('#swfw_button_color_enable');
        var swfwButtonColorPicker = $('#swfw_button_color');
        var swfwTextCheckbox = $('#swfw_text_color_enable');
        var swfwTextColorPicker = $('#swfw_text_color_css');

        // Function to handle the visibility of color picker based on checkbox state
        function swfwHandleColorPickerVisibility(swfw_checkbox, swfw_colorPicker) {
            if (swfw_checkbox.is(':checked')) {
                swfw_colorPicker.slideDown('slow');
            } else {
                swfw_colorPicker.slideUp('fast');
            }
        }

        // Check the initial state of the checkboxes and hide/show the color pickers accordingly
        swfwHandleColorPickerVisibility(swfwButtonCheckbox, swfwButtonColorPicker);
        swfwHandleColorPickerVisibility(swfwTextCheckbox, swfwTextColorPicker);

        // Toggle the visibility of the color pickers based on checkbox changes
        swfwButtonCheckbox.on('change', function () {
            swfwHandleColorPickerVisibility(swfwButtonCheckbox, swfwButtonColorPicker);
        });

        swfwTextCheckbox.on('change', function () {
            swfwHandleColorPickerVisibility(swfwTextCheckbox, swfwTextColorPicker);
        });
    });

})(jQuery);
