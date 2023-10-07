<?php
/*
Plugin Name: GWD SCSS Block
Description: Custom post type and styles for SCSS blocks.
Version: 1.0
Author: Wandering Woods Studio
*/

require_once(plugin_dir_path(__FILE__) . 'lib/scssphp/scssphp-1.11.1/scss.inc.php');
use ScssPhp\ScssPhp\Compiler;

/*======
 * CREATE UPLOAD DIRECTORY
 *======*/

function gwd_create_upload_directory() {
	$upload_dir = wp_upload_dir();
	$scss_dir = $upload_dir['basedir'] . '/gwd_scss_block';

	if (!file_exists($scss_dir)) {
		mkdir($scss_dir);
	}
}
register_activation_hook(__FILE__, 'gwd_create_upload_directory');



/*======
 * REGISTER POST TYPE
 *======*/

function gwd_register_scss_block_post_type() {
	$labels = array(
		'name' => 'SCSS Blocks',
		'singular_name' => 'SCSS Block',
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'has_archive' => false,
		'rewrite' => array('slug' => 'gwd_scss_block'),
		'supports' => array('title'),
	);

	register_post_type('gwd_scss_block', $args);
}
add_action('init', 'gwd_register_scss_block_post_type');



/*======
 * REGISTER CUSTOM FIELDS
 *======*/
 
function gwd_enqueue_codemirror() {
	// Get the current screen
	$current_screen = get_current_screen();
	
	// Check if it's the editor screen for the "gwd_scss_block" post type
	if ($current_screen && $current_screen->post_type === 'gwd_scss_block' && $current_screen->base === 'post') {
		// Enqueue CodeMirror core script (version 6.65.7).
		wp_enqueue_script('codemirror', plugin_dir_url(__FILE__) . 'assets/codemirror/lib/codemirror.js', array(), '6.65.7', true);
		wp_enqueue_style('codemirror', plugin_dir_url(__FILE__) . 'assets/codemirror/lib/codemirror.css', array(), '6.65.7');
		
		wp_enqueue_script('codemirror-mode-css', plugin_dir_url(__FILE__) . 'assets/codemirror/mode/css/css.js', array('codemirror'), '6.65.7', true );
		wp_enqueue_script('codemirror-mode-sass', plugin_dir_url(__FILE__) . 'assets/codemirror/mode/sass/sass.js', array('codemirror', 'codemirror-mode-css'), '6.65.7', true );
		
		// Load necessary CodeMirror addons and modes.
		wp_enqueue_script('codemirror-addon-lint', plugin_dir_url(__FILE__) . 'assets/codemirror/addon/lint/lint.js', array('codemirror'), '6.65.7', true);
		wp_enqueue_script('codemirror-addon-lint-css', plugin_dir_url(__FILE__) . 'assets/codemirror/addon/lint/css-lint.js', array('codemirror', 'codemirror-addon-lint'), '6.65.7', true);
		wp_enqueue_script('codemirror-addon-lint-html', plugin_dir_url(__FILE__) . 'assets/codemirror/addon/lint/html-lint.js', array('codemirror', 'codemirror-addon-lint'), '6.65.7', true);
		wp_enqueue_script('codemirror-addon-lint-javascript', plugin_dir_url(__FILE__) . 'assets/codemirror/addon/lint/javascript-lint.js', array('codemirror', 'codemirror-addon-lint'), '6.65.7', true);
		
		// Load CodeMirror features and addons.
		wp_enqueue_script('codemirror-addon-search', plugin_dir_url(__FILE__) . 'assets/codemirror/addon/search/search.js', array('codemirror'), '6.65.7', true);
		wp_enqueue_script('codemirror-addon-jump-to-line', plugin_dir_url(__FILE__) . 'assets/codemirror/addon/search/jump-to-line.js', array('codemirror'), '6.65.7', true);
		wp_enqueue_script('codemirror-addon-fullscreen', plugin_dir_url(__FILE__) . 'assets/codemirror/addon/display/fullscreen.js', array('codemirror'), '6.65.7', true);
		wp_enqueue_script('codemirror-addon-active-line', plugin_dir_url(__FILE__) . 'assets/codemirror/addon/selection/active-line.js', array('codemirror'), '6.65.7', true);
		wp_enqueue_script('codemirror-addon-closebrackets', plugin_dir_url(__FILE__) . 'assets/codemirror/addon/edit/closebrackets.js', array('codemirror'), '6.65.7', true);
		wp_enqueue_script('codemirror-addon-matchbrackets', plugin_dir_url(__FILE__) . 'assets/codemirror/addon/edit/matchbrackets.js', array('codemirror'), '6.65.7', true);
		wp_enqueue_script('codemirror-addon-matchtags', plugin_dir_url(__FILE__) . 'assets/codemirror/addon/edit/matchtags.js', array('codemirror'), '6.65.7', true);
		
		// Load the Dracula theme for CodeMirror.
		wp_enqueue_style('codemirror-dracula-theme', plugin_dir_url(__FILE__) . 'assets/codemirror/theme/dracula.css', array('codemirror'), '6.65.7');
		
		wp_enqueue_script('codemirror-init', plugin_dir_url(__FILE__) . 'assets/codemirror-init.js', array('codemirror'), '1.2', true);
	}
 }
add_action('admin_enqueue_scripts', 'gwd_enqueue_codemirror');
 
function gwd_add_custom_fields_meta_box() {
	 add_meta_box(
		 'gwd_codemirror_metabox',
		 'SCSS Code',
		 'gwd_codemirror_metabox_callback',
		 'gwd_scss_block', // Replace with your post type
		 'normal',
		 'default'
	 );
	 
	 add_meta_box(
		 'gwd_custom_fields_box',
		 'Enqueue Settings',
		 'gwd_custom_fields_callback',
		 'gwd_scss_block', // Post type where you want to add the meta box.
		 'normal',
		 'default'
	 );
 }
 
function gwd_codemirror_metabox_callback($post) {
	// Retrieve the current value
	$codemirror_content = get_post_meta($post->ID, 'gwd_codemirror_content', true);
	 
	// Output the textarea
	echo '<textarea id="gwd_codemirror_content" name="gwd_codemirror_content" style="width: 100%; height: 80vh; min-height: 300px;">' . esc_textarea($codemirror_content) . '</textarea><style type="text/css">.CodeMirror-activeline {background-color: rgba(255,255,255,0.05); /* Customize the background color */border-left: 2px solid #fff; /* Customize the left border */}</style>';
}
 
function gwd_custom_fields_callback($post) {
	// Get the current values of the custom fields
	$location_value = get_post_meta($post->ID, 'gwd_scss_block_location', true);
	$conditional_selector = get_post_meta($post->ID, 'gwd_scss_block_conditional_selector', true);
	$priority_value = get_post_meta($post->ID, 'gwd_scss_block_priority', true);
 
	// Output HTML for the fields
	?>
	<br/><label for="gwd_scss_block_location">Where to Include:</label>
	<select id="gwd_scss_block_location" name="gwd_scss_block_location">
		<option value="Entire Website" <?php selected($location_value, 'Entire Website'); ?>>Entire Website</option>
		<option value="Conditional Selector" <?php selected($location_value, 'Conditional Selector'); ?>>Conditional Selector</option>
	</select><br><br/>
 
	<label for="gwd_scss_block_conditional_selector">Module Name</label>
	<input type="text" id="gwd_scss_block_conditional_selector" name="gwd_scss_block_conditional_selector" value="<?php echo esc_attr($conditional_selector); ?>"><br><br/>
 
	<label for="gwd_scss_block_priority">Priority:</label>
	<select id="gwd_scss_block_priority" name="gwd_scss_block_priority">
		<?php
		for ($i = 1; $i <= 10; $i++) {
			echo '<option value="' . $i . '" ' . selected($priority_value, $i, false) . '>' . $i . '</option>';
		}
		?>
	</select>
	<?php
}
 
function gwd_save_custom_fields($post_id) {
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if ('gwd_scss_block' !== get_post_type($post_id)) return;
 
	// Save the custom fields
	if (isset($_POST['gwd_codemirror_content'])) {
		$codemirror_content_notags = strip_tags( $_POST['gwd_codemirror_content'] );
		$codemirror_content_filtered = wp_filter_nohtml_kses( $codemirror_content_notags );
		$codemirror_content_fixed = str_replace( scss_code_elements_find_and_replace(false), scss_code_elements_find_and_replace( true ), $codemirror_content_filtered );
		 
		update_post_meta($post_id, 'gwd_codemirror_content', $codemirror_content_fixed);
	}
	 
	if (isset($_POST['gwd_scss_block_location'])) {
		update_post_meta($post_id, 'gwd_scss_block_location', sanitize_text_field($_POST['gwd_scss_block_location']));
	}
 
	if (isset($_POST['gwd_scss_block_conditional_selector'])) {
		update_post_meta($post_id, 'gwd_scss_block_conditional_selector', sanitize_text_field($_POST['gwd_scss_block_conditional_selector']));
	}
 
	if (isset($_POST['gwd_scss_block_priority'])) {
		update_post_meta($post_id, 'gwd_scss_block_priority', absint($_POST['gwd_scss_block_priority']));
	}
	
	
	$modified_time = get_post_modified_time('Y-m-d H:i:s', false, $post_id);
	
	$scss = new Compiler();
	$compiled_css = $scss->compile($codemirror_content_fixed);
	
	$upload_dir = wp_upload_dir();
	$scss_dir = $upload_dir['basedir'] . '/gwd_scss_block';
	$scss_file = $scss_dir . '/' . $post_id . '.css';
	file_put_contents($scss_file, $compiled_css);
	
	/*======
	 * ALLCSS COMPILE
	 *======*/
	/*
	// Calculate the timestamp for 15 seconds in the future
	$cron_timestamp = time() + 30;
	
	// Schedule the cron event
	wp_schedule_single_event($cron_timestamp, 'gwd_allcss_compile', array( $modified_time ) );
	*/
}
 
add_action('add_meta_boxes', 'gwd_add_custom_fields_meta_box');
add_action('save_post', 'gwd_save_custom_fields');
 
 
 
 /*=====
  * HIDE UNECESSARY META BOXES
  *=====*/

function gwd_remove_meta_boxes() {
	// Remove the "wpseo_meta" meta box.
	remove_meta_box('wpseo_meta', 'gwd_scss_block', 'normal');
  
	// Remove the "rocket_post_exclude" postbox.
	remove_meta_box('rocket_post_exclude', 'gwd_scss_block', 'side');
  
	// Remove the "members-cp" postbox.
	remove_meta_box('members-cp', 'gwd_scss_block', 'advanced');
}
add_action('add_meta_boxes', 'gwd_remove_meta_boxes', 100);


/*======
 * HIDE FROM YOAST SITEMAP
 *======*/

function exclude_custom_post_type_from_sitemap($excluded_ids) {
	 // Replace 'your_custom_post_type' with the actual name of your custom post type
	 $custom_post_type = 'gwd_scss_block';
 
	 // Get the IDs of posts belonging to the custom post type
	 $post_ids = get_posts(array(
		 'post_type' => $custom_post_type,
		 'posts_per_page' => -1,
		 'fields' => 'ids',
	 ));
 
	 // Merge the custom post type post IDs with the existing excluded IDs
	 $excluded_ids = array_merge($excluded_ids, $post_ids);
 
	 return $excluded_ids;
 }
 add_filter('wpseo_exclude_from_sitemap_by_post_ids', 'exclude_custom_post_type_from_sitemap');



/*======
 * SAVE CSS FILE
 *======*/
 
function scss_code_elements_find_and_replace( $replace = false ){
	$code_elements_find = array(
		'&amp;',
		'&gt;',
		'&lt;',
		"\'",
		'\"',
	);
	
	$code_elements_replace = array(
		'&',
		'>',
		'<',
		"'",
		'"',
	);
	
	if( $replace == true ){
		return $code_elements_replace;
	}else{
		return $code_elements_find;
	}
}

function gwd_css_compile_allpages_css( $modified_time ){
	$post_args = array(
		'post_type' => 'gwd_scss_block',
		'post_status' => 'publish',
		'posts_per_page' => -1, // Retrieve all posts
		'meta_query' => array(
			  array(
				  'key' => 'gwd_scss_block_location',
				  'value' => 'Entire Website',
				  'compare' => '=' // Compare for exact match
			  ),
		),
		'meta_key' => 'gwd_scss_block_priority', // Specify the custom meta key
		'orderby' => 'meta_value_num', // Order by numeric value
		'order' => 'ASC', // Sort in ascending order (low to high)
	);
	
	$scss_blocks = new WP_Query( $post_args );
	  
	if ( $scss_blocks->have_posts() ) {
		$compiled_css_string = '';
		
		while ( $scss_blocks->have_posts() ) {
			$scss_blocks->the_post();
			$the_ID = get_the_ID();
			$post_modtime = get_post_modified_time('U', false, $the_ID);
			$css_contents = file_get_contents( get_permalink( $the_ID ) . '?' . $post_modtime );
			
			$compiled_css_string .= $css_contents;
		}
		
		$upload_dir = wp_upload_dir();
		$scss_dir = $upload_dir['basedir'] . '/gwd_scss_block';
		$css_file = $scss_dir . '/allpages.css';
		file_put_contents($css_file, $compiled_css_string);
		
		if( get_option('gwd_allcss_modified') == false ){
			add_option('gwd_allcss_modified', $modified_time );
		}else{
			update_option('gwd_allcss_modified', $modified_time );
		}
	}
	
	// Restore original Post Data.
	wp_reset_postdata();
}
add_action( 'gwd_allcss_compile', 'gwd_css_compile_allpages_css', 10, 1 );



/*======
 * MODIFY PERMALINK STRUCTURE
 *======*/

function gwd_custom_permalink_structure($post_link, $post) {
	if ($post->post_type === 'gwd_scss_block') {
		$upload_dir = wp_upload_dir();
		$scss_dir = $upload_dir['baseurl'] . '/gwd_scss_block';
		$post_link = $scss_dir . '/' . $post->ID . '.css';
	}
	return $post_link;
}
add_filter('post_type_link', 'gwd_custom_permalink_structure', 10, 2);
 
 
 
/*======
 * ENQUEUE CSS FILES
 *======*/
  
function gwd_enqueue_scss_styles() {
	/*
	// Specify the URL or path to the stylesheet file you want to enqueue
	$upload_dir = wp_upload_dir();
	$allpages_stylesheet_url = $upload_dir['baseurl'] . '/gwd_scss_block/allpages.css';
	$allcss_modified = get_option('gwd_allcss_modified');
	
	if ( $allcss_modified ) {
		error_log(':::: ALLPAGES CSS EXISTS ::::');
		// Enqueue the stylesheet
		wp_enqueue_style('gwd_scss_block_allpages', $allpages_stylesheet_url, array(), $allcss_modified );
	}else{
		error_log('::::: ALLPAGES CSS DOES NOT EXIST @ ' . $allpages_stylesheet_url . ' :::::');
	}
	*/
	
	$page_id = get_the_ID();
		
	$post_args = array(
		'post_type' => 'gwd_scss_block',
		'post_status' => 'publish',
		'posts_per_page' => -1, // Retrieve all posts
		'meta_key' => 'gwd_scss_block_priority', // Specify the custom meta key
		'orderby' => 'meta_value_num', // Order by numeric value
		'order' => 'ASC', // Sort in ascending order (low to high)
	);
	
	/*$post_args = array(
		'post_type' => 'gwd_scss_block',
		'post_status' => 'publish',
		'posts_per_page' => -1, // Retrieve all posts
		'meta_query' => array(
			  array(
				  'key' => 'gwd_scss_block_location',
				  'value' => 'Conditional Selector',
				  'compare' => '=' // Compare for exact match
			  ),
		),
		'meta_key' => 'gwd_scss_block_priority', // Specify the custom meta key
		'orderby' => 'meta_value_num', // Order by numeric value
		'order' => 'ASC', // Sort in ascending order (low to high)
	);*/
	
	$scss_blocks = new WP_Query( $post_args );
	  
	if ( $scss_blocks->have_posts() ) {
		$post_content = get_the_content($page_id);
		
		while ( $scss_blocks->have_posts() ) {
			$scss_blocks->the_post();
			
			$scss_block_id = get_the_ID();
			
			$selector = get_post_meta($scss_block_id, 'gwd_scss_block_conditional_selector', true );
			$priority = get_post_meta($scss_block_id, 'gwd_scss_block_priority', true );
			$location = get_post_meta($scss_block_id, 'gwd_scss_block_location', true );
			$last_updated_timestamp = get_the_modified_time('U');
			
			$handle = 'gwd_scss_block_' . $scss_block_id;
			  
			if( 'Entire Website' == $location ){
				wp_enqueue_style($handle, get_permalink($scss_block_id), array(), $last_updated_timestamp );
			}else if( isset( $selector ) && !empty( $selector ) ){
				wp_register_style($handle, get_permalink($scss_block_id), array(), $last_updated_timestamp);
				
				if (strpos($post_content, $selector) !== false) {
					wp_enqueue_style($handle);
				}
			}
		}
	}
	
	// Restore original Post Data.
	wp_reset_postdata();
}
add_action('wp_enqueue_scripts', 'gwd_enqueue_scss_styles');



/*======
 * DELETE CSS FILE WHEN POST IS PERMANENTLY DELETED
 *======*/

function delete_scss_block_file($post_id) {
	// Check if the post being deleted is of the "gwd_scss_block" type
	if (get_post_type($post_id) === 'gwd_scss_block') {
		// Get the permalink of the post
		$permalink = get_permalink($post_id);
 
		// Extract the file path from the permalink
		$file_path = str_replace(get_site_url(), ABSPATH, $permalink);
 
		// Check if the file exists and is not a directory
		if (file_exists($file_path) && !is_dir($file_path)) {
			// Delete the file
			unlink($file_path);
		}
	}
}
add_action('before_delete_post', 'delete_scss_block_file', 10, 1);