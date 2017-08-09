<?php
if(!defined('ABSPATH')){die;}

class Mabel_RPNLite_Lang{
	private $domain;

	public function loadTextDomain() {
		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
	public function setDomain( $domain ) {
		$this->domain = $domain;
	}
}