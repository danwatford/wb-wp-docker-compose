<?php
/**
 * Plugin Name: Prevent Shipping of Non-Courier Products
 * Description: Prevent shipping of Non-Courier shipping-class products outside of local shipping zone or using a shipping method other than Local Pickup.
 * Version: 0.1
 * Author: Watford Consulting Ltd.
 * Author URI: https://www.watfordconsulting.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * {Plugin Name} is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * {Plugin Name} is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with {Plugin Name}. If not, see {License URI}.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Check for attempts to purchase a NonCourier class product when shipping to a non-local zone or when not using
 * the Local Pickup shipping method.
 */
add_action( 'woocommerce_after_checkout_validation',function ($data, $errors) {

    // Find the shipping class ID for products that can only be delivered locally.
    $nonCourierClassId = -1;
    foreach ( WC()->shipping()->get_shipping_classes() as $index => $shippingClass) {
        if ($shippingClass->slug == 'noncourier') {
            $nonCourierClassId = $shippingClass->term_id;
            break;
        }
    }

    // Check shipping packages to see whether they are going to be shipped to a non-local shipping zone. If so ensure
    // the packages don't contain any non-courier shippable products.
    $nonCourierItemFoundForShipping = false;
    $nonCourierItemsList = array();
    $cart = WC()->cart;

    // Get array of WC_Shipping_Rate objects corresponding to the shipping packages in the cart.
    $calculatedShippingMethods = $cart->calculate_shipping();

    $shipping_packages = $cart->get_shipping_packages();
    foreach($shipping_packages as $index => $shippingPackage) {
        // Names of 'local' shipping zone - e.g. those with CT, ME or TN postcodes - are prefixed with 'Local'.
        $localZonePrefix = 'Local';
        $wcShippingZone = wc_get_shipping_zone($shippingPackage);
        if (strncmp($wcShippingZone->get_zone_name(), $localZonePrefix, strlen($localZonePrefix)) !== 0) {
            // Shipping zone isn't local. We'll need to check products in the package if Local Pickup isn't used.
            if ($calculatedShippingMethods[$index]->get_method_id() !== 'local_pickup') {
                // Local pickup isn't being used. Check for the non-courier shipping class for products in this package.
                foreach ($shippingPackage['contents'] as $packageLineItemId => $packageLineItem) {
                    if ($packageLineItem['data']->get_shipping_class_id() === $nonCourierClassId) {
                        $nonCourierItemFoundForShipping = true;
                        $nonCourierItemsList[] = $packageLineItem['data']->get_name();
                    }
                }
            }
        }
    }

    if ($nonCourierItemFoundForShipping) {
        $cart_url = wc_get_cart_url();
        $msg = '
<p>Unfortunately one or more products in your shopping basket are limited to local delivery or must be picked up directly from the brewery.
This is usually the case for 10 Litre and 20 Litre Polypins.</p>
<p>Please change the delivery address, use the Local Pickup shipping option or remove the following items from
 <a href="' . $cart_url . '">your shopping basket</a>:</p>
';
        $errors->add('shipping', $msg . '<ul><li>'. implode('</li><li>', $nonCourierItemsList) .'</li></ul>');
    }
}, 10, 2);
