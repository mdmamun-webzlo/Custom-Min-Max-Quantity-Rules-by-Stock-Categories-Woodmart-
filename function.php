<?php
/**
 * ==============================================
 *  Custom Min/Max Quantity Rules (Woodmart Ready)
 *  Author: Md Mamun Miah / Webzlo
 * ==============================================
 */

/**
 * Set min/max quantity rules per product (with category conditions)
 */
add_filter('woocommerce_quantity_input_args', 'webzlo_min_max_quantity_rules', 10, 2);
function webzlo_min_max_quantity_rules($args, $product) {
    $stock = $product->get_stock_quantity();
    $product_cats = $product->get_category_ids();

    // ✅ Condition 1 categories: step 2, min 2
    $condition1_cats = array(402, 323);
    $is_condition1 = array_intersect($product_cats, $condition1_cats);

    // Get current cart quantity for this product
    $cart_qty = 0;
    foreach (WC()->cart->get_cart() as $cart_item) {
        if ($cart_item['product_id'] == $product->get_id()) {
            $cart_qty = $cart_item['quantity'];
            break;
        }
    }

    if ($stock >= 2 && $is_condition1) {
        $args['min_value']   = 2;
        $args['max_value']   = $stock;
        $args['step']        = 2;
        $args['input_value'] = $cart_qty > 0 ? $cart_qty : 2;
    } elseif ($stock >= 1) {
        $args['min_value']   = 1;
        $args['max_value']   = $stock;
        $args['step']        = 1;
        $args['input_value'] = $cart_qty > 0 ? $cart_qty : 1;
    }

    return $args;
}

/**
 * Validate cart quantities
 */
add_action('woocommerce_check_cart_items', 'webzlo_validate_cart_quantity');
function webzlo_validate_cart_quantity() {
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        $stock   = $product->get_stock_quantity();
        $qty     = $cart_item['quantity'];
        $product_cats = $product->get_category_ids();

        $condition1_cats = array(402, 323);
        $is_condition1 = array_intersect($product_cats, $condition1_cats);

        if ($stock >= 2 && $is_condition1) {
            if ($qty < 2 || $qty > $stock || $qty % 2 != 0) {
                wc_add_notice(sprintf(
                    __('The quantity for "%s" must be in steps of 2 and cannot exceed available stock (%d).', 'woocommerce'),
                    $product->get_name(),
                    $stock
                ), 'error');
            }
        } elseif ($qty > $stock) {
            wc_add_notice(sprintf(
                __('The quantity for "%s" cannot exceed available stock (%d).', 'woocommerce'),
                $product->get_name(),
                $stock
            ), 'error');
        }
    }
}

/**
 * Adjust cart quantities automatically
 */
add_action('woocommerce_before_calculate_totals', 'webzlo_adjust_cart_quantities');
function webzlo_adjust_cart_quantities($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        $stock   = $product->get_stock_quantity();
        $qty     = $cart_item['quantity'];
        $product_cats = $product->get_category_ids();

        $condition1_cats = array(402, 323);
        $is_condition1 = array_intersect($product_cats, $condition1_cats);

        if ($stock >= 2 && $is_condition1) {
            $step = 2;
            $new_qty = floor($qty / $step) * $step;
            if ($new_qty < 2) $new_qty = 2;
            if ($new_qty > $stock) $new_qty = $stock;
            if ($qty !== $new_qty) {
                $cart->set_quantity($cart_item_key, $new_qty);
            }
        } elseif ($stock >= 1 && $qty > $stock) {
            $cart->set_quantity($cart_item_key, $stock);
        }
    }
}

/**
 * Woodmart AJAX Re-bind JS for Quantity Step + Stock Handling
 */
add_action('wp_footer', 'webzlo_woodmart_disable_plus_stock');
function webzlo_woodmart_disable_plus_stock() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($){

        function disablePlusButtonsWoodmart() {
            // Handle mini-cart and cart page
            $('.woocommerce-mini-cart .mini_cart_item, .woocommerce-cart .cart_item').each(function(){
                var $item = $(this);
                var $qtyInput = $item.find('input.qty');
                var $plusBtn = $item.find('input.plus');

                if($qtyInput.length && $plusBtn.length){
                    var qty = parseInt($qtyInput.val(), 10);
                    var max = parseInt($qtyInput.attr('max'), 10);

                    if(!isNaN(max) && qty >= max){
                        $plusBtn.prop('disabled', true).addClass('disabled');
                        $qtyInput.prop('readonly', true);
                    } else {
                        $plusBtn.prop('disabled', false).removeClass('disabled');
                        $qtyInput.prop('readonly', false);
                    }
                }
            });
        }

        // Initial run
        disablePlusButtonsWoodmart();

        // Run after Woodmart AJAX updates
        $(document).on('woodmart-ajax-loaded woodmartUpdateCart woodmartThemeModuleReinit', function(){
            disablePlusButtonsWoodmart();
        });

        // WooCommerce AJAX fragments refresh
        $(document.body).on('wc_fragments_refreshed updated_wc_div updated_cart_totals', function(){
            disablePlusButtonsWoodmart();
        });

        // Optional fallback: check periodically
        setInterval(disablePlusButtonsWoodmart, 2000);

    });
    </script>
    <?php
}
