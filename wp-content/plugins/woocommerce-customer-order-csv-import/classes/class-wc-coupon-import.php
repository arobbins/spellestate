<?php
/**
 * WooCommerce Customer/Order CSV Import Suite
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order CSV Import Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order CSV Import Suite for your
 * needs please refer to http://docs.woothemes.com/document/customer-order-csv-import-suite/ for more information.
 *
 * @package     WC-Customer-CSV-Import-Suite/Classes
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2016, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! class_exists( 'WP_Importer' ) ) return;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooCommerce Coupon Importer class for managing the import process of a CSV file.
 *
 * Due to the lack of an easily accessible unique coupon identifier, merging of
 * coupons is not supported.
 */
class WC_CSV_Coupon_Import extends WP_Importer {

	private $id; // CSV attachment ID
	private $file_url; // CSV attachmente url

	// information to import from CSV file
	private $posts = array();

	// Counts
	public $log;
	private $processed_coupons = array();
	private $merged = 0;
	private $skipped = 0;
	private $imported = 0;

	private $file_url_import_enabled = true;
	private $delimiter;

	/**
	 * Construct and initialize the importer
	 */
	public function __construct() {
		$this->log = new WC_CSV_Customer_Log();

		$this->file_url_import_enabled = apply_filters( 'woocommerce_csv_coupon_file_url_import_enabled', true );
	}


	/**
	 * Manages the two separate stages of the CSV import process:
	 *
	 * 1. choosing the file
	 * 2. Handle the physical upload of the file and provide some configuration options
	 * 3. Perform the parsing/importing of the import file
	 */
	public function dispatch() {
		$this->header();

		if ( ! empty( $_POST['delimiter'] ) ) {
			$this->delimiter = stripslashes( trim( $_POST['delimiter'] ) );
		}

		if ( ! $this->delimiter ) $this->delimiter = ',';

		$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
		switch ( $step ) {
			case 0:
				$this->greet();
				break;
			case 1:
				check_admin_referer( 'import-upload' );
				if ( $this->handle_upload() )
					$this->import_options();
				break;
			case 2:
				check_admin_referer( 'import-woocommerce' );

				$this->id = (int) $_POST['import_id'];
				if ( $this->file_url_import_enabled )
					$this->file_url = esc_attr( $_POST['import_url'] );

				if ( $this->id )
					$file = get_attached_file( $this->id );
				else if ( $this->file_url_import_enabled )
					$file = ABSPATH . $this->file_url;

				if ( $file ) {
					echo '<div class="importer_loader"></div>';

					add_filter( 'http_request_timeout', array( $this, 'bump_request_timeout' ) );

					if ( function_exists( 'gc_enable' ) )
						gc_enable();

					@set_time_limit(0);
					@ob_flush();
					@flush();

					$this->import( $file );
				}
				break;
		}

		$this->footer();
	}


	/**
	 * Render the file import options:
	 * - Dry Run
	 * - Record Offset
	 */
	private function import_options() {
		?>
		<form action="<?php echo admin_url( 'admin.php?import=woocommerce_coupon_csv&step=2' ); ?>" method="post">
			<?php wp_nonce_field( 'import-woocommerce' ); ?>
			<input type="hidden" name="import_id" value="<?php echo $this->id; ?>" />
			<?php if ( $this->file_url_import_enabled ) : ?>
			<input type="hidden" name="import_url" value="<?php echo $this->file_url; ?>" />
			<?php endif; ?>
			<?php if ( $this->delimiter ) : ?>
			<input type="hidden" name="delimiter" value="<?php echo $this->delimiter; ?>" />
			<?php endif; ?>
			<input type="hidden" name="merge" value="<?php echo ! empty( $_REQUEST['merge'] ) ? "1" : "0" ?>" />
			<h3><?php _e( 'Advanced Options', 'woocommerce-customer-order-csv-import' ); ?></h3>
			<p>
				<label for="dry_run"><?php _e( 'Dry Run', 'woocommerce-customer-order-csv-import' ) ?></label>
				<input type="checkbox" value="1" name="dry_run" id="dry_run" />
				<span class="description"><?php _e( 'Perform a test dry run of the import process to check for errors prior to the actual import', 'woocommerce-customer-order-csv-import' ); ?></span>
			</p>
			<p>
				<label for="record_offset"><?php _e( 'Skip Records', 'woocommerce-customer-order-csv-import' ) ?></label>
				<input type="text" value="0" name="record_offset" id="record_offset" />
				<span class="description"><?php _e( 'Skip this number of records before parsing.  Use this option when importing very large files that are unable to complete in a single upload attempt.', 'woocommerce-customer-order-csv-import' ); ?></span>
			</p>

			<p class="submit"><input type="submit" class="button" value="<?php esc_attr_e( 'Submit', 'woocommerce-customer-order-csv-import' ); ?>" /></p>
		</form>
		<?php
	}


	/**
	 * The main controller for the actual import stage.
	 *
	 * @param string $file Path to the CSV file for importing
	 */
	private function import( $file ) {

		$this->import_start( $file );

		echo '<div class="progress">';
		$this->process_coupons();
		echo '</div>';

		// Show Result
		echo '<div class="updated settings-error below-h2"><p>' .

			/* translators: Placeholders: %1$s - number of imported records, %2$s - </em> number of merged records, %3$s - number of skipped records, %4$s - <strong> tag, %5$s - </strong> tag */
			sprintf( __( 'Import complete - imported %4$s%1$s%5$s, merged %4$s%2$s%5$s, skipped %4$s%3$s%5$s', 'woocommerce-customer-order-csv-import' ), $this->imported, $this->merged, $this->skipped, '<strong>', '</strong>' ) .
			'</p></div>';

		$this->log->show_log();

		$this->import_end();
	}


	/**
	 * Parses the CSV file and prepares us for the task of processing parsed data
	 *
	 * @param string $file Path to the CSV file for importing
	 */
	private function import_start( $file ) {
		if ( ! is_file( $file ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'woocommerce-customer-order-csv-import' ) . '</strong><br />';
			echo __( 'The file does not exist, please try again.', 'woocommerce-customer-order-csv-import' ) . '</p>';
			$this->footer();
			die();
		}

		$import_data = $this->parse( $file );

		if ( is_wp_error( $import_data ) ) {
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'woocommerce-customer-order-csv-import' ) . '</strong><br />';
			echo esc_html( $import_data->get_error_message() ) . '</p>';
			$this->footer();
			die();
		}

		$this->skipped = $import_data['skipped'];
		$this->posts   = $import_data['coupons'];
	}


	/**
	 * Performs post-import cleanup of files and the cache
	 */
	private function import_end() {

		wp_cache_flush();

		echo '<p>' . __( 'All done!', 'woocommerce-customer-order-csv-import' ) . ' <a href="' . admin_url( 'edit.php?post_type=shop_coupon' ) . '">' . __( 'View Coupons', 'woocommerce-customer-order-csv-import' ) . '</a>' .
		  ' or <a href="' . wc_customer_csv_import()->get_settings_url() . '">Import another file</a>.</p>';

		do_action( 'import_end' );
	}

	/**
	 * Handles the CSV upload and initial parsing of the file
	 *
	 * @return bool False if error uploading or invalid file, true otherwise
	 */
	private function handle_upload() {

		if ( empty( $_POST['file_url'] ) ) {

			$file = wp_import_handle_upload();

			if ( isset( $file['error'] ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'woocommerce-customer-order-csv-import' ) . '</strong><br />';
				echo esc_html( $file['error'] ) . '</p>';
				return false;
			}

			$this->id = (int) $file['id'];

		} else {

			if ( file_exists( ABSPATH . $_POST['file_url'] ) ) {

				$this->file_url = esc_attr( $_POST['file_url'] );

			} else {

				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'woocommerce-customer-order-csv-import' ) . '</strong></p>';
				return false;

			}

		}

		return true;
	}


	/**
	 * Create new coupons based on the parsed data
	 */
	private function process_coupons() {

		$this->imported = $this->merged = 0;

		// peforming a dry run?
		$dry_run = isset( $_POST['dry_run'] ) && $_POST['dry_run'] ? true : false;

		$this->log->add( '---' );
		$this->log->add( __( 'Processing coupons.', 'woocommerce-customer-order-csv-import' ) );

		$merging = ( ! empty( $_REQUEST['merge'] ) && $_REQUEST['merge'] ) ? true : false;

		foreach ( $this->posts as $post ) {

			// see class-wc-checkout.php for reference

			if ( $merging ) {
				// Only merge fields which are set
				$coupon_id = $post['id'];

				$this->log->add( sprintf( __( '> Merging coupon %s.', 'woocommerce-customer-order-csv-import' ), esc_html( $post['post_title'] ) ), true );

				$coupon_data = array(
					'ID' => $coupon_id,
				);
				if ( '' !== $post['post_excerpt'] ) $coupon_data['post_excerpt'] = $post['post_excerpt'];

				if ( ! $dry_run ) {
					$coupon_id = wp_update_post( $coupon_data );

					if ( is_wp_error( $coupon_id ) ) {
						$this->skipped++;

						/* translators: Placeholders: %1$s - coupon code, %2$s - error message */
						$this->log->add( sprintf( __( 'Failed to update coupon %1$s: %2$s', 'woocommerce-customer-order-csv-import' ), esc_html( $post['post_title'] ), esc_html( $coupon_id->get_error_message() ) ) );
						continue;
					}
				}
			} else {
				// inserting

				// make sure this coupon hasn't already been inserted
				if ( isset( $this->processed_coupons[ $post['post_title'] ] ) ){

					$this->skipped++;

					/* translators: Placeholders: %s - coupon code */
					$this->log->add( sprintf( __( "> Coupon '%s' already processed. Skipping.", 'woocommerce-customer-order-csv-import' ), esc_html( $post['post_title'] ) ), true );
					continue;
				}

				$coupon_data = array(
					'post_type'     => 'shop_coupon',
					'post_title'    => $post['post_title'],
					'post_status'   => 'publish',
					'ping_status'   => 'closed',
					'post_excerpt'  => $post['post_excerpt'],
					'post_author'   => 1,
					'post_password' => '',
				);

				if ( ! $dry_run ) {
					$coupon_id = wp_insert_post( $coupon_data );

					if ( is_wp_error( $coupon_id ) ) {

						$this->skipped++;

						/* translators: Placeholders: %1$s - coupon code, %2$s - error message */
						$this->log->add( sprintf( __( 'Failed to insert coupon %1$s: %2$s', 'woocommerce-customer-order-csv-import' ), esc_html( $post['post_title'] ), esc_html( $coupon_id->get_error_message() ) ) );
						continue;
					}

					// empty update to bump up the post_modified date to today's date (otherwise it would match the post_date, which wouldn't be quite right)
					// wp_update_post( array( 'ID' => $coupon_id ) );  // commented out because we currently don't allow the post_date to be set
				}

				$this->processed_coupons[ $post['post_title'] ] = true;
			}

			// meta data: create/update as long as there's a non-empty (or numeric) value, or we're inserting (not merging)
			if ( ! $dry_run ) {
				// add coupon postmeta
				foreach ( $post['postmeta'] as $meta ) {
					if ( is_numeric( $meta['value'] ) || ! empty( $meta['value'] ) || ! $merging ) {
						update_post_meta( $coupon_id, $meta['key'], $meta['value'] );
					}
				}
			}

			if ( $merging ) {

				$this->merged++;

				/* translators: Placeholders: %s - coupon code */
				$this->log->add( sprintf( __( '> Finished merging coupon  %s.', 'woocommerce-customer-order-csv-import' ), $post['post_title'] ) );
			} else {

				$this->imported++;

				/* translators: Placeholders: %s - coupon code */
				$this->log->add( sprintf( __( '> Finished importing coupon %s.', 'woocommerce-customer-order-csv-import' ), $post['post_title'] ) );
			}

		}

		$this->log->add( __( 'Finished processing coupons.', 'woocommerce-customer-order-csv-import' ) );

		unset( $this->posts );
	}


	/**
	 * Parse a CSV file
	 *
	 * @param string $file Path to CSV file for parsing
	 *
	 * @return array Information gathered from the CSV file
	 */
	private function parse( $file ) {
		$parser = new WC_CSV_Customer_Parser( 'coupons' );
		return $parser->parse( $file, $this->delimiter );
	}


	/**
	 * Display import page title
	 */
	private function header() {
		echo '<div class="wrap"><div class="icon32" id="icon-woocommerce-importer"><br></div>';
		echo '<h2>' . __( 'Import Coupons', 'woocommerce-customer-order-csv-import' ) . '</h2>';
	}


	/**
	 * Close div.wrap
	 */
	private function footer() {
		echo '<script> jQuery(".importer_loader, .progress").hide(); </script>';
		echo '</div>';
	}


	/**
	 * Display introductory text and file upload form
	 */
	private function greet() {
		echo '<div class="narrow">';
		echo '<p>'.__( 'Hi there! Upload a CSV file containing coupon data to import the contents into your shop.', 'woocommerce-customer-order-csv-import' ).'</p>';
		echo '<p>'.__( 'Choose a CSV (.csv) file to upload, then click Upload file and import.', 'woocommerce-customer-order-csv-import' ).'</p>';

		$action = 'admin.php?import=woocommerce_coupon_csv&amp;step=1&amp;merge=' . ( ! empty( $_GET['merge'] ) ? 1 : 0 );

		$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
		$size = size_format( $bytes );
		$upload_dir = wp_upload_dir();
		if ( ! empty( $upload_dir['error'] ) ) :
			?><div class="error"><p><?php _e( 'Before you can upload your import file, you will need to fix the following error:' ); ?></p>
			<p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
		else :
			?>
			<form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr( wp_nonce_url( $action, 'import-upload' ) ); ?>">
				<table class="form-table">
					<tbody>
						<tr>
							<th>
								<label for="upload"><?php _e( 'Choose a file from your computer:' ); ?></label>
							</th>
							<td>
								<input type="file" id="upload" name="import" size="25" />
								<input type="hidden" name="action" value="save" />
								<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
								<small><?php printf( __('Maximum size: %s' ), $size ); ?></small>
							</td>
						</tr>
						<?php if ( $this->file_url_import_enabled ) : ?>
						<tr>
							<th>
								<label for="file_url"><?php _e( 'OR enter path to file:', 'woocommerce-customer-order-csv-import' ); ?></label>
							</th>
							<td>
								<?php echo ' ' . ABSPATH . ' '; ?><input type="text" id="file_url" name="file_url" size="25" />
							</td>
						</tr>
						<?php endif; ?>
						<tr>
							<th><label><?php _e( 'Delimiter', 'woocommerce-customer-order-csv-import' ); ?></label><br/></th>
							<td><input type="text" name="delimiter" placeholder="," size="2" /></td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" class="button" value="<?php esc_attr_e( 'Upload file and import' ); ?>" />
				</p>
			</form>
			<?php
		endif;

		echo '</div>';
	}


	/**
	 * Added to http_request_timeout filter to force timeout at 60 seconds during import
	 *
	 * @see WP_Importer::bump_request_timeout()
	 * @param int $val timeout value
	 * @return int 60
	 */
	function bump_request_timeout( $val ) {
		return 60;
	}

}
