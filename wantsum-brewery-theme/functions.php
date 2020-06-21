<?php
add_action('wp_enqueue_scripts', 'wantsum_brewery_theme_enqueue_styles');
function wantsum_brewery_theme_enqueue_styles()
{
    // Load the Wantsum Brewery theme's style with a dependency on its parent style.
    $parent_style = 'storefront-style';
    wp_enqueue_style('wantsum-brewery-style', get_stylesheet_uri(), array($parent_style), wp_get_theme()->get('Version'));
}

// Enqueue our stylesheet for use with the WooCommerce Delivery Note plugin.
add_action('wcdn_head', 'wantsum_brewery_theme_enqueue_styles_for_wc_delivery_note');
function wantsum_brewery_theme_enqueue_styles_for_wc_delivery_note()
{
    // Seems that enqueuing our stylesheet doesn't work. Instead we need to print the link to the stylesheet.
    //wp_enqueue_style('wantsum-brewery-style', get_stylesheet_uri());
    $stylesheetUriHref = esc_url(get_stylesheet_uri());
    echo '<link rel="stylesheet" href="' . $stylesheetUriHref . '" type="text/css" media="screen,print" />';
}

add_action('after_setup_theme', 'wantsum_brewery_theme_add_woocommerce_support');
function wantsum_brewery_theme_add_woocommerce_support()
{
    add_theme_support('woocommerce');
}

// Enable woocommerce product galleries.
add_theme_support('wc-product-gallery-zoom');
add_theme_support('wc-product-gallery-lightbox');
add_theme_support('wc-product-gallery-slider');

// Uncomment to print template path at bottom of page.
//add_action('wp_footer', 'meks_which_template_is_loaded');
//function meks_which_template_is_loaded()
//{
//    if (is_super_admin()) {
//        global $template;
//        print_r($template);
//    }
//}

// Inhibit display of WooCommerce & Storefront links in footer.
add_filter('storefront_credit_link', '__return_false');

// Add credits for site design and ecommerce.
add_filter('storefront_credit_links_output', 'wantsum_brewery_theme_add_credit_links');
function wantsum_brewery_theme_add_credit_links($links_output)
{
    return $links_output . 'Website Designed by Andrew Dudley<br/>Ecommerce by <a href="https://watfordconsulting.com" target="_blank">Watford Consulting Ltd.</a>';
}

// Remove the link to the users account page from the footer on hand held devices.
add_filter('storefront_handheld_footer_bar_links', 'wantsum_brewery_theme_remove_handheld_footer_account_link');
function wantsum_brewery_theme_remove_handheld_footer_account_link($links)
{
    unset($links['my-account']);
    return $links;
}
