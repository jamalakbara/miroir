<?php
/**
 * Bottom line of the standalone pages (maintenance, staging access): copyright,
 * place and website credit.
 */

defined( 'ABSPATH' ) || exit;
?>
<p class="maint__copyright"><?php echo esc_html( miroir_copyright() ); ?><?php echo '' !== miroir_text( 'maint_place' ) ? ' · ' . esc_html( miroir_text( 'maint_place' ) ) : ''; ?> · <?php echo esc_html( miroir_text( 'maint_credit' ) ); ?> <a href="https://jamalakbara.com" target="_blank" rel="noopener">jamalakbara.com</a></p>
