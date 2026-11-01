<?php
/**
 * Store copy editable under Customize > Text & labels. Templates print
 * miroir_text( 'id' ) instead of a fixed string; the default is the
 * translatable lofi copy, so an untouched install reads exactly as drawn.
 * Accessibility-only labels stay plain __() strings.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Text & labels sections, in Customizer order.
 *
 * @return array id => title
 */
function miroir_text_sections() {
	return array(
		'header'      => __( 'Header & menu', 'miroir' ),
		'search'      => __( 'Search', 'miroir' ),
		'shop'        => __( 'Shop & collections', 'miroir' ),
		'empty'       => __( 'Empty results', 'miroir' ),
		'product'     => __( 'Product page', 'miroir' ),
		'states'      => __( 'Product states', 'miroir' ),
		'bag'         => __( 'Bag', 'miroir' ),
		'checkout'    => __( 'Checkout (WhatsApp review)', 'miroir' ),
		'sent'        => __( 'Order sent', 'miroir' ),
		'account'     => __( 'Account', 'miroir' ),
		'notices'     => __( 'Notice titles', 'miroir' ),
		'notfound'    => __( 'Page not found (404)', 'miroir' ),
		'maintenance' => __( 'Maintenance page', 'miroir' ),
	);
}

/**
 * Every editable string.
 *
 * @return array id => [ section, default, label? ]
 */
function miroir_texts() {
	static $texts = null;
	if ( null !== $texts ) {
		return $texts;
	}
	$texts = array(
		// Header & menu.
		'nav_search'      => array( 'header', __( 'search', 'miroir' ), __( 'Search link', 'miroir' ) ),
		'nav_account'     => array( 'header', __( 'my account', 'miroir' ), __( 'Account link', 'miroir' ) ),
		/* translators: %d: number of items in the bag. */
		'nav_bag'         => array( 'header', __( 'bag (%d)', 'miroir' ), __( 'Bag link (%d = item count)', 'miroir' ) ),
		'menu_title'      => array( 'header', __( 'Menu', 'miroir' ), __( 'Menu panel title', 'miroir' ) ),
		'menu_close'      => array( 'header', __( 'Close menu', 'miroir' ), __( 'Menu panel close button', 'miroir' ) ),
		/* translators: %s: current currency code. */
		'menu_currency'   => array( 'header', __( 'Currency · %s', 'miroir' ), __( 'Currency shortcut (%s = code)', 'miroir' ) ),

		// Search.
		'search_title'       => array( 'search', __( 'Search', 'miroir' ), __( 'Search panel title', 'miroir' ) ),
		'search_close'       => array( 'search', __( 'Close search', 'miroir' ), __( 'Close button', 'miroir' ) ),
		'search_label'       => array( 'search', __( 'Search products', 'miroir' ), __( 'Field label', 'miroir' ) ),
		'search_placeholder' => array( 'search', __( 'Linen shirt', 'miroir' ), __( 'Field placeholder', 'miroir' ) ),
		'search_submit'      => array( 'search', __( 'Search', 'miroir' ), __( 'Search button', 'miroir' ) ),
		'search_popular'     => array( 'search', __( 'POPULAR SEARCHES', 'miroir' ), __( 'Popular searches heading', 'miroir' ) ),

		// Maintenance page.
		'maint_status'    => array( 'maintenance', __( 'A LITTLE PAUSE', 'miroir' ), __( 'Status line', 'miroir' ) ),
		'maint_headline'  => array( 'maintenance', __( 'We’ll be back soon.', 'miroir' ), __( 'Headline', 'miroir' ) ),
		'maint_copy'      => array( 'maintenance', __( 'Our store is taking a short break for maintenance. Thank you for your patience while we make a few updates.', 'miroir' ), __( 'Message', 'miroir' ) ),
		'maint_label'     => array( 'maintenance', __( 'Already placed an order?', 'miroir' ), __( 'Order help heading', 'miroir' ) ),
		'maint_help'      => array( 'maintenance', __( 'Our team can still help with your existing order on WhatsApp.', 'miroir' ), __( 'Order help text', 'miroir' ) ),
		'maint_whatsapp'  => array( 'maintenance', __( 'Contact us on WhatsApp', 'miroir' ), __( 'WhatsApp button', 'miroir' ) ),
		'maint_retry'     => array( 'maintenance', __( 'Try again', 'miroir' ), __( 'Retry button', 'miroir' ) ),
		'maint_social'    => array( 'maintenance', __( 'Follow miroir on Instagram ↗', 'miroir' ), __( 'Instagram link', 'miroir' ) ),
		'maint_place'     => array( 'maintenance', __( 'Indonesia', 'miroir' ), __( 'Place after the copyright', 'miroir' ) ),

		// product.
		'prev_product' => array( 'product', __( 'Previous product', 'miroir' ), __( 'Previous product link', 'miroir' ) ),
		'next_product' => array( 'product', __( 'Next product', 'miroir' ), __( 'Next product link', 'miroir' ) ),
		'similar_title' => array( 'product', __( 'Find similar product', 'miroir' ), __( 'Similar products heading', 'miroir' ) ),
		'order_whatsapp' => array( 'product', __( 'Order via WhatsApp', 'miroir' ), __( 'WhatsApp order button (also the review page title)', 'miroir' ) ),
		'buy_hint' => array( 'product', __( 'Opens WhatsApp with this product, size and price filled in.', 'miroir' ), __( 'Hint under the WhatsApp button', 'miroir' ) ),
		'add_to_bag' => array( 'product', __( 'Add to bag', 'miroir' ), __( 'Add to bag button', 'miroir' ) ),
		'one_size' => array( 'product', __( 'One size', 'miroir' ), __( 'Size shown for products without sizes', 'miroir' ) ),
		'quantity_label' => array( 'product', __( 'Quantity', 'miroir' ), __( 'Label of the quantity selector', 'miroir' ) ),
		'out_of_stock' => array( 'product', __( 'Out of stock', 'miroir' ), __( 'Out of stock label', 'miroir' ) ),

		// states.
		'state_sold_out' => array( 'states', __( 'Sold out', 'miroir' ), __( 'Sold out: page title', 'miroir' ) ),
		'state_choose' => array( 'states', __( 'Choose a size', 'miroir' ), __( 'Choose a size: page title', 'miroir' ) ),
		'state_added' => array( 'states', __( 'Added to bag', 'miroir' ), __( 'Added to bag: page title', 'miroir' ) ),
		'added_notice' => array( 'states', __( 'Added to your bag', 'miroir' ), __( 'Added to bag: notice title', 'miroir' ) ),
		'added_close' => array( 'states', __( 'Close bag confirmation', 'miroir' ), __( 'Added to bag: close button', 'miroir' ) ),
		'added_error' => array( 'states', __( 'We could not confirm the update. Check your bag before trying again.', 'miroir' ), __( 'Added to bag: connection error', 'miroir' ) ),
		'state_added_note' => array( 'states', __( 'Shipping and the final total are confirmed in WhatsApp.', 'miroir' ), __( 'Added to bag: shipping note', 'miroir' ) ),
		'view_bag' => array( 'states', __( 'View bag', 'miroir' ), __( 'Added to bag: view bag button', 'miroir' ) ),
		'size_label' => array( 'states', __( 'Size *', 'miroir' ), __( 'Size field label', 'miroir' ) ),
		'sold_out_notice' => array( 'states', __( 'This size is sold out', 'miroir' ), __( 'Sold out: notice title', 'miroir' ) ),
		'sold_out_text' => array( 'states', __( 'Try another size or ask our team about availability.', 'miroir' ), __( 'Sold out: notice text', 'miroir' ) ),
		'ask_availability' => array( 'states', __( 'Ask about availability', 'miroir' ), __( 'Sold out: WhatsApp button', 'miroir' ) ),
		'select_size' => array( 'states', __( 'Select a size', 'miroir' ), __( 'Size select placeholder', 'miroir' ) ),
		'choose_notice' => array( 'states', __( 'Choose a size to continue', 'miroir' ), __( 'Choose a size: notice title', 'miroir' ) ),
		'choose_text' => array( 'states', __( 'Please select a size before adding this product to your bag.', 'miroir' ), __( 'Choose a size: notice text', 'miroir' ) ),
		'size_guide_link' => array( 'states', __( 'View size guide', 'miroir' ), __( 'Size guide link', 'miroir' ) ),

		// empty.
		'no_results' => array( 'empty', __( 'No results', 'miroir' ), __( 'Search without results: title', 'miroir' ) ),
		'no_results_text' => array( 'empty', __( 'Try a shorter search or check your spelling.', 'miroir' ), __( 'Search without results: text', 'miroir' ) ),
		'search_again' => array( 'empty', __( 'Search again', 'miroir' ), __( 'Search again button', 'miroir' ) ),
		'explore_all' => array( 'empty', __( 'Explore all products', 'miroir' ), __( 'Explore all products button', 'miroir' ) ),
		'no_matches' => array( 'empty', __( 'No matches', 'miroir' ), __( 'Filters without results: title', 'miroir' ) ),
		'clear_all' => array( 'empty', __( 'Clear all', 'miroir' ), __( 'Clear all filters link', 'miroir' ) ),
		'no_matches_heading' => array( 'empty', __( 'No products match your filters.', 'miroir' ), __( 'Filters without results: heading', 'miroir' ) ),
		'no_matches_text' => array( 'empty', __( 'Remove a filter or browse the full collection.', 'miroir' ), __( 'Filters without results: text', 'miroir' ) ),
		'clear_filters' => array( 'empty', __( 'Clear filters', 'miroir' ), __( 'Clear filters button', 'miroir' ) ),
		'empty_collection' => array( 'empty', __( 'Nothing in this collection yet.', 'miroir' ), __( 'Empty collection: heading', 'miroir' ) ),
		'empty_collection_text' => array( 'empty', __( 'Browse the full collection in the meantime.', 'miroir' ), __( 'Empty collection: text', 'miroir' ) ),
		'back_to_collection' => array( 'empty', __( 'Back to collection', 'miroir' ), __( 'Back to collection button', 'miroir' ) ),

		// shop.
		'load_more' => array( 'shop', __( 'Load more', 'miroir' ), __( 'Load more button', 'miroir' ) ),
		'filter_sort_link' => array( 'shop', __( 'Filter / sort +', 'miroir' ), __( 'Mobile filter link', 'miroir' ) ),
		'filter_colour' => array( 'shop', __( 'colour', 'miroir' ), __( 'Colour filter heading (sidebar)', 'miroir' ) ),
		'shop_title' => array( 'shop', __( 'shop women', 'miroir' ), __( 'Shop title and default sidebar heading', 'miroir' ) ),
		'filter_featured' => array( 'shop', __( 'featured', 'miroir' ), __( 'Default "featured" sidebar heading', 'miroir' ) ),
		'filters_title' => array( 'shop', __( 'Filter & sort', 'miroir' ), __( 'Filter panel title', 'miroir' ) ),
		'filters_close' => array( 'shop', __( 'Close filters', 'miroir' ), __( 'Filter panel close button', 'miroir' ) ),
		'sort_label' => array( 'shop', __( 'Sort by', 'miroir' ), __( 'Sort field label', 'miroir' ) ),
		'sort_date' => array( 'shop', __( 'Newest first', 'miroir' ), __( 'Sort: newest', 'miroir' ) ),
		'sort_popularity' => array( 'shop', __( 'Most popular', 'miroir' ), __( 'Sort: popularity', 'miroir' ) ),
		'sort_price' => array( 'shop', __( 'Price: low to high', 'miroir' ), __( 'Sort: price ascending', 'miroir' ) ),
		'sort_price_desc' => array( 'shop', __( 'Price: high to low', 'miroir' ), __( 'Sort: price descending', 'miroir' ) ),
		'filter_colour_panel' => array( 'shop', __( 'COLOUR', 'miroir' ), __( 'Colour filter heading (panel)', 'miroir' ) ),
		'filter_size_panel' => array( 'shop', __( 'SIZE', 'miroir' ), __( 'Size filter heading (panel)', 'miroir' ) ),

		// header.
		'currency_title' => array( 'header', __( 'Currency', 'miroir' ), __( 'Currency panel title', 'miroir' ) ),
		'currency_intro' => array( 'header', __( 'Choose your display currency. Our team confirms the final payable amount in WhatsApp.', 'miroir' ), __( 'Currency panel text', 'miroir' ) ),
		'currency_apply' => array( 'header', __( 'Apply currency', 'miroir' ), __( 'Currency apply button', 'miroir' ) ),
		'cancel' => array( 'header', __( 'Cancel', 'miroir' ), __( 'Cancel buttons', 'miroir' ) ),

		// bag.
		'stock_title' => array( 'bag', __( 'Check your bag', 'miroir' ), __( 'Stock problem: page title', 'miroir' ) ),
		'stock_notice' => array( 'bag', __( 'Your bag needs an update', 'miroir' ), __( 'Stock problem: notice title', 'miroir' ) ),
		'stock_gone' => array( 'bag', __( 'This item is no longer available. Remove it to continue.', 'miroir' ), __( 'Stock problem: item unavailable', 'miroir' ) ),
		'remove_item' => array( 'bag', __( 'Remove item', 'miroir' ), __( 'Stock problem: remove button', 'miroir' ) ),
		'stock_help' => array( 'bag', __( 'Continue to WhatsApp stays unavailable until your bag is updated.', 'miroir' ), __( 'Stock problem: help text', 'miroir' ) ),
		'bag_empty' => array( 'bag', __( 'Your bag is empty', 'miroir' ), __( 'Empty bag message', 'miroir' ) ),
		'return_to_shop' => array( 'bag', __( 'Return to shop', 'miroir' ), __( 'Empty bag button', 'miroir' ) ),
		'bag_title' => array( 'bag', __( 'Bag', 'miroir' ), __( 'Bag page title', 'miroir' ) ),
		'bag_updated' => array( 'bag', __( 'Bag updated.', 'miroir' ), __( 'Bag drawer: update confirmation', 'miroir' ) ),
		'bag_stock_error' => array( 'bag', __( 'The requested quantity of %s is unavailable. Please choose a smaller quantity.', 'miroir' ), __( 'Bag drawer: stock error (%s = product)', 'miroir' ) ),
		'col_product' => array( 'bag', __( 'product', 'miroir' ), __( 'Column: product', 'miroir' ) ),
		'col_price' => array( 'bag', __( 'price', 'miroir' ), __( 'Column: price', 'miroir' ) ),
		'col_quantity' => array( 'bag', __( 'quantity', 'miroir' ), __( 'Column: quantity', 'miroir' ) ),
		'col_subtotal' => array( 'bag', __( 'subtotal', 'miroir' ), __( 'Column: subtotal', 'miroir' ) ),
		'col_total' => array( 'bag', __( 'total', 'miroir' ), __( 'Column: total', 'miroir' ) ),
		'remove' => array( 'bag', __( 'Remove', 'miroir' ), __( 'Remove link', 'miroir' ) ),
		'bag_note_title' => array( 'bag', __( 'ordering via whatsapp', 'miroir' ), __( 'WhatsApp note: title', 'miroir' ) ),
		'bag_note' => array( 'bag', __( 'No online payment here. Your bag is sent to our team as a WhatsApp message; we confirm stock, shipping cost and any promo in the chat.', 'miroir' ), __( 'WhatsApp note: text', 'miroir' ) ),
		'bag_totals' => array( 'bag', __( 'bag totals', 'miroir' ), __( 'Totals heading', 'miroir' ) ),
		'subtotal' => array( 'bag', __( 'Subtotal', 'miroir' ), __( 'Subtotal row', 'miroir' ) ),
		'shipping' => array( 'bag', __( 'Shipping', 'miroir' ), __( 'Shipping row', 'miroir' ) ),
		'shipping_note' => array( 'bag', __( 'Confirmed in WhatsApp', 'miroir' ), __( 'Shipping value', 'miroir' ) ),
		'estimated_total' => array( 'bag', __( 'Estimated total', 'miroir' ), __( 'Estimated total row', 'miroir' ) ),
		'checkout_button' => array( 'bag', __( 'Checkout via WhatsApp', 'miroir' ), __( 'Checkout button', 'miroir' ) ),
		'continue_shopping' => array( 'bag', __( 'Continue shopping', 'miroir' ), __( 'Continue shopping links', 'miroir' ) ),

		// checkout.
		'checkout_intro' => array( 'checkout', __( 'Add your details and we\'ll open WhatsApp with your order written out. Our team confirms stock, shipping cost and payment in the chat.', 'miroir' ), __( 'Intro', 'miroir' ) ),
		'checkout_details' => array( 'checkout', __( 'your details', 'miroir' ), __( 'Section: your details', 'miroir' ) ),
		'checkout_name' => array( 'checkout', __( 'Full name *', 'miroir' ), __( 'Field: full name', 'miroir' ) ),
		'deliver_to' => array( 'checkout', __( 'deliver to', 'miroir' ), __( 'Section: deliver to', 'miroir' ) ),
		'checkout_country' => array( 'checkout', __( 'Country / Region *', 'miroir' ), __( 'Field: country', 'miroir' ) ),
		'checkout_city' => array( 'checkout', __( 'Town / City *', 'miroir' ), __( 'Field: town / city', 'miroir' ) ),
		'checkout_notes' => array( 'checkout', __( 'notes', 'miroir' ), __( 'Section: notes', 'miroir' ) ),
		'checkout_notes_label' => array( 'checkout', __( 'Order notes (optional)', 'miroir' ), __( 'Field: order notes', 'miroir' ) ),
		'checkout_notes_placeholder' => array( 'checkout', __( 'Size questions, gift wrap, preferred courier…', 'miroir' ), __( 'Order notes placeholder', 'miroir' ) ),
		'checkout_preview' => array( 'checkout', __( 'message preview', 'miroir' ), __( 'Section: message preview', 'miroir' ) ),
		'checkout_preview_hint' => array( 'checkout', __( 'Sent from your own WhatsApp', 'miroir' ), __( 'Message preview hint', 'miroir' ) ),
		'checkout_order' => array( 'checkout', __( 'your order', 'miroir' ), __( 'Order summary heading', 'miroir' ) ),
		'next_title' => array( 'checkout', __( 'What happens next', 'miroir' ), __( 'Next steps heading', 'miroir' ) ),
		'next_1' => array( 'checkout', __( 'Send the prefilled message in WhatsApp', 'miroir' ), __( 'Next step 1', 'miroir' ) ),
		'next_2' => array( 'checkout', __( 'We confirm stock and shipping cost', 'miroir' ), __( 'Next step 2', 'miroir' ) ),
		'next_3' => array( 'checkout', __( 'Pay by bank transfer, then we ship', 'miroir' ), __( 'Next step 3', 'miroir' ) ),
		'checkout_privacy' => array( 'checkout', __( 'WhatsApp opens in a new tab with this message. Nothing is charged on this site.', 'miroir' ), __( 'Note above the button', 'miroir' ) ),
		'checkout_submit' => array( 'checkout', __( 'Continue to WhatsApp', 'miroir' ), __( 'Submit button', 'miroir' ) ),

		// sent.
		'sent_missing' => array( 'sent', __( 'We could not find this order.', 'miroir' ), __( 'Order not found', 'miroir' ) ),
		'sent_title' => array( 'sent', __( 'Opening WhatsApp…', 'miroir' ), __( 'Page title', 'miroir' ) ),
		'sent_lead' => array( 'sent', __( 'Your order message is ready. Press send in WhatsApp so our team can confirm stock and shipping.', 'miroir' ), __( 'Lead text', 'miroir' ) ),
		'sent_retry' => array( 'sent', __( 'WhatsApp didn\'t open? Try again', 'miroir' ), __( 'Retry button', 'miroir' ) ),
		'sent_items' => array( 'sent', __( 'items', 'miroir' ), __( 'Summary: items', 'miroir' ) ),
		'sent_prepared' => array( 'sent', __( 'prepared', 'miroir' ), __( 'Summary: prepared', 'miroir' ) ),
		'sent_total' => array( 'sent', __( 'estimated total', 'miroir' ), __( 'Summary: estimated total', 'miroir' ) ),
		'sent_payment_title' => array( 'sent', __( 'payment, after we confirm in chat', 'miroir' ), __( 'Payment heading', 'miroir' ) ),
		'sent_payment_text' => array( 'sent', __( 'Transfer the confirmed total (product + shipping), then send the receipt in the same WhatsApp chat.', 'miroir' ), __( 'Payment instructions', 'miroir' ) ),
		'sent_summary' => array( 'sent', __( 'your order summary', 'miroir' ), __( 'Order summary heading', 'miroir' ) ),
		'payment' => array( 'sent', __( 'Payment', 'miroir' ), __( 'Payment row', 'miroir' ) ),
		'payment_default' => array( 'sent', __( 'Bank transfer after confirmation', 'miroir' ), __( 'Payment method fallback', 'miroir' ) ),
		'sent_fallback_title' => array( 'sent', __( 'Open WhatsApp', 'miroir' ), __( 'WhatsApp did not open: title', 'miroir' ) ),
		'sent_fallback_notice' => array( 'sent', __( 'Your message is ready', 'miroir' ), __( 'WhatsApp did not open: notice title', 'miroir' ) ),
		'sent_fallback_text' => array( 'sent', __( 'We couldn’t open WhatsApp. Your order has not been confirmed or sent by this website.', 'miroir' ), __( 'WhatsApp did not open: notice text', 'miroir' ) ),
		'sent_fallback_retry' => array( 'sent', __( 'Open WhatsApp again', 'miroir' ), __( 'WhatsApp did not open: retry button', 'miroir' ) ),
		'sent_copy' => array( 'sent', __( 'Copy order message', 'miroir' ), __( 'Copy message button', 'miroir' ) ),
		'contact_care' => array( 'sent', __( 'Contact customer care', 'miroir' ), __( 'Customer care button', 'miroir' ) ),
		'sent_copied' => array( 'sent', __( 'Message copied. Paste it into your WhatsApp conversation.', 'miroir' ), __( 'Copied confirmation', 'miroir' ) ),

		// account.
		'acc_orders' => array( 'account', __( 'Orders', 'miroir' ), __( 'Orders', 'miroir' ) ),
		'acc_orders_text' => array( 'account', __( 'View orders recorded by our team.', 'miroir' ), __( 'Dashboard: orders text', 'miroir' ) ),
		'acc_orders_link' => array( 'account', __( 'View orders', 'miroir' ), __( 'Dashboard: orders link', 'miroir' ) ),
		'acc_addresses' => array( 'account', __( 'Addresses', 'miroir' ), __( 'Dashboard: addresses', 'miroir' ) ),
		'acc_addresses_text' => array( 'account', __( 'Manage your billing and delivery details.', 'miroir' ), __( 'Dashboard: addresses text', 'miroir' ) ),
		'acc_addresses_link' => array( 'account', __( 'View addresses', 'miroir' ), __( 'Dashboard: addresses link', 'miroir' ) ),
		'acc_details' => array( 'account', __( 'Account details', 'miroir' ), __( 'Account details', 'miroir' ) ),
		'acc_details_text' => array( 'account', __( 'Update your name, email and password.', 'miroir' ), __( 'Dashboard: details text', 'miroir' ) ),
		'acc_details_link' => array( 'account', __( 'View account details', 'miroir' ), __( 'Dashboard: details link', 'miroir' ) ),
		'acc_intro' => array( 'account', __( 'Manage your details, saved addresses and orders here.', 'miroir' ), __( 'Dashboard intro', 'miroir' ) ),
		'acc_change_password' => array( 'account', __( 'Change password', 'miroir' ), __( 'Change password heading', 'miroir' ) ),
		'acc_change_password_text' => array( 'account', __( 'Leave these fields blank to keep your current password.', 'miroir' ), __( 'Change password text', 'miroir' ) ),
		'pw_updated' => array( 'account', __( 'Password updated', 'miroir' ), __( 'Password updated: title', 'miroir' ) ),
		'pw_updated_text' => array( 'account', __( 'Your password has been changed.', 'miroir' ), __( 'Password updated: text', 'miroir' ) ),
		'pw_updated_help' => array( 'account', __( 'You can now sign in with your new password.', 'miroir' ), __( 'Password updated: help', 'miroir' ) ),
		'log_in' => array( 'account', __( 'Log in', 'miroir' ), __( 'Log in buttons', 'miroir' ) ),
		'register_title' => array( 'account', __( 'Create account', 'miroir' ), __( 'Register: title', 'miroir' ) ),
		'register_intro' => array( 'account', __( 'Create an account to keep your details in one place.', 'miroir' ), __( 'Register: intro', 'miroir' ) ),
		'privacy_link' => array( 'account', __( 'Privacy policy', 'miroir' ), __( 'Privacy policy link text', 'miroir' ) ),
		'register_login' => array( 'account', __( 'Already registered? Log in', 'miroir' ), __( 'Register: log in link', 'miroir' ) ),
		'login_title' => array( 'account', __( 'Welcome', 'miroir' ), __( 'Log in: title', 'miroir' ) ),
		'login_lost' => array( 'account', __( 'Lost your password?', 'miroir' ), __( 'Log in: lost password link', 'miroir' ) ),
		'login_remember' => array( 'account', __( 'Remember me', 'miroir' ), __( 'Log in: remember me', 'miroir' ) ),
		'login_register' => array( 'account', __( 'Register', 'miroir' ), __( 'Log in: register button', 'miroir' ) ),
		'lost_title' => array( 'account', __( 'Forgot password', 'miroir' ), __( 'Forgot password: title', 'miroir' ) ),
		'lost_intro' => array( 'account', __( 'Enter your account email. We’ll send you a link to choose a new password.', 'miroir' ), __( 'Forgot password: intro', 'miroir' ) ),
		'back_to_login' => array( 'account', __( 'Back to log in', 'miroir' ), __( 'Back to log in buttons', 'miroir' ) ),
		'reset_title' => array( 'account', __( 'Set new password', 'miroir' ), __( 'Reset password: title', 'miroir' ) ),
		'reset_intro' => array( 'account', __( 'Choose a new password for your account.', 'miroir' ), __( 'Reset password: intro', 'miroir' ) ),
		'reset_help' => array( 'account', __( 'Use at least 12 characters. Avoid reusing a password from another account.', 'miroir' ), __( 'Reset password: help', 'miroir' ) ),
		'reset_sent_title' => array( 'account', __( 'Reset email sent', 'miroir' ), __( 'Reset email sent: title', 'miroir' ) ),
		'reset_sent_intro' => array( 'account', __( 'Check your inbox', 'miroir' ), __( 'Reset email sent: intro', 'miroir' ) ),
		'reset_sent_help' => array( 'account', __( 'If an account exists for this email, you’ll receive a password reset link. Check your spam folder too.', 'miroir' ), __( 'Reset email sent: help', 'miroir' ) ),
		'reset_sent_resend' => array( 'account', __( 'Resend reset email', 'miroir' ), __( 'Reset email sent: resend button', 'miroir' ) ),
		'my_account' => array( 'account', __( 'My account', 'miroir' ), __( 'My account title', 'miroir' ) ),
		'orders_empty' => array( 'account', __( 'No orders to show yet.', 'miroir' ), __( 'No orders: heading', 'miroir' ) ),
		'orders_empty_text' => array( 'account', __( 'Orders appear here once recorded by our team. If you have ordered through WhatsApp, contact us for an update.', 'miroir' ), __( 'No orders: text', 'miroir' ) ),
		'orders_empty_shop' => array( 'account', __( 'Explore the collection', 'miroir' ), __( 'No orders: shop button', 'miroir' ) ),
		'orders_empty_ask' => array( 'account', __( 'Ask about an existing order', 'miroir' ), __( 'No orders: ask button', 'miroir' ) ),
		'acc_edit_address' => array( 'account', __( 'Edit address', 'miroir' ), __( 'Edit address title', 'miroir' ) ),
		'acc_my_addresses' => array( 'account', __( 'My addresses', 'miroir' ), __( 'My addresses title', 'miroir' ) ),
		'acc_order_details' => array( 'account', __( 'Order details', 'miroir' ), __( 'Order details title', 'miroir' ) ),
		'acc_no_address' => array( 'account', __( 'You have not saved an address yet.', 'miroir' ), __( 'No saved address', 'miroir' ) ),
		'billing_address' => array( 'account', __( 'Billing address', 'miroir' ), __( 'Billing address', 'miroir' ) ),
		'shipping_address' => array( 'account', __( 'Shipping address', 'miroir' ), __( 'Shipping address', 'miroir' ) ),
		'orders_none' => array( 'account', __( 'No order has been made yet.', 'miroir' ), __( 'Orders table: none', 'miroir' ) ),
		'order_confirm_note' => array( 'account', __( 'Our team will confirm payment and shipping in WhatsApp.', 'miroir' ), __( 'Order details: note', 'miroir' ) ),
		'order_items' => array( 'account', __( 'Items', 'miroir' ), __( 'Order details: items heading', 'miroir' ) ),
		'order_awaiting' => array( 'account', __( 'Awaiting confirmation', 'miroir' ), __( 'Order details: shipping awaiting', 'miroir' ) ),
		'order_paid' => array( 'account', __( 'Bank transfer · paid', 'miroir' ), __( 'Order details: paid', 'miroir' ) ),
		'order_pending' => array( 'account', __( 'Bank transfer · pending', 'miroir' ), __( 'Order details: pending', 'miroir' ) ),
		'order_delivery' => array( 'account', __( 'Delivery details', 'miroir' ), __( 'Order details: delivery heading', 'miroir' ) ),
		'contact_whatsapp' => array( 'account', __( 'Contact us on WhatsApp', 'miroir' ), __( 'Order details: WhatsApp button', 'miroir' ) ),
		'back_to_orders' => array( 'account', __( 'Back to orders', 'miroir' ), __( 'Order details: back button', 'miroir' ) ),

		// notices.
		'notice_login' => array( 'notices', __( 'Incorrect login', 'miroir' ), __( 'Wrong email or password', 'miroir' ) ),
		'notice_saved' => array( 'notices', __( 'Changes saved', 'miroir' ), __( 'Details or address saved', 'miroir' ) ),
		'notice_reset' => array( 'notices', __( 'Reset link expired', 'miroir' ), __( 'Invalid password reset link', 'miroir' ) ),
		'notice_removed' => array( 'notices', __( 'Removed from your bag', 'miroir' ), __( 'Item removed', 'miroir' ) ),
		'notice_error' => array( 'notices', __( 'Please check', 'miroir' ), __( 'Other errors', 'miroir' ) ),
		'notice_success' => array( 'notices', __( 'Done', 'miroir' ), __( 'Other confirmations', 'miroir' ) ),
		'notice_info' => array( 'notices', __( 'Note', 'miroir' ), __( 'Other notes', 'miroir' ) ),

		// notfound.
		'nf_title' => array( 'notfound', __( 'Page not found', 'miroir' ), __( 'Title', 'miroir' ) ),
		'nf_text' => array( 'notfound', __( '404 — this page is no longer here. The link may have changed or the page may have been removed.', 'miroir' ), __( 'Text', 'miroir' ) ),
		'nf_placeholder' => array( 'notfound', __( 'Search by product name', 'miroir' ), __( 'Search placeholder', 'miroir' ) ),

		// Texts with placeholders filled in by the store.
		/* translators: %d: quantity. */
		'state_quantity'      => array( 'states', __( 'Quantity %d', 'miroir' ), __( 'Added to bag: quantity (%d = quantity)', 'miroir' ) ),
		/* translators: 1: number of items, 2: bag subtotal. */
		'state_bag_line'      => array( 'states', __( 'Bag (%1$d) · Subtotal %2$s', 'miroir' ), __( 'Added to bag: bag line (%1$d = items, %2$s = subtotal)', 'miroir' ) ),
		/* translators: %s: size. */
		'size_unavailable'    => array( 'states', __( '%s · unavailable', 'miroir' ), __( 'Sold out: size option (%s = size)', 'miroir' ) ),
		/* translators: %s: search terms. */
		'no_results_for'      => array( 'empty', __( 'No results for “%s”.', 'miroir' ), __( 'Search without results: heading (%s = search)', 'miroir' ) ),
		/* translators: 1: requested quantity, 2: available quantity. */
		'stock_counts'        => array( 'bag', __( 'Requested: %1$d · Available: %2$d', 'miroir' ), __( 'Stock problem: counts (%1$d requested, %2$d available)', 'miroir' ) ),
		/* translators: %d: available quantity. */
		'stock_update'        => array( 'bag', __( 'Update quantity to %d', 'miroir' ), __( 'Stock problem: update button (%d = available)', 'miroir' ) ),
		/* translators: %s: bank name. */
		'bank_name'           => array( 'sent', __( 'Bank name: %s', 'miroir' ), __( 'Bank line: name (%s from WooCommerce > Payments > Direct bank transfer)', 'miroir' ) ),
		/* translators: %s: account holder. */
		'bank_account_name'   => array( 'sent', __( 'Account name: %s', 'miroir' ), __( 'Bank line: account name (%s)', 'miroir' ) ),
		/* translators: %s: account number. */
		'bank_account_number' => array( 'sent', __( 'Account number: %s', 'miroir' ), __( 'Bank line: account number (%s)', 'miroir' ) ),
		/* translators: %s: customer first name. */
		'acc_hello'           => array( 'account', __( 'Hello, %s.', 'miroir' ), __( 'Dashboard greeting (%s = first name)', 'miroir' ) ),
		/* translators: %s: privacy policy link. */
		'register_help'       => array( 'account', __( 'Use at least 12 characters. Your personal data is used to manage your account. Read our %s.', 'miroir' ), __( 'Register: help (%s = privacy policy link)', 'miroir' ) ),
		/* translators: %s: order number. */
		'order_number'        => array( 'account', __( 'Order #%s', 'miroir' ), __( 'Order details: heading (%s = number)', 'miroir' ) ),
	);
	return $texts;
}

/**
 * Saved texts: Customizer values, or Elementor Site Settings > Miroir > Labels
 * while Elementor manages the site (filter in inc/elementor/kit-tab.php).
 *
 * @return array Text id => text.
 */
function miroir_saved_texts() {
	return (array) apply_filters( 'miroir_saved_texts', (array) get_theme_mod( 'miroir_text', array() ) );
}

/**
 * Renders with some texts replaced, e.g. the texts set on an Elementor widget
 * for the section it prints. Scopes nest; the innermost wins.
 *
 * @param array    $texts  Text id => text; empty texts are ignored.
 * @param callable $render Prints or returns the section.
 * @return mixed What $render returns.
 */
function miroir_with_texts( array $texts, callable $render ) {
	$GLOBALS['miroir_text_scopes'][] = array_filter( array_map( 'strval', $texts ), fn( $text ) => '' !== trim( $text ) );
	try {
		return $render();
	} finally {
		array_pop( $GLOBALS['miroir_text_scopes'] );
	}
}

/**
 * An editable string: a section's own text (miroir_with_texts()), the saved
 * value, or its default.
 *
 * @param string $id      Text id from miroir_texts().
 * @param mixed  ...$args sprintf() arguments for %s / %d placeholders.
 * @return string Unescaped text.
 */
function miroir_text( $id, ...$args ) {
	$texts   = miroir_texts();
	$default = $texts[ $id ][1] ?? $id;
	$mods    = miroir_saved_texts();
	$text    = isset( $mods[ $id ] ) && '' !== trim( (string) $mods[ $id ] ) ? (string) $mods[ $id ] : $default;
	foreach ( array_reverse( $GLOBALS['miroir_text_scopes'] ?? array() ) as $scope ) {
		if ( isset( $scope[ $id ] ) ) {
			$text = $scope[ $id ];
			break;
		}
	}
	if ( ! $args ) {
		return $text;
	}
	try {
		return vsprintf( $text, $args );
	} catch ( ValueError | ArgumentCountError $e ) {
		// An edited text lost or added a placeholder: fall back to the default.
		return vsprintf( $default, $args );
	}
}
