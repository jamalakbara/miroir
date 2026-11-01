<?php
/**
 * Copyright line: {year} is the current year, {site} the site title in lower case.
 *
 * @var array $attributes { format: empty = © {year} {site} }
 */

defined( 'ABSPATH' ) || exit;
?>
<p class="site-footer__copyright"><?php echo esc_html( miroir_copyright( (string) $attributes['format'] ) ); ?></p>
