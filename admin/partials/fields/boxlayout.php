<?php
	if(!defined('ABSPATH')){die;}
?>
<table class="form-table">
	<tr>
		<td>
			<div class="radio-img-option">
				<input onchange="changeExampleCss(this,'boxlayout')" type="radio" id="r-il" name="<?php echo $this->settingskey; ?>[boxlayout]" value="imageleft" <?php checked( 'imageleft' == $selected ); ?>>
				<label for="r-il">
					<img src="<?php echo  $this->imagebaseurl; ?>/image_left.png"/>
					<span><?php _e('Image left',MABEL_RPN_LITE_SLUG); ?></span>
				</label>
			</div>
		</td>
		<td>
			<div class="radio-img-option">
				<input onchange="changeExampleCss(this,'boxlayout')" type="radio" id="r-ir" name="<?php echo $this->settingskey; ?>[boxlayout]" value="imageright" <?php checked( 'imageright' == $selected ); ?>>
				<label for="r-ir">
					<img src="<?php echo  $this->imagebaseurl; ?>/image_right.png"/>
					<span><?php _e('Image right',MABEL_RPN_LITE_SLUG); ?></span>
				</label>
			</div>
		</td>
	</tr>
</table>