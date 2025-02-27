<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wpdoctor.se
 * @since      1.0.0
 *
 * @package    Wpdoctor_Woo_Cross_Sells
 * @subpackage Wpdoctor_Woo_Cross_Sells/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wpdoctor_Woo_Cross_Sells
 * @subpackage Wpdoctor_Woo_Cross_Sells/includes
 * @author     Dhanuka Gunarathna <dhanuka@wpdoctor.se>
 */
class Wpdoctor_Woo_Cross_Sells_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wpdoctor-woo-cross-sells',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
