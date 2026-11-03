<?php
/** Idempotent homepage upgrade. Run with: wp eval-file /tmp/apply-video-hero.php */
defined( 'ABSPATH' ) || exit;

( static function () {
	$home = (int) get_option( 'page_on_front' );
	if ( ! $home ) { WP_CLI::error( 'No front page is configured.' ); }
	if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'miroir/video-hero' ) ) {
		WP_CLI::error( 'Activate the updated Miroir theme first.' );
	}
	$is_hero = static fn( $caption ) => 'new arrivals: browse the collection' === trim( (string) $caption );
	$is_removed = static fn( $caption ) => in_array( trim( (string) $caption ), array( 'soft structure: stripes for every day', 'shop women: summer collection' ), true );
	$raw = (string) get_post_meta( $home, '_elementor_data', true );
	$fallback = (string) get_post_meta( $home, '_miroir_blocks_before_elementor', true );
	$content = (string) get_post_field( 'post_content', $home );
	if ( ! metadata_exists( 'post', $home, '_miroir_before_video_hero' ) ) {
		update_post_meta( $home, '_miroir_before_video_hero', wp_slash( array( 'elementor' => $raw, 'fallback' => $fallback, 'content' => $content ) ) );
	}
	$walk = static function ( $nodes ) use ( &$walk, $is_hero, $is_removed ) {
		$out = array();
		foreach ( $nodes as $node ) {
			if ( 'miroir-editorial' === ( $node['widgetType'] ?? '' ) ) {
				$caption = $node['settings']['caption'] ?? '';
				if ( $is_removed( $caption ) ) { continue; }
				if ( $is_hero( $caption ) ) {
					$node['widgetType'] = 'miroir-video-hero';
					unset( $node['settings']['smallImage'], $node['settings']['largeImage'], $node['settings']['layout'] );
				}
			}
			if ( ! empty( $node['elements'] ) ) { $node['elements'] = $walk( $node['elements'] ); }
			$out[] = $node;
		}
		return $out;
	};
	$elements = json_decode( $raw, true );
	if ( is_array( $elements ) && $elements ) {
		$updated = $walk( $elements );
		if ( $updated !== $elements ) { update_post_meta( $home, '_elementor_data', wp_slash( wp_json_encode( $updated ) ) ); }
	}
	$upgrade_blocks = static function ( $blocks ) use ( &$upgrade_blocks, $is_hero, $is_removed ) {
		$out = array();
		foreach ( $blocks as $block ) {
			if ( 'miroir/editorial' === $block['blockName'] ) {
				$caption = $block['attrs']['caption'] ?? '';
				if ( $is_removed( $caption ) ) { continue; }
				if ( $is_hero( $caption ) ) {
					$block['blockName'] = 'miroir/video-hero';
					unset( $block['attrs']['smallImage'], $block['attrs']['largeImage'], $block['attrs']['layout'] );
				}
			}
			if ( ! empty( $block['innerBlocks'] ) ) { $block['innerBlocks'] = $upgrade_blocks( $block['innerBlocks'] ); }
			$out[] = $block;
		}
		return $out;
	};
	foreach ( array( 'content' => $content, 'fallback' => $fallback ) as $key => $original ) {
		if ( false === strpos( $original, '<!-- wp:miroir/' ) ) { continue; }
		$blocks = parse_blocks( $original );
		$updated = $upgrade_blocks( $blocks );
		if ( $blocks === $updated ) { continue; }
		$serialized = wp_slash( serialize_blocks( $updated ) );
		if ( 'fallback' === $key ) {
			update_post_meta( $home, '_miroir_blocks_before_elementor', $serialized );
		} else {
			wp_update_post( array( 'ID' => $home, 'post_content' => $serialized ) );
		}
	}
	if ( did_action( 'elementor/loaded' ) ) { \Elementor\Plugin::$instance->files_manager->clear_cache(); }
	clean_post_cache( $home );
	WP_CLI::success( 'Home upgraded: video hero, arrivals, one editorial, centered banner and collection. Upload the campaign video in Elementor > Campaign video hero.' );
} )();
