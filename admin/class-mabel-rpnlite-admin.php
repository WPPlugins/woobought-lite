<?php
if(!defined('ABSPATH')){die;}

class Mabel_RPNLite_Admin{
	private $options;
	private $defaults;
	private $settingskey;
	private $imagebaseurl;
	
	public function __construct($defaults,$options,$settingskey){
		$this->defaults = $defaults;
		$this->imagebaseurl = MABEL_RPN_LITE_URL.'/admin/img';
		$this->options = $options;
		$this->settingskey = $settingskey;
	}
	
	public function getNewestPurchasedProducts(){
	    try {
            $products = get_transient('mabel-rpnlite-cached-products');
            if (!$products) {
                global $wpdb;

                $orderitems = $wpdb->get_results($wpdb->prepare("
                    SELECT i.*,im.meta_value as 'product_id',p.post_date_gmt
                    FROM {$wpdb->prefix}woocommerce_order_items i
                    INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta im on im.order_item_id = i.order_item_id and im.meta_key='_product_id'
                    INNER JOIN {$wpdb->prefix}posts p on p.id = i.order_id
                    WHERE order_item_type = 'line_item' and post_date_gmt >= DATE_SUB(NOW(), INTERVAL %d DAY)
                    GROUP BY order_id
                    ORDER BY post_date_gmt DESC
                    LIMIT %d",
                    $this->getOption('notificationage'), $this->getOption('limit')), OBJECT);

                $products = array();

                if (empty($orderitems)){
                    wp_die();
                }

                $newway = (version_compare( WC()->version, '2.6', '<')?true:false);

                foreach ($orderitems as $item) {
                    if($newway){
                        $product = wc_get_product($item->product_id);
                    }else{
                        $pf = new WC_Product_Factory();
                        $product = $pf ->get_product($item->product_id);
                    }
                    if($product) {
                        $order = get_post_meta($item->order_id);
                        array_push($products, array(
                                'id' => $item->order_id,
                                'name' => $product->get_title(),
                                'url' => $product->get_permalink(),
                                'date' => $item->post_date_gmt,
                                'image' => $product->get_image(),
                                'price' => get_woocommerce_currency_symbol() . $product->get_display_price(),
                                'buyer' => $this->createBuyerArray($order)
                            )
                        );
                    }
                }
                set_transient('mabel-rpnlite-cached-products', $products, 60); // Cache the results for 1 minute
            }
            echo(json_encode($products));
            wp_die(); // this is required to terminate immediately and return a proper response
        }catch(Exception $e){
            wp_die();
        }
	}

	private function createBuyerArray($order){
	    $buyer = array();

        $buyer['name'] = strlen($order['_billing_first_name'][0] > 0) ? $order['_billing_first_name'][0] : $this->t('someone');
        $buyer['city'] = strlen($order['_billing_city'][0]) > 0 ? $order['_billing_city'][0] : 'N/A';
        $buyer['state'] = strlen($order['_billing_state'][0]) > 0 ? $order['_billing_state'][0] : 'N/A';
        $buyer['country'] = strlen($order['_billing_country'][0]) > 0 ? WC()->countries->countries[$order['_billing_country'][0]] : 'N/A';

        return $buyer;
	}

	public function enqueueStyles() {
		if(isset($_GET['page']) && $_GET['page'] == MABEL_RPN_LITE_SLUG){
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( MABEL_RPN_LITE_SLUG, MABEL_RPN_LITE_URL.'/admin/css/mabel-rpnlite-admin.min.css', array(), MABEL_RPN_LITE_VERSION, 'all' );
		}
	}
	
	// Register js for the admin area.
	public function enqueueScripts() {
		if(isset($_GET['page']) && $_GET['page'] == MABEL_RPN_LITE_SLUG){
			wp_enqueue_script(MABEL_RPN_LITE_SLUG, MABEL_RPN_LITE_URL . '/admin/js/mabel-rpnlite-admin.min.js', array('jquery','wp-color-picker'), MABEL_RPN_LITE_VERSION, false);
		}
	}
	
	// Add a menu item in Dashboard>settings if you have the 'manage options' right and draw its options page via settings.php.
	public function addSettingsMenu(){
		add_options_page('Plugin '.$this->t('Settings'), MABEL_RPN_LITE_NAME, 'manage_options', MABEL_RPN_LITE_SLUG, array($this,'drawSettings') );
	}

	public function drawSettings(){
        include_once('partials/settings.php');
    }

    public function addSettingsLinkToPlugin( $links ) {
		$pro_link = array('<a style="color:green;" href="https://woobought.com/">Go Pro!</a>');
		$settings_link = array('<a href="' . admin_url( 'options-general.php?page=' . MABEL_RPN_LITE_SLUG ) . '">' . $this->t('Settings'). '</a>');
		return array_merge(  $settings_link,$pro_link, $links );
	}
	
	// Register sections & settings
	public function initSettings(){
		register_setting( 'box-options-rpn-lite', $this->settingskey);
		add_settings_section("section", "", null,'box-options-rpn-lite');
		
		add_settings_field('boxlayout',$this->t('Layout'),array($this,'displayBoxLayout'),'box-options-rpn-lite','section');
		add_settings_field('boxplacement',$this->t('Placement'),array($this,'displayBoxPlacement'),'box-options-rpn-lite','section');
		add_settings_field('boxbgcolor',$this->t('Background color'),array($this,'displayColorpicker'),'box-options-rpn-lite','section',array('id'=>'boxbgcolor'));
		
		register_setting( 'text-options-rpn-lite', $this->settingskey,array($this,'sanitizeInput'));
		add_settings_section("section2", "", null,'text-options-rpn-lite');
		
		add_settings_field('textcolor',$this->t('Color'),array($this,'displayColorpicker'),'text-options-rpn-lite','section2',array('id'=>'textcolor'));
		add_settings_field('text',$this->t('Message'),array($this,'displayTextOption'),'text-options-rpn-lite','section2');
		
		register_setting( 'display-options-rpn-lite', $this->settingskey);
		add_settings_section("section3", "", null,'display-options-rpn-lite');

		$s = $this->t('seconds');
		$ms = $this->t('minutes');
		$ds = $this->t('days');
		
		add_settings_field('notificationage',$this->t("Don't show purchases older than"),array($this,'displayDropDown'),'display-options-rpn-lite','section3',array('id'=>'notificationage','options'=>array('1 '.$this->t('day')=>1,'2 '.$ds=>2,'3 '.$ds=>3,'5 '.$ds=>5,'1 '.$this->t('week')=>7,'10 '.$ds=>10,'2 '.$this->t('weeks')=>14,'3 '.$this->t('weeks')=>21,'4 '.$this->t('weeks')=>28,'8 '.$this->t('weeks')=>56,'12 '.$this->t('weeks')=>84)));
		add_settings_field('notificationdelay',$this->t("Time between notifications") ,array($this,'displayDropDown'),'display-options-rpn-lite','section3',array('id'=>'notificationdelay','options'=>array('10 '.$s=>10,'20 '.$s=>20,'30 '.$s=>30,'40 '.$s=>40,'50 '.$s=>50,'1 '.$this->t('minute')=>60,'1.5 '.$ms=>90,'2 '.$ms=>120),'comment'=>$this->t("The time to wait before showing the next notification")));
	}
	
	// Display functions
	public function displayDropDown($args){
		$options = $args['options'];
		$id = $args['id'];
		$comment = isset($args['comment'])?$args['comment']:null;
		require('partials/fields/dropdownlist.php');
	}
	public function displayExcludeList($args){
		$id = $args['id'];
		$comment = $args['comment'];
		$values = json_decode($this->getOption($id),true);
		$pages = get_pages(array('post_type' => 'page'));
		require('partials/fields/textarea.php');
	}
	public function displayTextOption(){
		require('partials/fields/textoption.php');
	}
	public function displayBoxSize(){
		$selected = $this->getOption('boxsize');
		require('partials/fields/boxsize.php');
	}
	public function displayBoxLayout(){
		$selected = $this->getOption('boxlayout');
		require('partials/fields/boxlayout.php');
	}
	public function displayBoxPlacement(){
		$selected = $this->getOption('boxplacement');
		require('partials/fields/boxplacement.php');
	}
	public function displayColorpicker($args){
		$id = $args['id'];
		$value = $this->getOption($id);
		require('partials/fields/colorpicker.php');
	}
	
	// Sanitizing
	public function sanitizeInput($input){
		$output = array();
		 
		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if(isset($input[$key])){
				$output[$key] = sanitize_text_field($input[$key]);
			}
		}
		// Return the array processing any additional functions filtered by this action
		return $output;
	}
	
	// Private Helpers
	private function getOption($id){
		$o = isset($this->options[$id])?$this->options[$id]:$this->defaults[$id];
		return $o;
	}
	
	private function t($key,$echo = false){
		if($echo) _e($key,MABEL_RPN_LITE_SLUG);
		else return __($key,MABEL_RPN_LITE_SLUG);
	}
	
	private function valueortranslatedefault($key){
		return (empty($this->options[$key])? $this->t($this->defaults[$key]) : $this->options[$key]);
	}
}