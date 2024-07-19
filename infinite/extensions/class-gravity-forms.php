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

class Infinite_Gravity_Forms {
	/**
	 * The ID of the Gravity Form to get entries from.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $form_id    The ID of the Gravity Form.
	 */
	private $form_id = 1;

	/**
	 * Stripe secret key
	 *
	 * @var string
	 */
	private $sk;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		session_start();

		// Get stripe key
		$this->sk = (get_field('stripe_mode', 'option') == 'live') ? get_field('sk_live', 'option') : get_field('sk_test', 'option');

		// Checkout flows
		add_filter('gform_validation_' . $this->form_id, [$this, 'validate_cart'], 10, 2);
		add_filter('gform_validation_message_' . $this->form_id, [$this, 'invalid_cart_msg'], 10, 2);
		// add_action('gform_user_registered', [$this, 'save_gf_to_db'], 10, 4);
		add_action('gform_entry_created', [$this, 'save_gf_to_db'], 10, 2);
		add_action('gform_confirmation_' . $this->form_id, [$this, 'stripe_checkout_redirect'], 10, 4);

		// Changes address field full state names to state abbreviations
		add_filter('gform_us_states', [$this, 'use_state_abbreviations']);

		// Removes license info from display in admin
		add_filter('gform_settings_display_license_details', '__return_false');

		// Creates webhook endpoint for updating DB after stripe payments
		add_action('rest_api_init', function () {
			register_rest_route('bvwp/v1', '/stripe', [
				'methods' => 'POST',
				'callback' => [$this, 'stripe_wh_callback'],
				'permission_callback' => function () {
					return true;
				}
			]);
		});
	}

	/**
	 * Undocumented function
	 *
	 * @param  [type] $validation_result
	 * @return void
	 */
	public function validate_cart($validation_result) {
		$form  = $validation_result['form'];
		$entry = GFFormsModel::get_current_lead();
		$email = rgar($entry, '4');

		// Check student DB and get ID if exists
		$SID = $this->get_student($email);
		if (!$SID) return $validation_result;

		// Sets SESSION var isRegistered on each cart item
		$this->check_roster($SID);

		$isValid = [];
		foreach ($_SESSION['cart']['items'] as $item) {
			if ($item['isRegistered']) {
				$isValid[] = false;
			}
		}

		if (in_array(false, $isValid)) $validation_result['is_valid'] = false;
		// $validation_result['is_valid'] = false;

		return $validation_result;
	}

	/**
	 * Undocumented function
	 *
	 * @param  [type] $msg
	 * @param  [type] $form
	 * @return void
	 */
	public function invalid_cart_msg($msg, $form) {
		$entry = GFFormsModel::get_current_lead();
		$email = rgar($entry, '4');
		$new_msg = '';

		$SID = $this->get_student($email);
		$this->check_roster($SID);

		foreach ($_SESSION['cart']['items'] as $item) {
			if ($item['isRegistered']) {
				$new_msg .= '<li class="gform_submission_error">&bull; ' . get_the_title($item['id']) . '</li>';
			}
		}

		if (!empty($new_msg)) {
			$msg .= '<div><h2 class="gform_submission_error hide_summary">
		<span class="gform-icon gform-icon--close"></span>
		Looks like you are already registered for the following course(s):
		</h2>
		<ul style="margin:5px 0 5px 20px;">' . $new_msg . '</ul></div>';
		}

		return $msg;
	}

	/**
	 * Save GF submissions to custom db
	 *
	 * @since    1.0.0
	 * @param    object    $entry   Gravity Forms EntryObject.
	 * @param    object    $form    Gravity Forms FormObject.
	 */
	// public function save_gf_to_db($user_id, $feed, $entry, $pass) {
	public function save_gf_to_db($entry, $form) {
		if ($form['id'] == $this->form_id) {
			// Check if already exists using email
			$SID = $this->get_student($entry[4]);

			// Create new if doesn't exist
			if (!$SID) $SID = $this->create_student($entry);

			// Add to other tables
			// $_SESSION['rosters'] = $this->add_to_roster($SID);
			$_SESSION['order_id'] = $this->add_to_orders($SID);
		}
	}

	/**
	 * Undocumented function
	 *
	 * @param  [type] $SID
	 * @return void
	 */
	private function add_to_orders($SID) {
		global $wpdb;

		$data = [
			'cart' => json_encode($_SESSION['cart']['items']),
			'student_id' => $SID,
			'status' => 'payment_incomplete',
		];
		$format = [
			'%s',
			'%d',
			'%s'
		];

		$table = $wpdb->prefix . 'infinite_orders';
		$wpdb->insert($table, $data, $format);
		return $wpdb->insert_id;
	}


	/**
	 * Undocumented function
	 *
	 * @param  [type] $confirm
	 * @param  [type] $form
	 * @param  [type] $entry
	 * @param  [type] $ajax
	 * @return void
	 */
	public function stripe_checkout_redirect($confirm, $form, $entry, $ajax) {
		// Get form confirmation URL
		$success = rgar($confirm, 'redirect');

		// Init session array
		$session = [
			'client_reference_id' => $_SESSION['order_id'],
			'success_url' => $success,
			'cancel_url' => get_home_url() . '/cart',
			'mode' => 'payment',
			'customer_email' => $entry[4],
		];

		// Get cart session and create array of line items
		$line_items = [];
		foreach ($_SESSION['cart']['items'] as $item) {
			if ($item['isRegistered']) continue;

			// Get image
			$image = get_field('featured_image', $item['id']);
			$price = get_field('course_price', $item['id']) * 100;
			$name = get_the_title($item['id']) . ' (' . get_field('course_code', $item['id']) . ')';

			//
			$line_items[] = [
				// 'price' => get_field('price_id', $item['id']),
				'price_data' => [
					'currency' => 'USD',
					'tax_behavior' => 'inclusive',
					'unit_amount' => $price,
					'product_data' => [
						'name' => $name,
						'description' => $item['sched'],
						'images' => [$image['url']],
					],
				],
				'quantity' => 1,
			];
		}
		$session['line_items'] = $line_items;

		require_once get_stylesheet_directory() . '/infinite/vendor/autoload.php';

		$stripe = new \Stripe\StripeClient($this->sk);

		$response = $stripe->checkout->sessions->create($session);
		if (isset($response->url)) $confirm['redirect'] = $response->url;

		return $confirm;
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function use_state_abbreviations($states) {
		$new_states = array();
		foreach ($states as $state) {
			$new_states[GF_Fields::get('address')->get_us_state_code($state)] = $state;
		}

		return $new_states;
	}

	/**
	 * Undocumented function
	 *
	 * @param  [type] $entry
	 * @return void
	 */
	private function create_student($entry) {
		global $wpdb;

		$customer = [
			// 'user_id'				=> $user_id,
			'first_name'		=> $entry['3.3'],
			'middle_name'		=> $entry['3.4'],
			'last_name'			=> $entry['3.6'],
			'suffix'				=> $entry['3.8'],
			'primary_phone'	=> $entry[5],
			'primary_email'	=> $entry[4],
			'street1'				=> $entry['7.1'],
			'street2'				=> $entry['7.2'],
			'city'					=> $entry['7.3'],
			'state'					=> $entry['7.4'],
			'postal_code'		=> $entry['7.5'],
			'license1'			=> $entry[10],
			'license2'			=> $entry[12],
			'tc_agreement'	=> 1,
		];
		$format = [
			// '%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
		];

		$table = $wpdb->prefix . 'infinite_students';
		$response = $wpdb->insert($table, $customer, $format);
		return $wpdb->insert_id;
	}

	/**
	 * Checks if exists using email (in the future check against user table)
	 *
	 * @return void
	 */
	private function get_student($email) {
		global $wpdb;

		$table = $wpdb->prefix . 'infinite_students';
		$rq = "SELECT ID FROM $table WHERE primary_email = '$email' LIMIT 1";
		$row = $wpdb->get_row($rq, ARRAY_A);

		if ($row) return $row['ID'];

		return false;
	}

	/**
	 * Undocumented function
	 *
	 * @param  [type] $OID
	 * @return void
	 */
	public function add_to_roster($OID) {
		global $wpdb;

		// Get the order
		$otable = $wpdb->prefix . 'infinite_orders';
		$order = $wpdb->get_row("SELECT * FROM $otable WHERE ID = $OID", ARRAY_A);

		$SID = $order['student_id'];
		$items = json_decode($order['cart'], true);

		// Add to roster
		foreach ($items as $item) {
			$roster = [
				'course_id' => intval($item['id']),
				'student_id' => intval($SID),
				'order_id' => intval($OID),
				'schedule' => $item['sched'],
			];
			$format = [
				'%d',
				'%d',
				'%d',
				'%s',
			];

			$rtable = $wpdb->prefix . 'infinite_rosters';
			$wpdb->insert($rtable, $roster, $format);
		}

		// Update order status
		$wpdb->update($otable, ['status' => 'payment_complete'], ['ID' => $OID], ['%s'], ['%d']);
	}

	/**
	 * Undocumented function
	 *
	 * @param  [type] $SID
	 * @return void
	 */
	private function check_roster($SID) {
		global $wpdb;

		foreach ($_SESSION['cart']['items'] as $key => $item) {
			$id = $item['id'];
			$sched = $item['sched'];
			$table = $wpdb->prefix . 'infinite_rosters';
			$rq = "SELECT * FROM $table WHERE student_id = $SID AND course_id = $id AND schedule = '$sched' LIMIT 1";
			$result = $wpdb->get_row($rq, ARRAY_A);
			if ($result) {
				$_SESSION['cart']['items'][$key]['isRegistered'] = true;
			} else {
				$_SESSION['cart']['items'][$key]['isRegistered'] = false;
			}
		}
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function stripe_wh_callback() {
		require_once get_stylesheet_directory() . '/infinite/vendor/autoload.php';
		$stripe = new \Stripe\StripeClient($this->sk);
		$endpoint_secret = get_field('wh_secret', 'option');
		$payload = @file_get_contents('php://input');
		$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
		$event = null;

		try {
			$event = \Stripe\Webhook::constructEvent(
				$payload,
				$sig_header,
				$endpoint_secret
			);
		} catch (\UnexpectedValueException $e) {
			// Invalid payload
			http_response_code(400);
			exit();
		} catch (\Stripe\Exception\SignatureVerificationException $e) {
			// Invalid signature
			http_response_code(400);
			exit();
		}

		// Handle the event
		switch ($event->type) {
			case 'checkout.session.completed':
				$OID = $event->data->object->client_reference_id;

				$this->add_to_roster($OID);
				break;
			default:
				// Received other event type
				break;
		}

		http_response_code(200);
	}
}

$INFINITE_GRAVITY_FORMS = new Infinite_Gravity_Forms();
