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
	 * Undocumented function
	 *
	 * @return void
	 */
	public function ajax_hooks() {
		// add_action('wp_ajax_nopriv_update_student', [$this, 'ajax_update_student']);
		// add_action('wp_ajax_update_student', [$this, 'ajax_update_student']);
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
		$ID = $_REQUEST['ID'];
		$rtable = $wpdb->prefix . $this->table_name;
		$ref = $wpdb->get_row("SELECT * FROM $rtable WHERE ID = $ID LIMIT 1", ARRAY_A);

		// Get full roster
		$schedule = $ref['schedule'];
		$course_id = $ref['course_id'];
		$stable = $wpdb->prefix . 'infinite_students';
		$roster = $wpdb->get_results("SELECT Roster.ID, Roster.course_id, Roster.student_id, Roster.schedule, Students.ID, Students.first_name, Students.last_name, Students.license1, Students.license2 FROM $rtable as Roster LEFT JOIN $stable as Students ON Roster.student_id = Students.ID WHERE Roster.schedule = '$schedule' AND Roster.course_id = $course_id ORDER BY Students.first_name ASC", ARRAY_A);

		$args = [
			'title' => get_the_title($course_id),
			'schedule' => $schedule,
			'roster' => $roster,
		];

		get_template_part('infinite/admin/partials/view', 'roster', $args);
	}
}

$Infinite_Rosters = new Infinite_Rosters();
