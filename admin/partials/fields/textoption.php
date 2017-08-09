<?php
	if(!defined('ABSPATH')){die;}
?>
<textarea onchange="changeExampleCss(this,'text')" class="rpn-text" name="<?php echo $this->settingskey.'[text]';?>"><?php echo $this->valueortranslatedefault('text');?></textarea>
<div><em class="infotext"><?php $this->t('Available codes between double braces',true);?>: first_name, city, state, country, product_name, price</em></div>