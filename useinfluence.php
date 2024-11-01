<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://useinfluence.co
 * @since             1.0.0
 * @package           Influence
 *
 * @wordpress-plugin
 * Plugin Name:       Influence
 * Plugin URI:        https://github.com/InfluenceIO/wordpress-plugin
 * Description:       Influence WordPress Plugin for TrackingId Input.
 * Version:           1.0.8
 * Author:            Target, Inc
 * Author URI:        https://useinfluence.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       useinfluence
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'INFLUENCE_PLUGIN_VERSION', '1.0.8' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-useinfluence-activator.php
 */
function activate_useinfluence() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-useinfluence-activator.php';
	Useinfluence_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-useinfluence-deactivator.php
 */
function deactivate_useinfluence() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-useinfluence-deactivator.php';
	Useinfluence_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_useinfluence' );
register_deactivation_hook( __FILE__, 'deactivate_useinfluence' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-useinfluence.php';

if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) 
{
  require plugin_dir_path( __FILE__ ) . 'includes/useinfluence_send_wooco_data.php';
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_useinfluence() {

	$plugin = new Useinfluence();
	$plugin->run();

}

/**
 * The hook action to register plugin menu  method.
 */
add_action('admin_menu', 'influence_menu');

/**
 * The core plugin menu  method that is used to define app name app id etc,
 * admin-control and public-facing.
 */

function influence_menu(){
  $appName = 'Influence';
  $appID = 'influence-plugin';
  $influ_dir_url = plugin_dir_url(__FILE__);
  $icon = $influ_dir_url.'assets/plg-icon.png';
  add_menu_page($appName, $appName, 'administrator', $appID . '-top-level', 'influence_screen',$icon);
}

/**
 * The core pluginAdminScreen method that is used to define trackingId as input for app,
 */


function influence_screen() {
	
	 global $trackingId;
	 global $appKey;
	 global $wpdb;
     $campaign_id = '';
	 $query = $wpdb->get_results("SELECT * FROM tracking_id ORDER BY ID DESC LIMIT 1", OBJECT);
	 if(isset($query) && !empty($query))
	 {
		 foreach($query as $row)
		 {
			$trackingId = $row->trackingId;
			$campaign_id = $row->campaign_id; 
			$appKey = $row->app_key;
		 }
		 $action = "Update";
	 }else
	 {
		 $action = "Insert";
	 }

	 if(isset($_POST['trackingId'])){
			$trackingId = sanitize_text_field($_POST['trackingId']);
			$data['trackingId'] = $trackingId;
			//update_post_meta($post->ID, 'trackingId', $trackingId);
	 }
		
	 if(isset($_POST['campaign_id'])){
			$campaign_id = sanitize_text_field($_POST['campaign_id']);
			//$data['campaign_id'] = $campaign_id;
			update_option('useinflu_campaign_id',$campaign_id);
	 }		
		
			
	 if(isset($_POST['app_key'])){
			$appKey = sanitize_text_field($_POST['app_key']);
			$data['app_key'] = $appKey;
	 }
	
	if(isset($action) && $action=="Insert" && isset($data) && !empty($data)){
		$columns = array_keys($data);
		$columns = implode(",",$columns);
		$values = implode("','",$data);
		$values = "'".$values."'";
		$sql3 ="INSERT INTO tracking_id(".$columns.") VALUES (".$values.")";
		$wpdb->query($sql3);
	}elseif(isset($action) && $action=="Update" && isset($data) && !empty($data))
	{
		$update = array();
		foreach($data as $key=>$val)
		{
			$update[] = "`$key`="."'".$val."'";
		}
		$update = implode(",",$update);
		$sql = "UPDATE `tracking_id` SET ".$update." WHERE id = 1";
		$wpdb->query($sql);
	}
	$campaign_id = "";
	
	 $useinflu_campaign = get_option('useinflu_campaign_id');
     if(isset($useinflu_campaign) && !empty($useinflu_campaign))
	 {
		 $campaign_id = $useinflu_campaign;
	 }	 
	
	?>
	<a href='<?php esc_url_raw("https://useinfluence.co",null);?>'>
	<img src="<?php echo plugin_dir_url(__FILE__) . 'assets/logo-influence-2.a5936714.png' ?>" width="180px" height="50px" style="margin-top:20px;" />
	</a>
	<br />
	<h3 class='describe' style='font-family:sans-serif;padding: 10px;border-left:  5px solid  #999;background: #99999930;'>If you don't have an account -

            <a href='https://app.useinfluence.co/signup' target="_blank">
	<strong>signup here!</strong>
	</a>
	</h3>
	<form action='' method='POST'>
    <h2>Tracking ID : </h2>
  <input id='trackingId' type='text' name='trackingId' value="<?php echo $trackingId;?>" class='api' style='padding: 5px 10px; border-radius:5px;width: 300px;' placeholder='e.g. INF-xxxxxxxx'></input>
	<br/>
    <h2>Campaign ID : </h2>
  <input id='campaign_id' type='text' name='campaign_id' value="<?php echo $campaign_id;?>" class='campaign_id' style='padding: 5px 10px; border-radius:5px;width: 300px;' placeholder='Campaign Id'></input>
	<br/>	
    <h2>API Key : </h2>
  <input id='app_key' type='text' name='app_key' value="<?php echo $appKey;?>" class='api' style='padding: 5px 10px; border-radius:5px;width: 300px;' placeholder='API Key'></input>
	<br/>
    <hr/>
    <br/>
	<input type='submit' class='submit' style='padding: 5px 10px ;cursor:pointer; color:#fff; border-radius:5px;background-color:#097fff' value='Save'></input>
	<br />
	<a style="margin: 10px 0px 0px 10px !important; display: block;" href='https://help.useinfluence.co/hc/en-us/articles/360035612793-Integrating-with-Wordpress' target='_blank'>Where is my Tracking ID ?</a>
	<form>
	<?php
}

add_action('wp_head', 'useinfluence_trackingid');



function useinfluence_trackingid(){
	global $trackingId;
	global $wpdb;
	$query = $wpdb->get_results("SELECT trackingId FROM tracking_id ORDER BY ID DESC LIMIT 1", OBJECT);
	foreach($query as $row)
	{
				$trackingId = $row->trackingId;
	}
	echo "
	<script src='https://cdn.useinfluence.co/static/influence-analytics.js?trackingId=$trackingId' defer> </script>
		";
	}

run_useinfluence();

  ?>
