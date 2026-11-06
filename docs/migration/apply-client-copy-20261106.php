<?php
// Client copy for About us and Studio location. Safe to re-run; use on each environment: wp eval-file <file>.
$about = array(
	'Miroir is an Indonesian independent fashion label based in Bandung, known for blending modern silhouettes with romantic, vintage-inspired undertones.',
	'Founded on March 28, 2013, by designer Kania Rahim, the brand focuses on crafting timeless, feminine apparel featuring intricate details like floral embroideries, unique textures, and relaxed tailoring.',
	'Its signature tagline, “Believe in the shadow,” expresses the philosophy that clothing acts as a reflection and shadow of one’s personal identity.',
);

$page = get_page_by_path( 'about' );
if ( ! $page ) {
	WP_CLI::error( 'No about page.' );
}
$html   = implode( '', array_map( fn( $p ) => '<p class="wp-block-paragraph">' . esc_html( $p ) . '</p>', $about ) );
$blocks = implode( "\n\n", array_map( fn( $p ) => "<!-- wp:paragraph -->\n<p>" . esc_html( $p ) . "</p>\n<!-- /wp:paragraph -->", $about ) );

$data = json_decode( (string) get_post_meta( $page->ID, '_elementor_data', true ), true );
$set  = false;
if ( is_array( $data ) ) {
	foreach ( $data as &$container ) {
		foreach ( $container['elements'] as &$widget ) {
			if ( 'miroir-text-page' === ( $widget['widgetType'] ?? '' ) ) {
				$widget['settings']['text'] = $html;
				$set                        = true;
			}
		}
	}
	unset( $container, $widget );
}
if ( $set ) {
	update_post_meta( $page->ID, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
	update_post_meta( $page->ID, '_miroir_blocks_before_elementor', wp_slash( $blocks ) );
} else {
	WP_CLI::warning( 'About page has no Elementor text widget; updating post content only.' );
}
wp_update_post( array( 'ID' => $page->ID, 'post_title' => 'About us', 'post_content' => wp_slash( $blocks ) ) );

$studio = get_page_by_path( 'studio-location' );
if ( ! $studio ) {
	WP_CLI::error( 'No studio-location page. Run wp miroir theme setup first.' );
}
$content = strtr(
	$studio->post_content,
	array(
		'Visit the Miroir studio to see the collection in person.' => 'Visit the Miroir studio in Bandung, by appointment only.',
		'[Studio address to be confirmed.]' => 'Jl. Nayaga no 6, Bandung',
		'[Opening hours to be confirmed.]'  => 'By appointment only<br />Monday–Friday: 9AM–2PM<br />Saturday: 9AM–12PM<br />Sunday: Off',
		'Ask us on WhatsApp'                => 'Book an appointment on WhatsApp',
	)
);
wp_update_post( array( 'ID' => $studio->ID, 'post_content' => wp_slash( $content ) ) );

if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}
WP_CLI::success( 'Client copy applied.' );
