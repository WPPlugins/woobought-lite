<?php
	if(!defined('ABSPATH')){die;}
?>

<div class="wrap">
	<h1><?php echo esc_html(get_admin_page_title());?></h1>
	<h2 class="nav-tab-wrapper">
		<a data-tab="box_options" href="#" class="nav-tab nav-tab-active"><i class="dashicons dashicons-admin-customizer"></i> <span><?php _e('Notification design',MABEL_RPN_LITE_SLUG);?></span></a>
		<a data-tab="text_options" href="#" class="nav-tab"><i class="dashicons dashicons-editor-aligncenter"></i> <span><?php _e('Message',MABEL_RPN_LITE_SLUG);?></span></a>
		<a data-tab="display_options" href="#" class="nav-tab"><i class="dashicons dashicons-admin-settings"></i> <span><?php _e('Display Options',MABEL_RPN_LITE_SLUG);?></span></a>
	</h2>
	<form action="options.php" id="mabel-rpn-form" method="POST">
		<div class="tab tab-box_options">
			<?php
				settings_fields('box-rpn-lite-options');
				do_settings_sections( 'box-options-rpn-lite');
				echo '<div class="out">'.$this->t('Need more design options such as <b>size, dropshadow & rounded corners</b>? <a href="https://woobought.com/">Go Pro</a>!').'</div>';
				submit_button(__('Save All Changes',MABEL_RPN_LITE_SLUG));
			?>
		</div>
		<div class="tab tab-text_options" style="display:none;">
			<?php
				settings_fields('text-options-rpn-lite');
				do_settings_sections( 'text-options-rpn-lite');
				echo '<div class="out">'.$this->t('Need more display options such as <b>a fully customizable title, stock indication or show/hide settings</b>? <a href="https://woobought.com/">Go Pro</a>!').'</div>';
			submit_button(__('Save All Changes',MABEL_RPN_LITE_SLUG));
			?>
		</div>
		<div class="tab tab-display_options" style="display:none;">
			<?php
				settings_fields('display-options-rpn-lite');
				do_settings_sections( 'display-options-rpn-lite');
				echo '<div class="out">'.$this->t('Need more options such as <b>page exclusion & various timing options</b>? <a href="https://woobought.com">Go Pro</a>!').'</div>';
			submit_button(__('Save All Changes',MABEL_RPN_LITE_SLUG));
			?>
		</div>
	</form>
	<table class="form-table">
		<tr><th style="padding-top: 40px;"><?php _e('Example',MABEL_RPN_LITE_SLUG); ?></th><td>
		<div class="example-wrapper">
			<div class="rpn-example">
				<div class="rpn-example-img" style="background-image:url(<?php echo $this->imagebaseurl;?>/example.jpg)"></div>
				<div class="message-wrapper mabel-rpn-message">
					<h4>Dell Inspiron Desktop</h4>
					<span class="message"></span>
					<span class="mabel-rpn-hide">
						<a class="rpn-disable-btn"><?php _e("Disable",MABEL_RPN_LITE_SLUG); ?></a>
					</span>
				</div>
				<a class="mabel-rpn-close">×</a>
			</div>
		</div>
		</td></tr>
	</table>
</div>