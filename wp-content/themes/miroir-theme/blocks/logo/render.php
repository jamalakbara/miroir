<?php
/**
 * Logo linking home: the uploaded logo, or the Miroir wordmark (miroir_logo()).
 *
 * @var array $attributes { width: px for an uploaded logo, 0 = token default }
 */

defined( 'ABSPATH' ) || exit;

$width = (int) $attributes['width'];
?>
<a class="site-nav__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"<?php echo $width > 0 ? ' style="--logo-w:' . $width . 'px"' : ''; ?>><?php miroir_logo(); ?></a>
