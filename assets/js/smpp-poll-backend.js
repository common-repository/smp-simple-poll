(function ($) {

    jQuery(document).ready(function () {

        function colorSelector() {
            jQuery('#smpp_color_options').on('change', function () {
                if (this.value !== 'gradient') {
                    console.log('Solid select selector')
                    jQuery('.color2').addClass('hidden');
                }
                else {
                    jQuery('#smpp_colors input').removeClass('hidden');
                }
            });
        }
        function getRndInteger(min, max) {
            return Math.floor(Math.random() * (max - min)) + min;
        }

        let i = 2;
        jQuery('.smpp_add_option_btn').click(function (e) {
            i++;
            e.preventDefault();
            jQuery(`<tr class="smpp_append_option_filed_tr"> <td> <table class="form-table"> <tr> <td>Option name</td><td> <input type="text" value="" class="widefat" id="smpp_option" name="smpp_option[]" required/> <input type="hidden" name="smpp_option_id[]" id="smpp_option_id" value="${getRndInteger(947984929347923, 112984929347923)}"/> </td></tr></table> </td></tr>`).insertAfter(jQuery('.smpp_append_option_filed_tr:last-child'));
        });

        jQuery('.remove-option').on('click', function (e) {
            e.preventDefault();
            jQuery(this).closest('.smpp_append_option_filed_tr').remove();
        });

        function copyToClipboard(element) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(element).text()).select();
            document.execCommand("copy");
            $temp.remove();
        }
        jQuery('.shortcode code').on('click', function (e) {
            e.preventDefault();
            copyToClipboard($(this));

            $('.shortcode span').addClass('invisible');
            $('.shortcode span').removeClass('visible');
            $($(this).next()).removeClass('invisible');
            $($(this).next()).addClass('visible');

            setInterval(() => {
                $('.shortcode span').addClass('invisible');
            }, 1000);
        });

        colorSelector();


    });

})(jQuery);