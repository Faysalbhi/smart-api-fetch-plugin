<?php
global $listingpro_options;
$lp_detail_page_additional_detail_position = $listingpro_options['lp_detail_page_additional_styles']; 
if($lp_detail_page_additional_detail_position == 'right' ){
	echo '<div class="lp-classic-single-additional-detals">';
		echo listing_all_extra_fields($post->ID);
	echo '</div>';
}
?>
