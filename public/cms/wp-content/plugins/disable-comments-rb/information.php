<?php
/*  
 * RB Disable Comments
 * Version:           1.0.9 - 38451
 * Author:            RBS
 * Date:              03 02 2020 12:11:29 GMT
 */

if( !defined('WPINC') || !defined("ABSPATH") ){
	die();
}
wp_enqueue_script( 'plugin-install' );
//if ( 'plugin-information' != $tab ) {
	add_thickbox();
//}

?>
<style type="text/css">
.rbs_nw_wrap{
	margin-bottom: 10px; 
	display: grid; 
	grid-template-columns: 1fr 1fr;	
	column-gap: 25px;
	margin-right: 25px;
}	
.rbs_nw_item{
	background-color: #f9f9f9;
	margin: 0;
}
.rbs_nw_item_logo{
	text-align: center; 
	padding: 10px;
}
.rbs_nw_item_logo_ro{
	width: 150px;
	height: 150px;
}
.rbs_nw_item_logo_upz{
	margin-top: 30px; 
	margin-bottom: 15px; 
	width: 225px; 
	height: 100px;
}
.rbs_nw_item_header{
	text-align: center;
}
.rbs_nw_item_button{
	text-align: center; 
	margin-bottom: 10px; 
}
@media only screen and (max-width: 800px) {
  .rbs_nw_wrap{
    grid-template-columns: 1fr;	
    column-gap: 0;
    row-gap: 15px;
    margin: 0;
  }

  .rbs_nw_item_logo{
  	text-align: left;
  	float: left;
  }
  .rbs_nw_item_logo_upz{
  	margin-top: 7px; 
	margin-bottom: 7px; 
	width: 120px; 
	height: 80px;
  }
  .rbs_nw_item_logo_ro{
  	margin-top: 0px; 
  	margin-left: 15px;
  	margin-right: 5px;
	margin-bottom: 2px; 
	width: 80px; 
	height: 80px;
  }
}
</style>

<div class="rbs_nw_wrap">
	<div class="rbs_nw_item">
		<div class="rbs_nw_item_logo">
			<img class="rbs_nw_item_logo_ro" src="<?php echo RB_DISABLE_COMMENTS_URL; ?>assets/images/logo_rg.gif" width="150" hight="150">
		</div>
		<h3 class="rbs_nw_item_header">
			<?php _e('Create Free Gallery with Premium features with Robo Gallery'); ?>
		</h3>
		<div  class="rbs_nw_item_button">
			<a class="button-primary thickbox" href="<?php 
			echo self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=robo-gallery&amp;TB_iframe=true' ); 
			?>">
				<?php _e('Install Gallery'); ?>
			</a>
		</div>
	</div>
	
	<div class="rbs_nw_item">
		<div class="rbs_nw_item_logo">
			<img class="rbs_nw_item_logo_upz" src="<?php echo RB_DISABLE_COMMENTS_URL; ?>assets/images/upzilla_black_logo.svg" width="225" hight="100">
		</div>
		<h3 class="rbs_nw_item_header">
			<?php _e('Free website uptime and performance monitoring service for your WordPress', 'disable-comments-rb'); ?>
		</h3>
		<div  class="rbs_nw_item_button">
			<a class="button-primary" href="https://www.upzilla.co/?pk_campaign=wp_dc" target="_blank">
			<?php _e('Register Free Account'); ?></a>
		</div>
	</div>
</div>