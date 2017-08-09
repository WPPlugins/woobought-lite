<?php
if(!defined('ABSPATH')){die;}

// Core plugin class
class Mabel_WC_RecentlyPurchasedLite{
	protected $loader;
	protected $defaults;
	protected $settingskey = 'mb-wc-rpn-lite-settings';
	private $options;
	
	public function __construct(){		
		$this->loadDependencies();
		$this->setLanguageSupport();
		
		$this->loader->add_action('woocommerce_init',$this,'startup');
		
		// Kickoff
		$this->loader->run();
	}
	
	public function startup(){
		try{
			$this->checkDependencies();
			$this->setOptions();
			$this->adminHooks();
			$this->publicHooks();
			
			$this->loader->run();
		}catch(Exception $e){
			if($e->getCode() === 1){
				add_action( 'admin_notices', function() use($e){
					?>
					<div class="error fade">
						<p>
							<strong><?php echo esc_html($e->getMessage()); ?></strong>
						</p>
					</div>
					<?php
				});
			}
		}
	}
	
	private function checkDependencies(){
		if(version_compare( WC()->version, '2.3', '<'))
			throw new Exception( MABEL_RPN_LITE_NAME.' '.__('requires WooCommerce version 2.3 or higher',MABEL_RPN_LITE_SLUG),1);
	}
	
	private function loadDependencies(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mabel-rpnlite-lang.php';		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mabel-rpnlite-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mabel-rpnlite-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mabel-rpnlite-public.php';
		
		$this->loader = new Mabel_RPNLite_Loader();
	}

	private function setOptions(){
		$this->defaults = array(
			'limit' => 15,
			'boxbgcolor'=>'#ffffff',
			'textcolor'=>'#000000',
			'boxplacement'=>'bottom-left',
			'boxlayout' =>'imageleft',
			'text' => '{{first_name}} from {{city}}, {{country}} purchased {{product_name}} for {{price}}.',
			'notificationdelay'=>60,
			'firstnotification'=>10,
			'notificationage'=>7
		);
		$this->options = (array)get_option($this->settingskey);
	}
	// Add multi language support.
	private function setLanguageSupport(){
		$lang = new Mabel_RPNLite_Lang();
		$lang->setDomain( MABEL_RPN_LITE_SLUG);
		$this->loader->add_action( 'plugins_loaded', $lang, 'loadTextDomain' );
	}

	// Register all of the hooks related to the admin area functionality of the plugin.
	private function adminHooks(){
		$plugin_admin = new Mabel_RPNLite_Admin( $this->defaults,$this->options,$this->settingskey);
		
		// ajax functions
		$this->loader->add_action('wp_ajax_nopriv_mabel-rpnlite-getnew-purchased-products',$plugin_admin,'getNewestPurchasedProducts'); // if user is not logged in
		$this->loader->add_action('wp_ajax_mabel-rpnlite-getnew-purchased-products',$plugin_admin,'getNewestPurchasedProducts'); // if user is logged in
		
		// Add settings
		$this->loader->add_action( 'admin_init', $plugin_admin, 'initSettings');
		// Add menu item
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'addSettingsMenu' );
		
		// load scripts & styles
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueueScripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueueStyles' );
		
		// Add Settings link to the plugin
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . MABEL_RPN_LITE_SLUG . '.php' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'addSettingsLinkToPlugin' );
	}

	// Register all of the hooks related to the public-facing functionality of the plugin.
	private function publicHooks() {
		$plugin_public = new Mabel_RPNLite_public($this->defaults,$this->options,$this->settingskey );
		// load scripts & styles
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueueScripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueueStyles' );
	}
}