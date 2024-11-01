jQuery(document).ready(function () {

    function colorSelector() {
        jQuery('#smp_poll_color_options').on('change', function () {
            if (this.value !== 'gradient') {
                console.log('Solid select selector')
                jQuery('.color2').addClass('hidden');
            }
            else {
                jQuery('#smp_poll_colors input').removeClass('hidden');
            }
        });
    }
    colorSelector();
});