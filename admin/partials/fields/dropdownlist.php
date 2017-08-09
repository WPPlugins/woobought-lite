<?php 
	if(!defined('ABSPATH')){die;}
	$value = $this->getOption($id);
?>
<select name="<?php echo $this->settingskey; ?>[<?php echo $id;?>]" onchange="changeExampleCss(this,'<?php echo $id;?>')">
	<?php
		foreach($options as $key=>$val){
			$selected = $value == $val;
			echo '<option '.($selected?'selected':'').' value="'.$val.'">'.$key.'</option>';
		}
	?>
</select>
<?php
	if(!empty($comment)){
		echo '<em class="infotext">'.__($comment,MABEL_RPN_LITE_SLUG).'</em>';
	}
 ?>