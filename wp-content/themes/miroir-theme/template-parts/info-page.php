<?php
/**
 * Info page layout (screen 11 and the policy pages): title over an 820px column.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap info">
	<article class="wrap__col info__col">
		<h1 class="page-title"><?php the_title(); ?></h1>
		<div class="info__content"><?php the_content(); ?></div>
	</article>
</div>
