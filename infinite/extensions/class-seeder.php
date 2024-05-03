<?php

/**
 * Class that extends custom functionality to the plugin
 *
 * A class definition that allows for drop-in extension of the plugin that enables 
 * additional customizations and integrations that taylor to the clients needs and 
 * overall project scope.
 *
 * @link       https://r3blcreative.com
 * @since      1.0.0
 *
 * @package    Infinite
 * @subpackage Infinite/extensions
 */

class Infinite_Seeder {
	/**
	 * The slug used for this class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $slug
	 */
	private $slug = 'infinite';

	/**
	 * The init function
	 * 
	 * Use this to add custom hooks and filters to WP or to pre-load config files.
	 * In order for this function to work with WP hooks and filters, a class instance must
	 * be instantiated at the bottom of this doc.
	 */
	public function __construct() {
		add_action('admin_init', [$this, 'ajax_db_seeder_hooks']);
	}

	/**
	 * Handles the hooks for Db Seeder AJAX calls
	 *
	 * @since    1.0.0
	 */
	public function ajax_db_seeder_hooks() {
		add_action('wp_ajax_nopriv_' . $this->slug . '_db_seeder', [$this, 'ajax_db_seeder']);
		add_action('wp_ajax_' . $this->slug . '_db_seeder', [$this, 'ajax_db_seeder']);
	}

	/**
	 * Create separate public methods for each table you want to seed
	 *
	 * @since    1.0.0
	 */
	public function ajax_db_seeder() {
		// Verify Nonce
		if (!wp_verify_nonce($_REQUEST['nonce'], $this->slug . '_seeder_nonce')) {
			exit('No naughty business please');
		}

		// Runs seeders
		$this->seed_students();

		// Return to sender
		header('Location: ' . $_SERVER['HTTP_REFERER']);

		// Just in case...
		die();
	}

	/**
	 * Handles the admin view content display
	 *
	 * @since    1.0.0
	 */
	public function seeder_display() {
		$nonce = wp_create_nonce($this->slug . '_seeder_nonce');
		$link = admin_url('admin-ajax.php?action=' . $this->slug . '_db_seeder&nonce=' . $nonce);
?>
		<a href="<?php echo $link; ?>" data-nonce="<?php echo $nonce; ?>" class="infinite-button btn-primary">Seed the DB</a>
<?php
	}

	/**
	 * Create separate private methods for each table you want to seed
	 *
	 * @since    1.0.0
	 */
	public function seed_students() {
		global $wpdb;

		require_once get_stylesheet_directory() . '/infinite/vendor/autoload.php';

		$faker = Faker\Factory::create();
		$rows = [];

		for ($i = 1; $i <= 60; $i++) {
			$randDate = $faker->dateTimeThisYear('-1 week');
			$dob = $randDate->format('Y-m-d');
			$rows[] = [
				'first_name'			=> $faker->firstName(),
				'middle_name'			=> $faker->optional(15)->firstName(),
				'last_name'				=> $faker->lastName(),
				'suffix'					=> $faker->optional(5)->suffix(),
				'email_address'		=> $faker->email(),
				'primary_phone'		=> $faker->phoneNumber(),
				'dob'							=> $dob,
				'street1'					=> $faker->streetAddress(),
				'street2'					=> $faker->optional(40)->secondaryAddress(),
				'city'						=> $faker->city(),
				'state'						=> $faker->state(),
				'postal_code'			=> $faker->postcode(),
				'license1'				=> $faker->optional(40)->numberBetween(123456789, 987654321),
				'license2'				=> $faker->optional(40)->numberBetween(123456789, 987654321),
			];
		}

		$table = $wpdb->prefix . 'infinite_students';

		foreach ($rows as $row) {
			$wpdb->insert($table, $row);
		}
	}
}

$Infinite_Seeder = new Infinite_Seeder();
