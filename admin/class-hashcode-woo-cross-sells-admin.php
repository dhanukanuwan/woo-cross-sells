<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://hashcodeab.se
 * @since      1.0.0
 *
 * @package    Hashcode_Woo_Cross_Sells
 * @subpackage Hashcode_Woo_Cross_Sells/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Hashcode_Woo_Cross_Sells
 * @subpackage Hashcode_Woo_Cross_Sells/admin
 * @author     Dhanuka Gunarathna <dhanuka@hashcodeab.se>
 */
class Hashcode_Woo_Cross_Sells_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Cross-sells shortcode.
	 *
	 * @since 1.0.0
	 */
	public function hashcode_cs_shortcode() {
		add_shortcode( 'product_cross_sells', array( $this, 'hashcode_cs_shortcode_callback' ) );
	}

	/**
	 * Cross-sells shortcode.
	 *
	 * @since 1.0.0
	 * @param array $atts .
	 */
	public function hashcode_cs_shortcode_callback( $atts ) {

		$attributes = shortcode_atts(
			array(
				'id' => '',
			),
			$atts
		);

		$product_id = null;

		if ( isset( $attributes['id'] ) && ! empty( $attributes['id'] ) ) {
			$product_id = (int) $attributes['id'];
		} else {

			$product_id = get_the_ID();
		}

		ob_start();
		$this->hashcode_cross_sell_display( $product_id );
		return ob_get_clean();
	}

	/**
	 * Cross-sells columns filter.
	 *
	 * @since 1.0.0
	 * @param array $columns .
	 */
	public function hashcode_cross_sells_columns( $columns ) {

		$new_columns_number = WC_Admin_Settings::get_option( 'hashcode_cross_sell_columns' );

		if ( ! empty( $new_columns_number ) ) {
			$columns = (int) $new_columns_number;
		}

		return $columns;
	}

	/**
	 * Cross-sells display function.
	 *
	 * @since 1.0.0
	 * @param int $product_id .
	 */
	public function hashcode_cross_sell_display( $product_id ) {

		$cross_sells_ids = array();
		$cross_sells     = array();

		$product_data = wc_get_product( $product_id );

		if ( ! empty( $product_data ) && ! is_wp_error( $product_data ) ) {
			$cross_sells_ids = $product_data->get_cross_sells();
		}

		if ( ! empty( $cross_sells_ids ) ) {

			$related_products_display = WC_Admin_Settings::get_option( 'hashcode_cross_sell_related' );

			if ( ! empty( $related_products_display ) && 'hide' === $related_products_display ) {

				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

			}

			$cross_sells = array_filter( array_map( 'wc_get_product', $cross_sells_ids ), 'wc_products_array_filter_visible' );
			$columns     = apply_filters( 'woocommerce_cross_sells_columns', 4 );

			wc_set_loop_prop( 'name', 'cross-sells' );
			wc_set_loop_prop( 'columns', $columns );

			// Handle orderby and limit results.
			$order_by    = apply_filters( 'woocommerce_cross_sells_orderby', 'rand' );
			$order_type  = apply_filters( 'woocommerce_cross_sells_order', 'desc' );
			$cross_sells = wc_products_array_orderby( $cross_sells, $order_by, $order_type );
			/**
			 * Filter the number of cross sell products should on the product page.
			 *
			 * @param int $limit number of cross sell products.
			 * @since 3.0.0
			 */
			$limit       = intval( apply_filters( 'woocommerce_cross_sells_total', 4 ) );
			$cross_sells = $limit > 0 ? array_slice( $cross_sells, 0, $limit ) : $cross_sells;

			wc_get_template(
				'cart/cross-sells.php',
				array(
					'cross_sells'    => $cross_sells,

					// Not used now, but used in previous version of up-sells.php.
					'posts_per_page' => $limit,
					'orderby'        => $order_by,
					'columns'        => $columns,
				)
			);

		}
	}

	/**
	 * Cross-sells settings tab.
	 *
	 * @since 1.0.0
	 * @param array $tabs .
	 */
	public function hashcode_cross_sell_settings_tab( $tabs ) {

		$tabs['hashcode_cross_sell'] = __( 'Cross-sells on Product Pages', 'hashcode-woo-cross-sells' );

		return $tabs;
	}

	/**
	 * Cross-sells settings tab.
	 *
	 * @since 1.0.0
	 */
	public function hashcode_cross_sell_settings_tab_content() {

		WC_Admin_Settings::output_fields( $this->hashcode_cross_sell_settings() );
	}

	/**
	 * Cross-sells settings tab.
	 *
	 * @since 1.0.0
	 */
	public function hashcode_cross_sell_settings_tab_save() {

		WC_Admin_Settings::save_fields( $this->hashcode_cross_sell_settings() );
	}

	/**
	 * Cross-sells settings.
	 *
	 * @since 1.0.0
	 */
	private function hashcode_cross_sell_settings() {

		$settings = array(
			array(
				'name' => __( 'Product Cross-sells Global Settings', 'hashcode-woo-cross-sells' ),
				'type' => 'title',
				'desc' => __( 'These settings will be applied to all products available and, will only be applied on individual product pages.', 'hashcode-woo-cross-sells' ),
			),
			array(
				'name'     => __( 'Cross-sells Section Title', 'hashcode-woo-cross-sells' ),
				'desc_tip' => __( 'Override default WooCommerce Cross-sell section title', 'hashcode-woo-cross-sells' ),
				'id'       => 'hashcode_cross_sell_title',
				'type'     => 'text',
			),
			array(
				'name'              => __( 'Cross-sells Section Columns', 'hashcode-woo-cross-sells' ),
				'desc_tip'          => __( 'Number of columns to be displayed in a row.', 'hashcode-woo-cross-sells' ),
				'id'                => 'hashcode_cross_sell_columns',
				'type'              => 'number',
				'custom_attributes' => array(
					'min' => 1,
					'max' => 4,
				),
			),
			array(
				'name'     => __( 'Cross-sells display', 'hashcode-woo-cross-sells' ),
				'desc_tip' => __( 'Automatically display cross sells when cross sells are available for a product. ', 'hashcode-woo-cross-sells' ),
				'id'       => 'hashcode_cross_sell_display',
				'type'     => 'radio',
				'options'  => array(
					'auto'   => __( 'Automatically display cross sells when cross sells are available for a product.', 'hashcode-woo-cross-sells' ),
					'manual' => __( 'Activated manually by using [product_cross_sells] shortcode.', 'hashcode-woo-cross-sells' ),
				),
			),
			array(
				'name'     => __( 'Related Products', 'hashcode-woo-cross-sells' ),
				'desc_tip' => __( 'Hide or keep related product when cross sells are available for a product. ', 'hashcode-woo-cross-sells' ),
				'id'       => 'hashcode_cross_sell_related',
				'type'     => 'radio',
				'options'  => array(
					'hide' => __( 'Hide related products when cross sells are available.', 'hashcode-woo-cross-sells' ),
					'keep' => __( 'Do nothing.', 'hashcode-woo-cross-sells' ),
				),
			),
			array(
				'type' => 'sectionend',
			),
		);

		return $settings;
	}

	/**
	 * Cross-sells section title.
	 *
	 * @since 1.0.0
	 * @param string $section_title .
	 */
	public function hashcode_cross_sell_section_title( $section_title ) {

		$new_title = WC_Admin_Settings::get_option( 'hashcode_cross_sell_title' );

		$section_title = $new_title;

		return $section_title;
	}

	/**
	 * Cross-sells section automatic display.
	 *
	 * @since 1.0.0
	 */
	public function hashcode_cross_sell_section_display_action() {
		$this->hashcode_cross_sell_display( get_the_ID() );
	}

	/**
	 * Cross-sells section automatic display.
	 *
	 * @since 1.0.0
	 */
	public function hashcode_cross_sell_section_display() {

		if ( ! is_product() ) {
			return;
		}

		if ( is_admin() ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		$product_data = wc_get_product( get_the_ID() );

		if ( ! empty( $product_data ) && ! is_wp_error( $product_data ) ) {
			$cross_sells_ids = $product_data->get_cross_sells();
		}

		if ( ! empty( $cross_sells_ids ) ) {
			$display_type = WC_Admin_Settings::get_option( 'hashcode_cross_sell_display' );

			if ( ! empty( $display_type ) && 'auto' === $display_type ) {
				add_action( 'woocommerce_after_single_product_summary', array( $this, 'hashcode_cross_sell_section_display_action' ) );

				$related_products_display = WC_Admin_Settings::get_option( 'hashcode_cross_sell_related' );

				if ( ! empty( $related_products_display ) && 'hide' === $related_products_display ) {

					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

				}
			}
		}
	}
}
