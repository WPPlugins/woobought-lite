<?php
	if(!defined('ABSPATH')){die;}
	printf('<input id="cb-%s" type="checkbox" name="mb-bhi-settings[%s]" value="1" %s /><label for="cb-%s">%s</label>',$id,$id,checked($this->getOption($id),1,false),$id,__($label,MABEL_RPN_LITE_SLUG));
?>