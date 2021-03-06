<?php
/**
 * List Tables View
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * List Tables View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_List_View extends TablePress_View {

	/**
	 * Object for the All Tables List Table
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $wp_list_table;

	/**
	 * Set up the view with data and do things that are specific for this view
	 *
	 * @since 1.0.0
	 *
	 * @param string $action Action for this view
	 * @param array $data Data for this view
	 */
	public function setup( $action, $data ) {
		parent::setup( $action, $data );

		add_thickbox();
		$this->admin_page->enqueue_script( 'list', array( 'jquery' ), array(
			'list' => array(
				'shortcode_popup' => __( 'To embed this table into a post or page, use this Shortcode:', 'tablepress' ),
				'donation-message-already-donated' => __( 'Thank you very much! Your donation is highly appreciated. You just contributed to the further development of TablePress!', 'tablepress' ),
				'donation-message-maybe-later' => sprintf ( __( 'No problem! I still hope you enjoy the benefits that TablePress adds to your site. If you should change your mind, you\'ll always find the &#8220;Donate&#8221; button on the <a href="%s">TablePress website</a>.', 'tablepress' ), 'http://tablepress.org/' ),
			)
		) );

		if ( $data['messages']['first_visit'] )
			$this->add_header_message(
				'<strong><em>' . __( 'Welcome!', 'tablepress' ) . '</em></strong><br />'
				. __( 'Thank you for using TablePress for the first time!', 'tablepress' ) . ' '
				. sprintf( __( 'If you encounter any questions or problems, please visit the <a href="%1$s">FAQ</a>, the <a href="%2$s">documentation</a>, and the <a href="%3$s">Support</a> section on the <a href="%4$s">plugin website</a>.', 'tablepress' ), 'http://tablepress.org/faq/', 'http://tablepress.org/documentation/', 'http://tablepress.org/support/', 'http://tablepress.org/' ) . '<br /><br />'
				. $this->ajax_link( array( 'action' => 'hide_message', 'item' => 'first_visit', 'return' => 'list' ), __( 'Hide this message', 'tablepress' ) )
			);

		if ( $data['messages']['wp_table_reloaded_warning'] )
			$this->add_header_message(
				'<strong><em>' . __( 'Attention!', 'tablepress' ) . '</em></strong><br />'
				. __( 'You have activated the plugin WP-Table Reloaded, which can not be used together with TablePress.', 'tablepress' ) . '<br />'
				. __( 'It is strongly recommended that you switch from WP-Table Reloaded to TablePress, which not only fixes many problems, but also has more and better features than WP-Table Reloaded.', 'tablepress' ) . '<br />'
				. sprintf( __( 'Please follow the <a href="%s">migration guide</a> to move your tables and then deactivate WP-Table Reloaded!', 'tablepress' ), 'http://tablepress.org/migration-from-wp-table-reloaded/' ) . '<br />'
				. '<a href="' . TablePress::url( array( 'action' => 'import' ) ) . '" class="button button-primary button-large" title="' . __( 'Import your tables from WP-Table Reloaded', 'tablepress' ) . '" style="color:#ffffff;margin-top:5px;">' . __( 'Import your tables from WP-Table Reloaded', 'tablepress' ) . '</a>',
				'error'
			);

		if ( $data['messages']['donation_message'] )
			$this->add_header_message(
				'<img alt="' . __( 'Tobias Bäthge, developer of TablePress', 'tablepress' ) . '" src="https://secure.gravatar.com/avatar/50f1cff2e27a1f522b18ce229c057bc5?s=94" height="94" width="94" style="float:left;margin-right:10px;" />' .
				__( 'Hi, my name is Tobias, I\'m the developer of the TablePress plugin.', 'tablepress' ) . '<br /><br />' .
				__( 'Thanks for using it! You\'ve installed TablePress over a month ago.', 'tablepress' ) . ' ' .
				sprintf( _n( 'If everything works and you are satisfied with the results of managing your %s table, isn\'t that worth a coffee or two?', 'If everything works and you are satisfied with the results of managing your %s tables, isn\'t that worth a coffee or two?', $data['table_count'], 'tablepress' ), $data['table_count'] ) . '<br />' .
				sprintf( __( '<a href="%s">Donations</a> help me to continue user support and development of this <em>free</em> software &mdash; things for which I spend countless hours of my free time! Thank you very much!', 'tablepress' ), 'http://tablepress.org/donate/' ) . '<br /><br />' .
				__( 'Sincerly, Tobias', 'tablepress' ) . '<br /><br />' .
				sprintf( '<a href="%s" target="_blank"><strong>%s</strong></a>', 'http://tablepress.org/donate/', __( 'Sure, I\'ll buy you a coffee and support TablePress!', 'tablepress' ) ) . '&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;&nbsp;&nbsp;&nbsp;' .
				$this->ajax_link( array( 'action' => 'hide_message', 'item' => 'donation_nag', 'return' => 'list', 'target' => 'already-donated' ), __( 'I already donated.', 'tablepress' ) ) . '&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;&nbsp;&nbsp;&nbsp;' .
				$this->ajax_link( array( 'action' => 'hide_message', 'item' => 'donation_nag', 'return' => 'list', 'target' => 'maybe-later' ), __( 'No, thanks. Don\'t ask again.', 'tablepress' ) )
			);

		if ( $data['messages']['show_plugin_update'] ) {
			$message = '<strong><em>' . sprintf( __( 'Thank you for updating to TablePress %s!', 'tablepress' ), TablePress::version ) . '</em></strong><br />';
			if ( ! empty( $data['messages']['plugin_update_message'] ) )
				$message .= $data['messages']['plugin_update_message'] . '<br />';
			$message .= sprintf( __( 'Please read the <a href="%s">release announcement</a> for more information.', 'tablepress' ), 'http://tablepress.org/news/' ) . ' '
				. sprintf( __( 'If you like the new features and enhancements, <a href="%s">giving a donation</a> towards the further support and development of TablePress is recommended. Thank you!', 'tablepress' ), 'http://tablepress.org/donate/' )
				. '<br /><br />';
			$message .= $this->ajax_link( array( 'action' => 'hide_message', 'item' => 'plugin_update', 'return' => 'list' ), __( 'Hide this message', 'tablepress' ) );
			$this->add_header_message( $message );
		}

		$this->process_action_messages( array(
			'success_delete' => _n( 'The table was deleted successfully.', 'The tables were deleted successfully.', 1, 'tablepress' ),
			'success_delete_plural' => _n( 'The table was deleted successfully.', 'The tables were deleted successfully.', 2, 'tablepress' ),
			'error_delete' => __( 'Error: The table could not be deleted.', 'tablepress' ),
			'error_save' => __( 'Error: The table could not be saved.', 'tablepress' ),
			'success_copy' => _n( 'The table was copied successfully.', 'The tables were copied successfully.', 1, 'tablepress' ),
			'success_copy_plural' => _n( 'The table was copied successfully.', 'The tables were copied successfully.', 2, 'tablepress' ),
			'error_copy' => __( 'Error: The table could not be copied.', 'tablepress' ),
			'error_no_table' => __( 'Error: You did not specify a valid table ID.', 'tablepress' ),
			'error_load_table' => __( 'Error: This table could not be loaded!', 'tablepress' ),
			'error_bulk_action_invalid' => __( 'Error: This bulk action is invalid!', 'tablepress' ),
			'error_no_selection' => __( 'Error: You did not select any tables!', 'tablepress' ),
			'error_delete_not_all_tables' => __( 'Notice: Not all selected tables could be deleted!', 'tablepress' ),
			'error_copy_not_all_tables' => __( 'Notice: Not all selected tables could be copied!', 'tablepress' ),
			'success_import' => __( 'The tables were imported successfully.', 'tablepress' ),
			'success_import_wp_table_reloaded' => __( 'The tables were imported successfully from WP-Table Reloaded.', 'tablepress' )
		) );

		$this->add_text_box( 'head', array( $this, 'textbox_head' ), 'normal' );
		$this->add_text_box( 'tables-list', array( $this, 'textbox_tables_list' ), 'normal' );

		add_screen_option( 'per_page', array( 'label' => __( 'Tables', 'tablepress' ), 'default' => 20 ) ); // Admin_Controller contains function to allow changes to this in the Screen Options to be saved
		$this->wp_list_table = new TablePress_All_Tables_List_Table();
		$this->wp_list_table->set_items( $this->data['tables'] );
		$this->wp_list_table->prepare_items();

		// cleanup Request URI string, which WP_List_Table uses to generate the sort URLs
		$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'message', 'table_id' ), $_SERVER['REQUEST_URI'] );
	}

	/**
	 * Render the current view (in this view: without form tag)
	 *
	 * @since 1.0.0
	 */
	public function render() {
		?>
		<div id="tablepress-page" class="wrap">
		<?php screen_icon( 'tablepress' ); ?>
		<?php
			$this->print_nav_tab_menu();
			// print all header messages
			foreach ( $this->header_messages as $message ) {
				echo $message;
			}

			// For this screen, this is done in textbox_tables_list(), to get the fields into the correct <form>:
			// $this->do_text_boxes( 'header' );
		?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-<?php echo ( isset( $GLOBALS['screen_layout_columns'] ) && ( 2 == $GLOBALS['screen_layout_columns'] ) ) ? '2' : '1'; ?>">
					<div id="postbox-container-2" class="postbox-container">
						<?php
						$this->do_text_boxes( 'normal' );
						$this->do_meta_boxes( 'normal' );

						$this->do_text_boxes( 'additional' );
						$this->do_meta_boxes( 'additional' );

						// print all submit buttons
						$this->do_text_boxes( 'submit' );
						?>
					</div>
					<div id="postbox-container-1" class="postbox-container">
					<?php
						// print all boxes in the sidebar
						$this->do_text_boxes( 'side' );
						$this->do_meta_boxes( 'side' );
					?>
					</div>
				</div>
				<br class="clear" />
			</div>
		</div>
		<?php
	}

	/**
	 * Print the screen head text
	 *
	 * @since 1.0.0
	 */
	public function textbox_head( $data, $box ) {
		?>
		<p>
			<?php _e( 'This is a list of your tables.', 'tablepress' ); ?>
			<?php _e( 'Click the corresponding links within the list to edit, copy, delete, or preview a table.', 'tablepress' ); ?>
		</p>
		<p>
			<?php printf( __( 'To insert a table into a page, post, or text widget, copy its Shortcode %s and paste it at the desired place in the editor.', 'tablepress' ), '<input type="text" class="table-shortcode table-shortcode-inline" value="[' . TablePress::$shortcode . ' id=&lt;ID&gt; /]" readonly="readonly" />' ); ?>
			<?php _e( 'Each table has a unique ID that needs to be adjusted in that Shortcode.', 'tablepress' ); ?>
			<?php printf( __( 'You can also click the &#8220;%s&#8221; button in the editor toolbar to select and insert a table.', 'tablepress' ), __( 'Table', 'tablepress' ) ); ?>
		</p>
		<?php
	}

	/**
	 * Print the content of the "All Tables" text box
	 *
	 * @since 1.0.0
	 */
	public function textbox_tables_list( $data, $box ) {
		if ( ! empty( $_GET['s'] ) )
			printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;', 'tablepress' ) . '</span>', esc_html( stripslashes( $_GET['s'] ) ) );
	?>
<form method="get" action="">
	<?php
	if ( isset( $_GET['page'] ) )
		echo '<input type="hidden" name="page" value="' . esc_attr( $_GET['page'] ) . '" />' . "\n";
	$this->wp_list_table->search_box( __( 'Search Tables', 'tablepress' ), 'tables_search' ); ?>
</form>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<?php
		// this prints the nonce and action fields for this screen (done here instead of render(), due to moved <form>):
		$this->do_text_boxes( 'header' );
		$this->wp_list_table->display();
	?>
</form>
	<?php
	}

	/**
	 * Create HTML code for an AJAXified link
	 *
	 * @since 1.0.0
	 *
	 * @param array $params Parameters for the URL
	 * @param string $text Text for the link
	 * @return string HTML code for the link
	 */
	protected function ajax_link( $params = array( 'action' => 'list', 'item' => '' ), $text ) {
		$url = TablePress::url( $params, true, 'admin-post.php' );
		$action = esc_attr( $params['action'] );
		$item = esc_attr( $params['item'] );
		$target = isset( $params['target'] ) ? esc_attr( $params['target'] ) : '';
		return "<a class=\"ajax-link\" href=\"{$url}\" data-action=\"{$action}\" data-item=\"{$item}\" data-target=\"{$target}\">{$text}</a>";
	}

	/**
	 * Return the content for the help tab for this screen
	 *
	 * @since 1.0.0
	 */
	protected function help_tab_content() {
		return 'Help for the List Tables screen';
	}

} // class TablePress_List_View

/**
 * TablePress All Tables List Table Class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @see http://codex.wordpress.org/Class_Reference/WP_List_Table
 * @since 1.0.0
 * @uses WP_List_Table
 */
class TablePress_All_Tables_List_Table extends WP_List_Table {

	/**
	 * Number of items of the initial data set (before sort, search, and pagination)
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $items_count = 0;

	/**
	 * Initialize the List Table
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$screen = get_current_screen();

		// Hide "Last Modified By" column by default
		if ( false === get_user_option( 'manage' . $screen->id . 'columnshidden' ) )
			update_user_option( get_current_user_id(), 'manage' . $screen->id . 'columnshidden', array( 'table_last_modified_by' ), true );

		parent::__construct( array(
			'singular'	=> 'tablepress-table',		// singular name of the listed records
			'plural'	=> 'tablepress-all-tables', // plural name of the listed records
			'ajax'		=> false,					// does this list table support AJAX?
			'screen'	=> $screen					// WP_Screen object
		) );
	}

	/**
	 * Set the data items (here: tables) that are to be displayed by the List Tables, and their original count
	 *
	 * @since 1.0.0
	 *
	 * @param array $items Tables to be displayed in the List Table
	 */
	public function set_items( $items ) {
		$this->items = $items;
		$this->items_count = count( $items );
	}

	/**
	 * Check whether the user has permissions for certain AJAX actions
	 * (not used, but must be implemented in this child class)
	 *
	 * @since 1.0.0
	 *
	 * @return bool true (Default value)
	 */
	public function ajax_user_can() {
		return true;
	}

	/**
	 * Get a list of columns in this List Table.
	 * Format: 'internal-name' => 'Column Title'
	 *
	 * @since 1.0.0
	 *
	 * @return array List of columns in this List Table
	 */
	public function get_columns() {
		$columns = array(
			'cb' => $this->has_items() ? '<input type="checkbox" />' : '', // checkbox for "Select all", but only if there are items in the table
			'table_id' => __( 'ID', 'tablepress' ),
			'table_name' => __( 'Table Name', 'tablepress' ), // just "name" is special in WP, which is why we prefix every entry here, to be safe!
			'table_description' => __( 'Description', 'tablepress' ),
			'table_author' => __( 'Author', 'tablepress' ),
			'table_last_modified_by' => __( 'Last Modified By', 'tablepress' ),
			'table_last_modified' => __( 'Last Modified', 'tablepress' )
		);
		return $columns;
	}

	/**
	 * Get a list of columns that are sortable
	 * Format: 'internal-name' => array( $field for $item[$field], true for already sorted )
	 *
	 * @since 1.0.0
	 *
	 * @return array List of sortable columns in this List Table
	 */
	public function get_sortable_columns() {
		// no sorting on the Empty List placeholder
		if ( ! $this->has_items() )
			return array();

		$sortable_columns = array(
			'table_id' => array( 'id', true ), // true means its already sorted
			'table_name' => array( 'name', false ),
			'table_description' => array( 'description', false ),
			'table_author' => array( 'author', false ),
			'table_last_modified_by' => array( 'last_modified_by', false ),
			'table_last_modified' => array( 'last_modified', false )
		);
		return $sortable_columns;
	}

	/**
	 * Render a cell in the "cb" column
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row
	 * @return string HTML content of the cell
	 */
	protected function column_cb( $item ) {
		$user_can_copy_table = current_user_can( 'tablepress_copy_table', $item['id'] );
		$user_can_delete_table = current_user_can( 'tablepress_delete_table', $item['id'] );
		$user_can_export_table = current_user_can( 'tablepress_export_table', $item['id'] );

		if ( $user_can_copy_table || $user_can_delete_table || $user_can_export_table )
			return '<input type="checkbox" name="table[]" value="' . esc_attr( $item['id'] ) . '" />';
		else
			return '';
	}

	/**
	 * Render a cell in the "table_id" column
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row
	 * @return string HTML content of the cell
	 */
	protected function column_table_id( $item ) {
		return esc_html( $item['id'] );
	}

	/**
	 * Render a cell in the "table_name" column
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row
	 * @return string HTML content of the cell
	 */
	protected function column_table_name( $item ) {
		$user_can_edit_table = current_user_can( 'tablepress_edit_table', $item['id'] );
		$user_can_copy_table = current_user_can( 'tablepress_copy_table', $item['id'] );
		$user_can_delete_table = current_user_can( 'tablepress_delete_table', $item['id'] );
		$user_can_export_table = current_user_can( 'tablepress_export_table', $item['id'] );
		$user_can_preview_table = current_user_can( 'tablepress_preview_table', $item['id'] );

		$edit_url = TablePress::url( array( 'action' => 'edit', 'table_id' => $item['id'] ) );
		$copy_url = TablePress::url( array( 'action' => 'copy_table', 'item' => $item['id'], 'return' => 'list', 'return_item' => $item['id'] ), true, 'admin-post.php' );
		$export_url = TablePress::url( array( 'action' => 'export', 'table_id' => $item['id'] ) );
		$delete_url = TablePress::url( array( 'action' => 'delete_table', 'item' => $item['id'], 'return' => 'list', 'return_item' => $item['id'] ), true, 'admin-post.php' );
		$preview_url = TablePress::url( array( 'action' => 'preview_table', 'item' => $item['id'], 'return' => 'list', 'return_item' => $item['id'] ), true, 'admin-post.php' );

		if ( '' == trim( $item['name'] ) )
			$item['name'] = __( '(no name)', 'tablepress' );

		if ( $user_can_edit_table )
			$row_text = '<strong><a title="' . sprintf ( __( 'Edit &#8220;%s&#8221;', 'tablepress' ), esc_attr( $item['name'] ) ) . '" class="row-title" href="' . $edit_url . '">' . esc_html( $item['name'] ) . '</a></strong>';
		else
			$row_text = '<strong>' . esc_html( $item['name'] ) . '</strong>';

		$row_actions = array();
		if ( $user_can_edit_table )
			$row_actions['edit'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $edit_url, sprintf ( __( 'Edit &#8220;%s&#8221;', 'tablepress' ), esc_attr( $item['name'] ) ), __( 'Edit', 'tablepress' ) );
		$row_actions['shortcode hide-if-no-js'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', '#', '[' . TablePress::$shortcode . ' id=' . esc_attr( $item['id'] ) . ' /]', __( 'Show Shortcode', 'tablepress' ) );
		if ( $user_can_copy_table )
			$row_actions['copy'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $copy_url, sprintf ( __( 'Copy &#8220;%s&#8221;', 'tablepress' ), esc_attr( $item['name'] ) ), __( 'Copy', 'tablepress' ) );
		if ( $user_can_export_table )
			$row_actions['export'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $export_url, sprintf ( __( 'Export &#8220;%s&#8221;', 'tablepress' ), esc_attr( $item['name'] ) ), _x( 'Export', 'row action', 'tablepress' ) );
		if ( $user_can_delete_table )
			$row_actions['delete'] = sprintf( '<a href="%1$s" title="%2$s" class="delete-link">%3$s</a>', $delete_url, sprintf ( __( 'Delete &#8220;%s&#8221;', 'tablepress' ), esc_attr( $item['name'] ) ), __( 'Delete', 'tablepress' ) );
		if ( $user_can_preview_table )
			$row_actions['table-preview'] = sprintf( '<a href="%1$s" title="%2$s" target="_blank">%3$s</a>', $preview_url, sprintf ( __( 'Show a preview of &#8220;%s&#8221;', 'tablepress' ), esc_attr( $item['name'] ) ), __( 'Preview', 'tablepress' ) );

		return $row_text . $this->row_actions( $row_actions );
	}

	/**
	 * Render a cell in the "table_description" column
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row
	 * @return string HTML content of the cell
	 */
	protected function column_table_description( $item ) {
		if ( '' == trim( $item['description'] ) )
			$item['description'] = __( '(no description)', 'tablepress' );
		return esc_html( $item[ 'description' ] );
	}

	/**
	 * Render a cell in the "table_author" column
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row
	 * @return string HTML content of the cell
	 */
	protected function column_table_author( $item ) {
		return TablePress::get_user_display_name( $item['author'] );
	}

	/**
	 * Render a cell in the "last_modified_by" column
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row
	 * @return string HTML content of the cell
	 */
	protected function column_table_last_modified_by( $item ) {
		return TablePress::get_user_display_name( $item['options']['last_editor'] );
	}

	/**
	 * Render a cell in the "table_last_modified" column
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Data item for the current row
	 * @return string HTML content of the cell
	 */
	protected function column_table_last_modified( $item ) {
		$modified_timestamp = strtotime( $item['last_modified'] );
		$current_timestamp = current_time( 'timestamp' );
		$time_diff = $current_timestamp - $modified_timestamp;
		if ( $time_diff >= 0 && $time_diff < DAY_IN_SECONDS ) // time difference is only shown up to one day
			$time_diff = sprintf( __( '%s ago', 'tablepress' ), human_time_diff( $modified_timestamp, $current_timestamp ) );
		else
			$time_diff = TablePress::format_datetime( $item['last_modified'], 'mysql', '<br />' );

		$readable_time = TablePress::format_datetime( $item['last_modified'], 'mysql', ' ' );
		return '<abbr title="' . $readable_time . '">' . $time_diff . '</abbr>';
	}

	/**
	 * Get a list (name => title) bulk actions that are available
	 *
	 * @since 1.0.0
	 *
	 * @return array Bulk actions for this table
	 */
	public function get_bulk_actions() {
		$bulk_actions = array();

		if ( current_user_can( 'tablepress_copy_tables' ) )
			$bulk_actions['copy'] = _x( 'Copy', 'bulk action', 'tablepress' );
		if ( current_user_can( 'tablepress_export_tables' ) )
			$bulk_actions['export'] = _x( 'Export', 'bulk action', 'tablepress' );
		if ( current_user_can( 'tablepress_delete_tables' ) )
			$bulk_actions['delete'] = _x( 'Delete', 'bulk action', 'tablepress' );

		return $bulk_actions;
	}

	/**
	 * Render the bulk actions dropdown
	 * In comparsion with parent class, this has modified HTML (especially no field named "action" as that's being used already)!
	 *
	 * @since 1.0.0
	 */
	public function bulk_actions() {
		if ( is_null( $this->_actions ) ) {
			$no_new_actions = $this->_actions = $this->get_bulk_actions();
			// This filter can currently only be used to remove actions.
			$this->_actions = apply_filters( 'bulk_actions-' . $this->screen->id, $this->_actions );
			$this->_actions = array_intersect_assoc( $this->_actions, $no_new_actions );
			$two = '';
			$name_id = 'bulk-action-top';
		} else {
			$two = '2';
			$name_id = 'bulk-action-bottom';
		}

		if ( empty( $this->_actions ) )
			return;

		echo "<select name='$name_id' id='$name_id'>\n";
		echo "<option value='-1' selected='selected'>" . __( 'Bulk Actions', 'tablepress' ) . "</option>\n";
		foreach ( $this->_actions as $name => $title ) {
			echo "\t<option value='$name'$>$title</option>\n";
		}
		echo "</select>\n";
		echo '<input type="submit" name="" id="doaction' . $two . '" class="button action" value="' . __( 'Apply', 'tablepress' ) . '" />' . "\n";
	}

	/**
	 * Holds the message to be displayed when there are no items in the table
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		_e( 'No tables found.', 'tablepress' );
		if ( 0 === $this->items_count ) {
			$user_can_add_tables = current_user_can( 'tablepress_add_tables' );
			$user_can_import_tables = current_user_can( 'tablepress_import_tables' );

			$add_url = TablePress::url( array( 'action' => 'add' ) );
			$import_url = TablePress::url( array( 'action' => 'import' ) );

			if ( $user_can_add_tables && $user_can_import_tables )
				echo ' ' . sprintf( __( 'You should <a href="%s">add</a> or <a href="%s">import</a> a table to get started!', 'tablepress' ), $add_url, $import_url );
			elseif ( $user_can_add_tables )
				echo ' ' . sprintf( __( 'You should <a href="%s">add</a> a table to get started!', 'tablepress' ), $add_url );
			elseif ( $user_can_import_tables )
				echo ' ' . sprintf( __( 'You should <a href="%s">import</a> a table to get started!', 'tablepress' ), $import_url );
		}
	}

	/**
	 * Generate the elements above or below the table (like bulk actions and pagination)
	 * In comparsion with parent class, this has modified HTML (no nonce field), and a check whether there are items.
	 *
	 * @since 1.0.0
	 *
	 * @param string $which Location ("top" or "bottom")
	 */
	public function display_tablenav( $which ) {
		if ( ! $this->has_items() )
			return;
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<div class="alignleft actions">
				<?php $this->bulk_actions( $which ); ?>
			</div>
		<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
		?>
			<br class="clear" />
		</div>
		<?php
	}

	/**
	 * Callback to determine whether the given $item contains the search term
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Item that shall be searched
	 * @return bool Whether the search term was found or not
	 */
	protected function _search_callback( $item ) {
		static $term;
		if ( is_null( $term ) )
			$term = stripslashes( $_GET['s'] );

		$item = TablePress::$controller->model_table->load( $item['id'] ); // load table again, with data

		// search from easy to hard, so that "expensive" code maybe doesn't have to run
		if ( false !== stripos( $item['id'], $term )
		|| false !== stripos( $item['name'], $term )
		|| false !== stripos( $item['description'], $term )
		|| false !== stripos( TablePress::get_user_display_name( $item['author'] ), $term )
		|| false !== stripos( TablePress::get_user_display_name( $item['options']['last_editor'] ), $term )
		|| false !== stripos( TablePress::format_datetime( $item['last_modified'], 'mysql', ' ' ), $term )
		|| false !== stripos( json_encode( $item['data'] ), $term ) )
			return true;

		return false;
	}

	/**
	 * Callback to for the array sort function
	 *
	 * @since 1.0.0
	 *
	 * @param array $item_a First item that shall be compared to...
	 * @param array $item_b the second item
	 * @return int (-1, 0, 1) depending on which item sorts "higher"
	 */
	protected function _order_callback( $item_a, $item_b ) {
		global $orderby, $order;

		if ( 'last_modified_by' != $orderby ) {
			if ( $item_a[$orderby] == $item_b[$orderby] )
				return 0;
		} else {
			if ( $item_a['options']['last_editor'] == $item_b['options']['last_editor'] )
				return 0;
		}

		// certain fields require some extra work before being sortable
		switch ( $orderby ) {
			case 'last_modified':
				// Compare UNIX timestamps for "last modified", which actually is a mySQL datetime string
				$result = ( strtotime( $item_a['last_modified'] ) > strtotime( $item_b['last_modified'] ) ) ? 1 : -1;
				break;
			case 'author':
				// Get the actual author name, plain value is just the user ID
				$result = strnatcasecmp( TablePress::get_user_display_name( $item_a['author'] ), TablePress::get_user_display_name( $item_b['author'] ) );
				break;
			case 'last_modified_by':
				// Get the actual last editor name, plain value is just the user ID
				$result = strnatcasecmp( TablePress::get_user_display_name( $item_a['options']['last_editor'] ), TablePress::get_user_display_name( $item_b['options']['last_editor'] ) );
				break;
			default:
				// other fields (ID, name, description) are sorted as strings
				$result = strnatcasecmp( $item_a[$orderby], $item_b[$orderby] );
		}

		return ( 'asc' == $order ) ? $result : - $result;
	}

	/**
	 * Prepares the list of items for displaying, by maybe searching and sorting, and by doing pagination
	 *
	 * @since 1.0.0
	 */
	public function prepare_items() {
		global $orderby, $order, $s;
		wp_reset_vars( array( 'orderby', 'order', 's' ) );

		// Maybe search in the items
		if ( $s )
			$this->items = array_filter( $this->items, array( $this, '_search_callback' ) );

		// Maybe sort the items
		$_sortable_columns = $this->get_sortable_columns();
		if ( $orderby && ! empty( $this->items ) && isset( $_sortable_columns["table_{$orderby}"] ) )
			usort( $this->items, array( $this, '_order_callback' ) );

		// number of records to show per page
		$per_page = $this->get_items_per_page( 'tablepress_list_per_page', 20 ); // hard-coded, as in filter in Admin_Controller
		// page number the user is currently viewing
		$current_page = $this->get_pagenum();
		// number of records in the array
		$total_items = count( $this->items );

		// Slice items array to hold only items for the current page
		$this->items = array_slice( $this->items, ( ( $current_page-1 ) * $per_page ), $per_page );

		// Register pagination options and calculation results
		$this->set_pagination_args( array(
			'total_items' => $total_items,					// total number of records/items
			'per_page' => $per_page,						// number of items per page
			'total_pages' => ceil( $total_items/$per_page ) // total number of pages
		) );
	}

} // class TablePress_All_Tables_List_Table