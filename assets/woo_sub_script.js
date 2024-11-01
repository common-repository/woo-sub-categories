/*
 * 
 * GlocoForms Front end script
 * 
 * @since 1.0.0
 * 
 */

(function ($) {
    var woo_subcat = {
        initilaize: function () {
            $(document).ready(function () {
                $(document).on('click', '.woo_plus', function () {
                    $('.woo_nxt_lvl').slideUp();
                    $('.woo_plus').show();
                    $('.woo_minus').hide();
                    $(this).parents('.woo_subcategory_parent').find('.woo_nxt_lvl').slideDown();
                    $(this).hide();
                    $(this).parents('.woo_subcategory_parent').find('.woo_minus').show();
                });

                $(document).on('click', '.woo_minus', function () {
                    $('.woo_nxt_lvl').slideUp();
                    //$(this).parents('.woo_subcategory_parent').find('.woo_nxt_lvl').slideDown();
                    $('.woo_plus').show();
                    $('.woo_minus').hide();
                    $(this).hide();
                    $(this).parents('.woo_subcategory_parent').find('.woo_plus').show();
                });
            });
        }
    };

    woo_subcat.initilaize();
})(jQuery);

