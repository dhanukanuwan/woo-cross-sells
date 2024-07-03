<?php
/**
 * The file that defines the core plugin class
 *
 * @link       https://hashcodeab.se
 * @since      1.0.0
 *
 * @package    Hashcode_Woo_Cross_Sells
 * @subpackage Hashcode_Woo_Cross_Sells/includes
 */

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Hashcode_Woo_Cross_Sells
 * @subpackage Hashcode_Woo_Cross_Sells/includes
 * @author     Dhanuka Gunarathna <dhanuka@hashcodeab.se>
 */
class Hashcode_Woo_Cross_Sells {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Hashcode_Woo_Cross_Sells_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'HASHCODE_WOO_CROSS_SELLS_VERSION' ) ) {
			$this->version = HASHCODE_WOO_CROSS_SELLS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'hashcode-woo-cross-sells';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Hashcode_Woo_Cross_Sells_Loader. Orchestrates the hooks of the plugin.
	 * - Hashcode_Woo_Cross_Sells_i18n. Defines internationalization functionality.
	 * - Hashcode_Woo_Cross_Sells_Admin. Defines all hooks for the admin area.
	 * - Hashcode_Woo_Cross_Sells_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-hashcode-woo-cross-sells-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-hashcode-woo-cross-sells-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-hashcode-woo-cross-sells-admin.php';

		$this->loader = new Hashcode_Woo_Cross_Sells_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Hashcode_Woo_Cross_Sells_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Hashcode_Woo_Cross_Sells_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Hashcode_Woo_Cross_Sells_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_admin, 'hashcode_cs_shortcode' );
		$this->loader->add_action( 'template_redirect', $plugin_admin, 'hashcode_cross_sell_section_display' );
		$this->loader->add_action( 'woocommerce_settings_hashcode_cross_sell', $plugin_admin, 'hashcode_cross_sell_settings_tab_content' );
		$this->loader->add_action( 'woocommerce_settings_save_hashcode_cross_sell', $plugin_admin, 'hashcode_cross_sell_settings_tab_save' );

		$this->loader->add_filter( 'woocommerce_cross_sells_columns', $plugin_admin, 'hashcode_cross_sells_columns', 30 );
		$this->loader->add_filter( 'woocommerce_settings_tabs_array', $plugin_admin, 'hashcode_cross_sell_settings_tab', 50 );
		$this->loader->add_filter( 'woocommerce_product_cross_sells_products_heading', $plugin_admin, 'hashcode_cross_sell_section_title', 30 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Hashcode_Woo_Cross_Sells_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
