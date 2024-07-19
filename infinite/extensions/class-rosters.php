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

use setasign\Fpdi\Fpdi;

class Infinite_Rosters {
	/**
	 * The table name for the customers table.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $table_name    The name of the customers table.
	 */
	private $table_name = 'infinite_rosters';

	/**
	 * The tables config object.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      obj    $tables_config    The tables config object.
	 */
	private $tables_config;

	/**
	 * The init function
	 * 
	 * Use this to add custom hooks and filters to WP or to pre-load config files.
	 * In order for this function to work with WP hooks and filters, a class instance must
	 * be instantiated at the bottom of this doc.
	 */
	public function __construct() {
		// Load config files here
		$this->tables_config = $this->get_config($this->table_name);

		// Ajax hooks
		add_action('admin_init', [$this, 'ajax_hooks']);
	}

	/**
	 * Retrieves the tables config file and returns the specific tables config
	 * 
	 * @var		string		$table_name		The name of the table to get
	 */
	public function get_config($table_name) {
		if (defined('INF_TABLES') && property_exists(INF_TABLES, 'tables')) {
			foreach (INF_TABLES->tables as $table) {
				if ($table->table_name == $table_name) return $table;
			}
		}
	}

	/**
	 * Get customers table content.
	 *
	 * @since    1.0.0
	 * 
	 * @var		class		$INF	The class instance of Infinite_Admin or Infinite_Public
	 */
	public function get_table($INF) {
		global $wpdb;

		// Get column definitions here
		$cols = $this->tables_config->view_cols;

		// Get table rows here
		$rows = [];
		$table = $wpdb->prefix . $this->table_name;
		$ptable = $wpdb->prefix . 'posts';

		// Dynamic query params
		$s = (isset($_REQUEST['s'])) ? $_REQUEST['s'] : false;
		$orderby = (isset($_REQUEST['sortby'])) ? $_REQUEST['sortby'] : 'course_id';
		$direction = (isset($_REQUEST['sortdir'])) ? $_REQUEST['sortdir'] : 'DESC';
		$limit = (isset($_REQUEST['limit'])) ? $_REQUEST['limit'] : 25;
		$offset = (isset($_GET['pg'])) ? $limit * (intval($_GET['pg']) - 1) : 0;

		// Base queries
		$tq = "SELECT COUNT(*) FROM $table GROUP BY schedule";
		$rq = "SELECT *, COUNT(*) as registered_count FROM $table GROUP BY course_id, schedule";
		$rq2 = " ORDER BY $orderby $direction LIMIT $limit OFFSET $offset";

		// Search queries
		if ($s) {
			$pS = addslashes($s);
			$tq .= " WHERE ";
			$rq .= " WHERE ";

			foreach ($cols as $col) {
				if ($col->search) $sqa[] = $col->slug . " LIKE '%$pS%'";
			}

			$sq = implode(' OR ', $sqa);
			$tq .= $sq;
			$rq .= $sq;
		}

		// Queries
		$rq .= $rq2;

		// Count total number of records and figure page count
		$total = $wpdb->get_var($tq);
		$pages = ceil($total / $limit);

		// Get actual records
		$results = $wpdb->get_results($rq, ARRAY_A);

		// Insert Course Title into results array
		array_walk($results, function (&$value, $key) {
			$value['course_title'] = get_the_title($value['course_id']);
		});

		// Get primary action
		$primary_action = false;
		foreach ($this->tables_config->actions as $action) {
			if ($action->primary) $primary_action = $action->slug;
		}

		// Set additional template vars
		$actions = $this->tables_config->actions;
		$rows = $results;
		$screen = $INF->get_current_screen();
		$view = $INF->get_current_view();

		require_once WP_PLUGIN_DIR . '/infinite-plugin/admin/partials/comp_table.php';
	}

	/**
	 * Get a single customers details.
	 *
	 * @since    1.0.0
	 * 
	 * @var		class		$INF	The class instance of Infinite_Admin or Infinite_Public
	 */
	public function get_details($INF) {
		global $wpdb;

		// Rosters do not have ID's so initial ID is from a single roster record use that to get data
		$RID = $_REQUEST['ID'];
		$roster = $this->get_roster($RID);

		$data = $roster[0];

		$args = [
			'RID' => $RID,
			'title' => get_the_title($data['course_id']),
			'schedule' => $data['schedule'],
			'roster' => $roster,
		];

		get_template_part('infinite/admin/partials/view', 'roster', $args);
	}

	private function get_roster($RID) {
		global $wpdb;

		$table = $wpdb->prefix . $this->table_name;
		$ref = $wpdb->get_row("SELECT * FROM $table WHERE ID = $RID LIMIT 1", ARRAY_A);
		$schedule = $ref['schedule'];
		$CID = $ref['course_id'];

		$stable = $wpdb->prefix . 'infinite_students';
		$roster = $wpdb->get_results("SELECT Roster.ID as RID, Roster.course_id, Roster.student_id, Roster.schedule, Roster.passed, Students.ID, Students.first_name, Students.last_name, Students.license1, Students.license2 FROM $table as Roster LEFT JOIN $stable as Students ON Roster.student_id = Students.ID WHERE Roster.schedule = '$schedule' AND Roster.course_id = $CID ORDER BY Students.first_name ASC", ARRAY_A);

		return $roster;
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function ajax_hooks() {
		add_action('wp_ajax_nopriv_update_passed', [$this, 'ajax_update_passed']);
		add_action('wp_ajax_update_passed', [$this, 'ajax_update_passed']);
		//
		add_action('wp_ajax_nopriv_update_failed', [$this, 'ajax_update_failed']);
		add_action('wp_ajax_update_failed', [$this, 'ajax_update_failed']);
		//
		add_action('wp_ajax_nopriv_generate_signin', [$this, 'ajax_generate_signin']);
		add_action('wp_ajax_generate_signin', [$this, 'ajax_generate_signin']);
		//
		add_action('wp_ajax_nopriv_move_roster', [$this, 'ajax_move_roster']);
		add_action('wp_ajax_move_roster', [$this, 'ajax_move_roster']);
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function ajax_update_passed() {
		global $wpdb;

		// Verify Nonce
		if (!wp_verify_nonce($_REQUEST['nonce'], 'ajax_infinite_nonce')) {
			exit('No naughty business please');
		}

		$table = $wpdb->prefix . $this->table_name;
		$wpdb->update($table, ['passed' => true], ['ID' => $_REQUEST['RID'], 'student_id' => $_REQUEST['SID']]);

		// Return a JSON response
		echo json_encode(['success' => true, 'result' => 'PASS']);

		die();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function ajax_update_failed() {
		global $wpdb;

		// Verify Nonce
		if (!wp_verify_nonce($_REQUEST['nonce'], 'ajax_infinite_nonce')) {
			exit('No naughty business please');
		}

		$table = $wpdb->prefix . $this->table_name;
		$wpdb->update($table, ['passed' => false], ['ID' => $_REQUEST['RID'], 'student_id' => $_REQUEST['SID']]);

		// Return a JSON response
		echo json_encode(['success' => true, 'result' => 'FAIL']);

		die();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function ajax_move_roster() {
		global $wpdb;

		// Verify Nonce
		if (!wp_verify_nonce($_REQUEST['nonce'], 'ajax_move_roster')) {
			exit('No naughty business please');
		}

		global $wpdb;

		$RID = intval($_REQUEST['RID']);
		$data = $_REQUEST['updates'];
		$table = $wpdb->prefix . $this->table_name;
		$customer = $wpdb->update($table, $data, ['ID' => $RID], ['%s'], ['%d']);

		// Return to sender
		header('Location: ' . admin_url('admin.php?page=infinite-rosters'));

		// Just in case...
		die();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function ajax_generate_signin() {
		global $wpdb;

		// Verify Nonce
		if (!wp_verify_nonce($_REQUEST['nonce'], 'roster_nonce')) {
			exit('No naughty business please');
		}

		// Set headers
		//header("Content-Type: application/pdf");

		// Get student info
		$RID = $_REQUEST['RID'];
		$students = $this->get_roster($RID);
		//
		$data = $students[0];
		$CID = $data['course_id'];
		//
		$title = get_the_title($CID);
		$code = get_field('course_code', $CID);
		$instructor = get_field('instructor', $CID);

		require_once get_stylesheet_directory() . '/infinite/vendor/autoload.php';

		$tpl_path = get_stylesheet_directory() . '/infinite/pdf-templates/SigninTemplate.pdf';

		$pdf = new Fpdi('L', 'in', [8.5, 11]);
		$pdf->setSourceFile($tpl_path);
		$tpl = $pdf->importPage(1);
		$pdf->AddPage();
		$pdf->useTemplate($tpl, ['adjustPageSize' => true]);
		$pdf->SetFont('Helvetica');

		// Instructor
		$pdf->SetFontSize(10);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetXY(4.25, 0.6);
		$pdf->Write(0, $instructor);

		// Course Title
		$pdf->SetFontSize(18);
		$pdf->SetTextColor(0, 0, 255);
		$pdf->SetXY(2.95, 1.01);
		$pdf->Write(0, $title . ' (' . $code . ')');

		// Students
		$x = .95;
		$y = 1.28;
		$inc = 0.35;
		foreach ($students as $i => $student) {
			$i++; // Multiplier
			$adjust = $inc * $i;
			//
			$pdf->SetFontSize(12);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY($x, $y + $adjust);
			$pdf->Write(0, $student['first_name'] . ' ' . $student['last_name']);
			//
			$pdf->SetFontSize(12);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY($x + 5, $y + $adjust);
			if (isset($student['license2']) && !empty($student['license2'])) {
				$lic = 'XXX-XX-' . substr($student['license2'], 7, 4);
				$pdf->Write(0, $lic);
			} else {
				$pdf->Write(0, $student['license1']);
			}
		}

		$pdf->Output();

		die();
	}
}

$Infinite_Rosters = new Infinite_Rosters();
