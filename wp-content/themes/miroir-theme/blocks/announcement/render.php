<?php
/**
 * Announcement ticker above the navigation (header.php before the block theme).
 * Two groups of three copies scroll in CSS; only the first copy is read out.
 *
 * @var array $attributes { text, show }
 */

defined( 'ABSPATH' ) || exit;

$text = '' !== trim( $attributes['text'] ) ? $attributes['text'] : get_theme_mod( 'miroir_announcement', __( 'free shipping across indonesia', 'miroir' ) );
if ( empty( $attributes['show'] ) || '' === trim( (string) $text ) ) {
	return;
}
?>
<div class="announce">
	<div class="announce__track">
		<?php for ( $group = 0; $group < 2; $group++ ) : ?>
			<div class="announce__group"<?php echo $group ? ' aria-hidden="true"' : ''; ?>>
				<?php for ( $i = 0; $i < 3; $i++ ) : ?>
					<p class="announce__text"<?php echo $i ? ' aria-hidden="true"' : ''; ?>><?php echo esc_html( $text ); ?></p>
				<?php endfor; ?>
			</div>
		<?php endfor; ?>
	</div>
</div>
