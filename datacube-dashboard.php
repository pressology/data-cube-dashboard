<?php

/*
Plugin Name: DataCube Dashboard
Description: Plugin Enabling Monitoring Of Backups For DataCube
Version:     1.5
Author:      Elijah Mills
Author URI:  http://pressology.io
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
//$plugin_url = plugin_dir_url( __FILE__ );
define('plugin_url', plugin_dir_url( __FILE__ ));
add_action( 'admin_menu', 'datacube_dashboard_menu' );
add_action( 'wp_ajax_dc_send_alert', 'dc_ajax_email' );
add_action( 'wp_ajax_dc_build_view', 'dc_build_view' );
//add_action( 'wp_enqueue_scripts', 'load_dc_scripts');

function dc_ajax_email () {
	
	$dc_post_id = $_POST['postid'];
	$dc_post_name = get_the_title($dc_post_id);
	$dc_post_days = get_post_meta($dc_post_id, 'days_since_backup', true);
	
	$terms = get_the_terms($dc_post_id, 'brand');
	$term = array_pop($terms);
	$dc_brand = get_field('brand_id', $term);
	$dc_email = get_field('main_email', $term);
	
	wp_mail(
		$dc_email,
		'DataCube Backup Alert.',
		'Alert for client ' . $dc_post_name . ' -- It has been ' . $dc_post_days . ' since last successful backup.');
}

function datacube_dashboard_menu() {
    add_menu_page( 'DataCube Dashboard', 'DataCube', 'manage_options', 'datacube-dashboard', 'datacube_build_page' );
}

function get_dc_posts() {
	$args = array(
		'post_type' => 'client',
		'meta_query' => array(
			array(
				'key' => 'days_since_backup',
				'value' => '06',
				'compare' => '>'
			)
		)
	);
	
	$dc_posts_array = get_posts ( $args );
	
	foreach ($dc_posts_array as $dc_post) {
		$dc_days = get_post_meta( $dc_post->ID, 'days_since_backup', true);
		$dc_email = get_post_meta( $dc_post->ID, 'email', true);
		
		//Get brand name from Brand taxonomy.
		$terms = get_the_terms($dc_post, 'brand');
		$term = array_pop($terms);
		$dc_brand = get_field('brand_id', $term);
		?>
		<li>
			<?php echo $dc_post->post_title; ?> - Last Backup: <?php echo $dc_days; ?> Days - <button class='send-alert' name='<?php echo $dc_post->ID; ?>'>Send Alert</button>
			Brand : <?php print_r($dc_brand); ?>
		</li>
		<?php
	}
	
}

function load_dc_scripts() {
	wp_register_script('dc-dash-js', plugin_dir_url( __FILE__ ) . 'datacube-dashboard.js', array('jquery'));
	wp_enqueue_script('dc-dash-js');
	wp_enqueue_style( "dcdash-style", $plugin_url . "dcdash-style.css" );
}

function datacube_build_page() {
	load_dc_scripts();
	?>
	<div id="wrapper">
		<h1> Datacube Dashboard</h1>
		<div id="dc_dash_nav">
		<a class="dc_dash_nav_btn" id="overview_btn">Overview</a>
		<a class="dc_dash_nav_btn" id="brands_btn">Brands</a>
		</div>
		<div id="all-the-content">
		<div id="left-column">
			<h2> Alerts </h2>
			<div class="horizontal-line">
			</div>
			<?php get_dc_posts(); ?>
		</div>
		<div id="right-column">
			<h2> Brand Overview </h2>
			<div class="horizontal-line">
			</div>
		</div>
		</div>
	</div>
	<?php
	
}

function dc_build_view() {
	
	if($_POST['view'] == 'brand') {
		?>
	
		<h2>Brand View</h2>
		<div class='horizontal-line'></div>
		<div id='brand-list'>
			<table class="wp-list-table widefat fixed striped posts">
				<thead>
					<th scope="col" id="brand">Brand</th>
					<th scope="col" id="clients">Clients</th>
					<th scope="col" id="errors">Errors</th>
					<th scope="col" id="alert">Alert</th>
				</thead>
				<tbody id="the-list">
				<?php
				$brands = get_terms('brand');
				print_r($brands);

				foreach($brands as $brand) {
					wp_reset_query();
					$args = array(
						'post_type' => 'client',
						'tax_query' => array(
							array(
								'taxonomy' => 'brand',
								'field' => 'slug',
								'terms' => $brand
							)
						)
					);

					$clients = get_posts($args);
					$totalclients = 0;
					print_r($clients);

					foreach($clients as $client) {
						$totalclients++;
						$clientname = $client->post_title;
						echo $totalclients;
					}
					?>
						<tr id="brand">
							<td><?php echo $brand->slug; ?></td>
							<td><?php echo $totalclients; ?></td>
						</tr>
					<?php
	
				}

				?>
				</tbody>
			</table>
		</div>
		
		<?php
	}
	
	else if($_POST['view'] == 'overview') {
		?>
		
		<h2>Overview</h2>
		<div class='horizontal-line'></div>
		<div id='left-column'>
				<h2>Clients</h2>
				<div class='horizontal-line'></div>
				<table class="wp-list-table widefat fixed striped posts">
				<thead>
					<th scope="col" id="client">Client</th>
					<th scope="col" id="errors">Errors</th>
					<th scope="col" id="alert">Alert</th>
				</thead>
				<tbody id="the-list">
					<tr id="brand1">
						<td>DC000_dude</td>
						<td>No Backup (7 Days)</td>
						<td>No Alert Sent</td>
					</tr>
					<tr id="brand1">
						<td>DC000_this</td>
						<td>No Backup (7 Days)</td>
						<td>No Alert Sent</td>
					</tr>
					<tr id="brand1">
						<td>DC000_that</td>
						<td>No Backup (7 Days)</td>
						<td>No Alert Sent</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div id='right-column'>
			<h2>Brands</h2>
			<div class='horizontal-line'></div>
			<ul>
				<li>Nothing here yet.</li>
			</ul>
		</div>
		<?php
	}
	
	die();
}