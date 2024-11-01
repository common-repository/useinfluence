<?php

/**
 * Fired during plugin activation
 *
 * @link       https://useinfluence.co
 * @since      1.0.0
 *
 * @package    Useinfluence
 * @subpackage Useinfluence/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Useinfluence
 * @subpackage Useinfluence/includes
 * @author     Target Solutions <saransh@useinfluence.co>
 */
class Useinfluence_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;
		$sql1 = "CREATE TABLE IF NOT EXISTS tracking_id (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		trackingId varchar(20) NOT NULL,
		app_key longtext NOT NULL,
		PRIMARY KEY (id)
		)";

		$wpdb->query($sql1);
	}

}
