<?php
	if(!defined('ABSPATH')){die;}
?>
<table class="form-table">
	<tr>
		<td>
			<div class="radio-img-option">
				<input type="radio" id="r-bl" name="<?php echo $this->settingskey; ?>[boxplacement]" value="bottom-left" <?php checked( 'bottom-left' == $selected ); ?>>
				<label for="r-bl">
					<img src="<?php echo  $this->imagebaseurl; ?>/bottom_left.png"/>
					<span><?php _e('Bottom left',MABEL_RPN_LITE_SLUG); ?></span>
				</label>
			</div>
		</td>
		<td>
			<div class="radio-img-option">
				<input type="radio" id="r-br" name="<?php echo $this->settingskey; ?>[boxplacement]" value="bottom-right" <?php checked( 'bottom-right' == $selected ); ?>>
				<label for="r-br">
					<img src="<?php echo  $this->imagebaseurl; ?>/bottom_right.png"/>
					<span><?php _e('Bottom right',MABEL_RPN_LITE_SLUG); ?></span>
				</label>
			</div>
		</td>
	</tr>
</table>