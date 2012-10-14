<form class="searchform" method="get" action="<?php echo esc_url( home_url() ); ?>">
	<div>
		<input name="s" type="text" class="searchtext" value="" title="<?php echo esc_attr( apply_filters( 'sb_search_text', 'Search' ) ); ?>" size="10" tabindex="1" />
		<input type="submit" class="searchbutton button" value="Search" tabindex="2" />
	</div>
</form>