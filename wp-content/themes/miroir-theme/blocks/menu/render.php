<?php
/**
 * Links of a menu location (Appearance > Menus), or the default links while
 * none is assigned. Header menus use .site-nav__menu, footer menus the
 * footer link column.
 *
 * @var array $attributes { location }
 */

defined( 'ABSPATH' ) || exit;

$location = (string) $attributes['location'];
miroir_menu( $location, miroir_menu_fallback( $location ), 'primary' === $location ? 'site-nav__menu' : 'site-footer__links' );
