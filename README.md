# WooCommerce Custom Min/Max Quantity Rules by Stock & Categories (WoodMart)

This snippet allows you to **enforce minimum, maximum, and step-based quantities** in WooCommerce **per product and per category**, while maintaining full **WoodMart theme compatibility** and AJAX cart support.

It ensures users can only add quantities that match stock rules and category-specific step requirements.

---

## Features

1. **Category-Based Rules**

   * Example: Categories 402 & 323 → step 2, min 2, max = stock
   * All other products → default min 1, step 1, max = stock

2. **Stock-Aware Rules**

   * Automatically adjusts quantities based on **available stock**.

3. **Cart Validation**

   * Checks quantities in the cart and displays errors for invalid amounts.
   * Prevents users from bypassing quantity rules.

4. **Automatic Quantity Adjustment**

   * Adjusts cart quantities to nearest valid step automatically.

5. **WoodMart AJAX Support**

   * Disables “+” buttons when max quantity is reached
   * Makes quantity inputs read-only when at max
   * Re-applies rules after mini-cart/cart updates and WooCommerce AJAX refreshes

---

## Installation

1. Add the **PHP snippet** to your **child theme `functions.php`** or a **custom plugin**.
2. The **JavaScript** is included automatically in the footer via `wp_footer`.
3. Clear any caching plugins to ensure changes take effect.

---

## How It Works (Short Version)

1. Uses `woocommerce_quantity_input_args` to define **min, max, step, and default quantity**, with category conditions.
2. Validates cart quantities using `woocommerce_check_cart_items`.
3. Adjusts quantities dynamically on `woocommerce_before_calculate_totals` to match the correct step.
4. Handles WoodMart AJAX events to **disable buttons and enforce max values** in mini-cart and cart page.

---

## Example Rules

| Category   | Stock ≥ 2 | Min | Step | Max   | Notes                 |
| ---------- | --------- | --- | ---- | ----- | --------------------- |
| 402, 323   | Yes       | 2   | 2    | Stock | Step of 2 enforced    |
| All others | Yes       | 1   | 1    | Stock | Standard step         |
| Any        | 1         | 1   | 1    | 1     | Single stock products |

---

## Compatibility

* WooCommerce
* WoodMart Theme
* PHP 7.4+ recommended
* Supports AJAX mini-cart and cart updates

---

## Notes

* Cart validation prevents manual input bypass.
* Step enforcement ensures category-specific rules are maintained.
* Fallback `setInterval` ensures quantities stay in range even if AJAX events fail.

---

## Author

**Md Mamun Miah / Webzlo**

* [Webzlo](https://webzlo.com)

---

## License

MIT — free to use, modify, and distribute.

---

**One-line Summary:**
Enforces category- and stock-based minimum, maximum, and step quantities in WooCommerce, fully compatible with WoodMart and AJAX cart updates.

Do you want me to do that?

